<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TripDetail;
use App\Models\TripSensor;

class Trip extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function tripDetail()
    {
        return $this->hasOne(TripDetail::class);
    }

    public function tripSensors()
    {
        return $this->hasMany(TripSensor::class);
    }
}
