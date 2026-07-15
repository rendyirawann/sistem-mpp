<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Form Persyaratan (form Dukcapil) untuk layanan antrian online.
 *
 * - form_persyaratan       : definisi/template form + skema field (JSON)
 * - loket_form_persyaratan : pivot penautan form <-> loket (0..N form per loket)
 * - form_persyaratan_values: isian warga per antrian (JSON)
 *
 * Catatan collation: kolom id/*_id char(36) dipaksa utf8mb4_unicode_ci supaya
 * belongsToMany (join lokets.id = pivot.loket_id) tidak kena error 1267,
 * karena lokets.id & antrians.id bercollation utf8mb4_unicode_ci.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('form_persyaratan')) {
            Schema::create('form_persyaratan', function (Blueprint $table) {
                $table->char('id', 36)->collation('utf8mb4_unicode_ci')->primary();
                $table->string('kode', 40)->index();          // mis. F-1.02, F-2.01-LAHIR
                $table->string('nama');
                $table->text('deskripsi')->nullable();
                $table->json('skema')->nullable();            // definisi section + field
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('loket_form_persyaratan')) {
            Schema::create('loket_form_persyaratan', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->char('loket_id', 36)->collation('utf8mb4_unicode_ci');
                $table->char('form_persyaratan_id', 36)->collation('utf8mb4_unicode_ci');
                $table->unsignedInteger('urutan')->default(0);
                $table->timestamps();

                $table->index('loket_id');
                $table->index('form_persyaratan_id');
                $table->unique(['loket_id', 'form_persyaratan_id'], 'loket_form_unique');
            });
        }

        if (!Schema::hasTable('form_persyaratan_values')) {
            Schema::create('form_persyaratan_values', function (Blueprint $table) {
                $table->char('id', 36)->collation('utf8mb4_unicode_ci')->primary();
                $table->char('antrian_id', 36)->collation('utf8mb4_unicode_ci')->index();
                $table->char('form_persyaratan_id', 36)->collation('utf8mb4_unicode_ci')->index();
                $table->string('kode', 40)->nullable();       // salinan kode form saat submit
                $table->string('nama_form')->nullable();      // salinan nama form saat submit
                $table->json('nilai')->nullable();            // { field_key: value }
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('form_persyaratan_values');
        Schema::dropIfExists('loket_form_persyaratan');
        Schema::dropIfExists('form_persyaratan');
    }
};
