<?php

namespace App\Http\Controllers\Backend\Antrian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Antrian;
use App\Models\Skpd;
use DB;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Auth;
use Jenssegers\Agent\Agent;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Activitylog\Models\Activity;
use Illuminate\View\View;

class AntrianController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('permission:antrian.list', ['only' => ['index', 'getData']]);
        $this->middleware('permission:antrian.create', ['only' => ['store']]);
        $this->middleware('permission:antrian.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:antrian.delete', ['only' => ['destroy', 'massDelete']]);
        $this->middleware('permission:antrian.call', ['only' => ['call']]);
    }

    /* =====================================================
     * INDEX
     * ===================================================== */
    public function index(): View
    {
        $skpd = Skpd::where('isaktif', 1)->get();
        return view('backend.antrian.index', compact('skpd'));
    }

    /* =====================================================
     * DATATABLES
     * ===================================================== */
    public function getData(Request $request)
    {
        $query = Antrian::with('skpd')->orderByDesc('created_at');
        return DataTables::of($query)
            ->addColumn('skpd', fn($r) => $r->skpd->nama_skpd ?? '-')
            ->addColumn('nomor_antrian', fn($r) => $r->nomor_antrian)
            ->addColumn('tanggal', fn($r) => $r->tanggal?->format('d-m-Y'))
            ->addColumn('status', function ($r) {
                return match ($r->status) {
                    0 => '<span class="badge badge-light-warning">Menunggu</span>',
                    1 => '<span class="badge badge-light-success">Dipanggil</span>',
                    default => '<span class="badge badge-light-secondary">Selesai</span>',
                };
            })
            ->addColumn('action', function ($row) {

                if (
                    !auth()->user()->can('antrian.edit') &&
                    !auth()->user()->can('antrian.delete')
                ) {
                    return '-';
                }

                $html = '<div class="text-center">
                    <button class="btn btn-sm btn-light btn-active-light-primary"
                        data-bs-toggle="dropdown">
                        <i class="ki-outline ki-dots-vertical fs-3"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end fs-7">';

                if (auth()->user()->can('antrian.edit')) {
                    $html .= '
                        <li>
                            <a href="javascript:void(0)"
                               class="dropdown-item"
                               id="getEditRowData"
                               data-id="' . $row->id . '">
                                <i class="ki-outline ki-pencil fs-5 me-2 text-warning"></i>Edit
                            </a>
                        </li>';
                }

                if (auth()->user()->can('antrian.delete')) {
                    $html .= '
                        <li>
                            <a href="javascript:void(0)"
                               class="dropdown-item"
                               data-id="' . $row->id . '"
                               data-bs-toggle="modal"
                               data-bs-target="#Modal_Hapus_Data"
                               id="getDeleteId">
                                <i class="ki-outline ki-trash fs-5 me-2 text-danger"></i>Hapus
                            </a>
                        </li>';
                }

                $html .= '</ul></div>';

                return $html;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /* =====================================================
     * STORE
     * ===================================================== */
    public function store(Request $request)
    {
        $formattedTime = Carbon::now()->diffForHumans();

        $validator = \Validator::make($request->all(), [
            'skpd_id' => 'required',
            'nomor_antrian' => 'required',
            'tanggal' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        try {
            DB::beginTransaction();

            $data = new Antrian;
            $data->id = Uuid::uuid4();
            $data->skpd_id = $request->skpd_id;
            $data->nomor_antrian = $request->nomor_antrian;
            $data->tanggal = $request->tanggal;
            $data->status = 0;
            $data->save();

            $agent = new Agent;

            activity()
                ->useLog('Tambah Antrian')
                ->causedBy(auth()->user())
                ->performedOn($data)
                ->withProperties([
                    'ip' => $request->ip(),
                    'agent' => $agent->browser(),
                    'new' => $data->toArray()
                ])
                ->log('Menambah antrian ' . $data->nomor_antrian);

            DB::commit();

            return response()->json([
                'success' => 'Antrian berhasil ditambahkan',
                'time' => $formattedTime,
                'judul' => 'Berhasil'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Terjadi kesalahan',
                'errorMessage' => $e->getMessage()
            ], 500);
        }
    }

    /* =====================================================
     * CALL ANTRIAN
     * ===================================================== */
    public function call(Request $request)
    {
        $antrian = Antrian::findOrFail($request->id);

        if (
            !auth()->user()->hasRole('superadmin') &&
            $antrian->skpd_id !== auth()->user()->skpd_id
        ) {
            abort(403);
        }

        Antrian::where('skpd_id', $antrian->skpd_id)
            ->where('status', 1)
            ->update([
                'status' => 2,
                'waktu_selesai' => now()
            ]);

        $antrian->update([
            'status' => 1,
            'waktu_panggil' => now()
        ]);

        return response()->json(['success' => true]);
    }

    /* =====================================================
     * EDIT
     * ===================================================== */
    public function edit($id)
    {
        $data = Antrian::findOrFail($id);

        $html = view('backend.antrian.edit', compact('data'))->render();
        return response()->json(['html' => $html]);
    }

    /* =====================================================
     * UPDATE
     * ===================================================== */
    public function update(Request $request, $id)
    {
        Antrian::findOrFail($id)->update(
            $request->only([
                'skpd_id',
                'nomor_antrian',
                'nomor_urut',
                'status',
                'tanggal'
            ])
        );

        return response()->json([
            'success' => 'Antrian berhasil diperbarui'
        ]);
    }

    /* =====================================================
     * DELETE
     * ===================================================== */
    public function destroy(Request $request, $id)
    {
        Antrian::findOrFail($id)->delete();

        return response()->json([
            'success' => 'Antrian berhasil dihapus'
        ]);
    }
    public function panggil($id)
    {
        DB::beginTransaction();

        $antrian = Antrian::findOrFail($id);

        $lastUrut = Antrian::where('skpd_id', $antrian->skpd_id)
            ->whereNotNull('nomor_urut')
            ->max('nomor_urut');

        $antrian->nomor_urut = ($lastUrut ?? 0) + 1;
        $antrian->status = 1; // dipanggil
        $antrian->save();

        DB::commit();

        return response()->json(['success' => 'Antrian dipanggil']);
    }
}
