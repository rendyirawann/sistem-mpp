<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Antrian MPP') — Sistem MPP</title>
    <meta name="description"
        content="{{ $settings['hero_subtitle'] ?? 'Portal Antrean Online Sistem Layanan Publik Terpadu' }}">

    <link rel="shortcut icon" href="{{ asset('assets/media/logos/mpp_logo_premium.png') }}">

    {{-- Outfit — font utama desain publik Sistem MPP --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    {{-- Phosphor Icons --}}
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <style>
        :root {
            --ao-bg: #fdfdfd;
            --ao-surface: #ffffff;
            --ao-primary: #4f46e5;
            --ao-primary-dark: #4338ca;
            --ao-accent-light: #e0e7ff;
            --ao-navy: #111827;
            --ao-text: #111827;
            --ao-muted: #6b7280;
            --ao-border: rgba(0, 0, 0, .06);
            --ao-radius: 24px;
            --ao-shadow: 0 20px 40px -20px rgba(0, 0, 0, .1);
            --ao-font: 'Outfit', system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }

        * { box-sizing: border-box; }

        html { -webkit-text-size-adjust: 100%; scroll-behavior: smooth; }

        body {
            margin: 0;
            font-family: var(--ao-font);
            color: var(--ao-text);
            background: var(--ao-bg);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
        }
        .ao-main { flex: 1 0 auto; width: 100%; }

        img { max-width: 100%; display: block; }
        a { text-decoration: none; color: inherit; }

        .ao-container { width: 100%; max-width: 1280px; margin: 0 auto; padding: 0 clamp(16px, 4vw, 48px); }

        /* ===================== NAVBAR (GLASS PILL) ===================== */
        .ao-nav-wrap {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            display: flex; justify-content: center;
            padding: 16px 16px 0;
            transition: padding .35s ease;
            padding-top: max(16px, env(safe-area-inset-top));
        }
        .ao-nav {
            width: 100%; max-width: 1040px;
            display: flex; align-items: center; justify-content: space-between;
            gap: 16px;
            padding: 10px 18px;
            background: rgba(255, 255, 255, .65);
            -webkit-backdrop-filter: blur(20px) saturate(160%); backdrop-filter: blur(20px) saturate(160%);
            border: 1px solid rgba(0, 0, 0, .05);
            border-radius: 100px;
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, .08);
            transition: max-width .4s ease, background .3s ease, padding .3s ease;
        }
        .ao-nav-wrap.ao-scrolled { padding-top: 10px; }
        .ao-nav-wrap.ao-scrolled .ao-nav {
            max-width: 1280px;
            background: rgba(255, 255, 255, .85);
            padding: 12px 26px;
        }
        .ao-brand { display: flex; align-items: center; gap: 10px; font-weight: 800; color: var(--ao-text); font-size: 16px; letter-spacing: -0.5px; }
        .ao-brand img { width: 32px; height: 32px; object-fit: contain; }
        .ao-menu { display: flex; align-items: center; gap: 6px; }
        .ao-menu a {
            padding: 8px 18px; border-radius: 100px; font-size: 14px; font-weight: 600; color: var(--ao-muted);
            transition: background .15s, color .15s;
        }
        .ao-menu a:hover { background: var(--ao-accent-light); color: var(--ao-primary); }
        .ao-menu a.active { color: #fff; background: var(--ao-navy); }
        .ao-burger {
            display: none; background: none; border: none; cursor: pointer; padding: 6px;
            width: 40px; height: 40px; border-radius: 100px;
        }
        .ao-burger span { display: block; width: 22px; height: 2px; background: var(--ao-text); margin: 4px auto; border-radius: 2px; transition: .3s; }

        /* ===================== HERO (CLEAN / GRADIENT TEXT) ===================== */
        .ao-hero {
            position: relative; min-height: 68vh; display: flex; align-items: center;
            overflow: hidden;
            background: radial-gradient(circle at 50% 0%, rgba(79, 70, 229, .06) 0%, transparent 70%);
        }
        .ao-hero .ao-container { position: relative; z-index: 2; padding-top: 140px; padding-bottom: 90px; text-align: center; }
        .ao-hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: #fff; border: 1px solid var(--ao-border); box-shadow: 0 4px 14px -6px rgba(0,0,0,.08);
            border-radius: 100px; padding: 8px 18px; font-size: 13px; font-weight: 700; color: var(--ao-text);
            margin-bottom: 26px;
        }
        .ao-hero-badge i { color: var(--ao-primary); font-size: 16px; }
        .ao-hero h1 {
            font-size: clamp(34px, 6vw, 64px); font-weight: 800; line-height: 1.08; letter-spacing: -2px;
            margin: 0 auto 18px; max-width: 760px;
            background: linear-gradient(135deg, #111827 0%, #4f46e5 100%);
            -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;
        }
        .ao-hero p { font-size: clamp(15px, 2vw, 18px); max-width: 560px; color: var(--ao-muted); margin: 0 auto 32px; font-weight: 400; }
        .ao-btn {
            display: inline-flex; align-items: center; gap: 8px; cursor: pointer;
            padding: 15px 32px; border-radius: 100px; font-weight: 600; font-size: 15px; border: 0;
            font-family: var(--ao-font);
            transition: transform .15s, box-shadow .15s, background .15s;
        }
        .ao-btn-light { background: var(--ao-primary); color: #fff; box-shadow: 0 14px 30px -10px rgba(79, 70, 229, .5); }
        .ao-btn-light:hover { transform: scale(1.03); background: var(--ao-primary-dark); }
        .ao-btn-dark { background: var(--ao-navy); color: #fff; }
        .ao-btn-dark:hover { transform: scale(1.03); background: #000; }

        /* ===================== TENANT SECTION ===================== */
        .ao-section { position: relative; z-index: 3; padding-bottom: 70px; }
        .ao-panel {
            background: var(--ao-surface);
            border-radius: 32px;
            border: 1px solid var(--ao-border);
            box-shadow: var(--ao-shadow);
            padding: clamp(26px, 3vw, 44px) clamp(20px, 3vw, 44px);
        }
        .ao-panel-head {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 26px; gap: 12px; flex-wrap: wrap;
        }
        .ao-panel-title { display: flex; align-items: center; gap: 12px; font-size: 19px; font-weight: 700; color: var(--ao-text); letter-spacing: -0.5px; }
        .ao-panel-title .ao-ic {
            width: 44px; height: 44px; border-radius: 14px; background: var(--ao-accent-light); color: var(--ao-primary);
            display: flex; align-items: center; justify-content: center; font-size: 20px;
        }
        .ao-seeall { display: inline-flex; align-items: center; gap: 6px; color: var(--ao-primary); font-weight: 700; font-size: 14px; transition: gap .15s; }
        .ao-seeall:hover { gap: 10px; }

        .ao-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(235px, 1fr)); gap: 18px; }

        .ao-card {
            display: flex; flex-direction: column; background: var(--ao-surface);
            border: 1px solid rgba(0, 0, 0, .04); border-radius: 24px; padding: 24px 22px;
            box-shadow: 0 20px 40px -20px rgba(0, 0, 0, .05);
            transition: transform .2s, box-shadow .2s, border-color .2s; min-height: 205px;
        }
        .ao-card:hover { transform: translateY(-8px); box-shadow: 0 28px 50px -22px rgba(0, 0, 0, .12); border-color: var(--ao-accent-light); }
        .ao-card-logo {
            width: 54px; height: 54px; border-radius: 16px; background: var(--ao-accent-light); display: flex; align-items: center; justify-content: center;
            margin-bottom: 16px; overflow: hidden;
        }
        .ao-card-logo img { width: 66%; height: 66%; object-fit: contain; }
        .ao-card-name { font-weight: 700; font-size: 15px; color: var(--ao-text); margin-bottom: 4px; flex-grow: 1; letter-spacing: -0.3px; }
        .ao-card-sub { font-size: 12.5px; color: var(--ao-muted); margin-bottom: 14px; }
        .ao-card-btn {
            width: 100%; text-align: center; padding: 11px; border-radius: 100px; font-weight: 600; font-size: 13px;
            border: 1px solid rgba(0, 0, 0, .08); background: #fff; color: var(--ao-text); transition: .15s; cursor: pointer;
            font-family: var(--ao-font);
        }
        .ao-card-btn:hover { background: var(--ao-primary); color: #fff; border-color: var(--ao-primary); }
        .ao-card-btn.ao-soon { background: #f8f9fc; color: #9ca3af; border-color: rgba(0,0,0,.04); cursor: not-allowed; }
        .ao-card-btn.ao-soon:hover { background: #f8f9fc; color: #9ca3af; }

        .ao-card-top { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; }
        .ao-card-top .ao-card-logo { margin-bottom: 0; flex: 0 0 auto; }
        .ao-card-count {
            display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;
            font-size: 11px; font-weight: 700; color: var(--ao-primary);
            background: var(--ao-accent-light); border: none;
            padding: 5px 11px; border-radius: 100px; line-height: 1;
        }
        .ao-info {
            margin-left: auto; flex: 0 0 auto; width: 32px; height: 32px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
            border: 1px solid rgba(0, 0, 0, .08); background: #fff; color: var(--ao-muted); transition: .15s; font-size: 15px;
        }
        .ao-info:hover { color: var(--ao-primary); border-color: var(--ao-primary); background: var(--ao-accent-light); transform: translateY(-1px); }
        .ao-info:focus-visible { outline: 2px solid var(--ao-primary); outline-offset: 2px; }

        /* Modal daftar layanan */
        .ao-modal { position: fixed; inset: 0; z-index: 2100; display: none; align-items: center; justify-content: center; padding: 20px; }
        .ao-modal.open { display: flex; }
        .ao-modal-backdrop { position: absolute; inset: 0; background: rgba(17, 24, 39, .5); -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px); animation: ao-fade .22s ease both; }
        .ao-modal-box {
            position: relative; width: 100%; max-width: 440px; max-height: 82vh; overflow: auto;
            background: var(--ao-surface); color: var(--ao-text); border: none;
            border-radius: 32px; padding: 28px 26px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, .25);
            animation: ao-pop .26s cubic-bezier(.2, .8, .2, 1) both;
        }
        .ao-modal-x {
            position: absolute; top: 14px; right: 14px; width: 36px; height: 36px; border: none; background: #f3f4f6;
            border-radius: 50%; cursor: pointer; color: var(--ao-muted); display: flex; align-items: center; justify-content: center; transition: .15s; font-size: 16px;
        }
        .ao-modal-x:hover { background: var(--ao-accent-light); color: var(--ao-text); }
        .ao-modal-head { display: flex; align-items: center; gap: 14px; margin-bottom: 18px; padding-right: 34px; }
        .ao-modal-ic { flex: 0 0 auto; width: 48px; height: 48px; border-radius: 16px; background: var(--ao-primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 22px; }
        .ao-modal-title { font-size: 16px; font-weight: 700; color: var(--ao-text); line-height: 1.25; letter-spacing: -0.3px; }
        .ao-modal-sub { font-size: 12.5px; color: var(--ao-muted); margin-top: 2px; }
        .ao-modal-list { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 8px; }
        .ao-modal-list li { display: flex; align-items: center; gap: 12px; padding: 12px 15px; border: 1px solid var(--ao-border); border-radius: 16px; font-size: 14px; font-weight: 600; }
        .ao-modal-num { flex: 0 0 auto; width: 26px; height: 26px; border-radius: 9px; background: var(--ao-accent-light); color: var(--ao-primary); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; }
        .ao-modal-empty { text-align: center; color: var(--ao-muted); font-size: 13.5px; padding: 16px 0; }
        @keyframes ao-fade { from { opacity: 0; } to { opacity: 1; } }
        @keyframes ao-pop { from { opacity: 0; transform: translateY(14px) scale(.96); } to { opacity: 1; transform: none; } }
        @media (prefers-reduced-motion: reduce) { .ao-modal-backdrop, .ao-modal-box { animation: none; } }

        /* ===================== LAYANAN (LOKET) CARDS ===================== */
        .ao-svc-icon {
            width: 56px; height: 56px; border-radius: 18px; background: var(--ao-accent-light); color: var(--ao-primary);
            display: flex; align-items: center; justify-content: center; margin-bottom: 18px; font-size: 24px;
            transition: background .2s, color .2s, transform .2s;
        }
        .ao-card:hover .ao-svc-icon { background: var(--ao-primary); color: #fff; transform: scale(1.05); }
        .ao-card-name.ao-svc-title { color: var(--ao-text); font-size: 15px; }

        /* ===================== FOOTER (DARK ROUNDED BLOCK) ===================== */
        .ao-footer {
            background: var(--ao-navy); color: #d1d5db; padding: 56px 0 28px; margin-top: 20px; flex-shrink: 0;
            border-radius: 40px 40px 0 0;
        }
        .ao-footer-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr; gap: 36px; }
        .ao-footer h4 { color: #fff; font-size: 16px; font-weight: 700; margin: 0 0 18px; letter-spacing: -0.3px; }
        .ao-footer-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
        .ao-footer-brand img { width: 44px; height: 44px; object-fit: contain; }
        .ao-footer-brand span { font-weight: 800; font-size: 13px; color: #fff; letter-spacing: .3px; text-transform: uppercase; line-height: 1.3; }
        .ao-footer p { font-size: 13.5px; color: #9ca3af; margin: 0; }
        .ao-footer-item { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 16px; font-size: 14px; }
        .ao-footer-item .ao-fic {
            color: #a5b4fc; flex: 0 0 auto; margin-top: 2px;
            width: 36px; height: 36px; border-radius: 12px; background: rgba(255, 255, 255, .08);
            display: flex; align-items: center; justify-content: center; font-size: 16px;
            border: 1px solid rgba(255, 255, 255, .1);
        }
        .ao-footer-item .ao-lbl { color: #a5b4fc; font-size: 12px; }
        .ao-footer-item .ao-val { color: #fff; font-weight: 600; }
        .ao-footer-sep { height: 1px; background: rgba(255, 255, 255, .1); margin: 28px 0 18px; }
        .ao-footer-bottom { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
        .ao-copy { font-size: 12.5px; color: #9ca3af; }
        .ao-socials { display: flex; gap: 10px; }
        .ao-soc {
            width: 40px; height: 40px; border-radius: 50%; background: rgba(255, 255, 255, .08); color: #fff;
            border: 1px solid rgba(255, 255, 255, .1);
            display: flex; align-items: center; justify-content: center; transition: .15s;
        }
        .ao-soc:hover { background: var(--ao-primary); transform: translateY(-2px); }

        /* ===================== TOAST ===================== */
        .ao-toast {
            position: fixed; left: 50%; bottom: 28px; transform: translateX(-50%) translateY(20px);
            background: var(--ao-navy); color: #fff; padding: 13px 24px; border-radius: 100px; font-size: 14px; font-weight: 600;
            box-shadow: 0 20px 40px -14px rgba(0, 0, 0, .4);
            z-index: 2000; opacity: 0; pointer-events: none; transition: opacity .25s, transform .25s; max-width: 90vw; text-align: center;
        }
        .ao-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

        /* ===================== RESPONSIVE ===================== */
        @media (max-width: 820px) {
            .ao-burger { display: block; }
            .ao-menu {
                position: absolute; top: calc(100% + 10px); right: 0; left: 0;
                flex-direction: column; align-items: stretch; gap: 4px;
                background: rgba(255, 255, 255, .97); -webkit-backdrop-filter: blur(14px); backdrop-filter: blur(14px);
                border: 1px solid var(--ao-border); border-radius: 24px; padding: 10px; box-shadow: var(--ao-shadow);
                display: none;
            }
            .ao-menu.open { display: flex; }
            .ao-menu a { padding: 12px 18px; border-radius: 16px; }
            .ao-nav { position: relative; flex-wrap: wrap; border-radius: 28px; }
            .ao-footer-grid { grid-template-columns: 1fr 1fr; gap: 28px; }
        }
        @media (max-width: 560px) {
            .ao-grid { gap: 12px; }
            .ao-footer-grid { grid-template-columns: 1fr; }
            .ao-footer-bottom { justify-content: center; text-align: center; }
            .ao-hero .ao-container { padding-top: 120px; }
            .ao-panel { padding: 22px 16px; border-radius: 24px; }
            .ao-card { min-height: 0; padding: 18px 16px; }
            .ao-footer { border-radius: 28px 28px 0 0; }
        }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .ao-card, .ao-btn, .ao-nav { transition: none; }
        }
    </style>
    @stack('styles')
</head>

<body>
    @php
        $brand = $settings['brand_name'] ?? 'Portal Antrian MPP';
    @endphp

    {{-- ============ NAVBAR ============ --}}
    <div class="ao-nav-wrap" id="aoNavWrap">
        <nav class="ao-nav">
            <a href="{{ route('antrian-online') }}" class="ao-brand">
                <img src="{{ asset('assets/media/logos/mpp_logo_premium.png') }}" alt="Logo"
                    onerror="this.onerror=null;this.src='{{ asset('assets/media/logos/logo_deliserdang.png') }}';">
                <span>{{ $brand }}</span>
            </a>
            <button class="ao-burger" id="aoBurger" aria-label="Menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
            <div class="ao-menu" id="aoMenu">
                <a href="{{ route('antrian-online') }}" class="{{ request()->routeIs('antrian-online') ? 'active' : '' }}">Beranda</a>
                <a href="{{ route('antrian-online.list') }}" class="{{ request()->routeIs('antrian-online.list') || request()->routeIs('antrian-online.layanan') ? 'active' : '' }}">Layanan</a>
                <a href="#ao-footer">Kontak</a>
                <a href="{{ $settings['hero_button_link'] ?? '#' }}" target="{{ \Illuminate\Support\Str::startsWith($settings['hero_button_link'] ?? '#', 'http') ? '_blank' : '_self' }}">Bantuan</a>
            </div>
        </nav>
    </div>

    <main class="ao-main">
        @yield('content')
    </main>

    {{-- ============ FOOTER ============ --}}
    <footer class="ao-footer" id="ao-footer">
        <div class="ao-container">
            <div class="ao-footer-grid">
                <div>
                    <div class="ao-footer-brand">
                        <img src="{{ asset('assets/media/logos/mpp_logo_premium.png') }}" alt="Logo"
                            onerror="this.onerror=null;this.src='{{ asset('assets/media/logos/logo_deliserdang.png') }}';">
                        <span>{{ $settings['footer_brand'] ?? 'Sistem Layanan Publik Terpadu' }}</span>
                    </div>
                    <p>{{ $settings['footer_description'] ?? '' }}</p>
                </div>

                <div>
                    <h4>Kontak Kami</h4>
                    <div class="ao-footer-item">
                        <span class="ao-fic"><i class="ph ph-envelope-simple"></i></span>
                        <span><span class="ao-lbl">Email</span><br><span class="ao-val">{{ $settings['footer_email'] ?? '-' }}</span></span>
                    </div>
                    <div class="ao-footer-item">
                        <span class="ao-fic"><i class="ph ph-phone"></i></span>
                        <span><span class="ao-lbl">Telepon</span><br><span class="ao-val">{{ $settings['footer_phone'] ?? '-' }}</span></span>
                    </div>
                </div>

                <div>
                    <h4>Alamat</h4>
                    <p class="ao-val" style="color:#fff;font-weight:600;font-size:14px;">{!! nl2br(e($settings['footer_address'] ?? '-')) !!}</p>
                </div>
            </div>

            <div class="ao-footer-sep"></div>

            <div class="ao-footer-bottom">
                <div class="ao-copy">{{ $settings['footer_copyright'] ?? '' }}</div>
                <div class="ao-socials">
                    @foreach ($socials as $soc)
                        <a class="ao-soc" href="{{ $soc->url }}" target="_blank" rel="noopener noreferrer"
                            aria-label="{{ $soc->label ?? $soc->platform }}">
                            @include('antrian_online.partials.social_icon', ['platform' => $soc->platform])
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>

    {{-- ============ MODAL DAFTAR LAYANAN ============ --}}
    <div class="ao-modal" id="aoSvcModal" role="dialog" aria-modal="true" aria-labelledby="aoSvcTitle">
        <div class="ao-modal-backdrop" data-ao-close></div>
        <div class="ao-modal-box" role="document">
            <button type="button" class="ao-modal-x" data-ao-close aria-label="Tutup">
                <i class="ph ph-x"></i>
            </button>
            <div class="ao-modal-head">
                <span class="ao-modal-ic"><i class="ph ph-buildings"></i></span>
                <div>
                    <div class="ao-modal-title" id="aoSvcTitle">Layanan</div>
                    <div class="ao-modal-sub" id="aoSvcSub"></div>
                </div>
            </div>
            <ul class="ao-modal-list" id="aoSvcList"></ul>
        </div>
    </div>

    <div class="ao-toast" id="aoToast"></div>

    <script>
        (function () {
            // Navbar melebar saat scroll
            var wrap = document.getElementById('aoNavWrap');
            function onScroll() {
                if (window.scrollY > 30) wrap.classList.add('ao-scrolled');
                else wrap.classList.remove('ao-scrolled');
            }
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();

            // Burger menu (mobile)
            var burger = document.getElementById('aoBurger');
            var menu = document.getElementById('aoMenu');
            if (burger) {
                burger.addEventListener('click', function () {
                    var open = menu.classList.toggle('open');
                    burger.setAttribute('aria-expanded', open ? 'true' : 'false');
                });
                menu.querySelectorAll('a').forEach(function (a) {
                    a.addEventListener('click', function () { menu.classList.remove('open'); });
                });
            }

            // Toast helper
            var toastEl = document.getElementById('aoToast');
            window.aoToast = function (msg) {
                toastEl.textContent = msg;
                toastEl.classList.add('show');
                clearTimeout(window.__aoToastT);
                window.__aoToastT = setTimeout(function () { toastEl.classList.remove('show'); }, 2800);
            };
            @if (session('error'))
                window.addEventListener('load', function () { window.aoToast(@json(session('error'))); });
            @endif

            // Modal daftar layanan per-tenant
            var svcModal = document.getElementById('aoSvcModal');
            var svcList = document.getElementById('aoSvcList');
            var svcTitle = document.getElementById('aoSvcTitle');
            var svcSub = document.getElementById('aoSvcSub');
            function esc(s) { return String(s).replace(/[&<>]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;' }[c]; }); }
            function openSvc(name, items) {
                if (!svcModal) return;
                svcTitle.textContent = name || 'Layanan';
                svcSub.textContent = items.length + ' layanan tersedia';
                if (items.length) {
                    svcList.innerHTML = items.map(function (it, i) {
                        return '<li><span class="ao-modal-num">' + (i + 1) + '</span><span>' + esc(it) + '</span></li>';
                    }).join('');
                } else {
                    svcList.innerHTML = '<li class="ao-modal-empty">Belum ada layanan terdaftar.</li>';
                }
                svcModal.classList.add('open');
                document.body.style.overflow = 'hidden';
            }
            function closeSvc() {
                if (!svcModal) return;
                svcModal.classList.remove('open');
                document.body.style.overflow = '';
            }
            document.addEventListener('click', function (e) {
                var btn = e.target.closest('.ao-info');
                if (btn) {
                    var items = [];
                    try { items = JSON.parse(btn.getAttribute('data-layanan') || '[]'); } catch (_) {}
                    openSvc(btn.getAttribute('data-tenant'), items);
                    return;
                }
                if (e.target.closest('[data-ao-close]')) closeSvc();
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && svcModal && svcModal.classList.contains('open')) closeSvc();
            });
        })();
    </script>
    @stack('scripts')
</body>

</html>
