<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWilayahProvinsiTable extends Migration
{
    public function up()
    {
        Schema::create('wilayah_provinsi', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();      // contoh: '11', '12', atau '11' tapi simpan string
            $table->string('nama');
            $table->text('kode_pos')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wilayah_provinsi');
    }
}
