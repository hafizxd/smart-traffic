<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
            ->addField('carbon_monoxide', $request->carbon_monoxide)
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
