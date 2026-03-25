<?php

namespace App\Console\Commands;

use App\Models\ForkLift;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportForkLifts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-fork-lifts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import ForkLift records from an Excel file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = base_path('Copy of Danh sách xe nâng điện.xlsx');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Loading file: {$filePath}");

        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            array_shift($rows);

            $count = 0;
            foreach ($rows as $row) {
                // User defined columns:
                // Col 1: name (index 0)
                // Col 2: serial_number (index 1)
                // Col 3: dvql (index 2)
                // Col 4: brand (index 3)

                $name = $row[0] ?? null;
                $serialNumber = $row[1] ?? null;
                $dvql = $row[2] ?? null;
                $brand = $row[3] ?? null;

                if (empty($serialNumber) && empty($name)) {
                    continue;
                }

                ForkLift::updateOrCreate(
                    [
                        'serial_number' => $serialNumber,
                    ],
                    [
                        'name' => $name,
                        'dvql' => $dvql,
                        'brand' => $brand,
                        'total_working_hours' => 0,
                        'status' => 'active', // Default status
                    ]
                );

                $count++;
            }

            $this->info("Successfully imported {$count} ForkLift records.");
            return 0;

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}
