<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

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
    })




    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
