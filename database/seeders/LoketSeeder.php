<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lokets')->insert([
            [
                'kode_loket' => 'PBB',
                'nama_loket' => 'Loket PBB',
                'prefix_antrian' => 'A',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_loket' => 'BPHTB',
                'nama_loket' => 'Loket BPHTB',
                'prefix_antrian' => 'B',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_loket' => 'BANK',
                'nama_loket' => 'Loket Bank Sumut',
                'prefix_antrian' => 'C',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
