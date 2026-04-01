<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('skpd', function (Blueprint $table) {
            // Tambahkan flag apakah sabtu buka atau libur
            $table->boolean('is_sabtu_buka')->default(false)->after('tutup_jumat');
            // Tambahkan jam sabtu
            $table->time('buka_sabtu')->default('08:00:00')->after('is_sabtu_buka');
            $table->time('tutup_sabtu')->default('15:00:00')->after('buka_sabtu');
        });
    }

    public function down()
    {
        Schema::table('skpd', function (Blueprint $table) {
            $table->dropColumn(['is_sabtu_buka', 'buka_sabtu', 'tutup_sabtu']);
        });
    }
};
