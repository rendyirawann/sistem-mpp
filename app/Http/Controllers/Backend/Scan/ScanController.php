<?php

namespace App\Http\Controllers\Backend\Scan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Antrian;

class ScanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('backend.scan.index');
    }

    /** Cari detail antrean dari hasil scan QR (format: no|skpd|tanggal|id) atau no_antrian. */
    public function lookup(Request $request)
    {
        $kode    = trim((string) $request->input('kode', ''));
        $tanggal = trim((string) $request->input('tanggal', ''));
        if ($kode === '') {
            return response()->json(['success' => false, 'message' => 'Kode kosong.'], 422);
        }

        // QR tiket online: "{no_antrian}|{skpd}|{tanggal}|{antrian_id}"
        $parts = explode('|', $kode);
        $id    = count($parts) >= 4 ? trim($parts[3]) : $kode;

        $with = ['customer', 'loket.skpd', 'formValues.form'];
        // Coba by ID (dari QR = UUID unik, selalu akurat)
        $antrian = Antrian::with($with)->find($id);
        if (!$antrian) {
            // fallback: cari by nomor antrian. Nomor online berulang tiap hari per layanan,
            // jadi kalau tanggal diisi -> saring by tanggal agar tepat (bukan sekadar yang terbaru).
            $cari = count($parts) >= 1 ? trim($parts[0]) : $kode;
            $q = Antrian::with($with)->where('no_antrian', $cari);
            if ($tanggal !== '') {
                $q->whereDate('tanggal', $tanggal);
            }
            $antrian = $q->latest('created_at')->first();
        }

        if (!$antrian) {
            return response()->json(['success' => false, 'message' => 'Antrean tidak ditemukan.'], 404);
        }

        // Tenant hanya boleh melihat antrean instansinya
        $u = auth()->user();
        if (!$u->hasRole('Superadmin') && (string) $u->skpd_id !== (string) $antrian->skpd_id) {
            return response()->json(['success' => false, 'message' => 'Antrean ini milik instansi lain.'], 403);
        }

        $c    = $antrian->customer;
        $jk   = $c->jk ?? null;
        $foto = $antrian->foto_wajah ? asset('storage/' . $antrian->foto_wajah) : null;

        return response()->json([
            'success'    => true,
            'message'    => 'Antrean ditemukan: ' . $antrian->no_antrian,
            'no_antrian' => $antrian->no_antrian,
            'sumber'     => $antrian->sumber ?? 'kiosk',
            'status'     => ['Menunggu', 'Dipanggil', 'Selesai', 'Batal'][$antrian->status] ?? '-',
            'tanggal'    => $antrian->tanggal ? Carbon::parse($antrian->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y') : '-',
            'skpd'       => optional(optional($antrian->loket)->skpd)->nama_skpd ?? '-',
            'layanan'    => optional($antrian->loket)->nama_loket ?? '-',
            'nik'        => $c->nik ?? '-',
            'nama'       => $c->nama ?? '-',
            'jk'         => $jk === 'L' ? 'Laki-Laki' : ($jk === 'P' ? 'Perempuan' : '-'),
            'no_hp'      => $c->no_hp ?? '-',
            'foto'       => $foto,
            'forms'      => $antrian->formValues->map(fn ($fv) => $fv->toDisplay())->values(),
        ]);
    }
}
