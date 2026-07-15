<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menambahkan security header yang AMAN namun TIDAK terlalu ketat:
 * - Tidak memakai Content-Security-Policy ketat (agar CDN Google Fonts,
 *   Phosphor/FontAwesome, iframe YouTube, dan inline script tetap jalan).
 * - Kamera (face-api / QR scan) tetap diizinkan (Permissions-Policy camera=self).
 * - HSTS hanya dikirim saat koneksi HTTPS (aman untuk local HTTP).
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Hindari MIME-sniffing.
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Cegah clickjacking (halaman kita tidak boleh di-embed lintas origin).
        if (!$response->headers->has('X-Frame-Options')) {
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        }

        // Batasi kebocoran referrer, tapi tetap kirim origin ke tujuan HTTPS.
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Batasi cross-domain policy file (Flash/PDF legacy).
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        // Izinkan kamera untuk verifikasi wajah & scan QR; matikan yang tidak dipakai.
        $response->headers->set(
            'Permissions-Policy',
            'camera=(self), microphone=(), geolocation=(), payment=()'
        );

        // HSTS hanya di HTTPS agar tidak mengunci akses lewat HTTP di local.
        if ($request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        return $response;
    }
}
