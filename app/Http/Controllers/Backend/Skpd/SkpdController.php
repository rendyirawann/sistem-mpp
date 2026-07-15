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
        $this->middleware('permission:skpd.edit', ['only' => ['edit', 'update', 'batchJamOperasional', 'batchStatusAll']]);
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
        $query = Skpd::query();

        return DataTables::of($query)
            ->addColumn('isaktif', function ($r) {
                $status = $r->isaktif
                    ? '<span class="badge badge-light-success">Aktif</span>'
                    : '<span class="badge badge-light-danger">Nonaktif</span>';

                if ($r->is_force_close) {
                    $status .= '<br><span class="badge badge-light-danger mt-1">Force Closed</span>';
                }

                return $status;
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

                // 📅 Kalender Kuota Antrian
                $html .= '
                    <li>
                        <a href="' . url('kalender-antrian/' . $row->id) . '"
                           class="dropdown-item d-flex align-items-center">
                            <i class="ki-outline ki-calendar fs-5 me-2 text-primary"></i>
                            Kalender Kuota
                        </a>
                    </li>';

                // 🗑 Hapus — disembunyikan jika instansi sudah punya data antrian
                if (auth()->user()->can('skpd.delete') && !$row->hasAntrianData()) {
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
            'buka_sabtu' => 'required',
            'tutup_sabtu' => 'required',
            'kuota_harian' => 'required|numeric',
            'kuota_online' => 'nullable|numeric|min:0',
            'kuota_kiosk' => 'nullable|numeric|min:0',
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
            $data->kuota_online = $request->kuota_online ?? 40;
            $data->kuota_kiosk = $request->kuota_kiosk ?? 60;
            $data->is_force_close = $request->has('is_force_close') ? 1 : 0;
            $data->is_sabtu_buka = $request->has('is_sabtu_buka') ? 1 : 0;
            $data->is_antrianonline = $request->has('is_antrianonline') ? 1 : 0;
            $data->buka_sabtu = $request->buka_sabtu;
            $data->tutup_sabtu = $request->tutup_sabtu;
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
        // Admin menentukan jam sendiri untuk SELURUH instansi (batch)
        $validator = \Validator::make($request->all(), [
            'buka_senin_kamis'  => 'required|date_format:H:i',
            'tutup_senin_kamis' => 'required|date_format:H:i|after:buka_senin_kamis',
            'buka_jumat'        => 'required|date_format:H:i',
            'tutup_jumat'       => 'required|date_format:H:i|after:buka_jumat',
            'buka_sabtu'        => 'required|date_format:H:i',
            'tutup_sabtu'       => 'required|date_format:H:i|after:buka_sabtu',
        ], [
            'buka_senin_kamis.required'  => 'Jam buka Senin–Kamis wajib diisi',
            'tutup_senin_kamis.required' => 'Jam tutup Senin–Kamis wajib diisi',
            'tutup_senin_kamis.after'    => 'Jam tutup Senin–Kamis harus lebih besar dari jam buka',
            'buka_jumat.required'        => 'Jam buka Jumat wajib diisi',
            'tutup_jumat.required'       => 'Jam tutup Jumat wajib diisi',
            'tutup_jumat.after'          => 'Jam tutup Jumat harus lebih besar dari jam buka',
            'buka_sabtu.required'        => 'Jam buka Sabtu wajib diisi',
            'tutup_sabtu.required'       => 'Jam tutup Sabtu wajib diisi',
            'tutup_sabtu.after'          => 'Jam tutup Sabtu harus lebih besar dari jam buka',
            '*.date_format'              => 'Format jam harus HH:MM',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Normalisasi HH:MM -> HH:MM:SS agar konsisten dengan data lama
        $fmt = fn ($t) => strlen(trim((string) $t)) === 5 ? trim($t) . ':00' : trim((string) $t);

        $jam = [
            'buka_senin_kamis'  => $fmt($request->buka_senin_kamis),
            'tutup_senin_kamis' => $fmt($request->tutup_senin_kamis),
            'buka_jumat'        => $fmt($request->buka_jumat),
            'tutup_jumat'       => $fmt($request->tutup_jumat),
            'buka_sabtu'        => $fmt($request->buka_sabtu),
            'tutup_sabtu'       => $fmt($request->tutup_sabtu),
        ];

        try {
            \DB::beginTransaction();

            // Ubah seluruh jam operasional di tabel SKPD
            Skpd::query()->update($jam);

            // Log Activity
            activity()
                ->useLog('Ubah Masal Jam Layanan')
                ->causedBy(Auth::user())
                ->withProperties(['jam' => $jam])
                ->log('Mengatur jam pelayanan seluruh tenant secara massal');

            \DB::commit();

            // Panggil Event WebSocket agar layar kios langsung refresh
            try {
                event(new StatusTenantUpdated());
            } catch (\Exception $e) {
            }

            return response()->json([
                'success' => 'Jam operasional seluruh tenant berhasil diperbarui!'
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
            'buka_sabtu' => 'required',
            'tutup_sabtu' => 'required',
            'kuota_harian' => 'required|numeric',
            'kuota_online' => 'nullable|numeric|min:0',
            'kuota_kiosk' => 'nullable|numeric|min:0',
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

            // 🔒 Nama instansi terkunci jika sudah punya data antrian (semua role)
            if ($data->hasAntrianData() && $request->nama_skpd !== $data->nama_skpd) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Nama instansi tidak dapat diubah karena sudah memiliki data antrian. Anda hanya dapat menonaktifkannya.',
                    'judul' => 'Tidak Diizinkan',
                ], 422);
            }

            // 🔒 Kuota baru meng-override SEMUA tanggal ke depan, KECUALI tanggal yang
            // sudah ada antrean -> dibekukan dgn kuota lama (tulis override di kalender).
            $oldOnline = (int) ($oldData['kuota_online'] ?? 40);
            $oldKiosk  = (int) ($oldData['kuota_kiosk'] ?? 60);
            $newOnline = (int) ($request->kuota_online ?? 40);
            $newKiosk  = (int) ($request->kuota_kiosk ?? 60);
            if ($newOnline !== $oldOnline || $newKiosk !== $oldKiosk) {
                $tglBerantrean = \App\Models\Antrian::where('skpd_id', $id)
                    ->whereDate('tanggal', '>=', Carbon::today()->toDateString())
                    ->select('tanggal')->distinct()->pluck('tanggal');
                foreach ($tglBerantrean as $t) {
                    \App\Models\KuotaTanggal::firstOrCreate(
                        ['skpd_id' => $id, 'tanggal' => Carbon::parse($t)->toDateString()],
                        ['kuota_online' => $oldOnline, 'kuota_kiosk' => $oldKiosk]
                    );
                }
            }

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
            $data->kuota_online = $request->kuota_online ?? 40;
            $data->kuota_kiosk = $request->kuota_kiosk ?? 60;
            $data->is_force_close = $request->has('is_force_close') ? 1 : 0;
            $data->is_sabtu_buka = $request->has('is_sabtu_buka') ? 1 : 0;
            $data->is_antrianonline = $request->has('is_antrianonline') ? 1 : 0;
            $data->buka_sabtu = $request->buka_sabtu;
            $data->tutup_sabtu = $request->tutup_sabtu;
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

            // 🔒 Tidak boleh dihapus jika sudah punya data antrian (semua role)
            if ($data->hasAntrianData()) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Instansi tidak dapat dihapus karena sudah memiliki data antrian. Anda hanya dapat menonaktifkannya.',
                    'judul' => 'Tidak Diizinkan',
                ], 422);
            }

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

            // 🔒 Lindungi instansi yang sudah punya data antrian
            $allSkpd   = Skpd::whereIn('id', $ids)->get();
            $deletable = $allSkpd->reject(fn ($s) => $s->hasAntrianData());
            $protected = $allSkpd->count() - $deletable->count();

            if ($deletable->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Instansi terpilih sudah memiliki data antrian, tidak dapat dihapus. Hanya bisa dinonaktifkan.'
                ]);
            }

            // Data SKPD untuk logging (hanya yang akan dihapus)
            $skpd = $deletable;

            // Hapus data SKPD (hanya yang tidak punya data antrian)
            Skpd::whereIn('id', $deletable->pluck('id')->all())->delete();

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
                'message' => $deletable->count() . ' SKPD berhasil dihapus'
                    . ($protected > 0 ? ', ' . $protected . ' dilindungi karena sudah punya data antrian' : '')
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

    public function batchStatusAll(Request $request)
    {
        $status = $request->status; // 1 = Tutup, 0 = Buka

        try {
            DB::beginTransaction();

            Skpd::query()->update(['is_force_close' => $status]);

            // Log Activity
            $label = $status == 1 ? 'MENUTUP' : 'MEMBUKA';
            activity()
                ->useLog('Ubah Masal Status Layanan')
                ->causedBy(Auth::user())
                ->log("{$label} SELURUH layanan tenant sekaligus.");

            DB::commit();

            try {
                event(new StatusTenantUpdated());
            } catch (\Exception $e) {
            }

            return response()->json([
                'success' => "Seluruh layanan tenant berhasil " . ($status == 1 ? 'ditutup' : 'dibuka') . "!"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Gagal mengubah status layanan',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
