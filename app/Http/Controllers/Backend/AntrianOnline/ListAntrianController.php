<?php

namespace App\Http\Controllers\Backend\AntrianOnline;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Antrian;
use Yajra\DataTables\Facades\DataTables;

/**
 * Daftar Antrian Online per tenant untuk memeriksa isian form persyaratan warga.
 * Dua tab: "berjalan" (hari ini & akan datang — reset harian) dan "selesai" (sudah lewat hari).
 */
class ListAntrianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('backend.antrian_online.index', [
            'isSuper' => auth()->user()->hasRole('Superadmin'),
        ]);
    }

    public function data(Request $request)
    {
        $u     = auth()->user();
        $tab   = $request->input('tab', 'berjalan');
        $today = Carbon::today()->toDateString();

        $q = Antrian::query()
            ->with(['customer', 'loket.skpd'])
            ->withCount('formValues')
            ->where('sumber', 'online');

        // Tenant hanya melihat instansinya; Superadmin melihat semua
        if (!$u->hasRole('Superadmin')) {
            $q->where('skpd_id', $u->skpd_id);
        }

        if ($tab === 'selesai') {
            $q->whereDate('tanggal', '<', $today);
        } else {
            $q->whereDate('tanggal', '>=', $today); // berjalan: hari ini & akan datang
        }

        $q->orderBy('tanggal', $tab === 'selesai' ? 'desc' : 'asc')->orderBy('no_urut', 'asc');

        return DataTables::of($q)
            ->addColumn('no_antrian', fn ($r) => '<span class="fw-bolder text-primary">' . e($r->no_antrian) . '</span>')
            ->addColumn('tanggal_f', fn ($r) => $r->tanggal ? Carbon::parse($r->tanggal)->locale('id')->isoFormat('ddd, D MMM Y') : '-')
            ->addColumn('nama', fn ($r) => e(optional($r->customer)->nama ?? '-'))
            ->addColumn('nik', fn ($r) => e(optional($r->customer)->nik ?? '-'))
            ->addColumn('instansi', fn ($r) => e(optional(optional($r->loket)->skpd)->nama_skpd ?? '-'))
            ->addColumn('layanan', fn ($r) => e(optional($r->loket)->nama_loket ?? '-'))
            ->addColumn('status', function ($r) {
                $map = [0 => ['Menunggu', 'warning'], 1 => ['Dipanggil', 'info'], 2 => ['Selesai', 'success'], 3 => ['Batal', 'danger']];
                [$lbl, $cls] = $map[$r->status] ?? ['-', 'secondary'];
                return '<span class="badge badge-light-' . $cls . '">' . $lbl . '</span>';
            })
            ->addColumn('form', function ($r) {
                return $r->form_values_count > 0
                    ? '<span class="badge badge-light-primary">' . $r->form_values_count . ' form</span>'
                    : '<span class="text-muted fs-8">—</span>';
            })
            ->addColumn('action', function ($r) {
                return '<button class="btn btn-sm btn-light-primary btn-detail" data-id="' . $r->id . '">'
                    . '<i class="ki-outline ki-eye fs-5"></i> Detail</button>';
            })
            ->rawColumns(['no_antrian', 'status', 'form', 'action'])
            ->make(true);
    }

    /** Detail 1 antrean online: data diri + foto + isian form persyaratan. */
    public function detail($id)
    {
        $u = auth()->user();
        $antrian = Antrian::with(['customer', 'loket.skpd', 'formValues.form'])
            ->where('sumber', 'online')->find($id);

        if (!$antrian) {
            return response()->json(['success' => false, 'message' => 'Antrean tidak ditemukan.'], 404);
        }
        if (!$u->hasRole('Superadmin') && (string) $u->skpd_id !== (string) $antrian->skpd_id) {
            return response()->json(['success' => false, 'message' => 'Antrean milik instansi lain.'], 403);
        }

        $c  = $antrian->customer;
        $jk = $c->jk ?? null;

        return response()->json([
            'success'    => true,
            'no_antrian' => $antrian->no_antrian,
            'status'     => ['Menunggu', 'Dipanggil', 'Selesai', 'Batal'][$antrian->status] ?? '-',
            'tanggal'    => $antrian->tanggal ? Carbon::parse($antrian->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y') : '-',
            'skpd'       => optional(optional($antrian->loket)->skpd)->nama_skpd ?? '-',
            'layanan'    => optional($antrian->loket)->nama_loket ?? '-',
            'nik'        => $c->nik ?? '-',
            'nama'       => $c->nama ?? '-',
            'jk'         => $jk === 'L' ? 'Laki-Laki' : ($jk === 'P' ? 'Perempuan' : '-'),
            'no_hp'      => $c->no_hp ?? '-',
            'foto'       => $antrian->foto_wajah ? asset('storage/' . $antrian->foto_wajah) : null,
            'forms'      => $antrian->formValues->map(fn ($fv) => $fv->toDisplay())->values(),
        ]);
    }
}
