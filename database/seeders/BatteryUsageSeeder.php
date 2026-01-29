<?php

namespace Database\Seeders;

use App\Models\BatteryUsage;
use Illuminate\Database\Seeder;

class BatteryUsageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BatteryUsage::create([
            'battery_id' => 1,
            'fork_lift_id' => 1,
            'charger_bar' => 4,
            'screen_bar' => 100,
            'hour_initial' => 1242.0,
            'installed_at' => now()->subHours(16),
            'hour_out' => 1250.0,
            'removed_at' => now()->subHours(8),
            'working_hours' => 8.0,
            'installed_by' => 1,
            'removed_by' => 1,
            'status' => 'finished',
        ]);

        BatteryUsage::create([
            'battery_id' => 2,
            'fork_lift_id' => 2,
            'charger_bar' => 4,
            'screen_bar' => 95,
            'hour_initial' => 446.0,
            'installed_at' => now()->subHours(4), // 4 hours running -> 446 + 4 = 450
            'status' => 'running',
            'installed_by' => 1,
        ]);

        BatteryUsage::create([
            'battery_id' => 3,
            'fork_lift_id' => 1,
            'charger_bar' => 3,
            'screen_bar' => 88,
            'hour_initial' => 9.5,
            'installed_at' => now()->subMinutes(30), // 0.5 hours running -> 9.5 + 0.5 = 10
            'status' => 'running',
            'installed_by' => 1,
        ]);
    }
}
