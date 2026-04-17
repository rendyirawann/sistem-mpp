<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LayananSkmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = database_path('data/layananmpp.csv');
        if (!file_exists($csvFile)) {
            $this->command->error("CSV file not found at: {$csvFile}");
            return;
        }

        $handle = fopen($csvFile, 'r');
        if ($handle === false) {
            $this->command->error("Failed to open CSV file: {$csvFile}");
            return;
        }

        // Get the header to skip it
        $header = fgetcsv($handle, 1000, ';');

        $count = 0;
        while (($data = fgetcsv($handle, 1000, ';')) !== false) {
            // Check if we have at least 4 columns
            if (count($data) >= 4) {
                \DB::table('layanan_skm')->insert([
                    'id_opd'     => $data[0],
                    'opd'        => $data[1],
                    'id_layanan' => $data[2],
                    'layanan'    => $data[3],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $count++;
            }
        }
        fclose($handle);

        $this->command->info("Successfully imported {$count} records into layanan_skm table.");
    }
}
