<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cookie;

class KioskSecurity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil secret dari config
        $secret = config('kiosk.secret', 'mppdeli2026');
        $cookieName = 'kiosk_authorized';
        $expectedValue = md5($secret);

        // 1. PROSES UNLOCK VIA URL: ?unlock=...
        if ($request->has('unlock') && $request->query('unlock') === $secret) {
            session(['kiosk_unlocked' => $expectedValue]);
            session()->save();

            $cookie = cookie($cookieName, $expectedValue, 2628000); // 5 Tahun
            return redirect()->route('home')->withCookie($cookie);
        }

        // 2. VERIFIKASI: Nilai di browser HARUS sama dengan md5 dari secret saat ini
        // Kita gunakan $request->cookie() untuk mendapatkan nilai yang sudah di-decrypt (jika ada) 
        // atau Cookie::get() jika tidak di-encrypt.
        $sessionValue = session('kiosk_unlocked');
        $cookieValue = $request->cookie($cookieName) ?? Cookie::get($cookieName);

        $isAuthorized = ($sessionValue === $expectedValue) || ($cookieValue === $expectedValue);

        if (!$isAuthorized) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Kiosk Unauthorized'], 403);
            }
            
            return redirect()->route('kiosk.auth');
        }

        return $next($request);
    }
}
