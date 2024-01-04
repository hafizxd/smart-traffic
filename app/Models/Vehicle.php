<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function vehicleImages()
    {
        return $this->hasMany(\App\Models\VehicleImage::class);
    }
}
