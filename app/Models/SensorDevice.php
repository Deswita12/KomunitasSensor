<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorDevice extends Model
{
    protected $fillable = ['device_id', 'name', 'is_active', 'order'];

    protected $casts = ['is_active' => 'boolean'];

    public static function activeDeviceIds(): array
    {
        return static::where('is_active', true)
            ->orderBy('order')
            ->pluck('device_id')
            ->toArray();
    }
}