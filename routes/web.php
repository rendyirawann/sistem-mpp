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
use App\Http\Controllers\Backend\Master\LayananSkmController;
use App\Http\Controllers\SkmController;

// ANTRIAN ONLINE (PUBLIC)
use App\Http\Controllers\AntrianOnlineController;
use App\Http\Controllers\RegistrasiOnlineController;
use App\Http\Controllers\WilayahController;

// DISPLAY TV
use App\Http\Controllers\Backend\Display\DisplaySettingController;
use App\Http\Controllers\Backend\Display\AnnouncementController;

// ANTRIAN ONLINE (BACKEND)
use App\Http\Controllers\Backend\AntrianOnline\ListAntrianController;
use App\Http\Controllers\Backend\Kalender\KalenderController;
use App\Http\Controllers\Backend\Scan\ScanController;
use App\Http\Controllers\Backend\Setting\LandingSettingController;
use App\Http\Controllers\Backend\FormPersyaratan\FormPersyaratanController;

Route::get('/get-last-panggilan', [FrontController::class, 'checkLastPanggilan'])->name('antrian.check');

// --- ROUTES SKM (SURVEY) ---
Route::get('/skm', [SkmController::class, 'index'])->name('skm.index');
Route::post('/skm/check', [SkmController::class, 'checkAntrian'])->middleware('throttle:20,1')->name('skm.check');
Route::post('/skm/store', [SkmController::class, 'store'])->middleware('throttle:20,1')->name('skm.store');
// Tambahkan ini di group yang public (sebelum atau sesudah route skm)
Route::get('/proxy/sukmadeli/{endpoint}', [SkmController::class, 'getReferensiSukma'])
    ->name('skm.proxy');

Route::post('/login')
    ->middleware('throttle:5,1')
    ->name('login');

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/detail', [DashboardController::class, 'getDetailCard'])->name('dashboard.detail');

    Route::get('/dashboard/export', [DashboardController::class, 'exportLaporan'])->name('dashboard.export');
    // Route untuk mengambil detail antrian di dashboard (AJAX)
    // Tambahkan route ini di dalam group dashboard
    Route::get('/dashboard/detail-rekap', [DashboardController::class, 'getDetailRekap'])->name('dashboard.detail_rekap');

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

    Route::post('/skpd/batch-jam-operasional', [SkpdController::class, 'batchJamOperasional'])->name('skpd.batch-jam');
    Route::post('/skpd/batch-status-all', [SkpdController::class, 'batchStatusAll'])->name('skpd.batch-status-all');

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
    Route::get('/loket/export-pdf', [LoketController::class, 'exportPdf'])->name('loket.export-pdf');
    Route::post('/loket/mass-delete', [LoketController::class, 'massDelete'])->name('loket.mass-delete');
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

    Route::resource('master/layanan-skm', LayananSkmController::class);
    Route::get('/get/master/layanan-skm/data', [LayananSkmController::class, 'getData'])->name('get.master.layanan-skm.data');
    Route::post('/layanan-skm/mass-delete', [LayananSkmController::class, 'massDelete'])->name('layanan-skm.mass-delete');

    //MANAJEMEN SKM (HASIL SURVEY)
    Route::resource('master/skm', \App\Http\Controllers\Backend\Master\SkmController::class)
        ->except(['destroy', 'create', 'store'])
        ->names([
            'index' => 'master.skm.index',
            'edit' => 'master.skm.edit',
            'update' => 'master.skm.update',
            'show' => 'master.skm.show',
        ]);
    Route::get('/get/master/skm/data', [\App\Http\Controllers\Backend\Master\SkmController::class, 'getData'])->name('master.skm.getData');
    Route::post('/skm/sync', [\App\Http\Controllers\Backend\Master\SkmController::class, 'syncData'])->name('master.skm.sync');

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

    // ===================== DISPLAY SETTING BACKEND (SUPERADMIN ONLY) =====================
    Route::get('/display-setting', [DisplaySettingController::class, 'index'])->name('display-setting.index');
    Route::post('/display-setting/update', [DisplaySettingController::class, 'updateSetting'])->name('display-setting.update');
    Route::post('/display-setting/banner', [DisplaySettingController::class, 'storeBanner'])->name('display-setting.banner.store');
    Route::post('/display-setting/banner/{id}/toggle', [DisplaySettingController::class, 'toggleBanner'])->name('display-setting.banner.toggle');
    Route::delete('/display-setting/banner/{id}', [DisplaySettingController::class, 'deleteBanner'])->name('display-setting.banner.delete');

    // ===================== DISPLAY ANNOUNCEMENT SYSTEM (SUPERADMIN ONLY) =====================
    Route::get('/display-announcement', [AnnouncementController::class, 'index'])->name('announcement.index');
    Route::get('/get-announcement', [AnnouncementController::class, 'getAnnouncement'])->name('announcement.data');
    Route::post('/display-announcement/play', [AnnouncementController::class, 'play'])->name('announcement.play');
    Route::post('/display-announcement/play-all', [AnnouncementController::class, 'playAll'])->name('announcement.play_all');
    Route::post('/display-announcement/stop', [AnnouncementController::class, 'stop'])->name('announcement.stop');
    Route::post('/display-announcement', [AnnouncementController::class, 'store'])->name('announcement.store');
    Route::get('/display-announcement/{id}', [AnnouncementController::class, 'show'])->name('announcement.show');
    Route::put('/display-announcement/{id}', [AnnouncementController::class, 'update'])->name('announcement.update');
    Route::delete('/display-announcement/{id}', [AnnouncementController::class, 'destroy'])->name('announcement.delete');

    // ===================== KALENDER KUOTA ANTRIAN (Superadmin & tenant pemilik) =====================
    Route::get('/kalender-antrian', [KalenderController::class, 'index'])->name('kalender.index');
    Route::get('/kalender-antrian/{skpd}', [KalenderController::class, 'show'])->name('kalender.show');
    Route::post('/kalender-antrian/{skpd}/set', [KalenderController::class, 'setKuota'])->name('kalender.set');

    // ===================== SCAN QR TIKET ANTREAN (Superadmin & tenant) =====================
    Route::get('/scan-antrean', [ScanController::class, 'index'])->name('scan.index');
    Route::post('/scan-antrean/lookup', [ScanController::class, 'lookup'])->name('scan.lookup');

    // ===================== LIST ANTRIAN ONLINE (per tenant) =====================
    Route::get('/list-antrian-online', [ListAntrianController::class, 'index'])->name('antrian-online-list.index');
    Route::get('/list-antrian-online/data', [ListAntrianController::class, 'data'])->name('antrian-online-list.data');
    Route::get('/list-antrian-online/{id}', [ListAntrianController::class, 'detail'])->name('antrian-online-list.detail');

    // ===================== FORM PERSYARATAN — MANAJEMEN (SUPERADMIN ONLY) =====================
    Route::get('/form-persyaratan', [FormPersyaratanController::class, 'index'])->name('form-persyaratan.index');
    Route::get('/form-persyaratan/data', [FormPersyaratanController::class, 'data'])->name('form-persyaratan.data');
    Route::get('/form-persyaratan/create', [FormPersyaratanController::class, 'create'])->name('form-persyaratan.create');
    Route::post('/form-persyaratan', [FormPersyaratanController::class, 'store'])->name('form-persyaratan.store');
    Route::get('/form-persyaratan/{id}/edit', [FormPersyaratanController::class, 'edit'])->name('form-persyaratan.edit');
    Route::put('/form-persyaratan/{id}', [FormPersyaratanController::class, 'update'])->name('form-persyaratan.update');
    Route::delete('/form-persyaratan/{id}', [FormPersyaratanController::class, 'destroy'])->name('form-persyaratan.destroy');

    // ===================== LANDING ANTRIAN ONLINE — PENGATURAN (SUPERADMIN ONLY) =====================
    Route::get('/landing-setting', [LandingSettingController::class, 'index'])->name('landing-setting.index');
    Route::post('/landing-setting/update', [LandingSettingController::class, 'updateSettings'])->name('landing-setting.update');
    Route::post('/landing-setting/social', [LandingSettingController::class, 'storeSocial'])->name('landing-setting.social.store');
    Route::post('/landing-setting/social/{id}/toggle', [LandingSettingController::class, 'toggleSocial'])->name('landing-setting.social.toggle');
    Route::delete('/landing-setting/social/{id}', [LandingSettingController::class, 'deleteSocial'])->name('landing-setting.social.delete');
    Route::post('/landing-setting/tenant/{id}/toggle', [LandingSettingController::class, 'toggleTenant'])->name('landing-setting.tenant.toggle');
});

require __DIR__ . '/auth.php';
// --- ROUTE UNTUK KIOS ANTRIAN (Ubah Bagian Bawah Jadi Ini) ---

//use App\Http\Controllers\AntrianController; // Pastikan baris ini ada di paling atas file, kalau sudah ada hapus yang ini.

// 1. Halaman Depan Kios (Memanggil AntrianController fungsi index)
// 1. Landing Page (Public)
Route::get('/', [FrontController::class, 'landing'])->name('landing');
Route::get('/daftar-instansi', [FrontController::class, 'daftarInstansi'])->name('daftar.instansi');


// 2. Halaman Depan Kios (Memanggil FrontController fungsi index) - Pindahkan ke /kiosk-mpp
Route::get('/kiosk-auth', [FrontController::class, 'showKioskAuth'])->name('kiosk.auth');
Route::post('/kiosk-auth', [FrontController::class, 'verifyKioskAuth'])->middleware('throttle:10,1')->name('kiosk.verify');

Route::middleware('kiosk-security')->group(function () {
    Route::get('/kiosk-mpp', [FrontController::class, 'index'])->name('home');

    // 2. Proses Ambil Antrian (Saat tombol Input ditekan)
    Route::post('/ambil-antrian', [FrontController::class, 'ambilAntrian'])->name('ambil.antrian');

    Route::get('/kios/grid-skpd', [FrontController::class, 'getGridSkpd'])->name('kios.grid');
});

// 3. Halaman Display Monitor TV (Public)
Route::get('/display', [FrontController::class, 'displayMonitor'])->name('display.monitor');
Route::get('/api/display-data', [FrontController::class, 'getDisplayData'])->name('api.display.data');
Route::post('/api/display/announcement-finished', [FrontController::class, 'announcementFinished'])->name('api.display.announcement_finished');

// 4. ANTRIAN ONLINE (Public, rate-limited)
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/antrian-online', [AntrianOnlineController::class, 'index'])->name('antrian-online');
    Route::get('/list-tenant', [AntrianOnlineController::class, 'listTenant'])->name('antrian-online.list');
    Route::get('/antrian-online/tenant/{skpd}', [AntrianOnlineController::class, 'layanan'])->name('antrian-online.layanan');

    // Wizard registrasi antrian online (per layanan/loket)
    Route::get('/antrian-online/registrasi/{loket}', [RegistrasiOnlineController::class, 'show'])->name('antrian-online.registrasi');
    Route::post('/antrian-online/registrasi/{loket}/draft', [RegistrasiOnlineController::class, 'saveStep'])->name('antrian-online.registrasi.draft');
    Route::post('/antrian-online/registrasi/{loket}/submit', [RegistrasiOnlineController::class, 'submit'])->name('antrian-online.registrasi.submit');

    // Dropdown wilayah bertingkat (untuk form persyaratan)
    Route::get('/wilayah/provinsi', [WilayahController::class, 'provinsi'])->name('wilayah.provinsi');
    Route::get('/wilayah/kabupaten/{provinsi}', [WilayahController::class, 'kabupaten'])->name('wilayah.kabupaten');
    Route::get('/wilayah/kecamatan/{kabupaten}', [WilayahController::class, 'kecamatan'])->name('wilayah.kecamatan');
    Route::get('/wilayah/desa/{kecamatan}', [WilayahController::class, 'desa'])->name('wilayah.desa');
});

// -------------------------------------------------------------
