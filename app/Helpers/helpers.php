<?php

if (! function_exists('connectInfluxDB')) {
    function connectInfluxDB()
    {
        $client = new \InfluxDB2\Client([
            'url' => env('INFLUXDB_HOST'),
            'token' => env('INFLUXDB_TOKEN'),
            'bucket' => env('INFLUXDB_BUCKET'),
            'org' => env('INFLUXDB_ORG'),
            'precision' => \InfluxDB2\Model\WritePrecision::S
        ]);
        
        return $client;
    }
}

if (! function_exists('composeReply')) {
    function composeReply($success, $message, $payload, $statusCode=200)
    {
        return response()->json([ 
            'success' => $success,
            'message' => $message,
            'payload' => $payload
        ], $statusCode);
    }
}