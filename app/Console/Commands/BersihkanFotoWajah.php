<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Antrian;

class BersihkanFotoWajah extends Command
{
    protected $signature = 'antrian:bersihkan-wajah';
    protected $description = 'Hapus foto wajah antrian online dari minggu-minggu sebelumnya (data antrian tetap, hanya file gambar dihapus).';

    public function handle(): int
    {
        $base        = 'antrian-wajah';
        $currentWeek = now()->isoFormat('GGGG-[W]WW'); // contoh: 2026-W27

        $deleted = 0;
        if (Storage::disk('public')->exists($base)) {
            foreach (Storage::disk('public')->directories($base) as $dir) {
                if (basename($dir) !== $currentWeek) {
                    Storage::disk('public')->deleteDirectory($dir);
                    $deleted++;
                }
            }
        }

        // Null-kan kolom foto_wajah utk baris di luar minggu berjalan (file sudah dihapus).
        // Data antrian tetap utuh, hanya referensi gambar yang dibersihkan.
        $nulled = Antrian::whereNotNull('foto_wajah')
            ->where('foto_wajah', 'not like', $base . '/' . $currentWeek . '/%')
            ->update(['foto_wajah' => null]);

        $this->info("Cleanup selesai. Folder minggu lama dihapus: {$deleted}. Referensi foto di-null: {$nulled}.");
        return self::SUCCESS;
    }
}
