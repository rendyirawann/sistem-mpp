<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWilayahKecamatanTable extends Migration
{
    public function up()
    {
        Schema::create('wilayah_kecamatan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();         // contoh: '1101010'
            $table->string('nama');
            $table->text('kode_pos')->nullable();
            $table->string('kode_kabupaten', 15)->index(); // parent -> wilayah_kabupaten.kode
            $table->timestamps();

            $table->foreign('kode_kabupaten')->references('kode')->on('wilayah_kabupaten')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wilayah_kecamatan');
    }
}
