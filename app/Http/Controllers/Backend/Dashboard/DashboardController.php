<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Skpd;
use App\Models\Loket;
use Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // BASE QUERY ANTRIAN
        $antrianQuery = Antrian::query();

        // 🔐 FILTER SKPD (KECUALI SUPERADMIN)
        if (!auth()->user()->hasRole('Superadmin')) {
            $antrianQuery->where('skpd_id', auth()->user()->skpd_id);
        }

        $loketQuery = Loket::query();

        if (!auth()->user()->hasRole('Superadmin')) {
            $loketQuery->where('skpd_id', auth()->user()->skpd_id);
        }

        $data = [
            // TOTAL ANTRIAN
            'total_antrian' => (clone $antrianQuery)
                            ->count(),

            // ANTRIAN HARI INI
            'antrian_hari_ini' => (clone $antrianQuery)
                                    ->hariIni()
                                    ->count(),

            // MENUNGGU (STATUS 0)
            'antrian_menunggu' => (clone $antrianQuery)
                                    ->where('status', 0)
                                    ->hariIni()
                                    ->count(),

            // DIPANGGIL (STATUS 1)
            'antrian_dipanggil' => (clone $antrianQuery)
                                    ->where('status', 1)
                                    ->hariIni()
                                    ->count(),

            // ===============================
            // LOKET
            // ===============================
            'total_loket' => (clone $loketQuery)->count(),

            'loket_aktif' => (clone $loketQuery)
                                ->where('isaktif', 1)
                                ->count(),

            'loket_nonaktif' => (clone $loketQuery)
                                ->where('isaktif', 0)
                                ->count(),

            // TOTAL SKPD
            'total_skpd' => auth()->user()->hasRole('Superadmin')
                            ? Skpd::count()
                            : 1,
        ];

        return view('backend.dashboard.index', compact('data'));
    }
}
