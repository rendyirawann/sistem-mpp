<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWilayahDesaTable extends Migration
{
    public function up()
    {
        Schema::create('wilayah_desa', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 25)->unique();         // contoh: '1101010001'
            $table->string('nama');
            $table->text('kode_pos')->nullable();
            $table->string('kode_kecamatan', 20)->index(); // parent -> wilayah_kecamatan.kode
            $table->timestamps();

            $table->foreign('kode_kecamatan')->references('kode')->on('wilayah_kecamatan')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wilayah_desa');
    }
}
