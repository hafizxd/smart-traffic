<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SensorController extends Controller
{
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
