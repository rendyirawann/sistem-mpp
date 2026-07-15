<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel `skpd` (instansi). Skema penuh sesuai produksi.
 * Guarded: hanya dibuat bila belum ada (aman di server yang tabelnya
 * sudah lebih dulu ada dari import SQL). Kolom tambahan yang di-ALTER
 * migrasi lain di-skip via guard hasColumn di masing-masing migrasi itu.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('skpd')) {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TABLE `skpd` (
  `id` char(36) NOT NULL,
  `nama_skpd` varchar(255) NOT NULL,
  `external_id_sukma` int(11) DEFAULT NULL COMMENT 'ID Referensi untuk API Sukmadeli',
  `lokasi` varchar(255) DEFAULT NULL,
  `kepala_skpd` varchar(255) DEFAULT NULL,
  `nip_kepala` varchar(255) DEFAULT NULL,
  `logo_skpd` varchar(255) DEFAULT NULL,
  `isaktif` tinyint(1) NOT NULL,
  `buka_senin_kamis` time NOT NULL DEFAULT '08:00:00',
  `tutup_senin_kamis` time NOT NULL DEFAULT '15:00:00',
  `buka_jumat` time NOT NULL DEFAULT '08:00:00',
  `tutup_jumat` time NOT NULL DEFAULT '15:30:00',
  `is_sabtu_buka` tinyint(1) NOT NULL DEFAULT 0,
  `buka_sabtu` time NOT NULL DEFAULT '08:00:00',
  `tutup_sabtu` time NOT NULL DEFAULT '15:00:00',
  `kuota_harian` int(11) NOT NULL DEFAULT 0,
  `kuota_online` int(11) NOT NULL DEFAULT 40,
  `kuota_kiosk` int(11) NOT NULL DEFAULT 60,
  `is_force_close` tinyint(1) NOT NULL DEFAULT 0,
  `is_antrianonline` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `skpd_isaktif_index` (`isaktif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('skpd');
    }
};
