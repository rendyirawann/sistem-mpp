<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('antrians')) {
            return;
        }

        Schema::table('antrians', function (Blueprint $table) {
            // Pembeda asal antrian. Data lama otomatis 'kiosk' (default) -> tidak error.
            if (!Schema::hasColumn('antrians', 'sumber')) {
                $table->string('sumber', 20)->default('kiosk')->after('status'); // 'kiosk' | 'online'
            }
            // Foto wajah hasil verifikasi (khusus antrian online)
            if (!Schema::hasColumn('antrians', 'foto_wajah')) {
                $table->string('foto_wajah')->nullable()->after('sumber');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('antrians')) {
            return;
        }
        Schema::table('antrians', function (Blueprint $table) {
            if (Schema::hasColumn('antrians', 'foto_wajah')) {
                $table->dropColumn('foto_wajah');
            }
            if (Schema::hasColumn('antrians', 'sumber')) {
                $table->dropColumn('sumber');
            }
        });
    }
};
