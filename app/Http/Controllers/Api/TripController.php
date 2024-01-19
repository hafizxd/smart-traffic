<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TripController extends Controller
{
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
        } else {
            return composeReply(true, "Succes", json_decode($response));
            // echo $response;
        }
    }
}
