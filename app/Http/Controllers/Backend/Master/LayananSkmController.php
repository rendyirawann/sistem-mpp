<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;

use App\Models\LayananSkm;
use App\Models\Skpd;
use App\Models\Skm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Jenssegers\Agent\Agent;

class LayananSkmController extends Controller
{
    function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('permission:layanan_skm.list', ['only' => ['index', 'getData']]);
        $this->middleware('permission:layanan_skm.show', ['only' => ['show']]);
        $this->middleware('permission:layanan_skm.create', ['only' => ['store']]);
        $this->middleware('permission:layanan_skm.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:layanan_skm.delete', ['only' => ['destroy']]);
        $this->middleware('permission:layanan_skm.massdelete', ['only' => ['massDelete']]);
    }

    public function index()
    {
        return view('backend.master.layanan_skm.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = LayananSkm::with('skpd')->orderBy('layanan', 'asc');

            if (!empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function($q) use ($searchValue) {
                    $q->where('layanan', 'LIKE', "%{$searchValue}%")
                      ->orWhere('opd', 'LIKE', "%{$searchValue}%");
                });
            }

            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('skpd_name', function ($data) {
                    return $data->skpd ? $data->skpd->nama_skpd : $data->opd;
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at ? $data->created_at->format('d-m-Y H:i') : '-';
                })
                ->addColumn('action', function ($data) {
                    $buttons = '<div class="text-end">';

                    $buttons .= '
                        <a href="#"
                            class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 mb-1"
                            id="getShowRowData" 
                            data-id="' . $data->id . '"
                            data-bs-toggle="modal"
                            data-bs-target="#Modal_Show_Data">
                            <i class="ki-outline ki-eye fs-2"></i>
                        </a>';

                    $buttons .= '
                        <a href="#"
                            class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 mb-1"
                            id="getEditRowData" 
                            data-id="' . $data->id . '">
                            <i class="ki-outline ki-pencil fs-2"></i>
                        </a>';

                    $buttons .= '
                        <a href="#"
                            class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm"
                            data-id="' . $data->id . '"
                            data-bs-toggle="modal"
                            data-bs-target="#Modal_Hapus_Data"
                            id="getDeleteId">
                            <i class="ki-outline ki-trash fs-2"></i>
                        </a>';

                    $buttons .= '</div>';
                    return $buttons;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function show($id)
    {
        $data = LayananSkm::with('skpd')->findOrFail($id);
        $html = view('backend.master.layanan_skm.show', compact('data'))->render();
        return response()->json(['html' => $html]);
    }

    public function store(Request $request)
    {
        $formattedTime = Carbon::now()->diffForHumans();

        $validator = Validator::make($request->all(), [
            'id_opd'     => 'required',
            'id_layanan' => 'required|integer',
            'layanan'    => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        try {
            DB::beginTransaction();

            $skpd = Skpd::where('external_id_sukma', $request->id_opd)->first();

            $layananSkm = LayananSkm::create([
                'id_opd'     => $request->id_opd,
                'opd'        => $skpd ? $skpd->nama_skpd : '-',
                'id_layanan' => $request->id_layanan,
                'layanan'    => $request->layanan,
            ]);

            $newData = $layananSkm->toArray();
            $agent = new Agent;

            activity()
                ->useLog('tambah layanan skm')
                ->causedBy(auth()->user())
                ->performedOn($layananSkm)
                ->withProperties([
                    'ip' => $request->ip(),
                    'agent' => [
                        'browser' => $agent->browser() . ' ' . $agent->version($agent->browser()),
                        'os'      => $agent->platform() . ' ' . $agent->version($agent->platform()),
                        'device'  => $agent->device(),
                    ],
                    'new' => $newData,
                ])
                ->log('Membuat data layanan SKM');

            DB::commit();

            return response()->json([
                'success' => 'Layanan SKM berhasil disimpan.',
                'time'    => $formattedTime,
                'judul'   => 'Berhasil',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'time'  => $formattedTime,
                'judul' => 'Gagal',
            ]);
        }
    }

    public function edit($id)
    {
        $data = LayananSkm::findOrFail($id);
        $skpds = Skpd::all();
        $html = view('backend.master.layanan_skm.edit', compact('data', 'skpds'))->render();
        return response()->json(['html' => $html]);
    }

    public function update(Request $request, $id)
    {
        $formattedTime = Carbon::now()->diffForHumans();
        $layananSkm = LayananSkm::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'id_opd'     => 'required',
            'id_layanan' => 'required|integer',
            'layanan'    => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        try {
            DB::beginTransaction();

            $oldData = $layananSkm->getOriginal();
            $skpd = Skpd::where('external_id_sukma', $request->id_opd)->first();

            $layananSkm->update([
                'id_opd'     => $request->id_opd,
                'opd'        => $skpd ? $skpd->nama_skpd : '-',
                'id_layanan' => $request->id_layanan,
                'layanan'    => $request->layanan,
            ]);

            $newData = $layananSkm->toArray();
            $agent = new Agent;

            activity()
                ->useLog('edit layanan skm')
                ->causedBy(Auth::user())
                ->performedOn($layananSkm)
                ->withProperties([
                    'ip' => $request->ip(),
                    'agent' => [
                        'browser' => $agent->browser() . ' ' . $agent->version($agent->browser()),
                        'os'      => $agent->platform() . ' ' . $agent->version($agent->platform()),
                        'device'  => $agent->device(),
                    ],
                    'old' => $oldData,
                    'new' => $newData
                ])
                ->log('Mengedit Data Layanan SKM');

            DB::commit();

            return response()->json([
                'success' => 'Layanan SKM berhasil diperbarui.',
                'time'    => $formattedTime,
                'judul'   => 'Berhasil',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'time'  => $formattedTime,
                'judul' => 'Gagal',
            ]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $formattedTime = Carbon::now()->diffForHumans();

        try {
            DB::beginTransaction();

            $layananSkm = LayananSkm::findOrFail($id);
            $deleteData = $layananSkm->getOriginal();
            $layananSkm->delete();

            $agent = new Agent();
            activity()
                ->useLog('hapus layanan skm')
                ->causedBy(Auth::user())
                ->performedOn($layananSkm)
                ->withProperties([
                    'ip' => $request->ip(),
                    'agent' => [
                        'browser' => $agent->browser() . ' ' . $agent->version($agent->browser()),
                    ],
                    'delete' => $deleteData,
                ])
                ->log('Menghapus Data Layanan SKM');

            DB::commit();

            return response()->json([
                'success' => 'Layanan SKM berhasil dihapus.',
                'time'    => $formattedTime,
                'judul'   => 'Berhasil',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'time'  => $formattedTime,
                'judul' => 'Gagal',
            ]);
        }
    }

    public function massDelete(Request $request)
    {
        try {
            DB::beginTransaction();

            $ids = $request->ids;
            $items = LayananSkm::whereIn('id', $ids)->get();

            foreach ($items as $item) {
                $deleteData = $item->getOriginal();
                $item->delete();

                activity()
                    ->useLog('massdelete layanan skm')
                    ->causedBy(Auth::user())
                    ->performedOn($item)
                    ->withProperties(['delete' => $deleteData])
                    ->log('Menghapus Data Layanan SKM (Mass)');
            }

            DB::commit();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    }

