<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel `skm` (Survey Kepuasan Masyarakat). FK ke antrians(id).
 * Skema penuh; kolom is_synced juga di-handle guarded oleh migrasi ALTER-nya.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('skm')) {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TABLE `skm` (
  `id` char(36) NOT NULL,
  `antrian_id` char(36) NOT NULL,
  `nilai` int(11) NOT NULL COMMENT '1=Buruk, 2=Cukup, 3=Baik, 4=Sangat Baik',
  `is_synced` tinyint(1) NOT NULL DEFAULT 1,
  `umur` int(11) DEFAULT NULL,
  `jk` varchar(10) DEFAULT NULL,
  `pendidikan` varchar(50) DEFAULT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `disabilitas` varchar(20) DEFAULT NULL,
  `jenis_layanan_id` int(11) DEFAULT NULL,
  `kritik_saran` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `u1` tinyint(1) DEFAULT NULL COMMENT 'Persyaratan',
  `u2` tinyint(1) DEFAULT NULL COMMENT 'Prosedur',
  `u3` tinyint(1) DEFAULT NULL COMMENT 'Waktu',
  `u4` tinyint(1) DEFAULT NULL COMMENT 'Biaya',
  `u5` tinyint(1) DEFAULT NULL COMMENT 'Produk',
  `u6` tinyint(1) DEFAULT NULL COMMENT 'Kompetensi',
  `u7` tinyint(1) DEFAULT NULL COMMENT 'Perilaku',
  `u8` tinyint(1) DEFAULT NULL COMMENT 'Sarana',
  `u9` tinyint(1) DEFAULT NULL COMMENT 'Penanganan',
  `is_pungli` tinyint(1) DEFAULT 0 COMMENT '0=Tidak Ada, 1=Ada Pungli (Pertanyaan ke-10)',
  `pungli_kontak` varchar(100) DEFAULT NULL,
  `pungli_keterangan` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `skm_antrian_id_foreign` (`antrian_id`),
  CONSTRAINT `skm_antrian_id_foreign` FOREIGN KEY (`antrian_id`) REFERENCES `antrians` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('skm');
    }
};
