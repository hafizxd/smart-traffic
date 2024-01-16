<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carpooling extends Model
{
    protected $guarded = [];

    public function driver()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(\App\Models\Vehicle::class);
    }

    public function carpoolingPassangers()
    {
        return $this->hasMany(\App\Models\CarpoolingPassanger::class);
    }
}
