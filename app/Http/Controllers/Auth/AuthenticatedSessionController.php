<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Jenssegers\Agent\Agent;
use Illuminate\Validation\ValidationException;
use App\Events\ForceLogoutNotification;
use App\Models\User;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }



    public function store(LoginRequest $request)
    {
        try {
            $request->authenticate();
        } catch (ValidationException $e) {

            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => $e->errors()
                ], 422);
            }

            throw $e;
        }


        $request->session()->regenerate();

        auth()->user()->update([
            'last_ip' => $request->ip(),
            'last_login' => now(),
        ]);

        $agent = new Agent;
        activity()
            ->useLog('login')
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
            ])
            ->log('Login berhasil');

        if ($request->expectsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Login berhasil'
            ]);
        }

        return redirect()->intended(route('dashboard'));
    }

    // public function store(LoginRequest $request)
    // {
    //     // 1. Cari tahu dulu user_id berdasarkan email sebelum login
    //     $user = User::where('email', $request->email)->first();

    //     if ($user) {
    //         // 2. Cek apakah ada sesi aktif di database untuk user ini
    //         $hasActiveSession = \Illuminate\Support\Facades\DB::table('sessions')
    //             ->where('user_id', $user->id)
    //             ->exists();

    //         if ($hasActiveSession) {
    //             // 3. Jika ada, TOLAK login belakangan
    //             if ($request->expectsJson()) {
    //                 return response()->json([
    //                     'errors' => [
    //                         'email' => ['Akun Anda sedang aktif di perangkat lain. Silakan logout dari perangkat tersebut terlebih dahulu.']
    //                     ]
    //                 ], 422);
    //             }

    //             throw ValidationException::withMessages([
    //                 'email' => ['Akun Anda sedang aktif di perangkat lain. Silakan logout dari perangkat tersebut terlebih dahulu.'],
    //             ]);
    //         }
    //     }

    //     // 4. Jika tidak ada sesi aktif, baru jalankan proses login normal
    //     try {
    //         $request->authenticate();
    //     } catch (ValidationException $e) {
    //         if ($request->expectsJson()) {
    //             return response()->json(['errors' => $e->errors()], 422);
    //         }
    //         throw $e;
    //     }

    //     $request->session()->regenerate();

    //     // Update data login terakhir
    //     auth()->user()->update([
    //         'last_ip' => $request->ip(),
    //         'last_login' => now(),
    //     ]);

    //     // ============================================================
    //     // 🔥 1. CEK PERANGKAT (BLOCK MOBILE)
    //     // ============================================================
    //     $agent = new Agent();

    //     // Cek apakah user menggunakan HP atau Tablet
    //     if ($agent->isMobile() || $agent->isTablet()) {

    //         // PENTING: Bolehkan Superadmin login via HP (Opsional)
    //         // Hapus 'if' ini jika Superadmin juga DILARANG pakai HP
    //         if (!$user->hasRole('Superadmin')) {

    //             // Logout user segera
    //             Auth::guard('web')->logout();
    //             $request->session()->invalidate();
    //             $request->session()->regenerateToken();

    //             // Kirim pesan error
    //             throw \Illuminate\Validation\ValidationException::withMessages([
    //                 'email' => 'Demi keamanan, Akun Tenant HANYA boleh login melalui Komputer/PC Desktop.',
    //             ]);
    //         }
    //     }

    //     // ============================================================
    //     // 🔥 2. SINGLE DEVICE LOGIN (MATIKAN SESI DI PC LAIN)
    //     // ============================================================
    //     // Fitur ini akan melogout akun ini di browser/komputer lain 
    //     // segera setelah login di sini berhasil.
    //     // Syarat: Middleware AuthenticateSession harus aktif di bootstrap/app.php
    //     try {
    //         broadcast(new ForceLogoutNotification($user->id))->toOthers();
    //     } catch (\Exception $e) {
    //         // Abaikan error jika websocket server belum jalan, biar login tetap bisa
    //         \Log::error("Broadcast logout gagal: " . $e->getMessage());
    //     }
    //     Auth::logoutOtherDevices($request->password);


    //     // ============================================================
    //     // 3. LOGGING & UPDATE DATA (Kode Lama Anda)
    //     // ============================================================
    //     $user->update([
    //         'last_ip' => $request->ip(),
    //         'last_login' => now(),
    //     ]);

    //     activity()
    //         ->useLog('login')
    //         ->causedBy($user)
    //         ->withProperties([
    //             'ip' => $request->ip(),
    //             'agent' => [
    //                 'browser' => $agent->browser() . ' ' . $agent->version($agent->browser()),
    //                 'os' => $agent->platform() . ' ' . $agent->version($agent->platform()),
    //                 'device' => $agent->device(),
    //                 'is_mobile' => $agent->isMobile(),
    //                 'is_desktop' => $agent->isDesktop(),
    //                 'raw' => $request->header('User-Agent'),
    //             ],
    //             'request' => [
    //                 'method' => $request->method(),
    //                 'url' => $request->fullUrl(),
    //             ],
    //         ])
    //         ->log('Login berhasil');

    //     if ($request->expectsJson()) {
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Login berhasil'
    //         ]);
    //     }

    //     return redirect()->intended(route('dashboard'));
    // }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $agent = new Agent;

        activity()
            ->useLog('logout')
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
            ])
            ->log('Logout berhasil');


        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
