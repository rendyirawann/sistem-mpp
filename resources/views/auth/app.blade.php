<!DOCTYPE html>
<html lang="id">
<head>
    <title>@yield('title', 'Sistem Layanan Terpadu - Login')</title>
    <meta charset="utf-8" />
    <meta name="description" content="Sistem Informasi Layanan Publik Terpadu" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/mpp_logo_premium.png') }}" />
    
    {{-- Phosphor Icons & Fonts --}}
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />

    <style>
        :root {
            --background: #fdfdfd;
            --surface: #ffffff;
            --primary: #111827;
            --secondary: #6b7280;
            --accent: #4f46e5;
            --accent-light: #e0e7ff;
            --radius-xl: 40px;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--background);
            background-image: radial-gradient(circle at 0% 50%, rgba(79, 70, 229, 0.05) 0%, transparent 50%),
                              radial-gradient(circle at 100% 50%, rgba(17, 24, 39, 0.03) 0%, transparent 50%);
            min-height: 100vh;
            color: var(--primary);
        }

        .auth-container {
            display: flex;
            min-height: 100vh;
        }

        .auth-hero {
            flex: 1.2;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-form-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            background: var(--surface);
            box-shadow: -20px 0 40px rgba(0,0,0,0.02);
            position: relative;
            z-index: 10;
        }

        .hero-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: var(--radius-xl);
            padding: 60px;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: 0 30px 60px -20px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: 0 auto;
        }

        .brand-logo-large {
            width: 120px;
            height: 120px;
            object-fit: contain;
            background: white;
            border-radius: 24px;
            padding: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            margin-bottom: 40px;
        }

        .btn-apple {
            background: var(--primary);
            color: white;
            border-radius: 100px;
            padding: 16px 32px;
            font-weight: 600;
            font-size: 1.125rem;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            border: none;
            width: 100%;
        }
        .btn-apple:hover {
            background: #000;
            color: white;
            transform: scale(1.02);
        }
        .btn-apple.primary {
            background: var(--accent);
        }
        .btn-apple.primary:hover {
            background: #4338ca;
        }

        .custom-input {
            background: var(--background) !important;
            border: 2px solid transparent !important;
            border-radius: 16px !important;
            padding: 18px 24px !important;
            font-size: 1.1rem !important;
            font-weight: 500 !important;
            color: var(--primary) !important;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.02) !important;
            transition: all 0.2s !important;
        }
        .custom-input:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 4px var(--accent-light) !important;
            background: var(--surface) !important;
        }

        @media (max-width: 991px) {
            .auth-container { flex-direction: column; }
            .auth-hero { flex: none; padding: 40px 20px; text-align: center; }
            .hero-card { padding: 40px 20px; }
            .auth-form-wrapper { flex: none; padding: 40px 20px; border-radius: var(--radius-xl) var(--radius-xl) 0 0; }
        }
    </style>
    @stack('stylesheets')
</head>

<body>
    <div class="auth-container">
        
        <div class="auth-hero">
            <div class="hero-card">
                <img src="{{ asset('assets/media/logos/mpp_logo_premium.png') }}" alt="Logo" class="brand-logo-large">
                <h1 class="fw-black text-gray-900 mb-4" style="font-size: 3rem; letter-spacing: -1px; line-height: 1.1;">Sistem Layanan<br>Publik Terpadu</h1>
                <p class="fs-4 text-gray-500 mb-8 lh-base">Platform manajemen layanan publik generasi baru. Cepat, transparan, dan dapat diandalkan oleh masyarakat.</p>
                <div class="d-flex align-items-center gap-4">
                    <span class="badge bg-white text-gray-800 border shadow-sm px-4 py-3 rounded-pill fw-bold fs-6">
                        <i class="ph-fill ph-shield-check text-success me-2"></i> Akses Teramankan
                    </span>
                    <span class="badge bg-white text-gray-800 border shadow-sm px-4 py-3 rounded-pill fw-bold fs-6">
                        <i class="ph-fill ph-lightning text-warning me-2"></i> Performa Tinggi
                    </span>
                </div>
            </div>
        </div>

        <div class="auth-form-wrapper">
            <div class="w-100" style="max-width: 450px; margin: 0 auto;">
                @yield('content')
            </div>
        </div>
        
    </div>

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    @stack('scripts')
</body>
</html>
