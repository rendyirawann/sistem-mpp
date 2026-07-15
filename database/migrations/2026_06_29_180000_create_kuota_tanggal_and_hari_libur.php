<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kuota per tanggal per SKPD (override default per-bulan). Diatur via kalender admin.
        if (!Schema::hasTable('kuota_tanggal')) {
            Schema::create('kuota_tanggal', function (Blueprint $table) {
                $table->id();
                $table->char('skpd_id', 36);
                $table->date('tanggal');
                $table->integer('kuota_online')->default(0);
                $table->integer('kuota_kiosk')->default(0);
                $table->timestamps();
                $table->unique(['skpd_id', 'tanggal']);
                $table->index('tanggal');
            });
        }

        // Hari libur nasional (sinkron dari Nager.Date) + override manual admin.
        if (!Schema::hasTable('hari_libur')) {
            Schema::create('hari_libur', function (Blueprint $table) {
                $table->id();
                $table->date('tanggal')->unique();
                $table->string('nama')->nullable();
                $table->string('sumber', 20)->default('manual'); // 'api' | 'manual'
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kuota_tanggal');
        Schema::dropIfExists('hari_libur');
    }
};
