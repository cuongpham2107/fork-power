<?php

namespace Database\Seeders;

use App\Models\Battery;
use Illuminate\Database\Seeder;

class BatterySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Battery::create([
            'serial_number' => 'LG28753',
            'code' => 'BAT-001',
            'type' => 'Lithium',
            'capacity' => '100Ah',
            'voltage' => '24V',
            'size' => '200x150x100mm',
            'used_at' => '2022-01-01',
            'status' => 'standby',
        ]);

        Battery::create([
            'serial_number' => 'LG28754',
            'code' => 'BAT-002',
            'type' => 'Alkaline',
            'capacity' => '200Ah',
            'voltage' => '48V',
            'size' => '300x200x150mm',
            'used_at' => '2022-01-01',
            'status' => 'in_use',
        ]);

        Battery::create([
            'serial_number' => 'LG28755',
            'code' => 'BAT-003',
            'type' => 'Lithium',
            'capacity' => '150Ah',
            'voltage' => '36V',
            'size' => '250x180x120mm',
            'used_at' => '2022-01-01',
            'status' => 'in_use',
        ]);
    }
}
