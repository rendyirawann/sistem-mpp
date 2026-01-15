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

class LoketController extends Controller
{
    function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('permission:loket.list', ['only' => ['index', 'getData']]);
        $this->middleware('permission:loket.create', ['only' => ['store']]);
        $this->middleware('permission:loket.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:loket.delete', ['only' => ['destroy']]);
        $this->middleware('permission:loket.massdelete', ['only' => ['massDelete']]);
    }

    public function index(): View
    {
        $skpd = Skpd::orderBy('id', 'desc')
        ->get();
        return view('backend.loket.index',compact('skpd'));
    }

    public function getData(Request $request)
    {
        $query = Loket::query();

        if (!auth()->user()->hasRole('Superadmin')) {
            $query->where('skpd_id', auth()->user()->skpd_id);
        }

        $query->orderByDesc('created_at');


        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('nama_loket', 'like', "%{$search}%")
                  ->orWhere('kode_tenant', 'like', "%{$search}%")
                  ->orWhere('prefix_tenant', 'like', "%{$search}%");
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

                if (auth()->user()->can('loket.delete')) {
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

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'nama_loket'    => 'required|string|max:255',
            'kode_tenant'   => 'required|string|max:50',
            'prefix_tenant' => 'required|string|max:5',
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

        if (!auth()->user()->hasRole('Superadmin')) {
            $query->where('skpd_id', auth()->user()->skpd_id);
        }

        $query->update($request->only([
            'nama_loket','kode_tenant','prefix_tenant','isaktif','skpd_id'
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
        $lokets = Loket::withTrashed()
            ->whereIn('id', $ids)
            ->get();

        if ($lokets->isEmpty()) {
            return response()->json([
                'status'  => 'warning',
                'message' => 'Data Loket tidak ditemukan'
            ]);
        }

        // ===============================
        // FORCE DELETE (HAPUS PERMANEN)
        // ===============================
        Loket::withTrashed()
            ->whereIn('id', $ids)
            ->forceDelete();

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
            'message' => count($ids).' Loket berhasil dihapus permanen'
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
