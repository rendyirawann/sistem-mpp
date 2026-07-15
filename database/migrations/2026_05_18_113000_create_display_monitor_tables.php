<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel untuk Pengaturan Halaman Display Monitor TV
        Schema::create('display_settings', function (Blueprint $table) {
            $table->id();
            $table->string('video_youtube_id', 50)->default('qK65r2c462I'); // Default North Sumatra cinematic drone video
            $table->text('ticker_text')->nullable();
            $table->timestamps();
        });

        // Seed data awal agar tabel tidak kosong
        DB::table('display_settings')->insert([
            'video_youtube_id' => 'qK65r2c462I',
            'ticker_text' => 'Selamat Datang di Mal Pelayanan Publik Kabupaten Deli Serdang. Mari melayani dengan ramah, cepat, transparan, dan prima. Silakan tunggu giliran nomor antrian Anda dipanggil. NIK Anda terdaftar dengan aman di database MPP. Sukseskan Mal Pelayanan Publik Deli Serdang!',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 2. Tabel untuk Gambar Banner Iklan Monitor TV
        Schema::create('display_banners', function (Blueprint $table) {
            $table->id();
            $table->string('image_path'); // Path penyimpanan file gambar iklan
            $table->string('title', 150)->nullable();
            $table->integer('order_index')->default(0); // Urutan tampil
            $table->boolean('is_active')->default(true); // Aktif/Nonaktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('display_banners');
        Schema::dropIfExists('display_settings');
    }
};
