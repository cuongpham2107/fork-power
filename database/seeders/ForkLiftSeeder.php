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
            'model' => 'FD30T-16',
            'status' => 'active',
            'total_working_hours' => 1000,
        ]);

        ForkLift::create([
            'name' => 'Toyota 02',
            'brand' => 'TOYOTA',
            'model' => '7FBRU25',
            'status' => 'active',
            'total_working_hours' => 2000,
        ]);

        ForkLift::create([
            'name' => 'Hyster 03',
            'brand' => 'HYSTER',
            'model' => 'H3.00XM',
            'status' => 'maintenance',
            'total_working_hours' => 3000,
        ]);
    }
}
