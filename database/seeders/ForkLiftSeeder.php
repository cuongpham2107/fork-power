<?php

namespace Database\Seeders;

use App\Models\ForkLift;
use Illuminate\Database\Seeder;

class ForkLiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ForkLift::create([
            'name' => 'Komatsu 01',
            'brand' => 'KOMATSU',
            'serial_number' => 'A31-00001',
            'status' => 'active',
            'total_working_hours' => 1000,
        ]);

        ForkLift::create([
            'name' => 'Toyota 02',
            'brand' => 'TOYOTA',
            'serial_number' => 'A31-00002',
            'status' => 'active',
            'total_working_hours' => 2000,
        ]);

        ForkLift::create([
            'name' => 'Hyster 03',
            'brand' => 'HYSTER',
            'serial_number' => 'M236-846149',
            'status' => 'maintenance',
            'total_working_hours' => 3000,
        ]);
    }
}
