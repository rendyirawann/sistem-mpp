<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('skpd')) {
            return;
        }

        Schema::table('skpd', function (Blueprint $table) {
            if (!Schema::hasColumn('skpd', 'is_sabtu_buka')) {
                $table->boolean('is_sabtu_buka')->default(false)->after('tutup_jumat');
            }
            if (!Schema::hasColumn('skpd', 'buka_sabtu')) {
                $table->time('buka_sabtu')->default('08:00:00')->after('is_sabtu_buka');
            }
            if (!Schema::hasColumn('skpd', 'tutup_sabtu')) {
                $table->time('tutup_sabtu')->default('15:00:00')->after('buka_sabtu');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('skpd')) {
            return;
        }
        Schema::table('skpd', function (Blueprint $table) {
            foreach (['is_sabtu_buka', 'buka_sabtu', 'tutup_sabtu'] as $col) {
                if (Schema::hasColumn('skpd', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
