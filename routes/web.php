<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Backend\Dashboard\DashboardController;

//PROFILE
use App\Http\Controllers\Backend\MyProfile\AccountController;
use App\Http\Controllers\Backend\MyProfile\ProfileController;
use App\Http\Controllers\Backend\MyProfile\SecurityController;
use App\Http\Controllers\Backend\MyProfile\ActivityController;
use App\Http\Controllers\Backend\MyProfile\LoginSessionController;

//USER MANAGEMENT
use App\Http\Controllers\Backend\UserManagement\UserController;
use App\Http\Controllers\Backend\UserManagement\RoleController;

//HELP
use App\Http\Controllers\Backend\Help\LogActivityController;


//MASTER
use App\Http\Controllers\Backend\Master\SatuanController;

//MASTER WILAYAH
use App\Http\Controllers\Backend\Master\Wilayah\WilayahProvinsiController;
use App\Http\Controllers\Backend\Master\Wilayah\WilayahKabupatenController;
use App\Http\Controllers\Backend\Master\Wilayah\WilayahKecamatanController;
use App\Http\Controllers\Backend\Master\Wilayah\WilayahDesaController;

// ANTRIAN
use App\Http\Controllers\Backend\Antrian\AntrianController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\Backend\Loket\LoketController;
// SKPD
use App\Http\Controllers\Backend\Skpd\SkpdController;
use App\Http\Controllers\SkmController;

//Route::get('/', function () {
//    return redirect()->route('login');
//});


Route::get('/', function () {
    return view('welcome'); // atau return "OK";
});

Route::get('/get-last-panggilan', [FrontController::class, 'checkLastPanggilan'])->name('antrian.check');

// --- ROUTES SKM (SURVEY) ---
Route::get('/skm', [SkmController::class, 'index'])->name('skm.index');
Route::post('/skm/check', [SkmController::class, 'checkAntrian'])->name('skm.check');
Route::post('/skm/store', [SkmController::class, 'store'])->name('skm.store');
// Tambahkan ini di group yang public (sebelum atau sesudah route skm)
Route::get('/proxy/sukmadeli/{endpoint}', [SkmController::class, 'getReferensiSukma'])
    ->name('skm.proxy');

Route::post('/login')
    ->middleware('throttle:5,1')
    ->name('login');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');
Route::get('/dashboard/detail', [DashboardController::class, 'detail'])->middleware(['auth'])->name('dashboard.detail');

Route::middleware('auth')->group(function () {

    Route::get('/my-account', [AccountController::class, 'index'])->name('account.index');
    Route::get('my-account/{id}/avatar', [AccountController::class, 'editAvatar'])->name('avatar-edit');
    Route::post('my-account/{id}/update-avatar', [AccountController::class, 'updateAvatar'])->name('avatar-update');

    Route::resource('my-profile', ProfileController::class);
    Route::resource('my-security', SecurityController::class);
    Route::post('my-security', [SecurityController::class, 'store'])->name('change.password');

    Route::get('/my-activity', [ActivityController::class, 'index'])->name('my-activity.index');
    Route::get('get-my-activity', [ActivityController::class, 'getActivity'])->name('get-my-activity');

    Route::get('/my-login-session', [LoginSessionController::class, 'index'])->name('my-login-session.index');
    Route::get('get-my-login-session', [LoginSessionController::class, 'getLoginSession'])->name('get-my-login-session');

    Route::resource('users', UserController::class);
    Route::get('get-users', [UserController::class, 'getUsers'])->name('get-users');
    Route::post('/users/mass-delete', [UserController::class, 'massDelete'])->name('users.mass-delete');
    Route::get('get-user-show-log/{id}', [UserController::class, 'getLoginSession'])->name('get-user-show-log');
    Route::get('get-user-show-log-activity/{id}', [UserController::class, 'getActivity'])->name('get-user-show-log-activity');
    Route::post('/users/{id}/ban', [UserController::class, 'ban'])->name('users.ban');
    Route::post('/users/{id}/unban', [UserController::class, 'unban'])->name('users.unban');

    Route::resource('skpd', SkpdController::class);
    Route::get('get-skpd', [SkpdController::class, 'getSkpd'])->name('get-skpd');
    Route::post('/skpd/mass-delete', [SkpdController::class, 'massDelete'])->name('skpd.mass-delete');
    Route::post('/skpd/sync-sukma', [SkpdController::class, 'syncSukma'])
        ->name('skpd.sync-sukma');

    Route::resource('antrian', AntrianController::class);

    /* DATATABLE */
    Route::get('get-antrian', [AntrianController::class, 'getAntrian'])
        ->name('antrian.get');

    /* INFO BOX */
    Route::get('/jumlah', [AntrianController::class, 'jumlah'])
        ->name('antrian.jumlah');

    Route::get('/sekarang', [AntrianController::class, 'sekarang'])
        ->name('antrian.sekarang');

    Route::get('/selanjutnya', [AntrianController::class, 'selanjutnya'])
        ->name('antrian.selanjutnya');

    Route::get('/sisa', [AntrianController::class, 'sisa'])
        ->name('antrian.sisa');

    Route::get('/selesai', [AntrianController::class, 'selesai'])
        ->name('antrian.selesai');

    Route::get('/global-info', [AntrianController::class, 'getGlobalNextInfo'])
        ->name('antrian.global-info');

    Route::get('/history', [AntrianController::class, 'getHistory'])->name('antrian.history');

    /* PANGGIL / UPDATE STATUS */
    Route::post('antrian/panggil', [AntrianController::class, 'panggil'])
        ->name('antrian.panggil');

    Route::get('/loket/get', [LoketController::class, 'getData'])->name('get-loket');
    Route::post('/loket/mass-delete', [SkpdController::class, 'massDelete'])->name('loket.mass-delete');
    Route::post('/loket/aktifkan', [LoketController::class, 'aktifkan'])
        ->name('loket.aktifkan');

    Route::post('/loket/nonaktifkan', [LoketController::class, 'nonaktifkan'])
        ->name('loket.nonaktifkan');

    Route::get('/loket/jumlah', [LoketController::class, 'jumlah'])
        ->name('loket.jumlah');

    Route::get('/loket/aktif', [LoketController::class, 'aktif'])
        ->name('loket.aktif');

    Route::get('/loket/nonaktif', [LoketController::class, 'nonaktif'])
        ->name('loket.nonaktif');
    Route::resource('loket', LoketController::class);

    // Route::get('get-antrian', [AntrianController::class, 'getData'])
    // ->name('get-antrian');
    // // mass delete (kalau dipakai)
    // Route::post('/antrian/mass-delete', [AntrianController::class, 'massDelete'])
    //     ->name('antrian.mass-delete');
    // // panggil antrian (khusus panggilan)
    // Route::post('/antrian/call', [AntrianController::class, 'call'])
    //     ->name('antrian.call');

    Route::resource('roles', RoleController::class);
    Route::get('get-datarole', [RoleController::class, 'getDataRoles'])->name('get-datarole');
    Route::post('/roles/mass-delete', [RoleController::class, 'massDelete'])->name('roles.mass-delete');
    Route::get('/select/role', [RoleController::class, 'select'])->name('role.select');

    Route::resource('log-activity', LogActivityController::class);
    Route::get('get-datalogactivity', [LogActivityController::class, 'getDataLogActivity'])->name('get-datalogactivity');


    //MASTER WILAYAH
    Route::get('/wilayah-provinsi', [WilayahProvinsiController::class, 'index'])->name('provinsi.index');
    Route::get('/wilayah-provinsi/data', [WilayahProvinsiController::class, 'getData'])->name('provinsi.data');

    Route::get('/wilayah-kabupaten', [WilayahKabupatenController::class, 'index'])->name('kabupaten.index');
    Route::get('/wilayah-kabupaten/data', [WilayahKabupatenController::class, 'getData'])->name('kabupaten.data');

    Route::get('/wilayah-kecamatan', [WilayahKecamatanController::class, 'index'])->name('kecamatan.index');
    Route::get('/wilayah-kecamatan/data', [WilayahKecamatanController::class, 'getData'])->name('kecamatan.data');

    Route::get('/wilayah-desa', [WilayahDesaController::class, 'index'])->name('desa.index');
    Route::get('/wilayah-desa/data', [WilayahDesaController::class, 'getData'])->name('desa.data');

    //MASTER
    Route::resource('master/satuan', SatuanController::class);
    Route::get('/get/master/satuan/data', [SatuanController::class, 'getData'])->name('get.master.satuan.data');
    Route::post('/satuan/mass-delete', [SatuanController::class, 'massDelete'])->name('satuan.mass-delete');
    Route::get('/select/satuan', [SatuanController::class, 'select'])->name('satuan.select');

    //SELECT WILAYAH
    Route::get('/wilayah_provinsi', [WilayahProvinsiController::class, 'select'])->name('wilayahprovinsi.select');
    Route::get('/wilayah_kabupaten', [WilayahKabupatenController::class, 'select'])->name('wilayahkabupaten.select');
    Route::get('/wilayah_kecamatan', [WilayahKecamatanController::class, 'select'])->name('wilayahkecamatan.select');
    Route::get('/wilayah_desa', [WilayahDesaController::class, 'select'])->name('wilayahdesa.select');




    Route::get('/check-auth', function () {
        $u = auth()->user();

        return [
            'user' => $u,
            'roles' => $u?->getRoleNames(),
            'permissions' => $u?->getPermissionNames(),
            'guard' => config('auth.defaults.guard')
        ];
    })->middleware('auth');
});

require __DIR__ . '/auth.php';
// --- ROUTE UNTUK KIOS ANTRIAN (Ubah Bagian Bawah Jadi Ini) ---

//use App\Http\Controllers\AntrianController; // Pastikan baris ini ada di paling atas file, kalau sudah ada hapus yang ini.

// 1. Halaman Depan Kios (Memanggil AntrianController fungsi index)
Route::get('/', [FrontController::class, 'index'])->name('home');

// 2. Proses Ambil Antrian (Saat tombol Input ditekan)
Route::post('/ambil-antrian', [FrontController::class, 'ambilAntrian'])->name('ambil.antrian');

// -------------------------------------------------------------
