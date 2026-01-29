<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatteryCharger extends Model
{
    protected $fillable = [
        'code',
        'location',
    ];

    protected $casts = [
        'code' => 'string',
        'location' => 'string',
    ];

    // Không có relationships trực tiếp dựa trên migration
}
