<?php

namespace App\Http\Controllers\Backend\Antrian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Antrian;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Events\PanggilanAntrian; // Event WebSocket Reverb
use App\Events\CooldownTriggered; // <--- Import Event Baru


class AntrianController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('permission:antrian.list', ['only' => ['index', 'getAntrian']]);
    }

    /**
     * Helper: Filter Query Berdasarkan Role User
     */
    private function filterBySkpd($query)
    {
        if (!auth()->user()->hasRole('Superadmin')) {
            $query->where('antrians.skpd_id', auth()->user()->skpd_id);
        }
        return $query;
    }

    /**
     * Halaman Utama Antrian
     */
    public function index()
    {
        return view('backend.antrian.index');
    }

    /**
     * API DataTables (List Antrian)
     */
    public function getAntrian()
    {
        $query = Antrian::query()
            ->select([
                'antrians.id',
                'antrians.no_antrian',
                'antrians.status',
                'antrians.loket_id',
                'antrians.waktu_ambil',
                'antrians.no_urut',
                'lokets.nama_loket',
                'skpd.nama_skpd'
            ])
            // JOIN dengan Fix Collation (utf8mb4_unicode_ci) agar tidak Error 500
            ->leftJoin('lokets', function ($join) {
                $join->on(DB::raw('lokets.id COLLATE utf8mb4_unicode_ci'), '=', DB::raw('antrians.loket_id COLLATE utf8mb4_unicode_ci'));
            })
            ->leftJoin('skpd', function ($join) {
                $join->on(DB::raw('skpd.id COLLATE utf8mb4_unicode_ci'), '=', DB::raw('antrians.skpd_id COLLATE utf8mb4_unicode_ci'));
            })
            ->hariIni();

        // Filter jika bukan Superadmin
        $this->filterBySkpd($query);

        // 🔥 UBAH LOGIKA SORTING DISINI
        // 1. Status 2 (Selesai) dilempar ke paling bawah (Nilai 1), sisanya di atas (Nilai 0)
        // 2. Baru diurutkan berdasarkan waktu ambil tiket
        $query->orderByRaw('CASE WHEN antrians.status = 2 THEN 1 ELSE 0 END ASC')
            ->orderBy('antrians.waktu_ambil', 'asc')
            ->orderBy('antrians.no_urut', 'asc');

        return DataTables::of($query)
            ->addColumn('status_label', function ($row) {
                return match ((int) $row->status) {
                    0 => '<span class="badge badge-light-warning">Menunggu</span>',
                    1 => '<span class="badge badge-light-success">Dipanggil</span>',
                    2 => '<span class="badge badge-light-primary">Selesai</span>',
                    default => '-',
                };
            })
            ->addColumn('is_active', fn($row) => (int)$row->status === 1)
            ->addColumn('is_first', function ($row) {
                // LOGIKA GLOBAL STRICT FIFO
                // Cari ID antrian paling lama menunggu SE-GEDUNG (Status 0 paling awal)
                $globalFirstId = Antrian::where('status', 0)
                    ->hariIni()
                    ->orderBy('waktu_ambil', 'asc')
                    ->orderBy('no_urut', 'asc')
                    ->value('id');

                // Tombol "Panggil" hanya aktif jika antrian ini adalah yang paling depan
                return $row->id === $globalFirstId;
            })
            ->rawColumns(['status_label'])
            ->make(true);
    }

    /**
     * Logic Utama: Memanggil Antrian (Termasuk Panggil Ulang)
     */
    public function panggil(Request $request)
    {
        // 1. CEK APAKAH SEDANG COOLDOWN (Server Side Lock)
        // Jika ada lock di cache, tolak request
        if (Cache::has('lock_panggilan_global')) {
            return response()->json([
                'success' => false,
                'message' => 'Sistem sedang memanggil antrian lain, harap tunggu audio selesai.'
            ], 429); // 429 = Too Many Requests
        }

        $request->validate([
            'id' => 'required|exists:antrians,id'
        ]);

        // 1. Ambil Data Antrian Target
        $query = Antrian::query();
        $this->filterBySkpd($query); // Pastikan user berhak memanggil ini
        $antrian = $query->where('id', $request->id)->first();

        if (!$antrian) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        // ==========================================
        // SKENARIO A: PANGGIL ULANG (RECALL)
        // ==========================================
        if ($antrian->status == 1) {
            // Update waktu panggil terakhir
            $antrian->update(['waktu_panggil' => now()]);

            // Kirim Sinyal ke WebSocket (Reverb) agar Kios bunyi lagi
            $payload = [
                'no_antrian' => $antrian->no_antrian,
                'loket'      => $antrian->loket->nama_loket,
                'skpd'       => $antrian->skpd->nama_skpd,
                'waktu'      => now()->format('H:i')
            ];
            PanggilanAntrian::dispatch($payload);

            // Update Cache Redis
            $this->updateCache($antrian);

            return response()->json(['success' => true, 'message' => 'Antrian dipanggil ulang']);
        }

        // ==========================================
        // SKENARIO B: PANGGILAN BARU (STATUS 0 -> 1)
        // ==========================================

        // Cek Urutan Global (Strict FIFO)
        // Pastikan tidak ada orang lain yang menunggu lebih lama dari orang ini
        $antrianTerdepanGlobal = Antrian::query()
            ->hariIni()
            ->where('status', 0)
            ->orderBy('waktu_ambil', 'asc')
            ->orderBy('no_urut', 'asc')
            ->first();

        if (!$antrianTerdepanGlobal || $antrianTerdepanGlobal->id !== $antrian->id) {
            $pesan = 'Antrian tidak berurutan!';
            if ($antrianTerdepanGlobal) {
                $loketName = $antrianTerdepanGlobal->loket->nama_loket ?? '-';
                $pesan = "Harap tunggu! Antrian {$antrianTerdepanGlobal->no_antrian} di {$loketName} datang lebih dulu.";
            }
            return response()->json(['success' => false, 'message' => $pesan], 422);
        }

        // Eksekusi Panggil Baru
        $antrian->update([
            'status'        => 1,
            'waktu_panggil' => now()
        ]);

        // Kirim WebSocket (Reverb)
        $payload = [
            'no_antrian' => $antrian->no_antrian,
            'loket'      => $antrian->loket->nama_loket,
            'skpd'       => $antrian->skpd->nama_skpd,
            'waktu'      => now()->format('H:i')
        ];
        PanggilanAntrian::dispatch($payload);

        // C. 🔥 FITUR BARU: TRIGGER GLOBAL COOLDOWN
        $durasi = 30; // Detik (Sesuaikan dengan panjang audio)

        // 1. Pasang Lock di Server (Redis) selama 30 detik
        Cache::put('lock_panggilan_global', true, $durasi);

        // 2. Kirim Sinyal ke Semua Admin lain agar tombolnya disable
        CooldownTriggered::dispatch($durasi);

        // Simpan Cache Redis
        $this->updateCache($antrian);

        return response()->json(['success' => true, 'message' => 'Antrian berhasil dipanggil']);
    }

    /**
     * Helper: Update Cache Redis dengan Fix Collation
     */
    private function updateCache($antrian)
    {
        // Query Join Manual dengan DB::raw agar aman dari error collation
        $data = Antrian::select('antrians.*', 'lokets.nama_loket', 'skpd.nama_skpd')
            ->leftJoin('lokets', function ($join) {
                $join->on(DB::raw('lokets.id COLLATE utf8mb4_unicode_ci'), '=', DB::raw('antrians.loket_id COLLATE utf8mb4_unicode_ci'));
            })
            ->leftJoin('skpd', function ($join) {
                $join->on(DB::raw('skpd.id COLLATE utf8mb4_unicode_ci'), '=', DB::raw('antrians.skpd_id COLLATE utf8mb4_unicode_ci'));
            })
            ->where('antrians.id', $antrian->id)
            ->first();

        // Simpan ke Redis selamanya (sampai ditimpa panggilan baru)
        Cache::forever('panggilan_terakhir', $data);
    }

    /**
     * Statistik: Jumlah Total Hari Ini
     */
    public function jumlah()
    {
        $query = Antrian::query()->hariIni();
        $this->filterBySkpd($query);
        return $query->count();
    }

    /**
     * Statistik: Nomor Sedang Dipanggil
     */
    public function sekarang()
    {
        $query = Antrian::query()
            ->hariIni()
            ->where('status', 1)
            ->orderByDesc('waktu_panggil');

        $this->filterBySkpd($query);
        return $query->value('no_antrian') ?? '-';
    }

    /**
     * Statistik: Nomor Selanjutnya
     */
    public function selanjutnya()
    {
        $query = Antrian::query()
            ->hariIni()
            ->where('status', 0)
            ->orderBy('no_urut');

        $this->filterBySkpd($query);
        return $query->value('no_antrian') ?? '-';
    }

    /**
     * Statistik: Sisa Antrian Menunggu
     */
    public function sisa()
    {
        $query = Antrian::query()
            ->hariIni()
            ->where('status', 0);

        $this->filterBySkpd($query);
        return $query->count();
    }

    /**
     * Statistik: Jumlah Selesai
     */
    public function selesai()
    {
        $query = Antrian::query()
            ->hariIni()
            ->where('status', 2);

        $this->filterBySkpd($query);
        return $query->count();
    }

    /**
     * API: Mengambil 5 Riwayat Panggilan Terakhir
     */
    public function getHistory()
    {
        $history = Antrian::select(
            'antrians.no_antrian',
            'antrians.waktu_panggil',
            'lokets.nama_loket',
            'skpd.nama_skpd' // <--- Tambah Select SKPD
        )
            // JOIN Loket
            ->leftJoin('lokets', function ($join) {
                $join->on(DB::raw('lokets.id COLLATE utf8mb4_unicode_ci'), '=', DB::raw('antrians.loket_id COLLATE utf8mb4_unicode_ci'));
            })
            // JOIN SKPD (Baru)
            ->leftJoin('skpd', function ($join) {
                $join->on(DB::raw('skpd.id COLLATE utf8mb4_unicode_ci'), '=', DB::raw('antrians.skpd_id COLLATE utf8mb4_unicode_ci'));
            })
            ->whereNotNull('waktu_panggil')
            ->hariIni()
            ->orderBy('waktu_panggil', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'no_antrian'    => $item->no_antrian,
                    'nama_loket'    => $item->nama_loket,
                    'nama_skpd'     => $item->nama_skpd, // <--- Masukkan ke Respon JSON
                    'waktu_panggil' => \Carbon\Carbon::parse($item->waktu_panggil)
                        ->timezone('Asia/Jakarta')
                        ->format('H:i')
                ];
            });

        return response()->json($history);
    }

    /**
     * API: Info Card "Giliran Berikutnya" (Global)
     */
    /**
     * API: Info Card Global (Sedang Dipanggil & Selanjutnya)
     */
    public function getGlobalNextInfo()
    {
        // 1. CARI YANG SEDANG DIPANGGIL (Status 1, Paling Baru Dipanggil)
        $current = Antrian::where('status', 1)
            ->hariIni()
            ->with(['loket', 'skpd'])
            ->orderBy('waktu_panggil', 'desc') // Yang baru dipanggil paling atas
            ->first();

        // 2. CARI GILIRAN BERIKUTNYA (Status 0, Paling Lama Nunggu)
        $next = Antrian::where('status', 0)
            ->hariIni()
            ->with(['loket', 'skpd'])
            ->orderBy('waktu_ambil', 'asc')
            ->orderBy('no_urut', 'asc')
            ->first();

        return response()->json([
            'current' => $current ? [
                'status'     => 'exist',
                'no_antrian' => $current->no_antrian,
                'loket'      => $current->loket->nama_loket ?? '-',
                'skpd'       => $current->skpd->nama_skpd ?? '-',
                'waktu'      => $current->waktu_panggil ? \Carbon\Carbon::parse($current->waktu_panggil)->format('H:i') : '-'
            ] : ['status' => 'empty'],

            'next' => $next ? [
                'status'     => 'exist',
                'no_antrian' => $next->no_antrian,
                'loket'      => $next->loket->nama_loket ?? '-',
                'skpd'       => $next->skpd->nama_skpd ?? '-',
                'waktu'      => $next->waktu_ambil ? \Carbon\Carbon::parse($next->waktu_ambil)->format('H:i') : '-'
            ] : ['status' => 'empty']
        ]);
    }
}
