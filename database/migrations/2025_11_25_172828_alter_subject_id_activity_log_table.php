<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            // Ubah subject_id menjadi CHAR(36)
            $table->char('subject_id', 36)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            // Kembalikan ke bigint jika rollback
            $table->unsignedBigInteger('subject_id')->nullable()->change();
        });
    }
};
