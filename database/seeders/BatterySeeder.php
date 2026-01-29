<?php

namespace Database\Seeders;

use App\Models\Battery;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BatterySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Battery::create([
            'code' => 'BAT-001',
            'type' => 'Lithium',
            'capacity' => '100Ah',
            'voltage' => '24V',
            'size' => '200x150x100mm',
            'total_working_hours' => 1250.00,
            'status' => 'standby',
        ]);

        Battery::create([
            'code' => 'BAT-002',
            'type' => 'Alkaline',
            'capacity' => '200Ah',
            'voltage' => '48V',
            'size' => '300x200x150mm',
            'total_working_hours' => 446.00, // Starts at 446
            'status' => 'in_use',
        ]);

        Battery::create([
            'code' => 'BAT-003',
            'type' => 'Lithium',
            'capacity' => '150Ah',
            'voltage' => '36V',
            'size' => '250x180x120mm',
            'total_working_hours' => 9.50, // Starts at 9.5
            'status' => 'in_use',
        ]);
    }
}
