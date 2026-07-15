<!DOCTYPE html>
<html lang="id" class="notranslate" translate="no">

<head>
    <meta charset="UTF-8">
    <title>Display Monitor Antrian - MPP Kabupaten Deli Serdang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google" content="notranslate">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Asset Metronic --}}
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/logo_deliserdang.png') }}" />

    {{-- Google Fonts: Outfit (font utama desain Sistem MPP) --}}
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- FontAwesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    {{-- Load Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-gradient: radial-gradient(circle at top right, #1e1b4b 0%, #111827 45%, #000000 100%);
            --text-color: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.6);
            --header-title: #ffffff;
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
            --glass-shadow: rgba(0, 0, 0, 0.4);
            --glass-primary-bg: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(67, 56, 202, 0.15) 100%);
            --glass-primary-border: rgba(99, 102, 241, 0.25);
            --glass-primary-shadow: rgba(99, 102, 241, 0.2);
            --queue-num-color: #ffffff;
            --queue-num-shadow: rgba(99, 102, 241, 0.5);
            --mini-card-bg: rgba(255, 255, 255, 0.02);
            --mini-card-border: rgba(255, 255, 255, 0.05);
            --mini-card-text: #ffffff;
            --ticker-bg: rgba(0, 0, 0, 0.65);
            --ticker-border: rgba(255, 255, 255, 0.08);
            --ticker-text-color: rgba(255, 255, 255, 0.85);
            --separator-color: rgba(255, 255, 255, 0.1);
        }

        :root.theme-light {
            --bg-gradient: radial-gradient(circle at top right, #e2f1ff 0%, #f1f5f9 45%, #cbd5e1 100%);
            --text-color: #1e293b;
            --text-muted: rgba(30, 41, 59, 0.6);
            --header-title: #0f172a;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(0, 0, 0, 0.08);
            --glass-shadow: rgba(0, 0, 0, 0.08);
            --glass-primary-bg: linear-gradient(135deg, rgba(99, 102, 241, 0.06) 0%, rgba(67, 56, 202, 0.1) 100%);
            --glass-primary-border: rgba(99, 102, 241, 0.35);
            --glass-primary-shadow: rgba(99, 102, 241, 0.1);
            --queue-num-color: #0f172a;
            --queue-num-shadow: rgba(99, 102, 241, 0.15);
            --mini-card-bg: rgba(0, 0, 0, 0.02);
            --mini-card-border: rgba(0, 0, 0, 0.05);
            --mini-card-text: #1e293b;
            --ticker-bg: rgba(255, 255, 255, 0.9);
            --ticker-border: rgba(0, 0, 0, 0.08);
            --ticker-text-color: #1e293b;
            --separator-color: rgba(0, 0, 0, 0.08);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-gradient);
            color: var(--text-color);
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: background 0.5s ease, color 0.5s ease;
        }

        .main-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 20px;
            height: calc(100vh - 50px); /* Leave space for bottom ticker */
        }

        /* Glassmorphism Cards */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: 0 15px 35px var(--glass-shadow);
            transition: all 0.5s ease;
            overflow: hidden;
        }

        .glass-card-primary {
            background: var(--glass-primary-bg);
            border: 1px solid var(--glass-primary-border);
            box-shadow: 0 20px 45px var(--glass-primary-shadow);
        }

        /* Large Calling Card */
        .calling-card {
            position: relative;
        }

        .calling-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #6366f1, #50cd89, #ffc700, #6366f1);
            background-size: 300% 100%;
            animation: gradient-flow 5s linear infinite;
        }

        @keyframes gradient-flow {
            0% { background-position: 0% 50%; }
            100% { background-position: 300% 50%; }
        }

        .queue-number {
            font-size: 7.5rem;
            font-weight: 900;
            letter-spacing: -2px;
            color: var(--queue-num-color);
            text-shadow: 0 0 30px var(--queue-num-shadow);
            line-height: 1;
            transition: color 0.5s ease, text-shadow 0.5s ease;
        }

        .queue-number.calling-active {
            animation: pulse-glow 1s infinite alternate;
        }

        @keyframes pulse-glow {
            0% {
                transform: scale(1);
                text-shadow: 0 0 30px rgba(99, 102, 241, 0.5), 0 0 60px rgba(99, 102, 241, 0.2);
            }
            100% {
                transform: scale(1.05);
                text-shadow: 0 0 50px rgba(80, 205, 137, 0.8), 0 0 100px rgba(80, 205, 137, 0.4);
                color: #50cd89;
            }
        }

        /* Header TV */
        .tv-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--separator-color);
            margin-bottom: 20px;
            transition: border-bottom 0.5s ease;
        }

        .clock-container {
            text-align: right;
        }

        #clock-time {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--text-color);
            line-height: 1;
            font-feature-settings: "tnum";
            transition: color 0.5s ease;
        }

        #clock-date {
            font-size: 0.95rem;
            color: var(--text-muted);
            font-weight: 600;
            margin-top: 4px;
            transition: color 0.5s ease;
        }

        /* Theme Switcher Button style */
        .btn-light-glass {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #ffffff;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
        }
        
        .btn-light-glass:hover {
            background: rgba(255, 255, 255, 0.18);
            transform: scale(1.08);
        }

        :root.theme-light .btn-light-glass {
            background: rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: #1e293b;
        }

        :root.theme-light .btn-light-glass:hover {
            background: rgba(0, 0, 0, 0.10);
        }

        /* News Ticker */
        .news-ticker {
            height: 50px;
            background: var(--ticker-bg);
            border-top: 1px solid var(--ticker-border);
            display: flex;
            align-items: center;
            overflow: hidden;
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.15);
            transition: background 0.5s ease, border-top 0.5s ease;
        }

        .ticker-title {
            background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
            color: white;
            padding: 0 25px;
            height: 100%;
            display: flex;
            align-items: center;
            z-index: 10;
            box-shadow: 5px 0 15px rgba(0,0,0,0.15);
            white-space: nowrap;
        }

        .ticker-content {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            box-sizing: border-box;
            position: relative;
        }

        .ticker-text {
            display: inline-block;
            padding-left: 100%;
            animation: marquee 25s linear infinite;
            color: var(--ticker-text-color);
            transition: color 0.5s ease;
        }

        @keyframes marquee {
            0% { transform: translate3d(0, 0, 0); }
            100% { transform: translate3d(-100%, 0, 0); }
        }

        /* Video Embed Aspect Ratio */
        .video-container {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 */
            height: 0;
            overflow: hidden;
            border-radius: 16px;
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        /* Slider Image Ads */
        .ad-slider {
            height: 100%;
            position: relative;
            border-radius: 16px;
            overflow: hidden;
        }

        .ad-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            background-size: cover;
            background-position: center;
        }

        .ad-slide.active {
            opacity: 1;
        }

        /* Micro Indicator for Slider */
        .slider-indicator {
            position: absolute;
            bottom: 12px;
            right: 15px;
            display: flex;
            gap: 6px;
            z-index: 10;
        }

        .slider-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .slider-dot.active {
            background: #6366f1;
            transform: scale(1.2);
            width: 16px;
            border-radius: 4px;
        }

        /* Mini Cards for Lists */
        .mini-card {
            background: var(--mini-card-bg);
            border: 1px solid var(--mini-card-border);
            color: var(--mini-card-text);
            border-radius: 16px;
            padding: 12px 18px;
            transition: all 0.3s ease;
        }

        .mini-card:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: translateY(-2px);
        }

        :root.theme-light .mini-card:hover {
            background: rgba(0, 0, 0, 0.04);
        }

        .mini-card.active-call {
            background: rgba(80, 205, 137, 0.08);
            border: 1px solid rgba(80, 205, 137, 0.2);
            animation: mini-pulse 1.5s infinite;
        }

        @keyframes mini-pulse {
            0% { box-shadow: 0 0 0 0 rgba(80, 205, 137, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(80, 205, 137, 0); }
            100% { box-shadow: 0 0 0 0 rgba(80, 205, 137, 0); }
        }

        /* Animate Icon calling */
        .pulse-speaker {
            display: inline-block;
            animation: speak-wave 1s infinite alternate;
        }

        @keyframes speak-wave {
            0% { transform: scale(1); opacity: 0.7; }
            100% { transform: scale(1.15); opacity: 1; color: #ffc700; }
        }

        .tv-title {
            color: var(--header-title) !important;
            transition: color 0.5s ease;
        }

        .tv-card-header {
            color: var(--header-title) !important;
            transition: color 0.5s ease;
        }

        .main-skpd-text {
            color: var(--text-color) !important;
            transition: color 0.5s ease;
        }

        .hint-text {
            color: var(--text-muted) !important;
            transition: color 0.5s ease;
        }
    </style>
</head>

<body>
    <!--begin::Autoplay Unlock Overlay-->
    <div id="autoplay-overlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center" style="background: radial-gradient(circle at center, rgba(10, 25, 49, 0.98) 0%, rgba(0, 0, 0, 0.99) 100%); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); z-index: 999999; transition: opacity 0.5s ease;">
        <div class="glass-card p-10 text-center d-flex flex-column align-items-center border border-warning border-opacity-25 shadow-lg" style="max-width: 550px; background: rgba(255, 255, 255, 0.03); box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5); border-radius: 24px;">
            <div class="symbol symbol-80px mb-6">
                <span class="symbol-label bg-light-warning text-warning rounded-circle" style="width: 80px; height: 80px; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 0 30px rgba(255, 199, 0, 0.25);">
                    <i class="fa fa-volume-high pulse-speaker text-warning" style="font-size: 2.8rem !important; animation: speak-wave 1s infinite alternate;"></i>
                </span>
            </div>
            <h2 class="text-white fw-boldest fs-1 mb-4 text-uppercase tracking-wider">AKTIFKAN SUARA MONITOR</h2>
            <p class="text-white text-opacity-70 fs-5 mb-8 px-4 lh-lg">
                Sistem Panggilan Suara TV Display memerlukan interaksi pengguna awal agar audio chime dan suara robot panggilan (Text-To-Speech) dapat aktif.
            </p>
            <button id="btn-unlock-autoplay" class="btn btn-warning btn-lg fw-boldest text-dark px-10 py-4 rounded-pill shadow-lg hover-scale fs-5" style="transition: all 0.3s ease;">
                <i class="fa fa-play text-dark me-2"></i> AKTIFKAN SUARA
            </button>
        </div>
    </div>
    <!--end::Autoplay Unlock Overlay-->
    {{-- Wrapper Utama --}}
    <div class="main-container">

        {{-- TV Header --}}
        <div class="tv-header">
            <div class="d-flex align-items-center gap-4">
                <img src="{{ asset('images/logo_pemda.png') }}" alt="Logo Deli Serdang" style="height: 50px !important; width: auto !important; object-fit: contain; filter: drop-shadow(0 2px 5px rgba(0,0,0,0.3));">
                <div>
                    <h1 class="tv-title fw-boldest fs-3 fs-lg-2 mb-0 lh-sm text-uppercase tracking-wider">MALL PELAYANAN PUBLIK</h1>
                    <span class="text-primary fw-boldest fs-6 text-uppercase ls-3">KABUPATEN DELI SERDANG</span>
                </div>
            </div>

            {{-- Jam, Tanggal & Theme Switcher --}}
            <div class="d-flex align-items-center gap-5">
                <button id="theme-toggle-btn" class="btn btn-light-glass rounded-circle shadow-xs" title="Ganti Tema (Light/Dark)" style="width: 46px; height: 46px; border: 1px solid var(--glass-border); background: var(--glass-bg);">
                    <i id="theme-icon" class="fa fa-moon fs-4 text-warning"></i>
                </button>

                <div class="clock-container">
                    <div id="clock-time">00:00:00</div>
                    <div id="clock-date">Memuat Tanggal...</div>
                </div>
            </div>
        </div>

        {{-- Layout Grid --}}
        <div class="row g-6 flex-grow-1 overflow-hidden" style="min-height: 0;">

            {{-- KIRI: Panggilan Utama (Current Call) --}}
            <div class="col-xl-5 d-flex flex-column h-100">
                <div class="glass-card glass-card-primary calling-card flex-grow-1 p-8 d-flex flex-column justify-content-between align-items-center text-center position-relative">
                    
                    {{-- Badge Status --}}
                    <div>
                        <span class="badge badge-lg bg-success text-white fw-boldest px-6 py-3 rounded-pill fs-7 shadow-sm">
                            <i class="fa fa-volume-high text-white me-2 pulse-speaker"></i> 
                            <span id="call-status-badge">SEDANG DIPANGGIL</span>
                        </span>
                    </div>

                    {{-- Konten Antrian --}}
                    <div id="current-call-container" class="w-100 my-auto">
                        <div id="main-number" class="queue-number">---</div>
                        
                        <div class="separator my-6 w-75 mx-auto" style="border-bottom: 1px solid var(--separator-color); transition: border-bottom 0.5s ease;"></div>

                        {{-- Nama SKPD/Dinas --}}
                        <h2 id="main-skpd" class="main-skpd-text fw-extrabold fs-2 fs-lg-1 text-uppercase text-truncate px-3 mb-3">
                            BELUM ADA PANGGILAN
                        </h2>

                        {{-- Nama Loket --}}
                        <div class="d-inline-block bg-white bg-opacity-10 rounded-pill px-6 py-2 border border-white border-opacity-10">
                            <span id="main-loket" class="fs-4 fw-bolder text-warning text-uppercase tracking-wide">
                                LOKET KOSONG
                            </span>
                        </div>
                    </div>

                    {{-- Running hint --}}
                    <div class="hint-text fs-9 fw-semibold">
                        *Silakan langsung menuju ke loket instansi yang tertera di atas
                    </div>
                </div>
            </div>

            {{-- TENGAH: Media & Promosi (Video & Banner Iklan) --}}
            <div class="col-xl-4 d-flex flex-column h-100 justify-content-between gap-6">
                
                {{-- Card 1: Video Profile --}}
                <div class="glass-card p-4">
                    <div class="video-container">
                        {{-- Video Embed: Dynamic YouTube Video ID from Backend settings --}}
                        <iframe id="embed-video" src="https://www.youtube.com/embed/{{ $youtubeId }}?autoplay=1&mute=1&loop=1&playlist={{ $youtubeId }}&controls=0&showinfo=0&rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                    </div>
                </div>

                {{-- Card 2: Banner Iklan Slider --}}
                <div class="glass-card flex-grow-1 p-4 position-relative" style="min-height: 200px;">
                    <div class="ad-slider" id="slider-container">
                        @foreach ($iklanImages as $index => $img)
                            <div class="ad-slide {{ $index === 0 ? 'active' : '' }}" style="background-image: url('{{ $img }}');"></div>
                        @endforeach

                        {{-- Indicators --}}
                        <div class="slider-indicator" id="slider-indicators">
                            @foreach ($iklanImages as $index => $img)
                                <div class="slider-dot {{ $index === 0 ? 'active' : '' }}"></div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- KANAN: Daftar Antrian (Previous Called & Next Candidate) --}}
            <div class="col-xl-3 d-flex flex-column h-100 justify-content-between gap-6">

                {{-- Card 1: Antrian Lain (History) --}}
                <div class="glass-card p-6 flex-grow-1 d-flex flex-column" style="height: 50%; min-height: 250px;">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h3 class="tv-card-header fw-boldest fs-5 mb-0 text-uppercase tracking-wider">
                            <i class="fa fa-history text-info me-2"></i> Panggilan Sebelumnya
                        </h3>
                    </div>

                    <div id="history-container" class="d-flex flex-column gap-3 overflow-y-auto pr-1 flex-grow-1" style="max-height: 220px;">
                        {{-- Dynamically loaded --}}
                        <div class="text-center text-muted py-8 fs-7">Memuat riwayat...</div>
                    </div>
                </div>

                {{-- Card 2: Antrian Selanjutnya --}}
                <div class="glass-card p-6 flex-grow-1 d-flex flex-column" style="height: 50%; min-height: 250px;">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h3 class="tv-card-header fw-boldest fs-5 mb-0 text-uppercase tracking-wider">
                            <i class="fa fa-users text-warning me-2"></i> Giliran Berikutnya
                        </h3>
                        <span class="badge bg-light-warning text-warning fw-bolder fs-9 rounded-pill px-3 py-1">Standby</span>
                    </div>

                    <div id="next-container" class="d-flex flex-column gap-3 overflow-y-auto pr-1 flex-grow-1" style="max-height: 220px;">
                        {{-- Dynamically loaded --}}
                        <div class="text-center text-muted py-8 fs-7">Memuat data berikutnya...</div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- News Ticker (Bottom Bar) --}}
    <div class="news-ticker">
        <div class="ticker-title">
            <i class="fa fa-bullhorn me-2"></i> INFORMASI MPP
        </div>
        <div class="ticker-content">
            <div class="ticker-text" id="ticker-marquee-text">
                {{ $tickerText }}
            </div>
        </div>
    </div>

    {{-- Audio Chime --}}
    <audio id="tingtung" src="{{ asset('assets/audio/tingtung.mp3') }}"></audio>

    {{-- Scripts Bundle Metronic --}}
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>

    <script type="module">
        const bell = document.getElementById('tingtung');
        let speechQueue = [];
        let isSpeaking = false;

        // ==========================================
        // DYNAMIC CLOCK & DATE
        // ==========================================
        function updateTVClock() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            const hrs = String(now.getHours()).padStart(2, '0');
            const mins = String(now.getMinutes()).padStart(2, '0');
            const secs = String(now.getSeconds()).padStart(2, '0');

            document.getElementById('clock-time').innerText = `${hrs}:${mins}:${secs}`;
            document.getElementById('clock-date').innerText = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
        }
        setInterval(updateTVClock, 1000);
        updateTVClock();

        // ==========================================
        // AUTO ADVERTISING SLIDER CAROUSEL
        // ==========================================
        function initSlider() {
            const slides = document.querySelectorAll('.ad-slide');
            const dots = document.querySelectorAll('.slider-dot');
            if (slides.length <= 1) return;

            let currentSlide = 0;

            setInterval(() => {
                slides[currentSlide].classList.remove('active');
                dots[currentSlide].classList.remove('active');
                
                currentSlide = (currentSlide + 1) % slides.length;
                
                slides[currentSlide].classList.add('active');
                dots[currentSlide].classList.add('active');
            }, 6000); // 6 seconds slide interval
        }
        initSlider();

        // ==========================================
        // FETCH DATA FROM SERVER
        // ==========================================
        function fetchDisplayData() {
            fetch("{{ route('api.display.data') }}")
                .then(res => res.json())
                .then(data => {
                    updateDisplayUI(data);
                })
                .catch(err => console.error("Error fetching display data", err));
        }

        function updateDisplayUI(data) {
            // Update Ticker Text & YouTube Video if changed dynamically in backend
            if (data.tickerText) {
                const marquee = document.getElementById('ticker-marquee-text');
                if (marquee && marquee.innerText.trim() !== data.tickerText.trim()) {
                    marquee.innerText = data.tickerText;
                }
            }
            if (data.youtubeId) {
                const iframe = document.getElementById('embed-video');
                if (iframe) {
                    const currentSrc = iframe.getAttribute('src');
                    const expectedSrc = `https://www.youtube.com/embed/${data.youtubeId}?autoplay=1&mute=1&loop=1&playlist=${data.youtubeId}&controls=0&showinfo=0&rel=0`;
                    // Only reload iframe if the ID actually changed to prevent flickering
                    if (!currentSrc.includes(data.youtubeId)) {
                        iframe.setAttribute('src', expectedSrc);
                    }
                }
            }

            // 1. Update Current Call (Big Card)
            const mainNum = document.getElementById('main-number');
            const mainSkpd = document.getElementById('main-skpd');
            const mainLoket = document.getElementById('main-loket');

            if (data.current) {
                // If it's a new call that wasn't already in the card
                if (mainNum.innerText !== data.current.no_antrian) {
                    mainNum.innerText = data.current.no_antrian;
                    mainSkpd.innerText = data.current.nama_skpd;
                    mainLoket.innerText = data.current.nama_loket;
                    
                    // Flash animation trigger
                    mainNum.classList.add('calling-active');
                    setTimeout(() => {
                        mainNum.classList.remove('calling-active');
                    }, 5000);
                }
            } else {
                mainNum.innerText = "---";
                mainSkpd.innerText = "BELUM ADA PANGGILAN";
                mainLoket.innerText = "LOKET KOSONG";
            }

            // 2. Update Previous Called List
            const historyContainer = document.getElementById('history-container');
            if (data.previous && data.previous.length > 0) {
                let html = '';
                data.previous.forEach((item, index) => {
                    html += `
                        <div class="mini-card d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="symbol symbol-30px">
                                    <span class="symbol-label bg-light-info text-info fw-boldest rounded-circle fs-8">${item.no_antrian}</span>
                                </div>
                                <div class="d-flex flex-column text-start">
                                    <span class="fw-bold fs-7 text-uppercase text-truncate" style="max-width: 150px; color: var(--text-color);">${item.nama_skpd}</span>
                                    <span class="fs-9" style="color: var(--text-muted);">${item.nama_loket}</span>
                                </div>
                            </div>
                            <span class="fs-9 fw-semibold" style="color: var(--text-muted);">${item.waktu}</span>
                        </div>
                    `;
                });
                historyContainer.innerHTML = html;
            } else {
                historyContainer.innerHTML = '<div class="text-center text-muted py-8 fs-7">Belum ada riwayat panggilan</div>';
            }

            // 3. Update Next Queue Candidates List
            const nextContainer = document.getElementById('next-container');
            if (data.next && data.next.length > 0) {
                let html = '';
                data.next.forEach((item) => {
                    html += `
                        <div class="mini-card d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="symbol symbol-30px">
                                    <span class="symbol-label bg-light-warning text-warning fw-boldest rounded-circle fs-8">${item.no_antrian}</span>
                                </div>
                                <div class="d-flex flex-column text-start">
                                    <span class="fw-bold fs-7 text-uppercase text-truncate" style="max-width: 150px; color: var(--text-color);">${item.nama_skpd}</span>
                                    <span class="fs-9" style="color: var(--text-muted);">${item.nama_loket}</span>
                                </div>
                            </div>
                            <span class="text-warning opacity-70 fs-9 fw-bold"><i class="fa fa-clock fs-9 me-1"></i>${item.waktu}</span>
                        </div>
                    `;
                });
                nextContainer.innerHTML = html;
            } else {
                nextContainer.innerHTML = '<div class="text-center text-muted py-8 fs-7">Semua antrian bersih</div>';
            }
        }

        // Fetch initial data
        fetchDisplayData();

        // ==========================================
        // TEXT-TO-SPEECH ANNOUNCEMENTS (QUEUE SPEAK)
        // ==========================================
        function putarAudioTV(data) {
            // Cancel current speeches and play chime
            speechSynthesis.cancel();
            speechQueue = [];
            
            bell.pause();
            bell.currentTime = 0;
            bell.play().then(() => {
                // When chime starts, we queue the voice
                let nomorRaw = data.no_antrian.toString().toUpperCase();
                let nomorDieja = nomorRaw.split('').map(char => {
                    if (char === '-') return '';
                    return char + '. ';
                }).join(' ');

                let textNomor = `Nomor antrian... ${nomorDieja}`;
                let textLoket = `Silakan menuju ke... ${data.skpd}... ${data.loket}.`;

                speechQueue.push(textNomor, textLoket, textNomor, textLoket);
                
                // Wait for chime to play (approx 1.5 seconds) then announce
                setTimeout(() => processSpeechQueue(), 1500);
            }).catch(e => {
                console.log("Audio play failed or user interaction required", e);
                // Fallback direct speaking if audio blocked
                let nomorRaw = data.no_antrian.toString().toUpperCase();
                let nomorDieja = nomorRaw.split('').map(char => {
                    if (char === '-') return '';
                    return char + '. ';
                }).join(' ');

                let textNomor = `Nomor antrian... ${nomorDieja}`;
                let textLoket = `Silakan menuju ke... ${data.skpd}... ${data.loket}.`;
                speechQueue.push(textNomor, textLoket, textNomor, textLoket);
                processSpeechQueue();
            });
        }

        // Voice Detection Helper
        let indonesianVoice = null;
        function loadVoices() {
            if (!('speechSynthesis' in window)) return;
            const voices = window.speechSynthesis.getVoices();
            indonesianVoice = voices.find(v => v.lang.includes('id-ID') || v.lang.includes('id_ID'));
            if (indonesianVoice) {
                console.log("🗣️ Indonesian TTS Voice selected:", indonesianVoice.name);
            }
        }
        if ('speechSynthesis' in window) {
            window.speechSynthesis.onvoiceschanged = loadVoices;
            loadVoices();
        }

        function processSpeechQueue() {
            if (speechQueue.length === 0) {
                isSpeaking = false;
                return;
            }
            isSpeaking = true;
            let text = speechQueue.shift();
            let utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            utterance.rate = 0.85;
            if (indonesianVoice) {
                utterance.voice = indonesianVoice;
            }
            utterance.onend = () => setTimeout(() => processSpeechQueue(), 400);
            utterance.onerror = () => setTimeout(() => processSpeechQueue(), 400);
            speechSynthesis.speak(utterance);
        }

        function notifyAnnouncementFinished() {
            fetch("{{ route('api.display.announcement_finished') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).catch(err => console.error("Error posting announcement finished:", err));
        }

        // ==========================================
        // LARAVEL REVERB WEBSOCKET LISTENER
        // ==========================================
        setTimeout(() => {
            if (window.Echo) {
                console.log("📺 Display Monitor TV: Connect to Reverb WebSocket Channel...");
                
                window.Echo.channel('antrian-channel')
                    .listen('.panggilan-baru', (e) => {
                        console.log("🔔 Panggilan baru terdeteksi:", e);
                        
                        let data = e.data;
                        
                        // 1. Play Indonesian TTS Voice Call
                        putarAudioTV(data);
                        
                        // 2. Fetch fresh database data to update current, history and next queues
                        fetchDisplayData();
                    })
                    .listen('.antrian-baru', (e) => {
                        console.log("🎫 Tiket antrian baru dicetak!");
                        fetchDisplayData();
                    })
                    .listen('.panggilan-pengumuman', (e) => {
                        console.log("📢 Pengumuman Suara TV Baru:", e);
                        
                        // 1. Matikan pembacaan TTS yang sedang berjalan
                        speechSynthesis.cancel();
                        
                        // 2. Play chime tingtung pembuka pengumuman
                        bell.pause();
                        bell.currentTime = 0;
                        bell.play().then(() => {
                            // Tunggu chime selesai (1.5 detik) lalu mulai membacakan pengumuman
                            setTimeout(() => {
                                let utterance = new SpeechSynthesisUtterance(e.text);
                                utterance.lang = 'id-ID';
                                utterance.rate = 0.82; // Sedikit lebih santai untuk pengumuman panjang
                                if (indonesianVoice) {
                                    utterance.voice = indonesianVoice;
                                }
                                
                                utterance.onend = function() {
                                    console.log("📢 TTS Pengumuman selesai dibacakan secara alami");
                                    notifyAnnouncementFinished();
                                };
                                utterance.onerror = function(event) {
                                    console.log("📢 TTS Pengumuman error/dibatalkan:", event.error);
                                    notifyAnnouncementFinished();
                                };

                                speechSynthesis.speak(utterance);
                            }, 1500);
                        }).catch(() => {
                            // Fallback jika audio diblokir browser
                            let utterance = new SpeechSynthesisUtterance(e.text);
                            utterance.lang = 'id-ID';
                            utterance.rate = 0.82;
                            if (indonesianVoice) {
                                utterance.voice = indonesianVoice;
                            }

                            utterance.onend = function() {
                                console.log("📢 TTS Pengumuman (fallback) selesai dibacakan secara alami");
                                notifyAnnouncementFinished();
                            };
                            utterance.onerror = function(event) {
                                console.log("📢 TTS Pengumuman (fallback) error/dibatalkan:", event.error);
                                notifyAnnouncementFinished();
                            };

                            speechSynthesis.speak(utterance);
                        });
                    })
                    .listen('.stop-pengumuman', (e) => {
                        console.log("🛑 Penghentian Pengumuman Suara TV diterima");
                        speechSynthesis.cancel();
                    });
            } else {
                console.error("Laravel Echo / Reverb could not be loaded");
            }
        }, 1200);

        // ==========================================
        // DYNAMIC THEME SWITCHER (LIGHT / DARK)
        // ==========================================
        const themeBtn = document.getElementById('theme-toggle-btn');
        const themeIcon = document.getElementById('theme-icon');

        // Load saved theme preference
        const savedTheme = localStorage.getItem('tv-theme') || 'dark';
        if (savedTheme === 'light') {
            document.documentElement.classList.add('theme-light');
            themeIcon.className = 'fa fa-sun fs-4 text-warning';
        } else {
            document.documentElement.classList.remove('theme-light');
            themeIcon.className = 'fa fa-moon fs-4 text-warning';
        }

        themeBtn.addEventListener('click', () => {
            const isLight = document.documentElement.classList.toggle('theme-light');
            if (isLight) {
                themeIcon.className = 'fa fa-sun fs-4 text-warning';
                localStorage.setItem('tv-theme', 'light');
            } else {
                themeIcon.className = 'fa fa-moon fs-4 text-warning';
                localStorage.setItem('tv-theme', 'dark');
            }
        });

        // Interaction bypass for browser autoplay rules via custom glassmorphic overlay
        const overlay = document.getElementById('autoplay-overlay');
        function handleAutoplayUnlock() {
            if (!overlay) return;
            
            // Unlock chime
            bell.play().then(() => {
                bell.pause();
                bell.currentTime = 0;
                console.log("🔊 Audio chime successfully unlocked!");
            }).catch((err) => {
                console.warn("⚠️ Failed to unlock audio chime:", err);
            });

            // Unlock SpeechSynthesis
            if ('speechSynthesis' in window) {
                const dummyUtterance = new SpeechSynthesisUtterance('');
                window.speechSynthesis.speak(dummyUtterance);
                console.log("🗣️ SpeechSynthesis successfully unlocked!");
            }

            // Smooth fade out overlay
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.style.display = 'none';
            }, 500);
        }

        if (overlay) {
            overlay.addEventListener('click', handleAutoplayUnlock, { once: true });
        } else {
            document.body.addEventListener('click', () => {
                bell.play().then(() => {
                    bell.pause();
                    bell.currentTime = 0;
                }).catch(() => {});
            }, { once: true });
        }
    </script>
</body>

</html>
