<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('skm') || Schema::hasColumn('skm', 'is_synced')) {
            return;
        }
        Schema::table('skm', function (Blueprint $table) {
            $table->boolean('is_synced')->default(1)->after('nilai');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('skm') && Schema::hasColumn('skm', 'is_synced')) {
            Schema::table('skm', function (Blueprint $table) {
                $table->dropColumn('is_synced');
            });
        }
    }
};
