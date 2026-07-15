<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lebarkan kolom `nama` wilayah dari varchar(30) -> varchar(150) agar muat
 * nama resmi (uppercase) dari dataset Kemendagri/OSS, mis.
 * "KABUPATEN PENUKAL ABAB LEMATANG ILIR".
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['wilayah_provinsi', 'wilayah_kabupaten', 'wilayah_kecamatan'] as $t) {
            if (Schema::hasTable($t) && Schema::hasColumn($t, 'nama')) {
                Schema::table($t, function (Blueprint $table) {
                    $table->string('nama', 150)->change();
                });
            }
        }
    }

    public function down(): void
    {
        // sengaja tidak menyempitkan kembali (berisiko truncate)
    }
};
