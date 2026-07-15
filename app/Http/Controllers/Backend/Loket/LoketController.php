<?php

namespace App\Http\Controllers\Backend\Loket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loket;
use App\Models\Skpd;
use DB;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Auth;
use Jenssegers\Agent\Agent;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
class LoketController extends Controller
{
    function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('permission:loket.list', ['only' => ['index', 'getData', 'exportPdf']]);
        $this->middleware('permission:loket.create', ['only' => ['store']]);
        $this->middleware('permission:loket.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:loket.delete', ['only' => ['destroy']]);
        $this->middleware('permission:loket.massdelete', ['only' => ['massDelete']]);
    }

    public function index(): View
    {
        // Tenant (non-Superadmin) hanya boleh melihat/memfilter instansinya sendiri,
        // agar dropdown filter selaras dengan data yang bisa diakses.
        $skpd = Skpd::query()
            ->when(!auth()->user()->hasRole('Superadmin'), function ($q) {
                $q->where('id', auth()->user()->skpd_id);
            })
            ->orderBy('nama_skpd')
            ->get();

        return view('backend.loket.index', compact('skpd'));
    }

    public function getData(Request $request)
    {
        $query = Loket::query()->with('skpd');

        if (!auth()->user()->hasRole('Superadmin')) {
            $query->where('skpd_id', auth()->user()->skpd_id);
        }

        // Filter berdasarkan Instansi (dropdown)
        if ($request->filled('skpd_id') && $request->skpd_id !== 'all') {
            $query->where('skpd_id', $request->skpd_id);
        }

        $query->orderByDesc('created_at');


        if (!empty($request->search['value'])) {
            $search = $request->search['value'];

            // Resolve dulu id SKPD yang namanya cocok (query tabel skpd saja).
            // Hindari whereHas (join antar kolom skpd_id = id) yang error karena
            // beda collation antar kolom di DB (1267 Illegal mix of collations).
            $skpdIds = Skpd::where('nama_skpd', 'like', "%{$search}%")->pluck('id')->all();

            $query->where(function ($q) use ($search, $skpdIds) {
                $q->where('nama_loket', 'like', "%{$search}%")
                  ->orWhere('kode_tenant', 'like', "%{$search}%")
                  ->orWhere('prefix_tenant', 'like', "%{$search}%");

                // Cari juga berdasarkan Nama Instansi (SKPD)
                if (!empty($skpdIds)) {
                    $q->orWhereIn('skpd_id', $skpdIds);
                }
            });
        }

        return DataTables::of($query)
            ->addColumn('nama_instansi', function ($r) {
            return $r->skpd->nama_skpd ?? '-';})
            ->setRowId('id') // ⬅️ PENTING
            ->addColumn('id', fn($r) => $r->id) // ⬅️ TAMBAHKAN BIAR AMAN
            ->addColumn('nama_loket', fn($r) => $r->nama_loket ?? '-')
            ->addColumn('kode_tenant', fn($r) => $r->kode_tenant)
            ->addColumn('prefix_tenant', fn($r) => $r->prefix_tenant)
            ->addColumn('isaktif', function ($r) {
                return $r->isaktif
                    ? '<span class="badge badge-light-success">Aktif</span>'
                    : '<span class="badge badge-light-danger">Nonaktif</span>';
            })
            ->addColumn('action', function ($row) {

                $html = '
                <div class="text-center">
                    <button class="btn btn-sm btn-light btn-active-light-primary"
                        data-bs-toggle="dropdown">
                        <i class="ki-outline ki-dots-vertical fs-3"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end fs-7">';

                if (auth()->user()->can('loket.edit')) {
                    $html .= '
                    <li>
                        <a href="javascript:void(0)" class="dropdown-item"
                           id="getEditRowData" data-id="'.$row->id.'">
                            <i class="ki-outline ki-pencil me-2 text-warning"></i>Edit
                        </a>
                    </li>';
                }

                if (auth()->user()->can('loket.delete') && !$row->hasAntrianData()) {
                    $html .= '
                    <li>
                        <a href="javascript:void(0)" class="dropdown-item"
                           data-id="'.$row->id.'"
                           data-bs-toggle="modal"
                           data-bs-target="#Modal_Hapus_Data"
                           id="getDeleteId">
                            <i class="ki-outline ki-trash me-2 text-danger"></i>Hapus
                        </a>
                    </li>';
                }

                $html .= '</ul></div>';

                return $html;
            })
            ->rawColumns(['isaktif','action'])
            ->make(true);
    }

    /**
     * Export daftar layanan (loket) ke PDF.
     * Mengikuti filter yang sedang aktif: pencarian (search) & instansi (skpd_id).
     */
    public function exportPdf(Request $request)
    {
        $query = Loket::query()->with('skpd');

        // Role guard: tenant hanya boleh export miliknya
        $namaInstansi = 'Semua Instansi';
        if (!auth()->user()->hasRole('Superadmin')) {
            $query->where('skpd_id', auth()->user()->skpd_id);
            // Label header menyesuaikan instansi tenant (bukan "Semua Instansi")
            $namaInstansi = optional(Skpd::find(auth()->user()->skpd_id))->nama_skpd ?? 'Instansi';
        }

        // Filter Instansi (dropdown)
        if ($request->filled('skpd_id') && $request->skpd_id !== 'all') {
            $query->where('skpd_id', $request->skpd_id);
            $namaInstansi = optional(Skpd::find($request->skpd_id))->nama_skpd ?? 'Instansi';
        }

        // Filter Pencarian (selaras dengan getData)
        if ($request->filled('search')) {
            $search = $request->search;

            // Resolve id SKPD dulu untuk menghindari join antar kolom beda collation.
            $skpdIds = Skpd::where('nama_skpd', 'like', "%{$search}%")->pluck('id')->all();

            $query->where(function ($q) use ($search, $skpdIds) {
                $q->where('nama_loket', 'like', "%{$search}%")
                  ->orWhere('kode_tenant', 'like', "%{$search}%")
                  ->orWhere('prefix_tenant', 'like', "%{$search}%");

                if (!empty($skpdIds)) {
                    $q->orWhereIn('skpd_id', $skpdIds);
                }
            });
        }

        // Ambil & urutkan berdasarkan nama instansi agar rapi
        $lokets = $query->get()
            ->sortBy(fn($l) => $l->skpd->nama_skpd ?? 'ZZZ')
            ->values();

        $pdf = Pdf::loadView('backend.loket.export_pdf', [
            'lokets'       => $lokets,
            'namaInstansi' => $namaInstansi,
            'tanggalCetak' => Carbon::now()->locale('id')->translatedFormat('d F Y H:i'),
        ]);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('daftar-layanan.pdf');
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'nama_loket'    => 'required|string|max:255',
            'kode_tenant'   => 'required|string|max:50',
            'prefix_tenant' => 'required|string|max:10',
            'isaktif'       => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        DB::beginTransaction();

        try {
            $data = new Loket;
            $data->id            = Uuid::uuid4();
            $data->skpd_id       = $request->skpd_id;
            $data->nama_loket    = $request->nama_loket;
            $data->kode_tenant   = $request->kode_tenant;
            $data->prefix_tenant = $request->prefix_tenant;
            $data->isaktif       = $request->isaktif;
            $data->save();

            DB::commit();

            return response()->json([
                'success' => 'Loket berhasil ditambahkan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Gagal menyimpan loket',
                'errorMessage' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $query = Loket::query();

        if (!auth()->user()->hasRole('Superadmin')) {
            $query->where('skpd_id', auth()->user()->skpd_id);
        }

        $loket = $query->findOrFail($id);
        $skpd  = Skpd::orderBy('nama_skpd')->get();

        return response()->json([
            'html' => view('backend.loket.edit', compact('loket', 'skpd'))->render()
        ]);
    }


    public function update(Request $request, $id)
    {
        $query = Loket::query();

        // 🔒 Role guard
        if (!auth()->user()->hasRole('Superadmin')) {
            $query->where('skpd_id', auth()->user()->skpd_id);
        }

        // 🎯 WAJIB: filter ID
        $query->where('id', $id);

        // ✅ VALIDASI UPDATE (ignore ID sendiri)
        $request->validate([
            'nama_loket' => 'required|string',
            'kode_tenant' => [
                'required',
                Rule::unique('lokets', 'kode_tenant')->ignore($id)
            ],
            'prefix_tenant' => 'required|string|max:5',
            'isaktif' => 'required|in:0,1',
            'skpd_id' => 'required'
        ]);

        // 🔒 Nama layanan terkunci jika sudah punya data antrian (semua role)
        $current = (clone $query)->first();
        if ($current && $current->hasAntrianData() && $request->nama_loket !== $current->nama_loket) {
            return response()->json([
                'error' => 'Nama layanan tidak dapat diubah karena sudah memiliki data antrian. Anda hanya dapat menonaktifkannya.',
            ], 422);
        }

        // 🚀 UPDATE DATA
        $query->update($request->only([
            'nama_loket',
            'kode_tenant',
            'prefix_tenant',
            'isaktif',
            'skpd_id'
        ]));

        return response()->json([
            'success' => 'Loket berhasil diperbarui'
        ]);
    }

    public function destroy($id)
    {
        $query = Loket::query();

        if (!auth()->user()->hasRole('Superadmin')) {
            $query->where('skpd_id', auth()->user()->skpd_id);
        }

        $loket = $query->findOrFail($id);

        // 🔒 Tidak boleh dihapus jika sudah punya data antrian (semua role)
        if ($loket->hasAntrianData()) {
            return response()->json([
                'error' => 'Layanan tidak dapat dihapus karena sudah memiliki data antrian. Anda hanya dapat menonaktifkannya.',
            ], 422);
        }

        $loket->delete();

        return response()->json([
            'success' => 'Loket berhasil dihapus'
        ]);
    }


    public function massDelete(Request $request)
{
    $formattedTime = Carbon::now()->diffForHumans();

    try {
        DB::beginTransaction();

        $ids = $request->ids;

        if (empty($ids)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No Loket selected for deletion.'
            ]);
        }

        // 🔐 hanya Superadmin
        if (!auth()->user()->hasRole('Superadmin')) {
            abort(403, 'Tidak diizinkan');
        }

        // ===============================
        // AMBIL DATA LOKET UNTUK AUDIT
        // ===============================
        $allLoket = Loket::whereIn('id', $ids)->get();

        if ($allLoket->isEmpty()) {
            return response()->json([
                'status'  => 'warning',
                'message' => 'Data Loket tidak ditemukan'
            ]);
        }

        // 🔒 Lindungi layanan yang sudah punya data antrian
        $deletable = $allLoket->reject(fn ($l) => $l->hasAntrianData());
        $protected = $allLoket->count() - $deletable->count();

        if ($deletable->isEmpty()) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Layanan terpilih sudah memiliki data antrian, tidak dapat dihapus. Hanya bisa dinonaktifkan.'
            ]);
        }

        $lokets = $deletable; // untuk logging

        // Hapus hanya yang tidak punya data antrian
        Loket::whereIn('id', $deletable->pluck('id')->all())->delete();

        DB::commit();

        // ===============================
        // LOG ACTIVITY (AUDIT)
        // ===============================
        $agent = new Agent();

        foreach ($lokets as $loket) {
            activity()
                ->useLog('massdelete loket')
                ->causedBy(Auth::user())
                ->performedOn($loket)
                ->withProperties([
                    'ip' => $request->ip(),
                    'agent' => [
                        'browser'    => $agent->browser().' '.$agent->version($agent->browser()),
                        'os'         => $agent->platform().' '.$agent->version($agent->platform()),
                        'device'     => $agent->device(),
                        'is_mobile'  => $agent->isMobile(),
                        'is_desktop' => $agent->isDesktop(),
                        'raw'        => $request->header('User-Agent'),
                    ],
                    'request' => [
                        'method' => $request->method(),
                        'url'    => $request->fullUrl(),
                    ],
                    'data' => $loket->toArray(),
                ])
                ->log('Menghapus data Loket: '.$loket->nama_loket);
        }

        return response()->json([
            'status'  => 'success',
            'message' => $deletable->count().' Loket berhasil dihapus'
                . ($protected > 0 ? ', '.$protected.' dilindungi karena sudah punya data antrian' : '')
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'status'       => 'error',
            'judul'        => 'Gagal',
            'message'      => 'Data Loket gagal dihapus',
            'time'         => $formattedTime,
            'errorMessage' => $e->getMessage()
        ], 500);
    }
}



}
