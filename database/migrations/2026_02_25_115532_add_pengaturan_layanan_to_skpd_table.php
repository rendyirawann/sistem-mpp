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
            if (!Schema::hasColumn('skpd', 'buka_senin_kamis')) {
                $table->time('buka_senin_kamis')->default('08:00:00')->after('isaktif');
            }
            if (!Schema::hasColumn('skpd', 'tutup_senin_kamis')) {
                $table->time('tutup_senin_kamis')->default('15:00:00')->after('buka_senin_kamis');
            }
            if (!Schema::hasColumn('skpd', 'buka_jumat')) {
                $table->time('buka_jumat')->default('08:00:00')->after('tutup_senin_kamis');
            }
            if (!Schema::hasColumn('skpd', 'tutup_jumat')) {
                $table->time('tutup_jumat')->default('15:30:00')->after('buka_jumat');
            }
            if (!Schema::hasColumn('skpd', 'kuota_harian')) {
                $table->integer('kuota_harian')->default(0)->after('tutup_jumat'); // 0 = Tanpa batas
            }
            if (!Schema::hasColumn('skpd', 'is_force_close')) {
                $table->boolean('is_force_close')->default(false)->after('kuota_harian');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('skpd')) {
            return;
        }
        Schema::table('skpd', function (Blueprint $table) {
            foreach (['buka_senin_kamis', 'tutup_senin_kamis', 'buka_jumat', 'tutup_jumat', 'kuota_harian', 'is_force_close'] as $col) {
                if (Schema::hasColumn('skpd', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
