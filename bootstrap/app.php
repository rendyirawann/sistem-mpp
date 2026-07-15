<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )


    // ->withMiddleware(function (Middleware $middleware): void {

    //     $middleware->alias([
    //         'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
    //         'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
    //         'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
    //     ]);

    // })

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: [
            'kiosk_authorized',
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'forbid-banned-user' => \Cog\Laravel\Ban\Http\Middleware\ForbidBannedUser::class,
            'kiosk-security' => \App\Http\Middleware\KioskSecurity::class,

        ]);
        // 🔥 TAMBAHKAN BARIS INI (Agar logoutOtherDevices berfungsi)
        $middleware->web(append: [
            \Illuminate\Session\Middleware\AuthenticateSession::class,
        ]);

        // Security header untuk SEMUA response (web & publik).
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
    })




    ->withExceptions(function (Exceptions $exceptions): void {
        // CSRF token kadaluarsa / tidak valid (419):
        // - AJAX/JSON -> balas JSON 419 agar front-end bisa menampilkan pesan & reload.
        // - Request biasa -> kembali ke halaman sebelumnya dengan input & pesan (bukan halaman 419 mentah).
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi Anda telah berakhir. Muat ulang halaman lalu coba lagi.',
                    'reload'  => true,
                ], 419);
            }

            return redirect()->back()
                ->withInput($request->except(['_token', 'password', 'password_confirmation']))
                ->with('error', 'Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan coba lagi.');
        });
    })->create();
