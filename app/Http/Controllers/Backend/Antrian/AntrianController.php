<?php

namespace App\Http\Controllers\Backend\Antrian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Antrian;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Auth;
// use App\Events\PanggilanAntrian;

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
    public function getAntrian()
    {
        $query = Antrian::query()
            ->select([
                'antrians.id',
                'antrians.no_antrian',
                'antrians.status',
                'lokets.nama_loket',
                'skpd.nama_skpd'
            ])
            ->leftJoin('lokets', function ($join) {
                $join->on(
                    DB::raw('lokets.id COLLATE utf8mb4_unicode_ci'),
                    '=',
                    DB::raw('antrians.loket_id COLLATE utf8mb4_unicode_ci')
                );
            })
            ->leftJoin('skpd', function ($join) {
                $join->on(
                    DB::raw('skpd.id COLLATE utf8mb4_unicode_ci'),
                    '=',
                    DB::raw('antrians.skpd_id COLLATE utf8mb4_unicode_ci')
                );
            })
            ->hariIni()
            ->orderBy('antrians.no_urut');

        $this->filterBySkpd($query);

        $firstWaitingId = Antrian::query()
            ->hariIni()
            ->where('status', 0)
            ->when(!auth()->user()->hasRole('Superadmin'), function ($q) {
                $q->where('skpd_id', auth()->user()->skpd_id);
            })
            ->orderBy('no_urut')
            ->value('id');

        return DataTables::of($query)
            ->addColumn('status_label', function ($row) {
                return match ((int) $row->status) {
                    0 => '<span class="badge badge-light-warning">Menunggu</span>',
                    1 => '<span class="badge badge-light-success">Dipanggil</span>',
                    default => '-',
                };
            })
            ->addColumn('is_active', fn($row) => (int)$row->status === 1)
            ->addColumn('is_first', fn($row) => $row->id === $firstWaitingId)
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
    public function panggil(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:antrians,id'
        ]);

        $query = Antrian::query();
        $this->filterBySkpd($query);

        $antrian = $query->where('id', $request->id)->first();

        if (!$antrian) {
            return response()->json([
                'success' => false,
                'message' => 'Data antrian tidak ditemukan'
            ], 404);
        }

        // 🔁 JIKA SUDAH DIPANGGIL → UPDATE WAKTU SAJA (AGAR KIOS BUNYI LAGI)
        if ($antrian->status == 1) {
            $antrian->update([
                'waktu_panggil' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Antrian dipanggil ulang'
            ]);
        }

        // 🔒 CEK ANTRIAN TERKECIL YANG MASIH MENUNGGU (VALIDASI URUTAN)
        $antrianPertama = Antrian::query()
            ->hariIni()
            ->where('status', 0)
            ->when(!auth()->user()->hasRole('Superadmin'), function ($q) {
                $q->where('skpd_id', auth()->user()->skpd_id);
            })
            ->orderBy('no_urut')
            ->first();

        // Validasi urutan (Opsional: bisa dimatikan kalau mau panggil acak)
        if (!$antrianPertama || $antrianPertama->id !== $antrian->id) {
            return response()->json([
                'success' => false,
                'message' => 'Harus memanggil antrian terdepan terlebih dahulu'
            ], 422);
        }

        // ✅ PANGGIL ANTRIAN BARU
        $antrian->update([
            'status'        => 1,
            'waktu_panggil' => now()
        ]);

        // --- [HAPUS BAGIAN BROADCAST INI] ---
        // $dataLengkap = ...
        // broadcast(new PanggilanAntrian($dataLengkap));
        // ------------------------------------

        return response()->json([
            'success' => true,
            'message' => 'Antrian berhasil dipanggil'
        ]);
    }
}
