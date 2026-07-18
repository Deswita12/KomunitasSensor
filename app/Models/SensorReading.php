<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    protected $fillable = ['device_id', 'temp', 'rh', 'iaq', 'pressure', 'state', 'recorded_at'];

    protected $casts = [
        'recorded_at' => 'datetime',
        'temp' => 'float',
        'rh' => 'float',
        'iaq' => 'float',
        'pressure' => 'float',
    ];
}
