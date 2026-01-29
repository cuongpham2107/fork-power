<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Battery extends Model
{
    protected $fillable = [
        'code',
        'type',
        'capacity',
        'voltage',
        'size',
        'total_working_hours',
        'status',
    ];

    protected $casts = [
        'total_working_hours' => 'decimal:2',
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
