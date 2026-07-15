<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Flag tenant yang sudah aktif untuk antrian online
        if (Schema::hasTable('skpd') && !Schema::hasColumn('skpd', 'is_antrianonline')) {
            Schema::table('skpd', function (Blueprint $table) {
                $table->boolean('is_antrianonline')->default(false)->after('is_force_close');
            });
        }

        // 2. Pengaturan situs (hero, footer, dll) — key/value sederhana
        if (!Schema::hasTable('site_settings')) {
            Schema::create('site_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        // 3. Tautan media sosial footer (icon dideteksi otomatis dari URL)
        if (!Schema::hasTable('social_links')) {
            Schema::create('social_links', function (Blueprint $table) {
                $table->id();
                $table->string('label')->nullable();
                $table->string('url');
                $table->string('platform')->nullable(); // hasil deteksi otomatis dari URL
                $table->integer('order_index')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('site_settings');
        if (Schema::hasTable('skpd') && Schema::hasColumn('skpd', 'is_antrianonline')) {
            Schema::table('skpd', function (Blueprint $table) {
                $table->dropColumn('is_antrianonline');
            });
        }
    }
};
