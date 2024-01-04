<?php

namespace App\Constants;

use App\Constants\BaseConstant;

class CarpoolingStatus extends BaseConstant
{
    const ADVERTISE = 1;
    const FULL = 2;
    const DEPARTURE = 3;
    const ARRIVE = 4;
    const DONE = 5;


    public static function labels()
    {
        return [
            static::ADVERTISE => 'Iklan',
            static::FULL => 'Kapasitas Penuh',
            static::DEPARTURE => 'Berangkat',
            static::ARRIVE => 'Sudah Tiba',
            static::DONE => 'Selesai',
        ];
    }
}