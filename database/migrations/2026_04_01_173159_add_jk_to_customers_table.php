<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('customers') || Schema::hasColumn('customers', 'jk')) {
            return;
        }
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('jk', ['L', 'P'])->nullable()->after('nama');
        });
    }

    public function down()
    {
        if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'jk')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('jk');
            });
        }
    }
};
