<?php

namespace App\Constants;

use App\Constants\BaseConstant;

class CarpoolingPassangerStatus extends BaseConstant
{
    const NEGOTIATE = 1;
    const DEAL = 2;
    const PICKED = 3;
    const DROPPED = 4;
    const DONE = 5;


    public static function labels()
    {
        return [
            static::NEGOTIATE => 'Negosiasi',
            static::DEAL => 'Deal Harga',
            static::PICKED => 'Sudah Naik',
            static::DROPPED => 'Sudah Tiba',
            static::DONE => 'Selesai',
        ];
    }
}