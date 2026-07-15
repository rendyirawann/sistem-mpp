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
            RolePermissionSeeder::class, // role + permission (harus sebelum UserSeeder)
            UserSeeder::class,           // akun Super Admin (assign role Superadmin)
            LoadWilayahSqlSeeder::class, // wilayah: provinsi/kabupaten/kecamatan/desa
            MppMasterDataSeeder::class,  // instansi, layanan, form persyaratan, setting, kuota, hari libur, display TV
        ]);

        // Catatan: LandingContentSeeder & FormPersyaratanSeeder digantikan oleh
        // MppMasterDataSeeder (snapshot SQL) agar deterministik & tidak duplikat.
        // Keduanya tetap tersedia bila ingin dijalankan manual.
    }
}
