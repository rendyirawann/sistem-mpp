<?php

namespace App\Services;

use App\Models\Antrian;
use App\Models\Loket;
use App\Models\Skpd;
use App\Models\SiteSetting;
use App\Models\KuotaTanggal;
use Carbon\Carbon;

class AntrianService
{
    /** Kuota default per hari per SKPD jika belum di-set admin. */
    public const DEFAULT_KUOTA = 100;

    public function kuotaHarian(Skpd $skpd): int
    {
        return ($skpd->kuota_harian && $skpd->kuota_harian > 0)
            ? (int) $skpd->kuota_harian
            : self::DEFAULT_KUOTA;
    }

    /** Jumlah antrian terpakai (gabungan kiosk + online) pada tanggal tertentu. */
    public function terpakai(string $skpdId, string $tanggal): int
    {
        return Antrian::where('skpd_id', $skpdId)->whereDate('tanggal', $tanggal)->count();
    }

    public function sisaKuota(Skpd $skpd, string $tanggal): int
    {
        return max(0, $this->kuotaHarian($skpd) - $this->terpakai($skpd->id, $tanggal));
    }

    /**
     * ===== KUOTA TERPISAH PER SUMBER (online / kiosk) =====
     */

    /**
     * Resolusi kuota [online, kiosk] untuk SKPD pada tanggal tertentu:
     *  1) Override per-tanggal (tabel kuota_tanggal) bila ada.
     *  2) Juli 2026  -> 60 online / 40 kiosk.
     *  3) Setelah Juli 2026 -> 100 online / 0 kiosk (full online).
     *  4) Sebelum Juli 2026 -> kuota per-SKPD (jaga operasi kiosk berjalan).
     */
    public function resolveKuota(Skpd $skpd, $tanggal): array
    {
        $date = $tanggal instanceof Carbon ? $tanggal : Carbon::parse($tanggal);

        // 1. Override per-tanggal dari Kalender (paling spesifik)
        $row = KuotaTanggal::where('skpd_id', $skpd->id)
            ->whereDate('tanggal', $date->toDateString())
            ->first();
        if ($row) {
            return ['online' => (int) $row->kuota_online, 'kiosk' => (int) $row->kuota_kiosk];
        }

        // 2. Default per-SKPD (Edit SKPD) -> berlaku untuk semua tanggal ke depan
        //    yang belum di-override khusus di Kalender.
        return [
            'online' => (int) ($skpd->kuota_online ?? 40),
            'kiosk'  => (int) ($skpd->kuota_kiosk ?? 60),
        ];
    }

    /** Kuota untuk sumber tertentu pada tanggal tsb. */
    public function kuotaSumber(Skpd $skpd, $tanggal, string $sumber): int
    {
        $k = $this->resolveKuota($skpd, $tanggal);
        return $sumber === 'online' ? $k['online'] : $k['kiosk'];
    }

    /** Jumlah antrian terpakai untuk sumber tertentu pada tanggal tsb. */
    public function terpakaiSumber(string $skpdId, string $tanggal, string $sumber): int
    {
        return Antrian::where('skpd_id', $skpdId)
            ->where('sumber', $sumber)
            ->whereDate('tanggal', $tanggal)
            ->count();
    }

    public function sisaKuotaSumber(Skpd $skpd, string $tanggal, string $sumber): int
    {
        return max(0, $this->kuotaSumber($skpd, $tanggal, $sumber) - $this->terpakaiSumber($skpd->id, $tanggal, $sumber));
    }

    /** Hari operasional online (ISO: 1=Sen .. 7=Min), diatur admin via setting. */
    public function hariOperasional(): array
    {
        $raw = SiteSetting::get('online_hari', '1,2,3,4,5');
        $days = array_values(array_filter(
            array_map('intval', explode(',', (string) $raw)),
            fn ($d) => $d >= 1 && $d <= 7
        ));
        return $days ?: [1, 2, 3, 4, 5];
    }

    /**
     * Tanggal tersedia untuk antrian online (jendela mingguan).
     * Mulai hari ini, cari hari operasional pertama DALAM minggu berjalan
     * yang kuotanya masih ada. Jika seluruh sisa minggu penuh -> null
     * (tutup sampai Senin minggu berikutnya).
     */
    public function tanggalTersediaOnline(Skpd $skpd): ?Carbon
    {
        foreach ($this->hariTersediaOnline($skpd) as $h) {
            if (!$h['penuh']) {
                return Carbon::parse($h['tanggal']);
            }
        }
        return null;
    }

    /**
     * Daftar hari (minggu berjalan) yang bisa dipilih untuk antrian online:
     * hari operasional (Sen-Jum), BUKAN libur (Sabtu/Minggu/nasional), + info sisa kuota.
     */
    public function hariTersediaOnline(Skpd $skpd): array
    {
        if (!$skpd->is_antrianonline) {
            return [];
        }

        $hari    = $this->hariOperasional();
        $holiday = app(HolidayService::class);
        $today   = Carbon::today();
        $endWeek = $today->copy()->endOfWeek(Carbon::SUNDAY);

        // Batas jam pengambilan untuk HARI INI. Lewat jam ini -> hari ini ditutup,
        // warga hanya bisa memilih hari berikutnya (meski kuota hari ini masih ada).
        // Cutoff KOSONG -> tanpa batas jam (hari ini selalu terbuka sampai akhir hari).
        $cutoff      = trim((string) SiteSetting::get('online_cutoff', '14:00'));
        $lewatCutoff = false;
        if ($cutoff !== '') {
            $jam = explode(':', $cutoff);
            $batasHariIni = $today->copy()->setTime((int) ($jam[0] ?? 14), (int) ($jam[1] ?? 0));
            $lewatCutoff  = Carbon::now()->greaterThanOrEqualTo($batasHariIni);
        }

        $out = [];
        for ($cursor = $today->copy(); $cursor->lte($endWeek); $cursor->addDay()) {
            if ($cursor->isToday() && $lewatCutoff) {
                continue; // hari ini sudah lewat jam cutoff -> tidak bisa dipilih
            }
            if (!in_array($cursor->dayOfWeekIso, $hari, true)) {
                continue;
            }
            if ($holiday->isLibur($cursor)) {
                continue;
            }
            $sisa = $this->sisaKuotaSumber($skpd, $cursor->toDateString(), 'online');
            $out[] = [
                'tanggal'       => $cursor->toDateString(),
                'hari'          => $cursor->locale('id')->isoFormat('dddd'),
                'tanggal_label' => $cursor->locale('id')->isoFormat('D MMM'),
                'sisa'          => $sisa,
                'penuh'         => $sisa <= 0,
            ];
        }
        return $out;
    }

    /**
     * Generate nomor antrian. Deret OFFLINE & ONLINE terpisah & independen,
     * berdasarkan grup prefix loket + tanggal + sumber.
     * - offline (kiosk): A-001
     * - online        : AO-001
     */
    public function generateNomor(Loket $loket, Carbon $tanggal, string $sumber): array
    {
        $prefix = $loket->prefix_tenant;
        $tgl    = $tanggal->toDateString();

        $lastUrut = Antrian::whereDate('tanggal', $tgl)
            ->where('sumber', $sumber)
            ->whereHas('loket', fn ($q) => $q->where('prefix_tenant', $prefix))
            ->max('no_urut');

        $seq        = (int) ($lastUrut ?? 0) + 1;
        $kodePrefix = $sumber === 'online' ? $prefix . 'O' : $prefix;

        return [
            'no_urut'    => $seq,
            'no_antrian' => $kodePrefix . '-' . str_pad((string) $seq, 3, '0', STR_PAD_LEFT),
        ];
    }
}
