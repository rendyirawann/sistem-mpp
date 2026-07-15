<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('display_announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->text('text');
            $table->integer('order_index')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed 3 data awal bawaan user
        DB::table('display_announcements')->insert([
            [
                'title' => 'Pengumuman Bebas Biaya Layanan Kependudukan',
                'text' => 'PERHATIAN KEPADA BAPAK/IBU SELURUH PENGUNJUNG MAL PELAYANAN PUBLIK KAB. DELI SERDANG. SEBAGAI INFORMASI, UNTUK SELURUH LAYANAN KEPENDUDUKAN PADA MAL PELAYANAN PUBLIK KAB. DELI SERDANG TIDAK DIPUNGUT BIAYA APAPUN. KAMI MENGHIMBAU KEPADA SELURUH PENGGUNA LAYANAN KEPENDUDUKAN AGAR LEBIH BERHATI-HATI TERHADAP OKNUM DILUAR PETUGAS RESMI PADA MAL PELAYANAN PUBLIK KAB. DELI SERDANG. ATAS KERJASAMANYA KAMI UCAPKAN TERIMA KASIH.',
                'order_index' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Himbauan Pendamping & Kuota KTP IKD',
                'text' => 'KEPADA BAPAK/IBU PENDAMPING DIMOHON UNTUK MENUNGGU DILUAR GEDUNG. UNTUK LAYANAN PENCETAKAN KTP SETIAP HARINYA TERSEDIA 100 KEPING KTP DAN MASYARAKAT DIWAJIBKAN UNTUK MENGUNDUH APLIKASI IKD. SELAIN MAL PELAYANAN PUBLIK KAB. DELI SERDANG MASYARAKAT JUGA DAPAT MELAKUKAN PENGURUSAN ADMINISTRASI KEPENDUDUKAN PADA LAYANAN PATEN KALI YANG TERSEDIA DISELURUH KECAMATAN YANG BERADA DI KAB. DELI SERDANG. ATAS KERJASAMANYA KAMI UCAPKAN TERIMA KASIH.',
                'order_index' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Himbauan Larangan Duduk di Lantai & Kebersihan',
                'text' => 'SELURUH PENGUNJUNG MAL PELAYANAN PUBLIK KAB. DELI SERDANG DILARANG UNTUK DUDUK DILANTAI, MEROKOK DAN MEMBAWA MAKANAN KE DALAM GEDUNG. PENGUNJUNG YANG TELAH SELESAI MENDAPATKAN LAYANAN DIHARAPKAN DAPAT MENINGGALKAN GEDUNG MAL PELAYANAN PUBLIK KAB. DELI SERDANG. BAGI PENGUNJUNG MAL PELAYANAN PUBLIK KABUPATEN DELI SERDANG YANG MEMBUTUHKAN INFORMASI DAPAT LANGSUNG KEBAGIAN INFORMASI. ATAS KERJASAMANYA KAMI UCAPKAN TERIMA KASIH.',
                'order_index' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('display_announcements');
    }
};
