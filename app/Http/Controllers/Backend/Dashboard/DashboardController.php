<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Skpd;
use App\Models\Loket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $loketQuery = Loket::query();
        if (!$user->hasRole('Superadmin')) {
            $antrianQuery->where('skpd_id', $user->skpd_id);
            $loketQuery->where('skpd_id', $user->skpd_id);
        }

        $data = [
            // Antrian
            'total_antrian'     => (clone $antrianQuery)->count(), // Total seumur hidup
            'antrian_hari_ini'  => (clone $antrianQuery)->hariIni()->count(),
            'antrian_menunggu'  => (clone $antrianQuery)->where('status', 0)->hariIni()->count(),
            'antrian_dipanggil' => (clone $antrianQuery)->where('status', 1)->hariIni()->count(),
            // 🔥 PERBAIKAN: Tambahkan perhitungan Antrian Selesai (Status 2)
            'antrian_selesai'   => (clone $antrianQuery)->where('status', 2)->hariIni()->count(),

            // 🔥 PERBAIKAN: Tambahkan perhitungan Layanan/Loket
            'total_loket'       => (clone $loketQuery)->count(),
            'loket_aktif'       => (clone $loketQuery)->where('isaktif', 1)->count(),
            'loket_nonaktif'    => (clone $loketQuery)->where('isaktif', 0)->count(),

            // 🔥 PERBAIKAN: Tambahkan Total SKPD (Instansi)
            'total_skpd'        => Skpd::count(),
        ];

        // ==========================================
        // 2. LOGIC FILTER REKAPITULASI (BARU)
        // ==========================================

        $rekapQuery = Antrian::query();

        // A. Filter SKPD (Role Based)
        if ($user->hasRole('Superadmin')) {
            // Jika Superadmin memilih SKPD tertentu
            if ($request->filled('skpd_id') && $request->skpd_id != 'all') {
                $rekapQuery->whereHas('loket', function ($q) use ($request) {
                    $q->where('skpd_id', $request->skpd_id);
                });
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

        $status = $request->status ?? 'all';

        if ($status !== 'all') {
            $rekapQuery->where('status', $status);
        }

        // C. Filter JK (Jenis Kelamin) — hanya berlaku untuk format Detail
        $jk = $request->jk ?? 'all';
        if ($jk !== 'all') {
            $rekapQuery->whereHas('customer', function ($q) use ($jk) {
                $q->where('jk', $jk);
            });
        }

        // D. Grouping Data (Rekap Layanan) - Kode Lama tapi variable status ikut dipassing
        $rekapLayanan = (clone $rekapQuery)
            ->select('loket_id', DB::raw('count(*) as total'))
            ->groupBy('loket_id')
            ->with(['loket', 'loket.skpd'])
            ->orderBy(DB::raw('count(*)'), 'desc')
            ->get();

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
            'bulan',
            'status',
            'jk'
        ));
    }

    // Method untuk mengambil detail list antrian via AJAX (Modal)
    public function getDetailRekap(Request $request)
    {
        $query = Antrian::query()
            ->with(['loket', 'skpd'])
            ->where('loket_id', $request->loket_id); // Filter berdasarkan loket yg diklik

        // Copy paste logic filter tanggal dari index agar datanya sinkron
        $filterType = $request->filter_type ?? 'hari_ini';
        $startDate  = $request->start_date ?? date('Y-m-d');
        $endDate    = $request->end_date ?? date('Y-m-d');
        $bulan      = $request->bulan ?? date('Y-m');

        switch ($filterType) {
            case 'hari_ini':
                $query->whereDate('tanggal', date('Y-m-d'));
                break;
            case 'per_tanggal':
                $query->whereDate('tanggal', $startDate);
                break;
            case 'per_bulan':
                $query->where('tanggal', 'like', "$bulan%");
                break;
            case 'range':
                $query->whereBetween('tanggal', [$startDate, $endDate]);
                break;
        }

        // Filter Status (jika ada)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter JK (jika ada)
        if ($request->filled('jk') && $request->jk !== 'all') {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('jk', $request->jk);
            });
        }

        $antrian = $query->orderBy('no_urut', 'asc')->get();

        // Return berupa partial view atau HTML table sederhana
        $html = view('backend.dashboard.partial_detail_table', compact('antrian'))->render();

        return response()->json(['html' => $html]);
    }

    /**
     * EXPORT PDF / EXCEL
     */
    // public function exportLaporan(Request $request)
    // {
    //     // 1. QUERY DASAR (Copy logic filter dari index, atau buat private function biar clean)
    //     $query = Antrian::query()->with(['loket', 'loket.skpd', 'customer']); // Pastikan load relasi

    //     // A. Filter Role SKPD
    //     if (!Auth::user()->hasRole('Superadmin')) {
    //         // Jika User Dinas Login: Paksa ambil antrian berdasarkan Loket milik Dinas tersebut
    //         $query->whereHas('loket', function ($q) {
    //             $q->where('skpd_id', Auth::user()->skpd_id);
    //         });
    //     } elseif ($request->filled('skpd_id') && $request->skpd_id != 'all') {
    //         $query->whereHas('loket', function ($q) use ($request) {
    //             $q->where('skpd_id', $request->skpd_id);
    //         });
    //     }

    //     // B. Filter Status
    //     if ($request->filled('status') && $request->status !== 'all') {
    //         $query->where('status', $request->status);
    //     }

    //     // C. Filter Tanggal
    //     $filterType = $request->filter_type ?? 'hari_ini';
    //     $startDate  = $request->start_date ?? date('Y-m-d');
    //     $endDate    = $request->end_date ?? date('Y-m-d');
    //     $bulan      = $request->bulan ?? date('Y-m');

    //     switch ($filterType) {
    //         case 'hari_ini':
    //             $query->whereDate('tanggal', date('Y-m-d'));
    //             break;
    //         case 'per_tanggal':
    //             $query->whereDate('tanggal', $startDate);
    //             break;
    //         case 'per_bulan':
    //             $query->where('tanggal', 'like', "$bulan%");
    //             break;
    //         case 'range':
    //             $query->whereBetween('tanggal', [$startDate, $endDate]);
    //             break;
    //     }

    //     // 2. EKSEKUSI QUERY & GROUPING
    //     // Kita ambil data mentah dulu, urutkan biar rapi
    //     $rawData = $query->orderBy('skpd_id')->orderBy('loket_id')->orderBy('created_at')->get();
    //     $total = $rawData->count();
    //     $labelPeriode = $filterType === 'hari_ini' ? date('d-m-Y') : "$startDate s/d $endDate";

    //     $type = $request->input('type', 'pdf');

    //     if ($type == 'excel') {
    //         // 🔥 JIKA EXCEL: Kirim $rawData (Flat) ke LaporanExport (yang pakai export_excel.blade.php)
    //         return Excel::download(new LaporanExport($rawData, $total, $labelPeriode), 'laporan_antrian.xlsx');
    //     } else {
    //         // 🔥 JIKA PDF: Lakukan Grouping Data
    //         $groupedData = $rawData->groupBy([
    //             function ($item) {
    //                 return $item->loket->skpd->nama_skpd ?? 'Tanpa Instansi';
    //             },
    //             function ($item) {
    //                 return $item->loket->nama_loket ?? 'Tanpa Loket';
    //             }
    //         ]);

    //         // Gunakan View PDF yang lama (export.blade.php)
    //         $pdf = Pdf::loadView('backend.dashboard.export', [
    //             'data' => $groupedData,
    //             'total' => $total,
    //             'labelPeriode' => $labelPeriode,
    //             'type' => 'pdf'
    //         ]);
    //         $pdf->setPaper('a4', 'landscape');
    //         return $pdf->stream('laporan_antrian.pdf');
    //     }
    // }

    public function exportLaporan(Request $request)
    {
        // ==========================================================
        // 1. BUILD QUERY DASAR (Shared untuk semua jenis laporan)
        // ==========================================================
        $query = Antrian::query();

        // A. FILTER INSTANSI (Gunakan whereHas agar akurat sesuai pemilik loket)
        if (!Auth::user()->hasRole('Superadmin')) {
            $query->whereHas('loket', function ($q) {
                $q->where('skpd_id', Auth::user()->skpd_id);
            });
        } elseif ($request->filled('skpd_id') && $request->skpd_id != 'all') {
            $query->whereHas('loket', function ($q) use ($request) {
                $q->where('skpd_id', $request->skpd_id);
            });
        }

        // B. FILTER TANGGAL
        $filterType = $request->filter_type ?? 'hari_ini';
        $startDate  = $request->start_date ?? date('Y-m-d');
        $endDate    = $request->end_date ?? date('Y-m-d');
        $bulan      = $request->bulan ?? date('Y-m');

        switch ($filterType) {
            case 'hari_ini':
                $query->whereDate('tanggal', date('Y-m-d'));
                break;
            case 'per_tanggal':
                $query->whereDate('tanggal', $startDate);
                break;
            case 'per_bulan':
                $query->where('tanggal', 'like', "$bulan%");
                break;
            case 'range':
                $query->whereBetween('tanggal', [$startDate, $endDate]);
                break;
        }

        // Label Periode untuk Judul Laporan
        $labelPeriode = $filterType === 'hari_ini' ? date('d-m-Y') : "$startDate s/d $endDate";

        // Tangkap Jenis Laporan (Detail atau Rekap) & Tipe File (PDF/Excel)
        $formatLaporan = $request->format_laporan ?? 'detail';
        $type = $request->input('type', 'pdf');

        // ==========================================================
        // 2. JIKA EXCEL (Selalu Detail / Raw Data)
        // ==========================================================
        if ($type == 'excel') {
            // Excel biasanya butuh data lengkap, jadi kita masukkan filter status juga
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Filter JK
            if ($request->filled('jk') && $request->jk !== 'all') {
                $query->whereHas('customer', function ($q) use ($request) {
                    $q->where('jk', $request->jk);
                });
            }

            // Load relasi lengkap
            $rawData = $query->with(['loket', 'loket.skpd', 'customer'])
                ->orderBy('created_at')
                ->get();
            $total = $rawData->count();

            return Excel::download(new LaporanExport($rawData, $total, $labelPeriode), 'laporan_antrian.xlsx');
        }

        // ==========================================================
        // 3. JIKA PDF: MODE REKAPITULASI (GENERAL)
        // ==========================================================
        if ($formatLaporan == 'rekap') {
            // Di mode rekap, kita TIDAK memfilter status (menghitung semua yang masuk)
            // Group by Loket ID dan hitung totalnya
            $dataRekap = $query->select('loket_id', DB::raw('count(*) as total'))
                ->with(['loket', 'loket.skpd'])
                ->groupBy('loket_id')
                ->get()
                ->sortBy(function ($row) {
                    // Urutkan berdasarkan Nama SKPD biar rapi
                    return $row->loket->skpd->nama_skpd ?? 'ZZZ';
                });

            $pdf = Pdf::loadView('backend.dashboard.export_rekap', [
                'data' => $dataRekap,
                'labelPeriode' => $labelPeriode
            ]);
            $pdf->setPaper('a4', 'portrait');
            return $pdf->stream('laporan_rekapitulasi.pdf');
        }

        // ==========================================================
        // 4. JIKA PDF: MODE DETAIL (DEFAULT)
        // ==========================================================

        // Filter Status (Hanya berlaku di mode Detail)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter JK (Jenis Kelamin)
        $jkFilter = $request->jk ?? 'all';
        if ($jkFilter !== 'all') {
            $query->whereHas('customer', function ($q) use ($jkFilter) {
                $q->where('jk', $jkFilter);
            });
        }

        // Load Relasi
        $query->with(['loket', 'loket.skpd', 'customer']);

        // Ambil Data & Urutkan
        $rawData = $query->orderBy('skpd_id')->orderBy('loket_id')->orderBy('created_at')->get();
        $total = $rawData->count();

        // Label JK untuk judul PDF
        $labelJk = match ($jkFilter) {
            'L' => ' — Filter: Laki-laki',
            'P' => ' — Filter: Perempuan',
            default => ''
        };

        // Grouping Data: Instansi -> Loket -> Antrian
        $groupedData = $rawData->groupBy([
            function ($item) {
                return $item->loket->skpd->nama_skpd ?? 'Tanpa Instansi';
            },
            function ($item) {
                return $item->loket->nama_loket ?? 'Tanpa Loket';
            }
        ]);

        $pdf = Pdf::loadView('backend.dashboard.export', [
            'data'         => $groupedData,
            'total'        => $total,
            'labelPeriode' => $labelPeriode . $labelJk,
            'type'         => 'pdf'
        ]);
        $pdf->setPaper('a4', 'landscape');
        return $pdf->stream('laporan_antrian_detail.pdf');
    }

    public function getDetailCard(Request $request)
    {
        $type = $request->type;
        $user = Auth::user();

        // Siapkan Query Dasar dengan Eager Loading agar Tabel tidak kosong
        $antrianQuery = Antrian::with(['loket', 'loket.skpd', 'customer'])->whereDate('created_at', date('Y-m-d'));
        $loketQuery   = Loket::with(['skpd']);
        $skpdQuery    = Skpd::query();

        if (!$user->hasRole('Superadmin')) {
            $antrianQuery->where('skpd_id', $user->skpd_id);
            $loketQuery->where('skpd_id', $user->skpd_id);
        }

        switch ($type) {
            // --- ANTRIAN ---
            case 'antrian_total': // Total Antrian (Biasanya hari ini)
                $data = (clone $antrianQuery)->orderBy('no_urut', 'desc')->get();
                $view = 'backend.dashboard.detail.antrian';
                break;
            case 'antrian_hari_ini':
                $data = (clone $antrianQuery)->orderBy('no_urut', 'desc')->get();
                $view = 'backend.dashboard.detail.antrian';
                break;
            case 'antrian_menunggu':
                $data = (clone $antrianQuery)->where('status', 0)->orderBy('no_urut')->get();
                $view = 'backend.dashboard.detail.antrian';
                break;
            case 'antrian_dipanggil':
                $data = (clone $antrianQuery)->where('status', 1)->orderBy('no_urut')->get();
                $view = 'backend.dashboard.detail.antrian';
                break;
            case 'antrian_selesai': // 🔥 CASE BARU
                $data = (clone $antrianQuery)->where('status', 2)->orderBy('updated_at', 'desc')->get();
                $view = 'backend.dashboard.detail.antrian';
                break;

            // --- LOKET ---
            case 'loket_all':
                $data = (clone $loketQuery)->orderBy('nama_loket')->get();
                $view = 'backend.dashboard.detail.loket';
                break;
            case 'loket_aktif':
                $data = (clone $loketQuery)->where('isaktif', 1)->orderBy('nama_loket')->get();
                $view = 'backend.dashboard.detail.loket';
                break;
            case 'loket_nonaktif':
                $data = (clone $loketQuery)->where('isaktif', 0)->orderBy('nama_loket')->get();
                $view = 'backend.dashboard.detail.loket';
                break;

            // --- SKPD ---
            case 'total_skpd': // 🔥 CASE BARU
                $data = (clone $skpdQuery)->orderBy('nama_skpd')->get();
                $view = 'backend.dashboard.detail.skpd'; // Kita pakai view loket saja atau buat baru, tapi sementara pakai loket logic
                // Atau return json simpel jika belum ada view khusus
                // Untuk sekarang kita asumsikan pakai view loket tapi kolomnya disesuaikan nanti
                $view = 'backend.dashboard.detail.loket';
                break;

            default:
                return response()->json(['html' => '<div class="alert alert-danger">Tipe tidak ditemukan</div>']);
        }

        // Render View ke HTML string
        $html = view($view, compact('data', 'type'))->render();

        return response()->json(['html' => $html]);
    }
}
