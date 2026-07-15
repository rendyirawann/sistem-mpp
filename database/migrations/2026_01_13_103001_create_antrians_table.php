<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel `antrians`. Skema penuh sesuai produksi (UUID PK).
 * Kolom sumber/foto_wajah juga di-handle guarded oleh migrasi ALTER-nya.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('antrians')) {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TABLE `antrians` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `skpd_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `loket_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_urut` int(11) DEFAULT NULL,
  `no_antrian` varchar(20) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=menunggu,1=dipanggil,2=selesai,3=lewati',
  `sumber` varchar(20) NOT NULL DEFAULT 'kiosk',
  `foto_wajah` varchar(255) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `waktu_ambil` datetime DEFAULT NULL,
  `waktu_panggil` datetime DEFAULT NULL,
  `waktu_selesai` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `antrians_skpd_id_tanggal_index` (`skpd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('antrians');
    }
};
