<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Skpd;
use App\Models\Loket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// 🔥 TAMBAHKAN 2 BARIS INI
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. QUERY KARTU STATISTIK (LOGIKA LAMA - TETAP ADA)
        $antrianQuery = Antrian::query();
        if (!$user->hasRole('Superadmin')) {
            $antrianQuery->where('skpd_id', $user->skpd_id);
        }

        $data = [
            'total_antrian'     => (clone $antrianQuery)->count(),
            'antrian_hari_ini'  => (clone $antrianQuery)->hariIni()->count(),
            'antrian_menunggu'  => (clone $antrianQuery)->where('status', 0)->hariIni()->count(),
            'antrian_dipanggil' => (clone $antrianQuery)->where('status', 1)->hariIni()->count(),
        ];

        // ==========================================
        // 2. LOGIC FILTER REKAPITULASI (BARU)
        // ==========================================

        $rekapQuery = Antrian::query();

        // A. Filter SKPD (Role Based)
        if ($user->hasRole('Superadmin')) {
            // Jika Superadmin memilih SKPD tertentu
            if ($request->filled('skpd_id')) {
                $rekapQuery->where('skpd_id', $request->skpd_id);
            }
        } else {
            // Tenant terkunci ke datanya sendiri
            $rekapQuery->where('skpd_id', $user->skpd_id);
        }

        // B. Filter Tanggal / Periode
        $filterType = $request->filter_type ?? 'hari_ini';
        $startDate  = $request->start_date ?? date('Y-m-d');
        $endDate    = $request->end_date ?? date('Y-m-d');
        $bulan      = $request->bulan ?? date('Y-m');

        switch ($filterType) {
            case 'hari_ini':
                $rekapQuery->whereDate('tanggal', date('Y-m-d'));
                break;
            case 'per_tanggal':
                $rekapQuery->whereDate('tanggal', $startDate);
                break;
            case 'per_bulan':
                // Format $bulan biasanya "2025-02"
                $rekapQuery->where('tanggal', 'like', "$bulan%");
                break;
            case 'range': // Bisa dipakai untuk Per Minggu juga
                $rekapQuery->whereBetween('tanggal', [$startDate, $endDate]);
                break;
        }

        // C. Grouping Data (Rekap Layanan)
        // Kita hitung jumlah antrian per Loket (Layanan)
        $rekapLayanan = (clone $rekapQuery)
            ->select('loket_id', DB::raw('count(*) as total'))
            ->groupBy('loket_id')
            ->with(['loket', 'loket.skpd']) // Load relasi biar nama muncul
            ->orderBy(DB::raw('count(*)'), 'desc')
            ->get();

        // Total dari hasil filter
        $totalRekap = $rekapQuery->count();

        // List SKPD untuk Dropdown Filter (Superadmin Only)
        $listSkpd = [];
        if ($user->hasRole('Superadmin')) {
            $listSkpd = Skpd::orderBy('nama_skpd')->get();
        }

        return view('backend.dashboard.index', compact(
            'data',
            'rekapLayanan',
            'totalRekap',
            'listSkpd',
            'filterType',
            'startDate',
            'endDate',
            'bulan'
        ));
    }

    /**
     * EXPORT PDF / EXCEL
     */
    public function exportLaporan(Request $request)
    {
        $user = Auth::user();
        $type = $request->export_type; // 'pdf' atau 'excel'

        // ==========================================
        // 1. LOGIC QUERY (SAMA PERSIS DENGAN INDEX)
        // ==========================================
        $rekapQuery = \App\Models\Antrian::query();

        // A. Filter SKPD
        if ($user->hasRole('Superadmin')) {
            if ($request->filled('skpd_id')) $rekapQuery->where('skpd_id', $request->skpd_id);
        } else {
            $rekapQuery->where('skpd_id', $user->skpd_id);
        }

        // B. Filter Tanggal
        $filterType = $request->filter_type ?? 'hari_ini';
        $startDate  = $request->start_date ?? date('Y-m-d');
        $endDate    = $request->end_date ?? date('Y-m-d');
        $bulan      = $request->bulan ?? date('Y-m');
        $labelPeriode = "";

        switch ($filterType) {
            case 'hari_ini':
                $rekapQuery->whereDate('tanggal', date('Y-m-d'));
                $labelPeriode = "Hari Ini (" . date('d-m-Y') . ")";
                break;
            case 'per_tanggal':
                $rekapQuery->whereDate('tanggal', $startDate);
                $labelPeriode = "Tanggal " . date('d-m-Y', strtotime($startDate));
                break;
            case 'per_bulan':
                $rekapQuery->where('tanggal', 'like', "$bulan%");
                $labelPeriode = "Bulan " . date('F Y', strtotime($bulan));
                break;
            case 'range':
                $rekapQuery->whereBetween('tanggal', [$startDate, $endDate]);
                $labelPeriode = date('d-m-Y', strtotime($startDate)) . " s/d " . date('d-m-Y', strtotime($endDate));
                break;
        }

        // C. Ambil Data
        $data = (clone $rekapQuery)
            ->select('loket_id', DB::raw('count(*) as total'))
            ->groupBy('loket_id')
            ->with(['loket', 'loket.skpd'])
            ->orderBy(DB::raw('count(*)'), 'desc')
            ->get();

        $total = $rekapQuery->count();


        // ==========================================
        // 2. EKSEKUSI EXPORT / PDF
        // ==========================================

        // JIKA PILIH EXCEL (.xlsx)
        if ($type == 'excel') {
            $fileName = "Laporan_Antrian_" . date('d-m-Y_His') . ".xlsx";

            // 🔥 Panggil Class Export yang baru kita buat
            return Excel::download(new LaporanExport($data, $total, $labelPeriode), $fileName);
        }

        // JIKA PILIH PDF (Tetap pakai Window Print)
        return view('backend.dashboard.export', compact('data', 'total', 'labelPeriode', 'type'));
    }

    public function detail(Request $request)
    {
        $type = $request->type;

        // ===============================
        // BASE QUERY
        // ===============================
        $antrianQuery = Antrian::query();
        $loketQuery   = Loket::query();

        // 🔐 FILTER SKPD (KECUALI SUPERADMIN)
        if (!auth()->user()->hasRole('Superadmin')) {
            $antrianQuery->where('skpd_id', auth()->user()->skpd_id);
            $loketQuery->where('skpd_id', auth()->user()->skpd_id);
        }

        switch ($type) {

            // ===============================
            // ANTRIAN
            // ===============================
            case 'antrian_all':
                $data = (clone $antrianQuery)
                    ->orderBy('no_urut')
                    ->get();
                $view = 'backend.dashboard.detail.antrian';
                break;

            case 'antrian_today':
                $data = (clone $antrianQuery)
                    ->hariIni()
                    ->orderBy('no_urut')
                    ->get();
                $view = 'backend.dashboard.detail.antrian';
                break;

            case 'antrian_menunggu':
                $data = (clone $antrianQuery)
                    ->where('status', 0)
                    ->orderBy('no_urut')
                    ->get();
                $view = 'backend.dashboard.detail.antrian';
                break;

            case 'antrian_dipanggil':
                $data = (clone $antrianQuery)
                    ->where('status', 1)
                    ->orderBy('no_urut')
                    ->get();
                $view = 'backend.dashboard.detail.antrian';
                break;

            // ===============================
            // LOKET
            // ===============================
            case 'loket_all':
                $data = (clone $loketQuery)
                    ->orderBy('nama_loket')
                    ->get();
                $view = 'backend.dashboard.detail.loket';
                break;

            case 'loket_aktif':
                $data = (clone $loketQuery)
                    ->where('isaktif', 1)
                    ->orderBy('nama_loket')
                    ->get();
                $view = 'backend.dashboard.detail.loket';
                break;

            case 'loket_nonaktif':
                $data = (clone $loketQuery)
                    ->where('isaktif', 0)
                    ->orderBy('nama_loket')
                    ->get();
                $view = 'backend.dashboard.detail.loket';
                break;

            default:
                abort(404);
        }

        return view($view, compact('data'));
    }
}
