<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('code') — @yield('title', 'Terjadi Kesalahan')</title>
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/mpp_logo_premium.png') }}">
    <style>
        /* Self-contained: tidak bergantung font/asset eksternal agar tetap tampil
           walau server bermasalah (500/503) atau CDN gagal (Safari-safe). */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #fdfdfd;
            color: #111827;
            min-height: 100vh;
            display: -webkit-box; display: -webkit-flex; display: flex;
            -webkit-box-align: center; -webkit-align-items: center; align-items: center;
            -webkit-box-pack: center; -webkit-justify-content: center; justify-content: center;
            padding: 24px;
            -webkit-font-smoothing: antialiased;
            background-image: radial-gradient(circle at 50% 0%, rgba(79, 70, 229, 0.06) 0%, transparent 60%);
        }
        .err-card {
            width: 100%;
            max-width: 520px;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 28px;
            box-shadow: 0 20px 50px -20px rgba(0, 0, 0, 0.12);
            padding: clamp(28px, 6vw, 48px);
            text-align: center;
        }
        .err-badge {
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #4f46e5;
            background: #e0e7ff;
            padding: 6px 16px;
            border-radius: 100px;
            margin-bottom: 22px;
        }
        .err-code {
            font-size: clamp(64px, 18vw, 104px);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -3px;
            background: -webkit-linear-gradient(315deg, #111827 0%, #4f46e5 100%);
            background: linear-gradient(135deg, #111827 0%, #4f46e5 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: #4f46e5; /* fallback jika text-fill tak didukung */
        }
        .err-title {
            font-size: clamp(18px, 5vw, 24px);
            font-weight: 700;
            margin: 14px 0 10px;
            letter-spacing: -0.5px;
        }
        .err-desc {
            font-size: 15px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .err-actions {
            display: -webkit-box; display: -webkit-flex; display: flex;
            -webkit-flex-wrap: wrap; flex-wrap: wrap;
            gap: 12px;
            -webkit-box-pack: center; -webkit-justify-content: center; justify-content: center;
        }
        .err-btn {
            display: inline-block;
            padding: 14px 28px;
            border-radius: 100px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            -webkit-transition: all 0.15s ease; transition: all 0.15s ease;
        }
        .err-btn-primary { background: #4f46e5; color: #fff; }
        .err-btn-primary:hover { background: #4338ca; }
        .err-btn-ghost { background: #fff; color: #111827; border-color: rgba(0, 0, 0, 0.1); }
        .err-btn-ghost:hover { background: #f3f4f6; }
        @media (max-width: 400px) {
            .err-btn { width: 100%; }
        }
    </style>
</head>

<body>
    <div class="err-card">
        <span class="err-badge">Sistem MPP</span>
        <div class="err-code">@yield('code')</div>
        <h1 class="err-title">@yield('title', 'Terjadi Kesalahan')</h1>
        <p class="err-desc">@yield('message', 'Maaf, terjadi kendala saat memproses permintaan Anda.')</p>
        <div class="err-actions">
            <a href="{{ url('/') }}" class="err-btn err-btn-primary">Kembali ke Beranda</a>
            <a href="javascript:history.back()" class="err-btn err-btn-ghost">Halaman Sebelumnya</a>
        </div>
    </div>
</body>

</html>
