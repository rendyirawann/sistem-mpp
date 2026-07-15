<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\HariLibur;

class HolidayService
{
    /** Apakah tanggal ini libur? (Sabtu/Minggu atau libur nasional/manual) */
    public function isLibur($tanggal): bool
    {
        $date = $tanggal instanceof Carbon ? $tanggal : Carbon::parse($tanggal);

        // Sabtu (6) & Minggu (7) selalu libur untuk antrian online
        if (in_array($date->dayOfWeekIso, [6, 7], true)) {
            return true;
        }

        // Set tanggal libur (cached) dari tabel hari_libur
        return in_array($date->toDateString(), $this->liburDates($date->year), true);
    }

    /** Daftar tanggal libur (string Y-m-d) pada tahun tertentu, di-cache. */
    public function liburDates(int $year): array
    {
        return Cache::remember("hari_libur_{$year}", now()->addHours(12), function () use ($year) {
            return HariLibur::whereYear('tanggal', $year)
                ->pluck('tanggal')
                ->map(fn ($d) => Carbon::parse($d)->toDateString())
                ->all();
        });
    }

    /** Sinkron libur nasional dari Nager.Date (GitHub: nager/Nager.Date). */
    public function sync(int $year): int
    {
        try {
            $resp = Http::withoutVerifying()->timeout(15)->get("https://date.nager.at/api/v3/PublicHolidays/{$year}/ID");
            if ($resp->failed()) {
                return 0;
            }
            $count = 0;
            foreach ($resp->json() as $h) {
                if (empty($h['date'])) continue;
                HariLibur::updateOrCreate(
                    ['tanggal' => $h['date']],
                    ['nama' => $h['localName'] ?? ($h['name'] ?? 'Libur Nasional'), 'sumber' => 'api']
                );
                $count++;
            }
            Cache::forget("hari_libur_{$year}");
            return $count;
        } catch (\Throwable $e) {
            \Log::error('Sync hari libur gagal: ' . $e->getMessage());
            return 0;
        }
    }
}
