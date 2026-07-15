<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\HolidayService;

class SyncHariLibur extends Command
{
    protected $signature = 'libur:sync {tahun? : Tahun yang disinkron (default: tahun ini & tahun depan)}';
    protected $description = 'Sinkron hari libur nasional Indonesia dari Nager.Date ke tabel hari_libur.';

    public function handle(HolidayService $holiday): int
    {
        $tahun = $this->argument('tahun');
        $years = $tahun ? [(int) $tahun] : [now()->year, now()->year + 1];

        foreach ($years as $y) {
            $n = $holiday->sync($y);
            $this->info("Tahun {$y}: {$n} hari libur disinkron.");
        }
        return self::SUCCESS;
    }
}
