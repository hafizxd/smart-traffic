<?php

namespace App\Constants;

use App\Constants\BaseConstant;

class CarpoolingPassangerStatus extends BaseConstant
{
    const NEGOTIATE = 1;
    const DEAL = 2;
    const DONE = 3;


    public static function labels()
    {
        return [
            static::NEGOTIATE => 'Negosiasi',
            static::DEAL => 'Deal Harga',
            static::DONE => 'Selesai',
        ];
    }
}