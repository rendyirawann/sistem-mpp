<?php

namespace App\Http\Controllers\Backend\Antrian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Antrian;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Auth;
use App\Events\PanggilanAntrian; // Jangan lupa import
use Illuminate\Support\Facades\Cache;

class AntrianController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('permission:antrian.list', ['only' => ['index', 'getAntrian']]);
    }

    /**
     * FILTER QUERY BERDASARKAN ROLE
     */
    private function filterBySkpd($query)
    {
        if (!auth()->user()->hasRole('Superadmin')) {
            $query->where('antrians.skpd_id', auth()->user()->skpd_id);
        }

        return $query;
    }

    /**
     * Halaman Panggilan Antrian
     */
    public function index()
    {
        return view('backend.antrian.index');
    }

    /**
     * DataTables
     */
    // public function getAntrian()
    // {
    //     $query = Antrian::query()
    //         ->select([
    //             'antrians.id',
    //             'antrians.no_antrian',
    //             'antrians.status',
    //             'lokets.nama_loket',
    //             'skpd.nama_skpd'
    //         ])
    //         ->leftJoin('lokets', function ($join) {
    //             $join->on(
    //                 DB::raw('lokets.id COLLATE utf8mb4_unicode_ci'),
    //                 '=',
    //                 DB::raw('antrians.loket_id COLLATE utf8mb4_unicode_ci')
    //             );
    //         })
    //         ->leftJoin('skpd', function ($join) {
    //             $join->on(
    //                 DB::raw('skpd.id COLLATE utf8mb4_unicode_ci'),
    //                 '=',
    //                 DB::raw('antrians.skpd_id COLLATE utf8mb4_unicode_ci')
    //             );
    //         })
    //         ->hariIni()
    //         ->orderBy('antrians.no_urut');

    //     $this->filterBySkpd($query);

    //     $firstWaitingId = Antrian::query()
    //         ->hariIni()
    //         ->where('status', 0)
    //         ->when(!auth()->user()->hasRole('Superadmin'), function ($q) {
    //             $q->where('skpd_id', auth()->user()->skpd_id);
    //         })
    //         ->orderBy('no_urut')
    //         ->value('id');

    //     return DataTables::of($query)
    //         ->addColumn('status_label', function ($row) {
    //             return match ((int) $row->status) {
    //                 0 => '<span class="badge badge-light-warning">Menunggu</span>',
    //                 1 => '<span class="badge badge-light-success">Dipanggil</span>',
    //                 default => '-',
    //             };
    //         })
    //         ->addColumn('is_active', fn($row) => (int)$row->status === 1)
    //         ->addColumn('is_first', fn($row) => $row->id === $firstWaitingId)
    //         ->rawColumns(['status_label'])
    //         ->make(true);
    // }

    public function getAntrian()
    {
        // ... (Query Select sama seperti sebelumnya) ...
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
            ->leftJoin('lokets', function ($join) {
                $join->on(DB::raw('lokets.id COLLATE utf8mb4_unicode_ci'), '=', DB::raw('antrians.loket_id COLLATE utf8mb4_unicode_ci'));
            })
            ->leftJoin('skpd', function ($join) {
                $join->on(DB::raw('skpd.id COLLATE utf8mb4_unicode_ci'), '=', DB::raw('antrians.skpd_id COLLATE utf8mb4_unicode_ci'));
            })
            ->hariIni();

        $this->filterBySkpd($query);

        // Sorting
        $query->orderBy('antrians.waktu_ambil', 'asc')
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

                // 🔥 LOGIKA BARU: GLOBAL STRICT
                // Cari ID antrian paling lama menunggu SE-GEDUNG (Tanpa filter loket/skpd)
                $globalFirstId = Antrian::where('status', 0)
                    ->hariIni()
                    ->orderBy('waktu_ambil', 'asc')
                    ->orderBy('no_urut', 'asc')
                    ->value('id');

                // Tombol hanya aktif jika ID antrian ini == ID antrian terlama se-gedung
                return $row->id === $globalFirstId;
            })
            ->rawColumns(['status_label'])
            ->make(true);
    }




    /**
     * Jumlah antrian
     */
    public function jumlah()
    {
        $query = Antrian::query()->hariIni();

        $this->filterBySkpd($query);

        return $query->count();
    }

    /**
     * Antrian sekarang
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
     * Antrian selanjutnya
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
     * Sisa antrian
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
     * Panggil antrian
     */
    // public function panggil(Request $request)
    // {
    //     $request->validate([
    //         'id' => 'required|exists:antrians,id'
    //     ]);

    //     $query = Antrian::query();
    //     $this->filterBySkpd($query);

    //     $antrian = $query->where('id', $request->id)->first();

    //     if (!$antrian) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Data antrian tidak ditemukan'
    //         ], 404);
    //     }

    //     // 🔁 JIKA SUDAH DIPANGGIL → UPDATE WAKTU SAJA (AGAR KIOS BUNYI LAGI)
    //     if ($antrian->status == 1) {
    //         $antrian->update([
    //             'waktu_panggil' => now()
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Antrian dipanggil ulang'
    //         ]);
    //     }

    //     // 🔒 CEK ANTRIAN TERKECIL YANG MASIH MENUNGGU (VALIDASI URUTAN)
    //     $antrianPertama = Antrian::query()
    //         ->hariIni()
    //         ->where('status', 0)
    //         ->when(!auth()->user()->hasRole('Superadmin'), function ($q) {
    //             $q->where('skpd_id', auth()->user()->skpd_id);
    //         })
    //         ->orderBy('no_urut')
    //         ->first();

    //     // Validasi urutan (Opsional: bisa dimatikan kalau mau panggil acak)
    //     if (!$antrianPertama || $antrianPertama->id !== $antrian->id) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Harus memanggil antrian terdepan terlebih dahulu'
    //         ], 422);
    //     }

    //     $antrian->update([
    //         'status'        => 1,
    //         'waktu_panggil' => now()
    //     ]);

    //     // 🔥 TAMBAHAN: SIMPAN DATA KE CACHE AGAR FRONTEND TIDAK QUERY DB TERUS
    //     // Kita simpan data lengkapnya agar frontend tinggal ambil jadi
    //     $dataUntukCache = Antrian::select(
    //         'antrians.*',
    //         'lokets.nama_loket',
    //         'skpd.nama_skpd'
    //     )
    //         ->leftJoin('lokets', 'lokets.id', '=', 'antrians.loket_id')
    //         ->leftJoin('skpd', 'skpd.id', '=', 'antrians.skpd_id')
    //         ->where('antrians.id', $antrian->id)
    //         ->first();

    //     // Simpan ke cache selamanya (sampai ada panggilan baru menimpa)
    //     // Key cache-nya kita namakan 'panggilan_terakhir'
    //     Cache::forever('panggilan_terakhir', $dataUntukCache);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Antrian berhasil dipanggil'
    //     ]);
    // }

    public function panggil(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:antrians,id'
        ]);

        // 1. Cek Apakah Ada Antrian Lain yang Sedang Dipanggil?
        $lastCall = Cache::get('panggilan_terakhir');
        // if ($lastCall) { ... (Kode cooldown opsional) ... }

        // 2. Ambil Data Antrian Target
        $query = Antrian::query();
        $this->filterBySkpd($query);
        $antrian = $query->where('id', $request->id)->first();

        if (!$antrian) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        // 3. LOGIKA PANGGIL ULANG (RECALL)
        if ($antrian->status == 1) {
            $antrian->update(['waktu_panggil' => now()]);

            // 🔥 PERBAIKAN 1: KIRIM SINYAL WEBSOCKET AGAR KIOS BUNYI LAGI
            $payload = [
                'no_antrian' => $antrian->no_antrian,
                'loket'      => $antrian->loket->nama_loket,
                'skpd'       => $antrian->skpd->nama_skpd,
                'waktu'      => now()->format('H:i')
            ];
            PanggilanAntrian::dispatch($payload);

            // Update Cache
            $this->updateCache($antrian);

            return response()->json(['success' => true, 'message' => 'Antrian dipanggil ulang']);
        }

        // 4. VALIDASI URUTAN GLOBAL (Strict FIFO)
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

        // 5. Eksekusi Panggil Baru
        $antrian->update([
            'status'        => 1,
            'waktu_panggil' => now()
        ]);

        // 6. Kirim WebSocket
        $payload = [
            'no_antrian' => $antrian->no_antrian,
            'loket'      => $antrian->loket->nama_loket,
            'skpd'       => $antrian->skpd->nama_skpd,
            'waktu'      => now()->format('H:i')
        ];
        PanggilanAntrian::dispatch($payload);

        // Simpan Cache
        $this->updateCache($antrian);

        return response()->json(['success' => true, 'message' => 'Antrian berhasil dipanggil']);
    }

    // 🔥 PERBAIKAN 2: GUNAKAN DB::RAW AGAR TIDAK ERROR COLLATION
    private function updateCache($antrian)
    {
        $data = Antrian::select('antrians.*', 'lokets.nama_loket', 'skpd.nama_skpd')
            ->leftJoin('lokets', function ($join) {
                $join->on(DB::raw('lokets.id COLLATE utf8mb4_unicode_ci'), '=', DB::raw('antrians.loket_id COLLATE utf8mb4_unicode_ci'));
            })
            ->leftJoin('skpd', function ($join) {
                $join->on(DB::raw('skpd.id COLLATE utf8mb4_unicode_ci'), '=', DB::raw('antrians.skpd_id COLLATE utf8mb4_unicode_ci'));
            })
            ->where('antrians.id', $antrian->id)
            ->first();

        Cache::forever('panggilan_terakhir', $data);
    }

    /**
     * Jumlah Antrian Selesai (Status = 2)
     */
    /**
     * Jumlah Antrian Selesai (Status = 2)
     */
    public function selesai()
    {
        $query = Antrian::query()
            ->hariIni()
            ->where('status', 2); // Status 2 = Selesai

        $this->filterBySkpd($query);

        return $query->count();
    }

    /**
     * API: Mengambil Info Antrian Global Terdepan (Untuk Card Utama)
     */
    public function getGlobalNextInfo()
    {
        // Cari antrian status 0 yang paling tua jamnya (Global FIFO)
        $next = Antrian::where('status', 0)
            ->hariIni()
            ->with(['loket', 'skpd']) // Load relasi biar nama loket muncul
            ->orderBy('waktu_ambil', 'asc')
            ->orderBy('no_urut', 'asc')
            ->first();

        if (!$next) {
            return response()->json([
                'status' => 'empty',
                'pesan'  => 'Tidak ada antrian menunggu'
            ]);
        }

        return response()->json([
            'status'     => 'exist',
            'no_antrian' => $next->no_antrian,
            'loket'      => $next->loket->nama_loket ?? '-',
            'skpd'       => $next->skpd->nama_skpd ?? '-',
            'waktu'      => $next->waktu_ambil->format('H:i')
        ]);
    }
}
