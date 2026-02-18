<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // =========================
        // TABLE: model_has_permissions
        // =========================
        Schema::table('model_has_permissions', function (Blueprint $table) {
            // Hapus foreign key index lama
            $table->dropIndex('model_has_permissions_model_id_model_type_index');

            // Ubah kolom model_id menjadi UUID
            $table->uuid('model_id')->change();

            // Tambah index baru
            $table->index(['model_id', 'model_type'], 'model_has_permissions_model_id_model_type_index');
        });

        // =========================
        // TABLE: model_has_roles
        // =========================
        Schema::table('model_has_roles', function (Blueprint $table) {
            // Hapus foreign key index lama
            $table->dropIndex('model_has_roles_model_id_model_type_index');

            // Ubah kolom model_id menjadi UUID
            $table->uuid('model_id')->change();

            // Tambah index baru
            $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropIndex('model_has_permissions_model_id_model_type_index');
            $table->unsignedBigInteger('model_id')->change();
            $table->index(['model_id', 'model_type'], 'model_has_permissions_model_id_model_type_index');
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropIndex('model_has_roles_model_id_model_type_index');
            $table->unsignedBigInteger('model_id')->change();
            $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');
        });
    }
};
