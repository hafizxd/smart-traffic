<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarpoolingPassanger extends Model
{
    protected $guarded = [];

    public function carpooling()
    {
        return $this->belongsTo(\App\Models\Carpooling::class);
    }

    public function passanger()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
