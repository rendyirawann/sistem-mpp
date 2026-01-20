<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('antrians', function (Blueprint $table) {
            $table->id();
            $table->string('nama_instansi')->nullable(); // Disdukcapil, Samsat, dll
            $table->string('nama_layanan')->nullable();  // KTP, Pajak, dll
            $table->string('nik')->nullable();           // Data Diri
            $table->string('nama_warga')->nullable();    // Data Diri
            $table->string('no_hp')->nullable();         // Data Diri
            $table->integer('nomor_urut')->nullable();
            $table->string('nomor_antrian')->nullable(); // A-001
            $table->enum('status', ['menunggu', 'dipanggil', 'selesai'])->default('menunggu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antrians');
    }
};
