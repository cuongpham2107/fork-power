<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Battery extends Model
{
    protected $fillable = [
        'serial_number',
        'code',
        'type',
        'capacity',
        'voltage',
        'size',
        'used_at',
        'status',
    ];

    public function batteryUsages()
    {
        return $this->hasMany(BatteryUsage::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'standby'); // Assuming 'standby' is the status for available batteries
    }
}
