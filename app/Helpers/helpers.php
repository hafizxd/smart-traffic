<?php

if (!function_exists('connectInfluxDB')) {
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

if (!function_exists('composeReply')) {
    function composeReply($success, $message, $payload, $statusCode = 200)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'payload' => $payload
        ], $statusCode);
    }
}

if (!function_exists('generateRandomCode')) {
    function generateRandomCode($prefix, $table, $column)
    {
        $rand = $prefix . "_" . mt_rand(1000000000, 9999999999);

        $data = DB::table($table)->select('id')->where($column, $rand)->first();
        if (isset($data))
            return generateRandomCode($prefix, $table, $column);

        return $rand;
    }
}