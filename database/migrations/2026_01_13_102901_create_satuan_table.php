<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel `satuan`. FK ke users(id) (uuid). Guarded hasTable.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('satuan')) {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TABLE `satuan` (
  `id` char(36) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama` (`nama`),
  KEY `idx_satuan_user_id` (`user_id`),
  CONSTRAINT `fk_satuan_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('satuan');
    }
};
