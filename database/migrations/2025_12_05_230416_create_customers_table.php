<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel `customers` (data pemohon). Skema penuh sesuai produksi
 * (UUID PK; kolom jk juga di-handle guarded oleh migrasi ALTER-nya).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('customers')) {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TABLE `customers` (
  `id` char(36) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `jk` enum('L','P') DEFAULT NULL,
  `nik` varchar(20) NOT NULL,
  `no_hp` varchar(15) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
