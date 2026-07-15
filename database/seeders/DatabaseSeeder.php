<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,          // user & role (login)
            LoadWilayahSqlSeeder::class, // wilayah: provinsi/kabupaten/kecamatan/desa
            MppMasterDataSeeder::class,  // instansi, layanan, form persyaratan, setting, kuota, hari libur, display TV
        ]);

        // Catatan: LandingContentSeeder & FormPersyaratanSeeder digantikan oleh
        // MppMasterDataSeeder (snapshot SQL) agar deterministik & tidak duplikat.
        // Keduanya tetap tersedia bila ingin dijalankan manual.
    }
}
