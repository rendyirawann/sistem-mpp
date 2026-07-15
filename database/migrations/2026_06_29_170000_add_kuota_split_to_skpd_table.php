<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('skpd')) {
            return;
        }

        Schema::table('skpd', function (Blueprint $table) {
            if (!Schema::hasColumn('skpd', 'kuota_online')) {
                $table->integer('kuota_online')->default(40)->after('kuota_harian');
            }
            if (!Schema::hasColumn('skpd', 'kuota_kiosk')) {
                $table->integer('kuota_kiosk')->default(60)->after('kuota_online');
            }
        });

        // Backfill: kuota kiosk mengikuti kuota_harian lama (jaga kapasitas walk-in)
        DB::statement('UPDATE skpd SET kuota_kiosk = kuota_harian WHERE kuota_harian IS NOT NULL AND kuota_harian > 0');
    }

    public function down(): void
    {
        if (!Schema::hasTable('skpd')) {
            return;
        }
        Schema::table('skpd', function (Blueprint $table) {
            foreach (['kuota_online', 'kuota_kiosk'] as $col) {
                if (Schema::hasColumn('skpd', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
