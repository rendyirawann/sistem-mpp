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

            $table->foreignId('loket_id')
                ->constrained('lokets')
                ->cascadeOnDelete();

            $table->integer('nomor_urut');
            $table->string('nomor_antrian', 10);
            $table->date('tanggal');

            $table->enum('status', [
                'menunggu',
                'dipanggil',
                'selesai',
                'batal'
            ])->default('menunggu');

            $table->timestamp('waktu_ambil')->useCurrent();
            $table->timestamp('waktu_panggil')->nullable();
            $table->timestamp('waktu_selesai')->nullable();

            $table->timestamps();

            // index untuk performa
            $table->index(['loket_id', 'tanggal']);
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
