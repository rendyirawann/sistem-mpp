<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LoadWilayahSqlSeeder extends Seeder
{
    public function run()
    {
        $sqlDir = database_path('seeders/sql');

        // urutan penting: provinsi -> kabupaten -> kecamatan -> desa
        $files = [
            $sqlDir . '/wilayah_provinsi.sql',
            $sqlDir . '/wilayah_kabupaten.sql',
            $sqlDir . '/wilayah_kecamatan.sql',
            $sqlDir . '/wilayah_desa.sql',
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($files as $f) {
            if (!File::exists($f)) {
                $this->command->warn("File not found: {$f} (skipping)");
                continue;
            }
            $this->command->info("Importing SQL: {$f}");
            $sql = File::get($f);

            // Jika file sangat besar, lebih aman untuk mem-parse per-statement,
            // tapi DB::unprepared() biasanya bekerja baik untuk file yang wajar.
            DB::unprepared($sql);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('All wilayah SQL files imported.');
    }
}
