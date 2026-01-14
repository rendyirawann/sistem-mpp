<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Set no_wa menjadi UNIQUE & NOT NULL
            $table->string('no_wa')->unique()->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_wa')->nullable()->change();
            $table->dropUnique(['no_wa']);
        });
    }
};
