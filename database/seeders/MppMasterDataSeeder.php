<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Seed data master/konfigurasi MPP dari snapshot SQL di database/seeders/sql/.
 *
 * Mencakup: instansi (skpd), layanan (lokets), form persyaratan + tautannya,
 * pengaturan situs (site_settings), media sosial, kuota tanggal, hari libur,
 * dan data Display TV.
 *
 * Idempoten: tiap tabel HANYA di-import bila masih kosong, sehingga aman
 * dijalankan ulang dan tidak menimpa data yang sudah ada di server.
 * (Data wilayah ditangani terpisah oleh LoadWilayahSqlSeeder.)
 */
class MppMasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $sqlDir = database_path('seeders/sql');

        // Urutan: yang direferensikan dulu (lokets/form) baru pivot.
        $tables = [
            'skpd',
            'lokets',
            'form_persyaratan',
            'loket_form_persyaratan',
            'site_settings',
            'social_links',
            'kuota_tanggal',
            'hari_libur',
            'display_settings',
            'display_banners',
            'display_announcements',
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $table) {
            $file = "{$sqlDir}/{$table}.sql";

            if (!File::exists($file)) {
                $this->command?->warn("Lewati {$table}: file {$table}.sql tidak ditemukan.");
                continue;
            }

            // Hanya import kalau tabel masih kosong (hindari duplikat / timpa data).
            if (DB::table($table)->count() > 0) {
                $this->command?->line("Lewati {$table}: sudah ada data (" . DB::table($table)->count() . " baris).");
                continue;
            }

            $sql = trim(File::get($file));
            // File tanpa statement INSERT (mis. tabel kosong) -> skip aman.
            if ($sql === '' || !preg_match('/INSERT\s+INTO/i', $sql)) {
                $this->command?->line("Lewati {$table}: tidak ada data untuk di-import.");
                continue;
            }

            DB::unprepared($sql);
            $this->command?->info("Import {$table}: " . DB::table($table)->count() . " baris.");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command?->info('Seed data master MPP selesai.');
    }
}
