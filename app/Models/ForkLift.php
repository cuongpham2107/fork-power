<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForkLift extends Model
{
    protected $fillable = [
        'name',
        'brand',
        'serial_number',
        'total_working_hours',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function batteryUsages()
    {
        return $this->hasMany(BatteryUsage::class);
    }

    public function activeUsages()
    {
        return $this->hasMany(BatteryUsage::class)->where('status', 'running');
    }
}
