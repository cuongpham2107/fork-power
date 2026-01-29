<?php

namespace Database\Seeders;

use App\Models\BatteryCharger;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BatteryChargerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BatteryCharger::create([
            'code' => 'CHG-001',
            'location' => 'Warehouse A',
        ]);

        BatteryCharger::create([
            'code' => 'CHG-002',
            'location' => 'Warehouse B',
        ]);

        BatteryCharger::create([
            'code' => 'CHG-003',
            'location' => 'Maintenance Bay',
        ]);
    }
}
