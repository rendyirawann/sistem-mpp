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


class SkpdController extends Controller
{


    function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('permission:skpd.list', ['only' => ['index','getSkpd']]);
        $this->middleware('permission:skpd.show', ['only' => ['show']]);
        $this->middleware('permission:skpd.create', ['only' => ['store']]);
        $this->middleware('permission:skpd.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:skpd.delete', ['only' => ['destroy']]);
        $this->middleware('permission:skpd.massdelete', ['only' => ['massDelete']]);
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.skpd.index');
    }




    public function getSkpd(Request $request)
    {
        $query = Skpd::query()->orderByDesc('created_at');

        return DataTables::of($query)
            ->addIndexColumn()

            ->editColumn('kepala_skpd', fn ($r) => $r->kepala_skpd ?? '-')
            ->editColumn('nip_kepala', fn ($r) => $r->nip_kepala ?? '-')

            ->addColumn('status', function ($r) {
                return $r->isAktif
                    ? '<span class="badge badge-light-success">Aktif</span>'
                    : '<span class="badge badge-light-danger">Nonaktif</span>';
            })

            ->addColumn('action', function ($r) {
                return '
                    <div class="text-end">
                        <button class="btn btn-sm btn-warning" onclick="editSkpd(`'.$r->id.'`)">
                            <i class="ki-outline ki-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteSkpd(`'.$r->id.'`)">
                            <i class="ki-outline ki-trash"></i>
                        </button>
                    </div>
                ';
            })

            ->rawColumns(['status', 'action'])
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
            'name' => 'required|string|max:255',
            'no_wa' => 'required|string|max:20|min:10|unique:users,no_wa',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'required|mimes:jpg,png,svg|max:2048',
            'roles' => 'required',

        ], [

            'name.required' => 'Nama Lengkap wajib diisi',
            'name.max' => 'Nama Lengkap maksimal 255 karakter',
            'name.string'      => 'Nomor WhatsApp harus berupa teks.',

            'no_wa.required'    => 'Nomor WhatsApp wajib diisi.',
            'no_wa.string'      => 'Nomor WhatsApp harus berupa teks.',
            'no_wa.max'         => 'Nomor WhatsApp maksimal 20 karakter.',
            'no_wa.min'         => 'Nomor WhatsApp minimal 10 karakter.',
            'no_wa.unique'      => 'Nomor WhatsApp sudah digunakan oleh pengguna lain.',

            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format Email tidak valid',
            'email.unique' => 'Email sudah terdaftar',

            'password.required' => 'Password wajib diisi',
            'password.min' => 'Kata Sandi minimal 8 krakter',
            'password.confirmed' => 'Kata Sandi tidak sama',

            'avatar.required' => 'Avatar wajib diisi',
            'avatar.mimes' => 'Avatar harus format .jpg .png .svg',
            'avatar.max' => 'Ukuran file Avatar maksimal 2 MB',

            'roles.required' => 'Role wajib diisi',


        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }



        // Logika penyimpanan data
        try {
            \DB::beginTransaction();

            $data = new User;
            // ===============================
            // SIMPAN AVATAR (PAKAI POLA SAMA)
            // ===============================
            if ($request->hasFile('avatar')) {
                $file      = $request->file('avatar');
                $extension = $file->getClientOriginalExtension();

                $filename = 'avatar-' . $data->id . '-' . time() . '.' . $extension;

                Storage::disk('public')->putFileAs(
                    'user/avatar/',
                    $file,
                    $filename
                );

                $data->avatar = $filename;
            }

            $data->id = Uuid::uuid4();
            $data->name = $request->name;
            $data->no_wa = $request->no_wa;
            $data->email = $request->email;
            $data->password = Hash::make($request->password);
            $data->assignRole($request->input('roles'));

            $data->save();


            // ===============================
            // FULL NEW SNAPSHOT
            // ===============================
            $newData = $data->toArray();

            $agent = new Agent;

            activity()
                ->useLog('tambah skpd')
                ->causedBy(auth()->user())
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
                ->log('Membuat akun skpd ' . $data->name);

            \DB::commit();

            return response()->json([
                'success' => 'Data berhasil disimpan.',
                'time' => $formattedTime,
                'judul' => 'Berhasil'
            ], 201);
        } catch (\Exception $e) {
            \DB::rollback();
            $errorMessage = $e->getMessage(); // Mendapatkan pesan kesalahan dari Exception
            return response()->json([
                'error' => 'Terjadi kesalahan di aplikasi, hubungi Developer.',
                'time' => $formattedTime,
                'judul' => 'Aplikasi Error',
                'errorMessage' => $errorMessage
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
    public function show($id): View
    {
        // Menemukan user berdasarkan id
        $data = User::findOrFail($id);

        // Mengirim data ke view
        return view('backend.skpd.show', compact('data'));
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
        $user = User::findOrFail($id);

        // Kirim data ke view untuk di-render
        $html = view('backend.skpd.edit', [

            'user' => $user,
            'userRole' => $user->getRoleNames()->toArray(),
            'roles' => Role::where('guard_name', '=', 'web')->select(['id', 'name'])->get(),
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'confirmed',
            'avatar' => 'mimes:jpg,png,svg|max:2048',
            'roles' => 'required',
        ], [
            'name.required' => 'Nama Lengkap wajib diisi',
            'name.max' => 'Nama Lengkap maksimal 255 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format Email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.confirmed' => 'Kata Sandi tidak sama',
            'avatar.mimes' => 'Avatar harus format .jpg .png .svg',
            'avatar.max' => 'Ukuran file Avatar maksimal 2 MB',
            'roles.required' => 'Role wajib diisi',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        try {
            \DB::beginTransaction();

            $data = User::findOrFail($id);
            $oldData = $data->toArray();

            if ($request->hasFile('avatar')) {

                // Hapus file lama
                if ($data->avatar && Storage::disk('public')->exists('user/avatar/' . $data->avatar)) {
                    Storage::disk('public')->delete('user/avatar/' . $data->avatar);
                }

                $file = $request->file('avatar');
                $extension = $file->getClientOriginalExtension();

                // Nama file aman & standar
                $filename = 'avatar-' . $data->id . '-' . time() . '.' . $extension;

                // Simpan file
                Storage::disk('public')->putFileAs(
                    'user/avatar/',
                    $file,
                    $filename
                );

                $data->avatar = $filename;
            }

            $data->name = $request->name;
            $data->email = $request->email;

            if (!empty($request->password)) {
                $data->password = Hash::make($request->password);
            }

            $data->save();

            // Sync roles
            DB::table('model_has_roles')->where('model_id', $id)->delete();
            $data->assignRole($request->input('roles'));

            // ===============================
            // FULL NEW SNAPSHOT
            // ===============================
            $newData = $data->toArray();

            // ===============================
            // LOG ACTIVITY (AUDIT FULL)
            // ===============================
            $agent = new \Jenssegers\Agent\Agent;

            activity()
                ->useLog('edit user')
                ->causedBy(Auth::user())
                ->performedOn($data)
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

                    // === CATAT SEMUA DATA SEBELUM & SESUDAH ===
                    'old' => $oldData,
                    'new' => $newData,
                ])
                ->log('Mengubah akun user ' . $data->name);





            \DB::commit();

            return response()->json([
                'success' => 'Data berhasil diperbaharui.',
                'time' => $formattedTime,
                'judul' => 'Berhasil',
            ]);
        } catch (\Exception $e) {
            \DB::rollback();
            $errorMessage = $e->getMessage();
            return response()->json([
                'error' => 'Terjadi kesalahan di aplikasi, hubungi Developer.',
                'time' => $formattedTime,
                'judul' => 'Aplikasi Error',
                'errorMessage' => $errorMessage,
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
            \DB::beginTransaction();

            $data = User::findOrFail($id);
            $getData = $data->toArray();

            // ===============================
            // HAPUS AVATAR JIKA ADA
            // ===============================
            if ($data->avatar) {
                $avatarPath = 'user/avatar/' . $data->avatar;

                if (Storage::disk('public')->exists($avatarPath)) {
                    Storage::disk('public')->delete($avatarPath);
                }
            }

            // ===============================
            // HAPUS USER
            // ===============================
            $data->delete();

            \DB::commit();

            // ===============================
            // LOG ACTIVITY (AUDIT FULL)
            // ===============================
            $agent = new \Jenssegers\Agent\Agent;

            activity()
                ->useLog('hapus user')
                ->causedBy(Auth::user())
                ->performedOn($data)
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
                    'get' => $getData,
                ])
                ->log('Menghapus akun user ' . $getData['name']);

            return response()->json([
                'success' => 'Data berhasil dihapus',
                'time' => $formattedTime,
                'judul' => 'Berhasil'
            ]);
        } catch (\Exception $e) {

            \DB::rollback();

            return response()->json([
                'error'        => 'Data Gagal dihapus',
                'time'         => $formattedTime,
                'judul'        => 'Gagal',
                'errorMessage' => $e->getMessage()
            ]);
        }
    }


    public function massDelete(Request $request)
    {
        $formattedTime = Carbon::now()->diffForHumans();
        try {
            \DB::beginTransaction();

            $ids = $request->ids;

            if (!empty($ids)) {
                // Dapatkan data pengguna yang akan dihapus untuk logging
                $users = User::whereIn('id', $ids)->get();

                // Hapus avatar masing-masing
                foreach ($users as $user) {
                    if ($user->avatar) {

                        $avatarPath = 'user/avatar/' . $user->avatar;

                        if (Storage::disk('public')->exists($avatarPath)) {
                            Storage::disk('public')->delete($avatarPath);
                        }
                    }
                }

                // Hapus pengguna
                User::whereIn('id', $ids)->delete();

                \DB::commit();

                $agent = new \Jenssegers\Agent\Agent;

                // Log activity untuk setiap pengguna yang dihapus
                foreach ($users as $user) {
                    // ===============================
                    // LOG ACTIVITY (AUDIT FULL)
                    // ===============================
                    activity()
                        ->useLog('massdelete user')
                        ->causedBy(Auth::user())
                        ->performedOn($user)
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
                            'get' => $user->toArray(),
                        ])
                        ->log('Menghapus akun user ' . $user['name']);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => count($ids) . ' skpd deleted successfully!'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No users selected for deletion.'
                ]);
            }
        } catch (\Exception $e) {
            \DB::rollback();
            $errorMessage = $e->getMessage(); // Mendapatkan pesan kesalahan dari Exception
            return response()->json(['error' => 'Data Gagal dihapus', 'time' => $formattedTime, 'judul' => 'Gagal', 'errorMessage' => $errorMessage]);
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
}
