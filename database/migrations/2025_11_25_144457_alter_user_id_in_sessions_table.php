<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            // Ubah kolom user_id menjadi CHAR(36)
            $table->char('user_id', 36)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            // Kembalikan ke bigint(20)
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }
};
