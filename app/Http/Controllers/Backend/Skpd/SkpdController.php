<?php

namespace App\Http\Controllers\Backend\Skpd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Skpd;
use DB;
use Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Auth;
use Jenssegers\Agent\Agent;
// use DataTables;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Activitylog\Models\Activity;
use App\Events\StatusTenantUpdated;


class SkpdController extends Controller
{


    function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('permission:skpd.list', ['only' => ['index', 'getSkpd']]);
        $this->middleware('permission:skpd.show', ['only' => ['show']]);
        $this->middleware('permission:skpd.create', ['only' => ['store']]);
        $this->middleware('permission:skpd.edit', ['only' => ['edit', 'update', 'batchJamOperasional']]);
        $this->middleware('permission:skpd.delete', ['only' => ['destroy']]);
        $this->middleware('permission:skpd.massdelete', ['only' => ['massDelete']]);
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        $roles = Role::orderBy('id', 'desc')
            ->get();


        return view('backend.skpd.index', compact('roles'));
    }



    public function getSkpd(Request $request)
    {
        $query = Skpd::query()->orderByDesc('created_at');
        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('nama_skpd', 'like', "%{$search}%")->orWhere('kepala_skpd', 'like', "%{$search}%")->orWhere('nip_kepala', 'like', "%{$search}%")->orWhere('lokasi', 'like', "%{$search}%");
            });
        }
        return DataTables::of($query)
            ->addColumn('nama_skpd', function ($r) {
                return $r->nama_skpd;
            })
            ->addColumn('kepala_skpd', function ($r) {
                return $r->kepala_skpd ?? '-';
            })
            ->addColumn('nip_kepala', function ($r) {
                return $r->nip_kepala ?? '-';
            })
            ->addColumn('no_urutan', function ($r) {
                return $r->no_urutan ?? '-';
            })
            ->addColumn('isaktif', function ($r) {
                return $r->isaktif
                    ? '<span class="badge badge-light-success">Aktif</span>'
                    : '<span class="badge badge-light-danger">Nonaktif</span>';
            })
            ->addColumn('action', function ($row) {

                if (
                    !auth()->user()->can('skpd.show') &&
                    !auth()->user()->can('skpd.edit') &&
                    !auth()->user()->can('skpd.delete')
                ) {
                    return '-';
                }

                $html = '
                <div class="text-center">
                    <button class="btn btn-sm btn-light btn-active-light-primary"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ki-outline ki-dots-vertical fs-3"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end fs-7">';

                // 🔍 Detail
                if (auth()->user()->can('skpd.show')) {
                    $html .= '
                        <li>
                            <a href="javascript:void(0)"
                               class="dropdown-item d-flex align-items-center"
                               id="getShowRowData"
                               data-id="' . $row->id . '">
                                <i class="ki-outline ki-eye fs-5 me-2 text-info"></i>
                                Detail
                            </a>
                        </li>';
                }

                // ✏️ Edit
                if (auth()->user()->can('skpd.edit')) {
                    $html .= '
                        <li>
                            <a href="javascript:void(0)"
                               class="dropdown-item d-flex align-items-center"
                               id="getEditRowData"
                               data-id="' . $row->id . '">
                                <i class="ki-outline ki-pencil fs-5 me-2 text-warning"></i>
                                Edit
                            </a>
                        </li>';
                }

                // 🗑 Hapus
                if (auth()->user()->can('skpd.delete')) {
                    $html .= '
                        <li>
                            <a href="javascript:void(0)"
                               class="dropdown-item d-flex align-items-center"
                               data-id="' . $row->id . '"
                               data-bs-toggle="modal"
                               data-bs-target="#Modal_Hapus_Data"
                               id="getDeleteId">
                                <i class="ki-outline ki-trash fs-5 me-2 text-danger"></i>
                                Hapus
                            </a>
                        </li>';
                }

                $html .= '
                    </ul>
                </div>';

                return $html;
            })

            ->rawColumns(['isaktif', 'action'])
            ->make(true);
    }






    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function store(Request $request)
    {
        $formattedTime = Carbon::now()->diffForHumans();

        $validator = \Validator::make($request->all(), [
            //  'kode_skpd'   => 'required|string|max:50|unique:skpd,kode_skpd',
            'nama_skpd'   => 'required|string|max:255',
            'lokasi'      => 'required|string|max:255',
            'kepala_skpd' => 'nullable|string|max:255',
            'nip_kepala'  => 'nullable|string|max:25',
            'isaktif'    => 'required|in:0,1',
            'logo_skpd' => 'nullable|mimes:jpg,png,svg|max:2048',
            'external_id_sukma' => 'nullable|numeric',
            'buka_senin_kamis' => 'required',
            'tutup_senin_kamis' => 'required',
            'buka_jumat' => 'required',
            'tutup_jumat' => 'required',
            'kuota_harian' => 'required|numeric',
        ], [

            //  'kode_skpd.required' => 'Kode SKPD wajib diisi',
            //  'kode_skpd.unique'   => 'Kode SKPD sudah digunakan',
            //  'kode_skpd.max'      => 'Kode SKPD maksimal 50 karakter',

            'nama_skpd.required' => 'Nama SKPD wajib diisi',
            'nama_skpd.max'      => 'Nama SKPD maksimal 255 karakter',
            'lokasi.required'    => 'Lokasi SKPD wajib diisi',
            'lokasi.max'         => 'Nama SKPD maksimal 255 karakter',

            'kepala_skpd.max'    => 'Nama Kepala SKPD maksimal 255 karakter',
            'nip_kepala.max'     => 'NIP Kepala maksimal 25 karakter',

            'logo_skpd.mimes' => 'Logo_skpd harus format .jpg .png .svg',
            'logo_skpd.max' => 'Ukuran file Logo_skpd maksimal 2 MB',

            'isaktif.required'  => 'Status wajib dipilih',
            'isaktif.in'        => 'Status tidak valid',
            'external_id_sukma.numeric' => 'ID Sukma harus berupa angka',

            'buka_senin_kamis.required' => 'Jam Buka Pelayanan Untuk Hari Senin - Kamis Wajib Diisi',
            'tutup_senin_kamis.required' => 'Jam Tutup Pelayanan Untuk Hari Senin - Kamis Wajib Diisi',
            'buka_jumat.required' => 'Jam Buka Pelayanan Untuk Hari Jumat Wajib Diisi',
            'tutup_jumat.required' => 'Jam Tutup Pelayanan Untuk Hari Jumat Wajib Diisi',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        try {
            \DB::beginTransaction();

            $data = new Skpd;
            if ($request->hasFile('logo_skpd')) {
                $file      = $request->file('logo_skpd');
                $extension = $file->getClientOriginalExtension();

                $filename = 'logo_skpd-' . $data->id . '-' . time() . '.' . $extension;

                Storage::disk('public')->putFileAs(
                    'user/logo_skpd/',
                    $file,
                    $filename
                );

                $data->logo_skpd = $filename;
            }
            $data->id          = \Ramsey\Uuid\Uuid::uuid4();
            //  $data->kode_skpd   = $request->kode_skpd;
            $data->nama_skpd   = $request->nama_skpd;
            $data->lokasi      = $request->lokasi;
            $data->kepala_skpd = $request->kepala_skpd;
            $data->nip_kepala  = $request->nip_kepala;
            $data->isaktif    = $request->isaktif;
            $data->external_id_sukma = $request->external_id_sukma;
            $data->buka_senin_kamis = $request->buka_senin_kamis;
            $data->tutup_senin_kamis = $request->tutup_senin_kamis;
            $data->buka_jumat = $request->buka_jumat;
            $data->tutup_jumat = $request->tutup_jumat;
            $data->kuota_harian = $request->kuota_harian;
            $data->is_force_close = $request->has('is_force_close') ? 1 : 0;
            $data->save();

            // ===============================
            // FULL NEW SNAPSHOT (AUDIT)
            // ===============================
            $newData = $data->toArray();
            $agent = new \Jenssegers\Agent\Agent;

            activity()
                ->useLog('Tambah Skpd')
                ->causedBy(auth()->user())
                ->performedOn($data)
                ->withProperties([
                    'ip' => $request->ip(),
                    'agent' => [
                        'browser'     => $agent->browser() . ' ' . $agent->version($agent->browser()),
                        'os'          => $agent->platform() . ' ' . $agent->version($agent->platform()),
                        'device'      => $agent->device(),
                        'is_mobile'   => $agent->isMobile(),
                        'is_desktop'  => $agent->isDesktop(),
                        'raw'         => $request->header('User-Agent'),
                    ],
                    'request' => [
                        'method' => $request->method(),
                        'url'    => $request->fullUrl(),
                    ],
                    'new' => $newData,
                ])
                ->log('Membuat data SKPD ' . $data->nama_skpd);

            \DB::commit();

            return response()->json([
                'success' => 'Data SKPD berhasil disimpan.',
                'time'    => $formattedTime,
                'judul'   => 'Berhasil'
            ], 201);
        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json([
                'error'        => 'Terjadi kesalahan di aplikasi, hubungi Developer.',
                'time'         => $formattedTime,
                'judul'        => 'Aplikasi Error',
                'errorMessage' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Menemukan user berdasarkan id
        $data = Skpd::findOrFail($id);

        // Render view ke dalam string HTML (bukan return view langsung)
        $html = view('backend.skpd.show', compact('data'))->render();

        return response()->json(['html' => $html]);
    }


    public function getLoginSession(Request $request, $id)
    {
        $postsQuery = Activity::with('causer')
            ->where('causer_id', $id)
            ->whereIn('log_name', ['login', 'logout'])
            ->orderBy('id', 'desc');

        $data = $postsQuery->get();


        return \DataTables::of($data)

            // =============================
            // CREATED_AT
            // =============================
            ->addColumn('created_at', function ($data) {
                if (empty($data->created_at)) {
                    return '<div class="text-end"><label class="badge badge-warning">Belum Pernah Login</label></div>';
                }
                return '<div class="text-end"><label class="badge badge-info">'
                    . $data->created_at->diffForHumans() .
                    '</label></div>';
            })

            // =============================
            // DESCRIPTION
            // =============================
            ->addColumn('description', function ($data) {
                return $data->description ?? '-';
            })

            // =============================
            // IP + AGENT (AMAN)
            // =============================
            ->addColumn('ip', function ($data) {
                $ip     = $data->properties['ip'] ?? '-';
                $browser = $data->properties['agent']['browser'] ?? '-';

                //return $ip . ' | ' . $browser;
                return $ip;
            })

            // =============================
            // OS + BROWSER (AMAN)
            // =============================
            ->addColumn('os', function ($data) {
                $os      = $data->properties['agent']['os'] ?? '-';
                $browser = $data->properties['agent']['browser'] ?? '-';

                return $os . ' - ' . $browser;
            })

            // =============================
            // DEVICE (AMAN)
            // =============================
            ->addColumn('device', function ($data) {
                $agent = $data->properties['agent'] ?? [];

                $isDesktop = $agent['is_desktop'] ?? false;
                $isMobile  = $agent['is_mobile'] ?? false;
                $deviceRaw = $agent['device'] ?? 'Unknown';

                if ($isDesktop) {
                    return '<i class="ki-outline ki-screen text-primary me-2"></i>Desktop';
                }

                if ($isMobile) {
                    return '<i class="ki-outline ki-phone text-warning me-2"></i>Mobile';
                }

                // Jika bukan desktop dan bukan mobile → unknown
                return '<i class="ki-outline ki-question-2 text-danger me-2"></i>' . $deviceRaw;
            })


            ->rawColumns(['created_at', 'description', 'ip', 'os', 'device'])
            ->make(true);
    }

    public function batchJamOperasional(Request $request)
    {
        $mode = $request->mode;

        // Tentukan Jam Sesuai Mode
        if ($mode === 'ramadan') {
            $buka_sk = '08:00:00';
            $tutup_sk = '15:00:00';
            $buka_jumat = '08:00:00';
            $tutup_jumat = '15:30:00';
        } else {
            // Mode Normal (Standar Jam Kerja ASN)
            // Silakan sesuaikan jika jam tutup normalnya berbeda
            $buka_sk = '08:00:00';
            $tutup_sk = '16:00:00';
            $buka_jumat = '08:00:00';
            $tutup_jumat = '16:30:00';
        }

        try {
            \DB::beginTransaction();

            // Ubah seluruh jam operasional di tabel SKPD
            Skpd::query()->update([
                'buka_senin_kamis' => $buka_sk,
                'tutup_senin_kamis' => $tutup_sk,
                'buka_jumat' => $buka_jumat,
                'tutup_jumat' => $tutup_jumat,
            ]);

            // Log Activity
            activity()
                ->useLog('Ubah Masal Jam Layanan')
                ->causedBy(Auth::user())
                ->log("Mengubah jam pelayanan seluruh tenant ke Mode " . ucfirst($mode));

            \DB::commit();

            // Panggil Event WebSocket agar layar kios langsung refresh
            try {
                event(new StatusTenantUpdated());
            } catch (\Exception $e) {
            }

            return response()->json([
                'success' => 'Jam operasional seluruh tenant berhasil diubah ke Mode ' . ucfirst($mode) . '!'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'error' => 'Gagal mengubah jam operasional',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getActivity(Request $request, $id)
    {

        $postsQuery = Activity::with('causer')
            ->where('causer_id', $id)
            ->whereNotIn('log_name', ['login', 'logout']) // Tambahkan klausa whereNotIn untuk log_name
            ->orderBy('id', 'desc');

        $data = $postsQuery->get();

        return \DataTables::of($data)

            // =============================
            // CREATED_AT
            // =============================
            ->addColumn('created_at', function ($data) {
                if (empty($data->created_at)) {
                    return '<div class="text-end"><label class="badge badge-warning">Belum Pernah Login</label></div>';
                }
                return '<div class="text-end"><label class="badge badge-info">'
                    . $data->created_at->diffForHumans() .
                    '</label></div>';
            })

            // =============================
            // DESCRIPTION
            // =============================
            ->addColumn('description', function ($data) {
                return $data->description ?? '-';
            })

            // =============================
            // IP + AGENT (AMAN)
            // =============================
            ->addColumn('ip', function ($data) {
                $ip     = $data->properties['ip'] ?? '-';
                $browser = $data->properties['agent']['browser'] ?? '-';

                //return $ip . ' | ' . $browser;
                return $ip;
            })

            // =============================
            // OS + BROWSER (AMAN)
            // =============================
            ->addColumn('os', function ($data) {
                $os      = $data->properties['agent']['os'] ?? '-';
                $browser = $data->properties['agent']['browser'] ?? '-';

                return $os . ' - ' . $browser;
            })

            // =============================
            // DEVICE (AMAN)
            // =============================
            ->addColumn('device', function ($data) {
                $agent = $data->properties['agent'] ?? [];

                $isDesktop = $agent['is_desktop'] ?? false;
                $isMobile  = $agent['is_mobile'] ?? false;
                $deviceRaw = $agent['device'] ?? 'Unknown';

                if ($isDesktop) {
                    return '<i class="ki-outline ki-screen text-primary me-2"></i>Desktop';
                }

                if ($isMobile) {
                    return '<i class="ki-outline ki-phone text-warning me-2"></i>Mobile';
                }

                // Jika bukan desktop dan bukan mobile → unknown
                return '<i class="ki-outline ki-question-2 text-danger me-2"></i>' . $deviceRaw;
            })


            ->rawColumns(['created_at', 'description', 'ip', 'os', 'device'])
            ->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Skpd::findOrFail($id);

        $html = view('backend.skpd.edit', [
            'user' => $user,
        ])->render();

        return response()->json(['html' => $html]);
    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $formattedTime = Carbon::now()->diffForHumans();

        $validator = \Validator::make($request->all(), [
            'nama_skpd'    => 'required|string|max:255',
            'lokasi'       => 'required|string|max:255',
            'kepala_skpd'  => 'nullable|string|max:255',
            'nip_kepala'   => 'nullable|string|max:25',
            'logo_skpd' => 'nullable|mimes:jpg,png,svg|max:2048',
            'isaktif'      => 'required|in:0,1',
            'external_id_sukma' => 'nullable|numeric',
            'buka_senin_kamis' => 'required',
            'tutup_senin_kamis' => 'required',
            'buka_jumat' => 'required',
            'tutup_jumat' => 'required',
            'kuota_harian' => 'required|numeric',
        ], [
            'nama_skpd.required' => 'Nama SKPD wajib diisi',
            'nama_skpd.max'      => 'Nama SKPD maksimal 255 karakter',
            'lokasi.required'    => 'Lokasi SKPD wajib diisi',
            'lokasi.max'         => 'Nama SKPD maksimal 255 karakter',

            'kepala_skpd.max'    => 'Nama Kepala SKPD maksimal 255 karakter',
            'nip_kepala.max'     => 'NIP Kepala maksimal 25 karakter',
            'logo_skpd.mimes' => 'Logo_skpd harus format .jpg .png .svg',
            'logo_skpd.max' => 'Ukuran file Logo_skpd maksimal 2 MB',
            'isaktif.required'   => 'Status wajib dipilih',
            'isaktif.in'         => 'Status tidak valid',
            'buka_senin_kamis.required' => 'Jam Buka Pelayanan Untuk Hari Senin - Kamis Wajib Diisi',
            'tutup_senin_kamis.required' => 'Jam Tutup Pelayanan Untuk Hari Senin - Kamis Wajib Diisi',
            'buka_jumat.required' => 'Jam Buka Pelayanan Untuk Hari Jumat Wajib Diisi',
            'tutup_jumat.required' => 'Jam Tutup Pelayanan Untuk Hari Jumat Wajib Diisi',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        try {
            \DB::beginTransaction();

            $data = Skpd::findOrFail($id);
            $oldData = $data->toArray();

            if ($request->hasFile('logo_skpd')) {

                // Hapus file lama
                if ($data->logo_skpd && Storage::disk('public')->exists('user/logo_skpd/' . $data->logo_skpd)) {
                    Storage::disk('public')->delete('user/logo_skpd/' . $data->logo_skpd);
                }

                $file = $request->file('logo_skpd');
                $extension = $file->getClientOriginalExtension();

                // Nama file aman & standar
                $filename = 'logo_skpd-' . $data->id . '-' . time() . '.' . $extension;

                // Simpan file
                Storage::disk('public')->putFileAs(
                    'user/logo_skpd/',
                    $file,
                    $filename
                );

                $data->logo_skpd = $filename;
            }
            // ===============================
            // UPDATE DATA SKPD
            // ===============================
            $data->nama_skpd   = $request->nama_skpd;
            $data->lokasi      = $request->lokasi;
            $data->kepala_skpd = $request->kepala_skpd;
            $data->nip_kepala  = $request->nip_kepala;
            $data->isaktif     = $request->isaktif;
            $data->external_id_sukma = $request->external_id_sukma;

            // Kolom baru yang tertinggal sebelumnya:
            $data->buka_senin_kamis = $request->buka_senin_kamis;
            $data->tutup_senin_kamis = $request->tutup_senin_kamis;
            $data->buka_jumat = $request->buka_jumat;
            $data->tutup_jumat = $request->tutup_jumat;
            $data->kuota_harian = $request->kuota_harian;
            $data->is_force_close = $request->has('is_force_close') ? 1 : 0;
            $data->save();

            // ===============================
            // FULL NEW SNAPSHOT
            // ===============================
            $newData = $data->toArray();

            // ===============================
            // LOG ACTIVITY (AUDIT FULL)
            // ===============================
            $agent = new \Jenssegers\Agent\Agent;

            activity()
                ->useLog('edit skpd')
                ->causedBy(Auth::user())
                ->performedOn($data)
                ->withProperties([
                    'ip' => $request->ip(),
                    'agent' => [
                        'browser'     => $agent->browser() . ' ' . $agent->version($agent->browser()),
                        'os'          => $agent->platform() . ' ' . $agent->version($agent->platform()),
                        'device'      => $agent->device(),
                        'is_mobile'   => $agent->isMobile(),
                        'is_desktop'  => $agent->isDesktop(),
                        'raw'         => $request->header('User-Agent'),
                    ],
                    'request' => [
                        'method' => $request->method(),
                        'url'    => $request->fullUrl(),
                    ],
                    'old' => $oldData,
                    'new' => $newData,
                ])
                ->log('Mengubah data SKPD ' . $data->nama_skpd);

            \DB::commit();

            // ==========================================
            // TAMBAHKAN TRIGGER EVENT WEBSOCKET DI SINI
            // ==========================================
            try {
                event(new StatusTenantUpdated());
            } catch (\Exception $e) {
                // Biarkan kosong agar jika websocket mati, fungsi edit tetap jalan
            }
            // ==========================================

            return response()->json([
                'success' => 'Data SKPD berhasil diperbaharui.',
                'time'    => $formattedTime,
                'judul'   => 'Berhasil',
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json([
                'error'        => 'Terjadi kesalahan di aplikasi, hubungi Developer.',
                'time'         => $formattedTime,
                'judul'        => 'Aplikasi Error',
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $formattedTime = Carbon::now()->diffForHumans();

        try {
            DB::beginTransaction();

            $data = Skpd::findOrFail($id);
            $getData = $data->toArray();

            $data->delete();

            DB::commit();

            // LOG ACTIVITY
            $agent = new \Jenssegers\Agent\Agent;

            activity()
                ->useLog('hapus skpd')
                ->causedBy(Auth::user())
                ->performedOn($data)
                ->withProperties([
                    'ip' => $request->ip(),
                    'agent' => [
                        'browser' => $agent->browser(),
                        'os'      => $agent->platform(),
                        'device'  => $agent->device(),
                    ],
                    'get' => $getData,
                ])
                ->log('Menghapus data SKPD ' . $getData['nama_skpd']);

            return response()->json([
                'success' => 'Data SKPD berhasil dihapus',
                'time' => $formattedTime,
                'judul' => 'Berhasil'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'error' => 'Data gagal dihapus',
                'errorMessage' => $e->getMessage()
            ], 500);
        }
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
                    'message' => 'No SKPD selected for deletion.'
                ]);
            }

            // Ambil data SKPD untuk logging sebelum dihapus
            $skpd = Skpd::whereIn('id', $ids)->get();

            // Hapus data SKPD
            Skpd::whereIn('id', $ids)->delete();

            DB::commit();

            $agent = new Agent();

            // ===============================
            // LOG ACTIVITY (AUDIT FULL)
            // ===============================
            foreach ($skpd as $skpd) {
                activity()
                    ->useLog('massdelete skpd')
                    ->causedBy(Auth::user())
                    ->performedOn($skpd)
                    ->withProperties([
                        'ip' => $request->ip(),
                        'agent' => [
                            'browser'     => $agent->browser() . ' ' . $agent->version($agent->browser()),
                            'os'          => $agent->platform() . ' ' . $agent->version($agent->platform()),
                            'device'      => $agent->device(),
                            'is_mobile'   => $agent->isMobile(),
                            'is_desktop'  => $agent->isDesktop(),
                            'raw'         => $request->header('User-Agent'),
                        ],
                        'request' => [
                            'method' => $request->method(),
                            'url'    => $request->fullUrl(),
                        ],
                        'data' => $skpd->toArray(),
                    ])
                    ->log('Menghapus data SKPD: ' . $skpd->nama_skpd);
            }

            return response()->json([
                'status'  => 'success',
                'message' => count($ids) . ' SKPD berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'        => 'error',
                'judul'         => 'Gagal',
                'message'       => 'Data gagal dihapus',
                'time'          => $formattedTime,
                'errorMessage'  => $e->getMessage()
            ]);
        }
    }

    public function ban(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'duration' => 'required|in:permanent,1h,24h,1w',
        ]);

        $user = User::findOrFail($id);

        // Cegah ban diri sendiri
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'Anda tidak dapat memban akun Anda sendiri.'], 422);
        }

        // Cegah ban ganda
        if ($user->isBanned()) {
            return response()->json(['error' => 'User sudah diban sebelumnya.'], 422);
        }

        // Durasi ban
        $expired = match ($request->duration) {
            '1h' => now()->addHour(),
            '24h' => now()->addDay(),
            '1w' => now()->addWeek(),
            default => null, // permanent
        };

        // Ban user
        $user->ban([
            'comment' => $request->reason,
            'expired_at' => $expired
        ]);

        // Log activity
        activity('ban user')
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties([
                'reason'     => $request->reason,
                'duration'   => $request->duration,
                'expired_at' => $expired,
                'ip'         => $request->ip(),
            ])
            ->log('Membanned user: ' . $user->name);

        return response()->json(['success' => 'User berhasil diban']);
    }


    public function unban(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Cegah unban jika user tidak dibanned
        if ($user->isNotBanned()) {
            return response()->json(['error' => 'User tidak dalam status banned.'], 422);
        }

        $user->unban();

        activity('unban user')
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties([
                'ip' => $request->ip()
            ])
            ->log('Mengaktifkan kembali user: ' . $user->name);

        return response()->json(['success' => 'User berhasil diaktifkan kembali']);
    }

    public function syncSukma()
    {
        try {
            // 1. Tembak API Sukma Deli (Bypass SSL verify jika perlu)
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->get('https://sukmadeli.deliserdangkab.go.id/api/v1/opd');

            if ($response->failed()) {
                return response()->json(['error' => 'Gagal menghubungi server Sukma Deli'], 500);
            }

            $dataApi = $response->json();
            $listOpd = $dataApi['opd_list'] ?? [];
            $count   = 0;

            \DB::beginTransaction();

            foreach ($listOpd as $item) {
                // LOGIKA UTAMA: UPDATE OR CREATE
                // Sistem akan mencari SKPD berdasarkan 'external_id_sukma'.
                // - Jika KETEMU: Update nama_skpd-nya (biar sinkron kalau ada perubahan nama).
                // - Jika TIDAK KETEMU: Buat data baru (UUID otomatis ter-generate oleh Model).

                Skpd::updateOrCreate(
                    [
                        'external_id_sukma' => $item['id'] // Kunci Pencarian (ID Sukma)
                    ],
                    [
                        'nama_skpd' => $item['opd'],       // Data yang diupdate/disimpan
                        'isaktif'   => 1,                  // Default aktif jika baru dibuat
                        // Field lain biarkan default/null
                    ]
                );
                $count++;
            }

            \DB::commit();

            return response()->json([
                'success' => 'Sinkronisasi Berhasil!',
                'message' => "Berhasil memproses {$count} data OPD dari Sukma Deli."
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'error' => 'Terjadi kesalahan sistem',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
