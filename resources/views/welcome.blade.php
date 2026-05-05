<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Informasi Layanan Publik Terpadu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal Resmi Sistem Informasi Layanan Publik Terpadu">
    
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/mpp_logo_premium.png') }}" />
    
    {{-- Phosphor Icons --}}
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --surface: #ffffff;
            --background: #fdfdfd;
            --primary: #111827;
            --secondary: #6b7280;
            --accent: #4f46e5;
            --accent-light: #e0e7ff;
            --radius: 24px;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--background);
            color: var(--primary);
            scroll-behavior: smooth;
        }

        /* Navbar */
        .navbar-custom {
            padding: 24px 0;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .navbar-logo {
            height: 48px;
            width: 48px;
            object-fit: contain;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .nav-link {
            color: var(--secondary) !important;
            font-weight: 500;
            font-size: 1rem;
            padding: 8px 16px !important;
            border-radius: 100px;
            transition: all 0.2s;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--primary) !important;
            background: rgba(0,0,0,0.04);
        }

        /* Hero */
        .hero-section {
            padding: 160px 0 100px;
            background: radial-gradient(circle at 50% 0%, rgba(79, 70, 229, 0.05) 0%, transparent 70%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            text-align: center;
        }

        .hero-title {
            font-size: 5rem;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -2px;
            margin-bottom: 24px;
            background: linear-gradient(135deg, #111827 0%, #4f46e5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            color: var(--secondary);
            max-width: 800px;
            margin: 0 auto 48px;
            line-height: 1.6;
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
            gap: 12px;
            border: none;
        }
        .btn-apple:hover {
            background: #000;
            color: white;
            transform: scale(1.02);
        }

        .btn-apple-outline {
            background: transparent;
            color: var(--primary);
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 100px;
            padding: 16px 32px;
            font-weight: 600;
            font-size: 1.125rem;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 12px;
        }
        .btn-apple-outline:hover {
            background: rgba(0,0,0,0.03);
            color: var(--primary);
        }

        /* Features */
        .feature-card {
            background: var(--surface);
            border-radius: 32px;
            padding: 48px;
            border: 1px solid rgba(0,0,0,0.04);
            box-shadow: 0 20px 40px -20px rgba(0,0,0,0.05);
            height: 100%;
            transition: all 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 60px -20px rgba(0,0,0,0.1);
        }

        .feature-icon {
            width: 64px;
            height: 64px;
            background: var(--accent-light);
            color: var(--accent);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 32px;
        }

        /* Info Section */
        .info-section {
            background: var(--primary);
            border-radius: 40px;
            padding: 80px;
            color: white;
            margin: 100px 20px;
        }

        .contact-box {
            background: rgba(255,255,255,0.1);
            border-radius: 24px;
            padding: 32px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
        }

        /* Footer */
        .footer {
            padding: 60px 0;
            border-top: 1px solid rgba(0,0,0,0.05);
            background: var(--surface);
        }

        @media (max-width: 991px) {
            .hero-title { font-size: 3rem; }
            .info-section { padding: 40px; margin: 40px 10px; }
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg fixed-top navbar-custom" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="{{ route('landing') }}">
                <img src="{{ asset('assets/media/logos/mpp_logo_premium.png') }}" alt="Logo" class="navbar-logo">
                <div>
                    <span class="fw-black fs-4 d-block lh-1 text-gray-900" style="letter-spacing: -0.5px;">SISTEM MPP</span>
                    <span class="fs-8 text-gray-500 fw-medium">Layanan Publik</span>
                </div>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="ph ph-list fs-1 text-dark"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto gap-2">
                    <li class="nav-item"><a class="nav-link active" href="{{ route('landing') }}">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('daftar.instansi') }}">Instansi</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Keunggulan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Informasi</a></li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('skm.index') }}" class="nav-link"><i class="ph ph-chart-line-up fs-4 me-1 align-middle"></i> Survey</a>
                    <a href="/login" class="btn btn-apple-outline px-4 py-2" style="font-size: 0.9rem;">Masuk</a>
                </div>
            </div>
        </div>
    </nav>

    <section class="hero-section" id="home">
        <div class="container">
            <span class="badge bg-white text-dark border shadow-sm px-4 py-2 rounded-pill fw-bold mb-6 d-inline-flex align-items-center gap-2">
                <i class="ph-fill ph-sparkle text-warning"></i> Transformasi Layanan Digital
            </span>
            <h1 class="hero-title">Satu Pintu.<br>Ribuan Kemudahan.</h1>
            <p class="hero-subtitle">Platform layanan publik terpadu yang didesain untuk kenyamanan, kecepatan, dan transparansi yang belum pernah ada sebelumnya.</p>
            <div class="d-flex justify-content-center gap-4 flex-wrap mt-8">
                <a href="{{ route('daftar.instansi') }}" class="btn-apple">
                    Jelajahi Layanan <i class="ph ph-arrow-right"></i>
                </a>
                <a href="#features" class="btn-apple-outline">
                    Pelajari Lebih Lanjut
                </a>
            </div>
            
            <div class="mt-15">
                <p class="text-gray-400 fw-medium mb-5 text-uppercase letter-spacing-1 fs-8">Dipercaya Oleh Berbagai Instansi</p>
                <div class="d-flex justify-content-center gap-6 flex-wrap opacity-50">
                    <i class="ph-fill ph-bank fs-2x"></i>
                    <i class="ph-fill ph-buildings fs-2x"></i>
                    <i class="ph-fill ph-hospital fs-2x"></i>
                    <i class="ph-fill ph-storefront fs-2x"></i>
                    <i class="ph-fill ph-shield-check fs-2x"></i>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20" id="features">
        <div class="container">
            <div class="text-center mb-15">
                <h2 class="fs-1 fw-black text-gray-900 mb-3" style="letter-spacing: -1px;">Didesain Untuk Anda.</h2>
                <p class="fs-5 text-gray-500">Memberikan pengalaman layanan publik yang paling efisien.</p>
            </div>

            <div class="row g-8">
                <div class="col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="ph-fill ph-rocket-launch"></i></div>
                        <h3 class="fw-bold fs-3 mb-4">Cepat & Responsif</h3>
                        <p class="text-gray-500 fs-5 lh-base">Arsitektur sistem modern yang menjamin setiap interaksi layanan berjalan seketika tanpa waktu tunggu.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="ph-fill ph-intersect"></i></div>
                        <h3 class="fw-bold fs-3 mb-4">Terintegrasi</h3>
                        <p class="text-gray-500 fs-5 lh-base">Semua data dan instansi terhubung dalam satu portal utama, menghapus birokrasi berulang yang membosankan.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="ph-fill ph-shield-check"></i></div>
                        <h3 class="fw-bold fs-3 mb-4">Transparan & Aman</h3>
                        <p class="text-gray-500 fs-5 lh-base">Seluruh proses dapat dilacak dengan jaminan keamanan data standar tinggi, memberikan Anda ketenangan pikiran.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact">
        <div class="container">
            <div class="info-section">
                <div class="row g-10 align-items-center">
                    <div class="col-lg-6">
                        <h2 class="fw-black fs-2x mb-6">Siap Melayani<br>Kebutuhan Anda.</h2>
                        <p class="fs-5 opacity-75 mb-10 lh-lg">Pusat Layanan Publik Terpadu hadir lebih dekat dengan Anda. Hubungi kami jika Anda membutuhkan bantuan lebih lanjut.</p>
                        
                        <div class="d-flex gap-4">
                            <a href="#" class="btn btn-light text-dark rounded-pill px-6 py-3 fw-bold"><i class="ph-fill ph-envelope me-2"></i> Kirim Pesan</a>
                            <a href="#" class="btn btn-outline-light rounded-pill px-6 py-3 fw-bold"><i class="ph-fill ph-phone-call me-2"></i> Hubungi</a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="contact-box mb-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="ph-fill ph-clock fs-2 text-white opacity-50 me-3"></i>
                                <h4 class="fw-bold mb-0 text-white">Jam Operasional</h4>
                            </div>
                            <div class="mt-4 ms-9 opacity-75 fs-5">
                                <div class="d-flex justify-content-between mb-2"><span>Senin - Kamis</span><span>08:00 - 15:00</span></div>
                                <div class="d-flex justify-content-between mb-2"><span>Jumat</span><span>08:00 - 15:30</span></div>
                                <div class="d-flex justify-content-between"><span>Sabtu - Minggu</span><span class="text-white fw-bold">TUTUP</span></div>
                            </div>
                        </div>
                        <div class="contact-box">
                            <div class="d-flex align-items-center">
                                <i class="ph-fill ph-map-pin fs-2 text-white opacity-50 me-3"></i>
                                <div>
                                    <h4 class="fw-bold mb-1 text-white">Lokasi Pusat</h4>
                                    <p class="mb-0 opacity-75 fs-6">Gedung Pelayanan Publik Utama.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="row g-8 mb-10">
                <div class="col-lg-5">
                    <img src="{{ asset('assets/media/logos/mpp_logo_premium.png') }}" width="48" alt="Logo" class="mb-5 rounded-3 shadow-sm bg-white p-1">
                    <h3 class="fw-black fs-4 mb-3 text-gray-900">Sistem Layanan Terpadu</h3>
                    <p class="text-gray-500 fs-6 pe-lg-10">Menciptakan ekosistem layanan publik yang modern, responsif, dan mudah diakses oleh seluruh lapisan masyarakat.</p>
                </div>
                <div class="col-lg-2 ms-auto">
                    <h4 class="fw-bold fs-6 mb-5 text-gray-900">Tautan Cepat</h4>
                    <ul class="list-unstyled d-flex flex-column gap-3">
                        <li><a href="#home" class="text-gray-500 text-hover-primary text-decoration-none">Beranda</a></li>
                        <li><a href="{{ route('daftar.instansi') }}" class="text-gray-500 text-hover-primary text-decoration-none">Instansi</a></li>
                        <li><a href="{{ route('skm.index') }}" class="text-gray-500 text-hover-primary text-decoration-none">Survey Kepuasan</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h4 class="fw-bold fs-6 mb-5 text-gray-900">Bantuan & Sosial</h4>
                    <ul class="list-unstyled d-flex flex-column gap-3 mb-6">
                        <li><a href="#" class="text-gray-500 text-hover-primary text-decoration-none">Pusat Bantuan</a></li>
                        <li><a href="#" class="text-gray-500 text-hover-primary text-decoration-none">Kebijakan Privasi</a></li>
                    </ul>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-icon btn-light rounded-circle text-gray-600"><i class="ph-fill ph-facebook-logo fs-4"></i></a>
                        <a href="#" class="btn btn-icon btn-light rounded-circle text-gray-600"><i class="ph-fill ph-instagram-logo fs-4"></i></a>
                        <a href="#" class="btn btn-icon btn-light rounded-circle text-gray-600"><i class="ph-fill ph-twitter-logo fs-4"></i></a>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center border-top pt-8">
                <span class="text-gray-400 fw-medium fs-7">&copy; {{ date('Y') }} Sistem MPP. Hak Cipta Dilindungi.</span>
                <span class="text-gray-400 fw-medium fs-7 mt-3 mt-md-0">Didesain dengan <i class="ph-fill ph-heart text-danger"></i> untuk Pelayanan Publik</span>
            </div>
        </div>
    </footer>

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
</body>
</html>
