<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWilayahKabupatenTable extends Migration
{
    public function up()
    {
        Schema::create('wilayah_kabupaten', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 15)->unique();          // contoh: '1101'
            $table->string('nama');
            $table->text('kode_pos')->nullable();
            $table->string('kode_provinsi', 10)->index();  // parent -> wilayah_provinsi.kode
            $table->string('tipe', 10)->nullable();       // 'KAB' atau 'KOTA'
            $table->timestamps();

            // FK optional — aktifkan jika yakin data parent ada saat migrate+seed
            $table->foreign('kode_provinsi')->references('kode')->on('wilayah_provinsi')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wilayah_kabupaten');
    }
}
