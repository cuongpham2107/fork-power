<?php

namespace App\Console\Commands;

use App\Models\Battery;
use Carbon\Carbon;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportBatteries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-batteries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import battery records from an Excel file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = base_path('Copy of Danh sách xe nâng điện, bình ắc quy xe nâng.xlsx');

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
                // Col 1: serial_number (index 0)
                // Col 2: voltage (index 1)
                // Col 3: capacity (index 2)
                // Col 4: used_at (index 3)
                // Col 5: code (index 4)

                $serialNumber = $row[0] ?? null;
                $voltage = $row[1] ?? null;
                $capacity = $row[2] ?? null;
                $usedAtRaw = $row[3] ?? null;
                $code = $row[4] ?? null;

                if (empty($serialNumber) && empty($code)) {
                    continue;
                }

                $usedAt = $this->parseDate($usedAtRaw);

                Battery::updateOrCreate(
                    [
                        'serial_number' => $serialNumber,
                    ],
                    [
                        'voltage' => $voltage,
                        'capacity' => $capacity,
                        'used_at' => $usedAt,
                        'code' => $code,
                        'status' => 'standby', // Default status from migration
                    ]
                );

                $count++;
            }

            $this->info("Successfully imported {$count} battery records.");
            return 0;

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // Handle Excel date serial number
        if (is_numeric($value) && $value > 10000) {
            return Date::excelToDateTimeObject($value);
        }

        // Handle year-only formats (e.g., 2015)
        if (is_numeric($value) && strlen($value) === 4) {
            return Carbon::createFromFormat('Y', $value)->startOfYear();
        }

        // Handle DD/MM/YYYY
        try {
            return Carbon::createFromFormat('d/m/Y', $value);
        } catch (\Exception $e) {
            // Keep trying or return null
        }

        // Fallback to Carbon parse
        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }
}
