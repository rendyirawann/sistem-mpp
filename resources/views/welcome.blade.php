<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Mal Pelayanan Publik - Kabupaten Deli Serdang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal Resmi Mal Pelayanan Publik Kabupaten Deli Serdang">
    
    {{-- Asset Bundle (Metronic & FontAwesome) --}}
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/logo_deliserdang.png') }}" />
    
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #009ef7;
            --primary-dark: #0069d9;
            --dark: #1e1e2d;
            --light: #f5f8fa;
            --success: #50cd89;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            scroll-behavior: smooth;
        }

        /* NAVBAR */
        .navbar-custom {
            transition: all 0.3s ease;
            padding: 20px 0;
            background: transparent;
        }
        .navbar-custom.scrolled {
            background: white;
            padding: 10px 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        /* HERO SECTION */
        .hero-section {
            background: linear-gradient(135deg, #009ef7 0%, #0069d9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding-top: 80px;
        }
        .hero-section::before {
            content: "";
            position: absolute;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            top: -100px;
            right: -100px;
        }

        .hero-title {
            font-weight: 800;
            font-size: 3.5rem;
            line-height: 1.1;
            margin-bottom: 1.5rem;
        }

        /* SKPD CARDS (Non-clickable for Preview) */
        .card-skpd-preview {
            border: none;
            border-radius: 20px;
            background: white;
            height: 100%;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.02);
            position: relative;
        }
        .card-skpd-preview .icon-wrapper {
            width: 50px;
            height: 50px;
            background: var(--light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            color: var(--primary);
        }

        /* SECTION STYLES */
        .section-title {
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }
        .section-subtitle {
            color: #7e8299;
            font-size: 1.1rem;
            margin-bottom: 3rem;
        }

        .bg-section-light {
            background-color: #f9fbfe;
        }

        /* FOOTER */
        .footer {
            background: var(--dark);
            color: white;
            padding: 80px 0 30px;
        }

        @media (max-width: 991px) {
            .hero-title { font-size: 2.5rem; }
        }
    </style>
</head>

<body>

    {{-- NAVBAR --}}
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top navbar-custom" id="mainNav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('landing') }}">
                <img src="{{ asset('images/logo_pemda.png') }}" width="40" alt="Logo" class="me-3">
                <div class="d-none d-sm-block">
                    <span class="fw-bolder fs-4 d-block lh-1">MPP</span>
                    <span class="fs-9 text-uppercase opacity-75 ls-1">Deli Serdang</span>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto fw-bold">
                    <li class="nav-item"><a class="nav-link px-4 active" href="{{ route('landing') }}">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link px-4" href="{{ route('daftar.instansi') }}">Daftar Instansi</a></li>
                    <li class="nav-item"><a class="nav-link px-4" href="#about">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link px-4" href="#contact">Kontak</a></li>
                    <li class="nav-item ms-lg-4">
                        <a class="btn btn-white btn-sm rounded-pill px-6" href="{{ route('skm.index') }}">
                            <i class="ki-outline ki-chart-line fs-4 me-1"></i> SKM
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <section class="hero-section text-white" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-10 mb-lg-0">
                    <span class="badge badge-light-warning fs-8 fw-bold px-4 py-2 mb-5 rounded-pill">PORTAL PELAYANAN PUBLIK TERPADU</span>
                    <h1 class="hero-title">Urus Segala Hal Dalam Satu Atap.</h1>
                    <p class="fs-4 opacity-75 mb-10">Selamat datang di Mal Pelayanan Publik Kabupaten Deli Serdang. Kami menghadirkan kecepatan, kemudahan, dan transparansi dalam setiap layanan publik Anda.</p>
                    <div class="d-flex flex-wrap gap-4">
                        <a href="{{ route('daftar.instansi') }}" class="btn btn-white btn-lg px-8 rounded-pill fw-bold">Pilih Layanan</a>
                        <a href="#about" class="btn btn-outline-white btn-lg px-8 rounded-pill fw-bold">Tentang Kami</a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="{{ asset('assets/media/illustrations/sigma-1/17.png') }}" class="img-fluid mw-450px" alt="Hero Illustration">
                </div>
            </div>
        </div>
    </section>

    {{-- SKPD PREVIEW SECTION --}}
    <section class="py-20 bg-section-light" id="skpd">
        <div class="container">
            <div class="text-center mb-15">
                <h2 class="section-title">Instansi Terintegrasi</h2>
                <p class="section-subtitle">Beberapa instansi yang telah bergabung di MPP Deli Serdang.</p>
            </div>

            <div class="row g-6 justify-content-center">
                @foreach($skpd->take(5) as $item)
                <div class="col-md-6 col-lg-2">
                    <div class="card card-skpd-preview shadow-sm text-center">
                        <div class="icon-wrapper mx-auto">
                            <i class="fa fa-building-columns fs-2"></i>
                        </div>
                        <h3 class="fs-7 fw-bolder text-dark mb-0 line-clamp-2" style="min-height: 2.5rem;">{{ $item->nama_skpd }}</h3>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="text-center mt-15">
                <a href="{{ route('daftar.instansi') }}" class="btn btn-primary btn-lg rounded-pill px-10 fw-bold shadow-sm">
                    <i class="fa fa-th-large me-2"></i> LIHAT SEMUA INSTANSI & LAYANAN
                </a>
            </div>
        </div>
    </section>

    {{-- ABOUT SECTION --}}
    <section class="py-20" id="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-10 mb-lg-0">
                    <img src="{{ asset('assets/media/illustrations/sigma-1/2.png') }}" class="img-fluid rounded-4" alt="About Image">
                </div>
                <div class="col-lg-6 ps-lg-15">
                    <h2 class="section-title">Melayani dengan Hati, Profesional, dan Terintegrasi.</h2>
                    <p class="fs-5 text-gray-600 mb-8">Mal Pelayanan Publik (MPP) Deli Serdang merupakan pusat pelayanan publik yang menggabungkan berbagai jenis layanan dari Pemerintah Pusat, Pemerintah Daerah, hingga BUMN/D dalam satu tempat.</p>
                    
                    <div class="d-flex align-items-center mb-5">
                        <div class="symbol symbol-40px me-5">
                            <span class="symbol-label bg-light-primary"><i class="fa fa-check text-primary"></i></span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fw-bolder text-dark fs-6">Efisien & Cepat</span>
                            <span class="text-muted fs-7">Proses pelayanan yang lebih ringkas dan terukur.</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-5">
                        <div class="symbol symbol-40px me-5">
                            <span class="symbol-label bg-light-success"><i class="fa fa-check text-success"></i></span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fw-bolder text-dark fs-6">Satu Pintu</span>
                            <span class="text-muted fs-7">Semua urusan selesai tanpa harus pindah gedung.</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-40px me-5">
                            <span class="symbol-label bg-light-info"><i class="fa fa-check text-info"></i></span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fw-bolder text-dark fs-6">Transparan</span>
                            <span class="text-muted fs-7">Informasi biaya dan syarat yang jelas bagi warga.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CONTACT SECTION --}}
    <section class="py-20 bg-section-light" id="contact">
        <div class="container">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="row g-0">
                    <div class="col-lg-5 bg-dark p-10 p-lg-15 text-white">
                        <h2 class="text-white fw-bolder fs-1 mb-10">Hubungi Kami</h2>
                        
                        <div class="d-flex align-items-center mb-8">
                            <i class="fa fa-map-marker-alt fs-2 text-primary me-5"></i>
                            <div>
                                <h4 class="text-white fw-bold fs-6 mb-1">Alamat</h4>
                                <p class="text-gray-500 fs-7 mb-0">P3UD Deli Serdang, Tanjung Morawa, Kabupaten Deli Serdang, Sumatera Utara.</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-8">
                            <i class="fa fa-phone fs-2 text-primary me-5"></i>
                            <div>
                                <h4 class="text-white fw-bold fs-6 mb-1">Telepon / WhatsApp</h4>
                                <p class="text-gray-500 fs-7 mb-0">0812-xxxx-xxxx</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fa fa-envelope fs-2 text-primary me-5"></i>
                            <div>
                                <h4 class="text-white fw-bold fs-6 mb-1">Email</h4>
                                <p class="text-gray-500 fs-7 mb-0">mpp@deliserdang.go.id</p>
                            </div>
                        </div>

                        <div class="mt-15">
                            <h4 class="text-white fw-bold fs-7 text-uppercase mb-5">Jam Operasional</h4>
                            <div class="d-flex justify-content-between border-bottom border-gray-700 py-2 fs-7">
                                <span>Senin - Kamis</span>
                                <span>08.00 - 15.00</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom border-gray-700 py-2 fs-7">
                                <span>Jumat</span>
                                <span>08.00 - 15.30</span>
                            </div>
                            <div class="d-flex justify-content-between py-2 fs-7 text-danger">
                                <span>Sabtu - Minggu</span>
                                <span class="fw-bold">TUTUP</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3982.355325492348!2d98.7753697!3d3.5048259!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x303131649646b96b%3A0x6a0f6764585c5b9f!2sP3UD%20Deli%20Serdang!5e0!3m2!1sid!2sid!4v1700000000000" 
                                width="100%" height="100%" style="border:0; min-height: 450px;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="footer">
        <div class="container">
            <div class="row g-10">
                <div class="col-lg-4">
                    <img src="{{ asset('images/logo_pemda.png') }}" width="60" alt="Logo Footer" class="mb-5">
                    <h3 class="text-white fw-bolder mb-5">Pemerintah Kabupaten<br>Deli Serdang</h3>
                    <p class="text-gray-600 fs-7">Portal Terpadu Mal Pelayanan Publik Deli Serdang. Melayani dengan hati untuk masyarakat sejahtera.</p>
                </div>
                <div class="col-lg-4 ms-lg-auto">
                    <h4 class="text-white fw-bold mb-5">Tautan Cepat</h4>
                    <ul class="list-unstyled text-gray-600 fs-7">
                        <li class="mb-3"><a href="#home" class="text-inherit">Beranda</a></li>
                        <li class="mb-3"><a href="{{ route('daftar.instansi') }}" class="text-inherit">Daftar Instansi</a></li>
                        <li class="mb-3"><a href="{{ route('skm.index') }}" class="text-inherit">Survey Kepuasan (SKM)</a></li>
                        <li class="mb-3"><a href="/login" class="text-inherit">Admin Login</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h4 class="text-white fw-bold mb-5">Ikuti Kami</h4>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-icon btn-light-facebook rounded-circle"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-icon btn-light-instagram rounded-circle"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="btn btn-icon btn-light-twitter rounded-circle"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <hr class="border-gray-800 my-10">
            <div class="text-center text-gray-700 fs-8 fw-bold">
                &copy; {{ date('Y') }} MPP KABUPATEN DELI SERDANG. DESIGNED BY IT DELI SERDANG.
            </div>
        </div>
    </footer>

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>

    <script>
        // Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                document.getElementById('mainNav').classList.add('scrolled', 'navbar-light');
                document.getElementById('mainNav').classList.remove('navbar-dark');
            } else {
                document.getElementById('mainNav').classList.remove('scrolled', 'navbar-light');
                document.getElementById('mainNav').classList.add('navbar-dark');
            }
        });
    </script>
</body>

</html>
