<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Truck;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;

class ImportFleetData extends Command
{
    protected $signature = 'fleet:import-csv {file}';
    protected $description = 'Import fleet data from OVERLAND CSV file';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Reading CSV file: {$filePath}");
        
        // Read file with proper encoding handling
        $fileContent = file_get_contents($filePath);
        $fileContent = mb_convert_encoding($fileContent, 'UTF-8', 'UTF-8');
        
        $rows = array_map('str_getcsv', explode("\n", $fileContent));
        
        // Skip first 2 rows (headers)
        $headers = $rows[1] ?? [];
        $dataRows = array_slice($rows, 2);

        // Find column indices
        $ownerIdx = $this->findColumnIndex($headers, 'TRUCK 0WNER');
        $truckIdx = $this->findColumnIndex($headers, 'TRUCKS');
        $trailerIdx = $this->findColumnIndex($headers, 'TRAILER');
        $driverIdx = $this->findColumnIndex($headers, 'DRIVER NAME');
        $typeIdx = $this->findColumnIndex($headers, 'TYPE');
        $clientIdx = $this->findColumnIndex($headers, 'CLIENT');
        $destIdx = $this->findColumnIndex($headers, 'DESTINA');

        $this->info("Column indices - Owner: {$ownerIdx}, Truck: {$truckIdx}, Driver: {$driverIdx}");

        $imported = 0;
        $skipped = 0;

        DB::beginTransaction();
        
        try {
            foreach ($dataRows as $rowNum => $row) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                $truckPlate = $this->cleanString($row[$truckIdx] ?? '');
                $driverName = $this->cleanString($row[$driverIdx] ?? '');
                
                if (empty($truckPlate)) {
                    $skipped++;
                    continue;
                }

                // Create or find driver
                $driver = null;
                if (!empty($driverName)) {
                    $driver = Driver::firstOrCreate(
                        ['name' => $driverName],
                        [
                            'license_number' => 'IMP-' . substr(md5($driverName), 0, 10),
                            'phone' => '',
                            'status' => 'active',
                            'performance_score' => 85
                        ]
                    );
                }

                // Parse truck data
                $brand = 'Imported';
                $model = 'Fleet';

                // Create or update truck
                $truck = Truck::updateOrCreate(
                    ['plate_number' => $truckPlate],
                    [
                        'brand' => $brand,
                        'model' => $model,
                        'year' => 2020,
                        'status' => 'active',
                        'driver_id' => $driver ? $driver->id : null,
                        'owner' => $this->cleanString($row[$ownerIdx] ?? ''),
                        'trailer' => $this->cleanString($row[$trailerIdx] ?? ''),
                        'type' => $this->cleanString($row[$typeIdx] ?? ''),
                        'client' => $this->cleanString($row[$clientIdx] ?? ''),
                        'destination' => $this->cleanString($row[$destIdx] ?? '')
                    ]
                );

                $imported++;
                
                if ($imported % 10 == 0) {
                    $this->info("Imported {$imported} trucks...");
                }
            }

            DB::commit();
            
            $this->info("Import completed successfully!");
            $this->info("Imported: {$imported} trucks");
            $this->info("Skipped: {$skipped} rows");
            
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Import failed: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }

    private function cleanString($string)
    {
        $string = trim($string);
        $string = preg_replace('/[^\x20-\x7E]/', '', $string); // Remove non-printable characters
        return $string;
    }

    private function findColumnIndex($headers, $searchTerm)
    {
        foreach ($headers as $index => $header) {
            if (stripos($header, $searchTerm) !== false) {
                return $index;
            }
        }
        return 0;
    }
}
