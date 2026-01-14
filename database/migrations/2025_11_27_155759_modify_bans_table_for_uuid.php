<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bans', function (Blueprint $table) {
            // Ubah kolom numeric menjadi UUID (char 36)
            $table->char('bannable_id', 36)->change();
            $table->char('created_by_id', 36)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bans', function (Blueprint $table) {
            // Kembalikan jika perlu (optional)
            $table->unsignedBigInteger('bannable_id')->change();
            $table->unsignedBigInteger('created_by_id')->nullable()->change();
        });
    }
};
