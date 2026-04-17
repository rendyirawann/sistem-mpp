<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Models\Skm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SkmController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('permission:skm.list', ['only' => ['index', 'getData']]);
        $this->middleware('permission:skm.show', ['only' => ['show']]);
        $this->middleware('permission:skm.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:skm.sync', ['only' => ['syncData']]);
    }

    public function index()
    {
        $unsyncedCount = Skm::where('is_synced', 0)->count();
        return view('backend.master.skm.index', compact('unsyncedCount'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = Skm::with(['antrian.customer', 'antrian.skpd'])->orderBy('created_at', 'desc');

            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('nama_customer', function ($data) {
                    return $data->antrian?->customer?->nama ?? '-';
                })
                ->addColumn('instansi', function ($data) {
                    return $data->antrian?->skpd?->nama_skpd ?? '-';
                })
                ->addColumn('status_sync', function ($data) {
                    if ($data->is_synced) {
                        return '<span class="badge badge-light-success">Synced</span>';
                    }
                    return '<span class="badge badge-light-danger">Pending</span>';
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

                    $buttons .= '</div>';
                    return $buttons;
                })
                ->rawColumns(['status_sync', 'action'])
                ->make(true);
        }
    }

    public function show($id)
    {
        $data = Skm::with(['antrian.customer', 'antrian.skpd', 'antrian.loket'])->findOrFail($id);
        $html = view('backend.master.skm.show', compact('data'))->render();
        return response()->json(['html' => $html]);
    }

    public function edit($id)
    {
        $data = Skm::findOrFail($id);
        $html = view('backend.master.skm.edit', compact('data'))->render();
        return response()->json(['html' => $html]);
    }

    public function update(Request $request, $id)
    {
        $skm = Skm::findOrFail($id);

        $request->validate([
            'umur' => 'required|numeric',
            'nilai' => 'required|numeric',
            'is_pungli' => 'required|in:0,1',
            'kritik_saran' => 'nullable|string'
        ]);

        try {
            $skm->update($request->only(['umur', 'nilai', 'is_pungli', 'pungli_kontak', 'pungli_keterangan', 'kritik_saran']));
            return response()->json(['success' => 'Data Survey berhasil diperbarui.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui data: ' . $e->getMessage()]);
        }
    }

    public function syncData(Request $request)
    {
        $pendingSurveys = Skm::where('is_synced', 0)->with(['antrian.skpd'])->get();

        if ($pendingSurveys->isEmpty()) {
            return response()->json(['status' => 'not_found', 'message' => 'Tidak ada data survey yang perlu disinkronisasi.']);
        }

        $url_api = "https://sukmadeli.deliserdangkab.go.id/api/v1/survey";
        $token   = "694e5225-d868-8323-8e10-21b0d3774720";
        $successCount = 0;
        $failCount = 0;

        foreach ($pendingSurveys as $skm) {
            $antrian = $skm->antrian;
            if (!$antrian || !$antrian->skpd) continue;

            $payload = [
                "id_skpd"      => (int)($antrian->skpd->external_id_sukma ?? 18),
                "umur"         => (int)$skm->umur,
                "jk"           => $skm->jk,
                "pendidikan"   => $skm->pendidikan,
                "pekerjaan"    => $skm->pekerjaan,
                "disabilitas"  => $skm->disabilitas,
                "id_pelayanan" => (int)$skm->jenis_layanan_id,
                "u1" => (int)$skm->u1,
                "u2" => (int)$skm->u2,
                "u3" => (int)$skm->u3,
                "u4" => (int)$skm->u4,
                "u5" => (int)$skm->u5,
                "u6" => (int)$skm->u6,
                "u7" => (int)$skm->u7,
                "u8" => (int)$skm->u8,
                "u9" => (int)$skm->u9
            ];

            try {
                $response = Http::withOptions(['verify' => false])
                    ->withHeaders(['X-API-TOKEN' => $token])
                    ->timeout(10)
                    ->post($url_api, $payload);

                if ($response->successful()) {
                    $skm->update(['is_synced' => 1]);
                    $successCount++;
                } else {
                    $failCount++;
                    Log::error("Sync Error for SKM ID {$skm->id}: " . $response->body());
                }
            } catch (\Exception $e) {
                $failCount++;
                Log::error("Sync Exception for SKM ID {$skm->id}: " . $e->getMessage());
            }
        }

        return response()->json([
            'status' => 'done',
            'message' => "Sinkronisasi selesai. Berhasil: {$successCount}, Gagal: {$failCount}.",
            'success_count' => $successCount,
            'fail_count' => $failCount
        ]);
    }
}
