<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Satuan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Jenssegers\Agent\Agent;


class SatuanController extends Controller
{
    function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('permission:satuan.list', ['only' => ['index','getData']]);
        $this->middleware('permission:satuan.show', ['only' => ['detail']]);
        $this->middleware('permission:satuan.create', ['only' => ['store']]);
        $this->middleware('permission:satuan.edit', ['only' => ['edit','update']]);
        $this->middleware('permission:satuan.delete', ['only' => ['destroy']]);
        $this->middleware('permission:satuan.massdelete', ['only' => ['massDelete']]);
    }

    /////////////////////// BEGIN LIST ///////////////////////
    public function index()
    {
        return view('backend.master.satuan.index');
    }
    /////////////////////// END LIST ///////////////////////

    /////////////////////// BEGIN DATATABLES ///////////////////////
   public function getData(Request $request)
{
    if ($request->ajax()) {
        $query = Satuan::with('user')->orderBy('nama', 'asc');

        if (!empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where('nama', 'LIKE', "%{$searchValue}%");
        }

        return datatables()->of($query)
            ->addIndexColumn()

            // Tambahkan kolom user_id
            ->addColumn('user_id', function ($data) {
                return $data->user?->name ?? '-' ; 
            })

            // Tambahkan kolom created_at
            ->addColumn('created_at', function ($data) {
                return $data->created_at ? $data->created_at->format('d-m-Y H:i') : '-';
            })

            // Tambahkan kolom updated_at
            ->addColumn('updated_at', function ($data) {
                return $data->updated_at ? $data->updated_at->format('d-m-Y H:i') : '-';
            })

            // Tombol Aksi
            ->addColumn('action', function($data) {
                $buttons = '<div class="text-end">';
                
                $buttons .= '
                    <a href="#"
                        class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 mb-1"
                        id="getShowRowData" 
                        data-id="'.$data->id.'"
                        data-bs-toggle="modal"
                        data-bs-target="#Modal_Show_Data">
                        <i class="ki-outline ki-eye fs-2"></i>
                    </a>';

                $buttons .= '
                    <a href="#"
                        class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 mb-1"
                        id="getEditRowData" 
                        data-id="'.$data->id.'">
                        <i class="ki-outline ki-pencil fs-2"></i>
                    </a>';

                $buttons .= '
                    <a href="#"
                        class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm"
                        data-id="'.$data->id.'"
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

    /////////////////////// END DATATABLES ///////////////////////

    /////////////////////// BEGIN SHOW ///////////////////////
public function show($id)
    {
        $data = Satuan::with('user')->findOrFail($id);
        $html = view('backend.master.satuan.show', compact('data'))->render();
        return response()->json(['html' => $html]);
    }
    /////////////////////// END SHOW ///////////////////////

    /////////////////////// BEGIN STORE ///////////////////////
    public function store(Request $request)
    {
        $formattedTime = Carbon::now()->diffForHumans();

        $validator = Validator::make($request->all(), [
            'nama'   => 'required|string|max:255|unique:satuan,nama',
        ], [
            'nama.required' => 'Nama satuan wajib diisi.',
            'nama.unique'   => 'Nama satuan sudah ada.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        try {
            \DB::beginTransaction();

            $satuan = Satuan::create([
                'nama'   => $request->nama,
                'user_id' => auth()->user()->id,
            ]);


            $newData = $satuan->toArray();

           $agent = new Agent;
    activity()
    ->useLog('tambah satuan')
    ->causedBy(auth()->user())
    ->performedOn($satuan)
    ->withProperties([
        'ip' => $request->ip(),
        'agent' => [
            'browser' => $agent->browser() . ' ' . $agent->version($agent->browser()),
            'os' => $agent->platform() . ' ' . $agent->version($agent->platform()),
            'device' => $agent->device(),
            'is_mobile' => $agent->isMobile(),
            'is_desktop' => $agent->isDesktop(),
            'raw' => $request->header('User-Agent'),
        ],
        'request' => [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
        ],
        'new' => $newData,
    ])
    ->log('Membuat data satuan');

            \DB::commit();

            return response()->json([
                'success' => 'Satuan berhasil disimpan.',
                'time'    => $formattedTime,
                'judul'   => 'Berhasil',
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'time'  => $formattedTime,
                'judul' => 'Gagal',
            ]);
        }
    }
    /////////////////////// END STORE ///////////////////////

    /////////////////////// BEGIN UPDATE ///////////////////////
    public function edit($id)
    {
        $data = Satuan::findOrFail($id);
        $html = view('backend.master.satuan.edit', compact('data'))->render();
        return response()->json(['html' => $html]);
    }

    public function update(Request $request, $id)
    {
        $formattedTime = Carbon::now()->diffForHumans();
        $satuan = Satuan::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama'   => 'required|string|max:255|unique:satuan,nama,' . $id . ',id',
        ], [
            'nama.required' => 'Nama satuan wajib diisi.',
            'nama.unique'   => 'Nama brand sudah ada.',
     
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        try {
            \DB::beginTransaction();

            $oldData = $satuan->getOriginal();

            $satuan->update([
                'nama'   => $request->nama,
                'user_id' => auth()->user()->id,
            ]);

          
        $newData = $satuan->toArray();
             // Agent Info
        $agent = new \Jenssegers\Agent\Agent;

        // ===== AUDIT TRAIL =====
        activity()
            ->useLog('edit satuan')
            ->causedBy(Auth::user())
            ->performedOn($satuan)
            ->withProperties([
                'ip' => $request->ip(),
                'agent' => [
                    'browser' => $agent->browser() . ' ' . $agent->version($agent->browser()),
                    'os'      => $agent->platform() . ' ' . $agent->version($agent->platform()),
                    'device'  => $agent->device(),
                    'is_mobile' => $agent->isMobile(),
                    'is_desktop' => $agent->isDesktop(),
                    'raw' => $request->header('User-Agent'),
                ],
                'request' => [
                    'method' => $request->method(),
                    'url'    => $request->fullUrl(),
                ],
                'old' => $oldData,   
                'new' => $newData    
            ])
            ->log('Mengedit Data Satuan');

            \DB::commit();

            return response()->json([
                'success' => 'Satuan berhasil diperbarui.',
                'time'    => $formattedTime,
                'judul'   => 'Berhasil',
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'time'  => $formattedTime,
                'judul' => 'Gagal',
            ]);
        }
    }
    /////////////////////// END UPDATE ///////////////////////

    /////////////////////// BEGIN DESTROY ///////////////////////
public function destroy(Request $request, $id)
    {
        $formattedTime = Carbon::now()->diffForHumans();

        try {
            \DB::beginTransaction();

            $satuan = Satuan::findOrFail($id);
            $deleteData = $satuan->getOriginal();
            $satuan->delete();


        // ===== AUDIT TRAIL =====
        $agent = new Agent();

            activity()
                ->useLog('hapus satuan')
                ->causedBy(Auth::user()) 
                ->performedOn($satuan)
                ->withProperties([
                    'ip' => $request->ip(),
                    'agent' => [
                        'browser' => $agent->browser() . ' ' . $agent->version($agent->browser()),
                        'os'      => $agent->platform() . ' ' . $agent->version($agent->platform()),
                        'device'  => $agent->device(),
                        'is_mobile' => $agent->isMobile(),
                        'is_desktop' => $agent->isDesktop(),
                        'raw' => $request->header('User-Agent'),
                    ],
                    'request' => [
                        'method' => $request->method(),
                        'url'    => $request->fullUrl(),
                    ],
                    'delete' => $deleteData,
                ])
                ->log('Menghapus Data Satuan');


            \DB::commit();

            return response()->json([
                'success' => 'Satuan berhasil dihapus.',
                'time'    => $formattedTime,
                'judul'   => 'Berhasil',
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'time'  => $formattedTime,
                'judul' => 'Gagal',
            ]);
        }
    }
    /////////////////////// END DESTROY ///////////////////////

    /////////////////////// BEGIN MASS DELETE ///////////////////////
    public function massDelete(Request $request)
    {
        $formattedTime = Carbon::now()->diffForHumans();

        try {
            \DB::beginTransaction();

            $ids = $request->ids;
            $satuan = Satuan::whereIn('id', $ids)->get();

            foreach ($satuan as $satuan) {
               
            $deleteData = $satuan->getOriginal();
                $satuan->delete();

                 // ===== AUDIT TRAIL =====
        $agent = new Agent();

            activity()
                ->useLog('massdelete satuan')
                ->causedBy(Auth::user()) 
                ->performedOn($satuan)
                ->withProperties([
                    'ip' => $request->ip(),
                    'agent' => [
                        'browser' => $agent->browser() . ' ' . $agent->version($agent->browser()),
                        'os'      => $agent->platform() . ' ' . $agent->version($agent->platform()),
                        'device'  => $agent->device(),
                        'is_mobile' => $agent->isMobile(),
                        'is_desktop' => $agent->isDesktop(),
                        'raw' => $request->header('User-Agent'),
                    ],
                    'request' => [
                        'method' => $request->method(),
                        'url'    => $request->fullUrl(),
                    ],
                    'delete' => $deleteData,
                ])
                ->log('Menghapus Data Satuan');

            }

            \DB::commit();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'error' => 'Gagal menghapus brand: ' . $e->getMessage(),
                'time'  => $formattedTime,
                'judul' => 'Gagal',
            ]);
        }
    }
    /////////////////////// END MASS DELETE ///////////////////////

    /////////////////////// BEGIN SELECT2 ///////////////////////
    public function select(Request $request)
    {
        $satuan = [];

        if ($request->has('q')) {
            $search = $request->q;

            $satuan = Satuan::select("id", "nama")
                ->where("nama", "LIKE", "%{$search}%")
                ->limit(10)
                ->get();
        } else {
            $satuan = Satuan::select("id", "nama")
                ->limit(10)
                ->get();
        }

        return response()->json($satuan);
    }
    /////////////////////// END SELECT2 ///////////////////////
}
