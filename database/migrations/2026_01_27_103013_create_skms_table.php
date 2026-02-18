<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('skm', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // Relasi ke antrian (biar tau ini survey untuk transaksi mana)
            $table->char('antrian_id', 36);

            // Data Survey
            $table->integer('nilai')->comment('1=Buruk, 2=Cukup, 3=Baik, 4=Sangat Baik');
            $table->text('kritik_saran')->nullable();

            $table->timestamps();

            $table->foreign('antrian_id')->references('id')->on('antrians')->onDelete('cascade');
        });
    }
};
