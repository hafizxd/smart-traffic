<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Trip;
use App\Models\Sensor;

class TripController extends Controller
{
    public function indexTrip()
    {
        $trips = Auth::user()->trips()->orderBy('created_at', 'asc')->get();

        return composeReply(true, 'Success', $trips);
    }

    public function show($id)
    {
        $trip = Auth::user()->trips()->with(['tripSensors', 'tripDetail'])->findOrFail($id);

        $trip->detail = json_decode($trip->tripDetail->data);
        unset($trip['tripDetail']);

        return composeReply(true, 'Success', $trip);
    }

    public function start(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bbox' => 'required|array',
            'points.coordinates' => 'required|array',
            'carbon_monoxide' => 'required'
        ]);

        if ($validator->fails()) {
            return composeReply(false, 'Validation fails.', [
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $trip = Trip::create([
                'user_id' => Auth::user()->id,
                'departure_at' => date('Y-m-d H:i:s'),
                'departure_latitude' => $request->bbox[0],
                'departure_longitude' => $request->bbox[1],
                'arrive_latitude' => $request->bbox[2],
                'arrive_longitude' => $request->bbox[3],
                'co_total' => $request->carbon_monoxide
            ]);

            $trip->tripDetail()->create([
                'data' => json_encode($request->toArray())
            ]);

            $tripSensorData = [];
            foreach ($request->sensors as $sensor) {
                $sensor = (object) $sensor;
                $sensor = Sensor::find($sensor->id);
                if (!isset($sensor))
                    throw new \Exception('sensor not found');

                $tripSensorData[] = [
                    'code' => $sensor->code,
                    'latitude' => $sensor->latitude,
                    'longitude' => $sensor->longitude,
                    'radius' => $sensor->radius,
                ];
            }

            $trip->tripSensors()->createMany($tripSensorData);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return composeReply(false, $th->getMessage(), [], 400);
        }

        return composeReply(true, 'Success', $trip);
    }

    public function end($id)
    {
        $trip = Auth::user()->trips()->findOrFail($id);
        $trip->update([
            'arrive_at' => date('Y-m-d H:i:s')
        ]);

        return composeReply(true, 'Success', $trip);
    }

    public function indexNavigation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'departure_latitude' => 'required',
            'departure_longitude' => 'required',
            'destination_latitude' => 'required',
            'destination_longitude' => 'required'
        ]);

        if ($validator->fails()) {
            return composeReply(false, 'Validation fails.', [
                'errors' => $validator->errors()
            ], 422);
        }

        $query = array(
            "key" => env('GRAPHHOOPER_API_KEY')
        );

        $curl = curl_init();

        $payload = array(
            "points" => array(
                array(
                    $request->departure_latitude,
                    $request->departure_longitude
                ),
                array(
                    $request->destination_latitude,
                    $request->destination_longitude
                )
            ),
            "algorithm" => "alternative_route",
            "alternative_route.max_paths" => 4,
            "alternative_route.max_weight_factor" => 2,
            "alternative_route.max_share_factor" => 1,
            "profile" => "scooter",
            "locale" => "en",
            "instructions" => false,
            "calc_points" => true,
            "points_encoded" => false
        );

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_URL => "https://graphhopper.com/api/1/route?" . http_build_query($query),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            return composeReply(false, $error, [], 500);
            // echo "cURL Error #:" . $error;
        }

        $response = json_decode($response);

        $db = connectInfluxDB();
        $queryApi = $db->createQueryApi();

        $lesserCO = 0;
        $selectedKeyPath = 0;

        foreach ($response->paths as $keyPath => $path) {
            $carbonMonoxide = 0;
            $sensorsData = [];

            foreach ($path->points->coordinates as $coordinate) {
                $sensors = DB::select('
                    SELECT ((ACOS(SIN(:lat1 * PI() / 180) * SIN(latitude * PI() / 180) + 
                            COS(:lat2 * PI() / 180) * COS(latitude * PI() / 180) * COS((:lon - longitude) * 
                            PI() / 180)) * 180 / PI()) * 60 * 1609.34) AS distance,
                            sensors.id 
                    FROM sensors 
                    HAVING distance<=50 
                    ORDER BY distance ASC
                ', ["lat1" => $coordinate[0], "lat2" => $coordinate[0], "lon" => $coordinate[1]]);

                if (!empty($sensors)) {
                    foreach ($sensors as $sensor) {
                        if (isset($sensorsData[$sensor->id]))
                            continue;

                        // get latest co
                        $bucket = "smart_traffic";
                        $query = "from(bucket: \"$bucket\")
                            |> range(start: -1h)
                            |> filter(fn: (r) => r._measurement == \"air_qualities\")
                            |> last()";

                        // ugly ahh
                        // get newest/latest record 
                        $latestCarbonMonoxide = 0;
                        $results = $queryApi->query($query);
                        if (count($results) > 0) {
                            $records = $results[0]->records;
                            if (count($records) > 0) {
                                $latestCarbonMonoxide = $records[0]->values["_value"];
                            }
                        }

                        $carbonMonoxide += $latestCarbonMonoxide;
                        $sensorsData[$sensor->id] = $sensor;
                    }
                }
            }

            $meanCO = 0;
            if ($carbonMonoxide > 0) {
                $meanCO = $carbonMonoxide / count($sensorsData);

                if ($lesserCO == 0 || ($carbonMonoxide < $lesserCO)) {
                    $selectedKeyPath = $keyPath;
                    $lesserCO = $carbonMonoxide;
                }
            }

            $response->paths[$keyPath]->is_selected = false;
            $response->paths[$keyPath]->carbon_monoxide = $meanCO;
            $response->paths[$keyPath]->sensors = array_values($sensorsData);
        }

        $db->close();

        $response->paths[$selectedKeyPath]->is_selected = true;

        return composeReply(true, "Succes", $response);
    }
}
