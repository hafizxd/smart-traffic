<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleImage extends Model
{
    protected $guarded = [];


    public function vehicle()
    {
        return $this->belongsTo(\App\Models\Vehicle::class);
    }
}
