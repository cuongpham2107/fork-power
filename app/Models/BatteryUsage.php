<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatteryUsage extends Model
{
    protected $fillable = [
        'battery_id',
        'fork_lift_id',
        'charger_bar',
        'screen_bar',
        'hour_initial',
        'installed_at',
        'hour_out',
        'removed_at',
        'working_hours',
        'installed_by',
        'removed_by',
        'status',
    ];

    protected $casts = [
        'charger_bar' => 'integer',
        'screen_bar' => 'integer',
        'hour_initial' => 'decimal:2',
        'installed_at' => 'datetime',
        'hour_out' => 'decimal:2',
        'removed_at' => 'datetime',
        'working_hours' => 'decimal:2',
        'status' => 'string',
    ];

    public function battery()
    {
        return $this->belongsTo(Battery::class);
    }

    public function forkLift()
    {
        return $this->belongsTo(ForkLift::class);
    }

    public function installedBy()
    {
        return $this->belongsTo(User::class, 'installed_by');
    }

    public function removedBy()
    {
        return $this->belongsTo(User::class, 'removed_by');
    }
}
