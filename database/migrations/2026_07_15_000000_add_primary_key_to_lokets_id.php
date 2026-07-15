<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel `lokets` sebelumnya tidak punya PRIMARY KEY di kolom `id`
 * (hanya unique pada `kode_tenant`). Akibatnya upsert/insert berbasis id
 * bisa menghasilkan baris kembar. Migration ini memasang PRIMARY KEY
 * secara defensif: bersihkan dulu baris kembar (sisakan satu), lalu ADD PK.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lokets')) {
            return;
        }

        // Sudah ada PRIMARY KEY? lewati.
        $hasPk = collect(DB::select("SHOW INDEX FROM lokets WHERE Key_name = 'PRIMARY'"))->isNotEmpty();
        if ($hasPk) {
            return;
        }

        // Buang baris ber-id kembar (sisakan 1 salinan) agar bisa dijadikan PK.
        $dupes = DB::select("SELECT id FROM lokets GROUP BY id HAVING COUNT(*) > 1");
        foreach ($dupes as $d) {
            $total = DB::table('lokets')->where('id', $d->id)->count();
            for ($i = 0; $i < $total - 1; $i++) {
                DB::delete("DELETE FROM lokets WHERE id = ? LIMIT 1", [$d->id]);
            }
        }

        // Pastikan tidak ada id NULL/kosong.
        DB::table('lokets')->whereNull('id')->orWhere('id', '')->delete();

        // Masih ada kembar? jangan paksa (hindari error), cukup lewati.
        $stillDupe = DB::select("SELECT id FROM lokets GROUP BY id HAVING COUNT(*) > 1");
        if (empty($stillDupe)) {
            DB::statement("ALTER TABLE lokets ADD PRIMARY KEY (id)");
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('lokets')) {
            return;
        }
        $hasPk = collect(DB::select("SHOW INDEX FROM lokets WHERE Key_name = 'PRIMARY'"))->isNotEmpty();
        if ($hasPk) {
            DB::statement("ALTER TABLE lokets DROP PRIMARY KEY");
        }
    }
};
