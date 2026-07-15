<?php

namespace App\Http\Controllers\Backend\Kalender;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Skpd;
use App\Models\Antrian;
use App\Models\KuotaTanggal;
use App\Services\AntrianService;
use App\Services\HolidayService;

class KalenderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Hanya Superadmin, atau pemilik SKPD itu sendiri (tenant). */
    private function authorizeSkpd(Skpd $skpd): void
    {
        $u = auth()->user();
        if (!$u->hasRole('Superadmin') && (string) $u->skpd_id !== (string) $skpd->id) {
            abort(403, 'Anda hanya dapat mengelola kalender instansi Anda sendiri.');
        }
    }

    /** Menu Kalender: tenant -> langsung kalendernya; Superadmin -> daftar SKPD. */
    public function index()
    {
        $u = auth()->user();
        if (!$u->hasRole('Superadmin') && $u->skpd_id) {
            return redirect()->route('kalender.show', $u->skpd_id);
        }

        $skpds = Skpd::where('isaktif', 1)
            ->whereRaw("EXISTS (SELECT * FROM lokets WHERE skpd.id COLLATE utf8mb4_unicode_ci = lokets.skpd_id COLLATE utf8mb4_unicode_ci AND isaktif = 1)")
            ->orderByRaw("CASE WHEN nama_skpd LIKE '%Catatan Sipil%' OR nama_skpd LIKE '%Pencatatan Sipil%' OR nama_skpd LIKE '%Kependudukan%' THEN 0 ELSE 1 END")
            ->orderBy('nama_skpd')
            ->get();

        return view('backend.kalender.index', compact('skpds'));
    }

    public function show($skpdId, Request $request)
    {
        $skpd = Skpd::findOrFail($skpdId);
        $this->authorizeSkpd($skpd);

        try {
            $cursor = Carbon::createFromFormat('Y-m', (string) $request->query('bulan', now()->format('Y-m')))->startOfMonth();
        } catch (\Throwable $e) {
            $cursor = now()->startOfMonth();
        }

        $start = $cursor->copy()->startOfMonth();
        $end   = $cursor->copy()->endOfMonth();

        $svc     = app(AntrianService::class);
        $holiday = app(HolidayService::class);

        // Hitung terambil per (tanggal, sumber) sekaligus
        $rows = Antrian::where('skpd_id', $skpdId)
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('DATE(tanggal) as tgl, sumber, COUNT(*) as total')
            ->groupBy('tgl', 'sumber')
            ->get();

        $taken = [];
        foreach ($rows as $r) {
            $taken[$r->tgl][$r->sumber ?? 'kiosk'] = (int) $r->total;
        }

        $days = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $ds      = $d->toDateString();
            $kuota   = $svc->resolveKuota($skpd, $d);
            $tOnline = $taken[$ds]['online'] ?? 0;
            $tKiosk  = $taken[$ds]['kiosk'] ?? 0;
            $libur   = $holiday->isLibur($d);
            $locked  = ($tOnline + $tKiosk) > 0; // sudah ada antrean -> tidak bisa diedit

            $days[] = [
                'tanggal'      => $ds,
                'day'          => $d->day,
                'libur'        => $libur,
                'is_today'     => $d->isToday(),
                'kuota_online' => $kuota['online'],
                'kuota_kiosk'  => $kuota['kiosk'],
                'taken_online' => $tOnline,
                'taken_kiosk'  => $tKiosk,
                'sisa_online'  => max(0, $kuota['online'] - $tOnline),
                'sisa_kiosk'   => max(0, $kuota['kiosk'] - $tKiosk),
                'locked'       => $locked,
                'editable'     => !$libur && !$locked,
            ];
        }

        return view('backend.kalender.show', [
            'skpd'       => $skpd,
            'days'       => $days,
            'leadBlanks' => $start->dayOfWeekIso - 1, // 0=Senin
            'bulanLabel' => $cursor->locale('id')->isoFormat('MMMM Y'),
            'bulan'      => $cursor->format('Y-m'),
            'prev'       => $cursor->copy()->subMonth()->format('Y-m'),
            'next'       => $cursor->copy()->addMonth()->format('Y-m'),
        ]);
    }

    public function setKuota($skpdId, Request $request)
    {
        $skpd = Skpd::findOrFail($skpdId);
        $this->authorizeSkpd($skpd);

        $data = $request->validate([
            'tanggal'      => 'required|date',
            'kuota_online' => 'required|integer|min:0|max:100000',
            'kuota_kiosk'  => 'required|integer|min:0|max:100000',
        ]);

        // Terkunci jika sudah ada antrean pada tanggal tsb
        $adaAntrean = Antrian::where('skpd_id', $skpdId)
            ->whereDate('tanggal', $data['tanggal'])
            ->exists();
        if ($adaAntrean) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal ini sudah memiliki antrean, kuota tidak dapat diubah.',
            ], 422);
        }

        KuotaTanggal::updateOrCreate(
            ['skpd_id' => $skpdId, 'tanggal' => $data['tanggal']],
            ['kuota_online' => $data['kuota_online'], 'kuota_kiosk' => $data['kuota_kiosk']]
        );

        return response()->json([
            'success'      => true,
            'message'      => 'Kuota tanggal berhasil diperbarui.',
            'kuota_online' => (int) $data['kuota_online'],
            'kuota_kiosk'  => (int) $data['kuota_kiosk'],
        ]);
    }
}
