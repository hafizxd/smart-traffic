<?php

namespace App\Http\Controllers\Api;

use App\Models\Sensor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SensorController extends Controller
{
    public function index(Request $request)
    {
        $sensors = Sensor::all();

        $db = connectInfluxDB();
        $queryApi = $db->createQueryApi();

        foreach ($sensors as $sensor) {
            $bucket = "smart_traffic";
            $query = "from(bucket: \"$bucket\")
                |> range(start: -1h)
                |> filter(fn: (r) => r._measurement == \"air_qualities\")
                |> filter(fn: (r) => r.code == \"" . $sensor->code . "\")
                |> last()";

            // ugly ahh
            // get newest/latest record 
            $sensor->carbon_monoxide = 0;
            $results = $queryApi->query($query);
            if (count($results) > 0) {
                $records = $results[0]->records;
                if (count($records) > 0) {
                    $sensor->carbon_monoxide = $records[0]->values["_value"];
                }
            }
        }
        $db->close();

        return composeReply(true, 'Success', $sensors);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|exists:sensors',
            'carbon_monoxide' => 'required'
        ]);

        $db = connectInfluxDB();
        $writeApi = $db->createWriteApi();

        $point = \InfluxDB2\Point::measurement('air_qualities')
            ->addTag('code', $request->code)
            ->addField('carbon_monoxide', floatval($request->carbon_monoxide))
            ->time(time());

        try {
            $writeApi->write($point);
            $writeApi->close();
        } catch (\InfluxDB2\ApiException $e) {
            return composeReply(false, 'Errors', $e->getMessage(), 500);
        }

        return composeReply(true, 'Success', []);
    }
}
