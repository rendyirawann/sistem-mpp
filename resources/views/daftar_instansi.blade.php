<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Instansi - Mal Pelayanan Publik Kabupaten Deli Serdang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- Asset Bundle --}}
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/logo_deliserdang.png') }}" />
    
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #009ef7;
            --dark: #1e1e2d;
            --light: #f5f8fa;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--light);
        }

        .navbar-custom {
            background: white;
            padding: 10px 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .header-section {
            background: linear-gradient(135deg, #009ef7 0%, #0069d9 100%);
            padding: 100px 0 60px;
            color: white;
            border-bottom-left-radius: 50% 20px;
            border-bottom-right-radius: 50% 20px;
            margin-bottom: 50px;
        }

        .card-skpd {
            border: none;
            border-radius: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
            background: white;
            height: 100%;
        }
        .card-skpd:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
        }
        .card-skpd .icon-wrapper {
            width: 60px;
            height: 60px;
            background: var(--light);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            color: var(--primary);
        }

        .modal-layanan .modal-content { border-radius: 25px; border: none; }
        .modal-layanan .modal-header { background: var(--primary); color: white; border-radius: 25px 25px 0 0; padding: 25px; }
        .list-layanan-item { padding: 15px; border-radius: 12px; background: var(--light); margin-bottom: 10px; display: flex; align-items: center; }

        .footer { background: var(--dark); color: white; padding: 40px 0; }
    </style>
</head>

<body>

    {{-- NAVBAR --}}
    <nav class="navbar navbar-expand-lg navbar-light fixed-top navbar-custom">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('landing') }}">
                <img src="{{ asset('images/logo_pemda.png') }}" width="40" alt="Logo" class="me-3">
                <div>
                    <span class="fw-bolder fs-4 d-block lh-1">MPP</span>
                    <span class="fs-9 text-uppercase opacity-75 ls-1">Deli Serdang</span>
                </div>
            </a>
            <div class="ms-auto fw-bold">
                <a href="{{ route('landing') }}" class="btn btn-light-primary btn-sm rounded-pill px-6">
                    <i class="fa fa-arrow-left me-2"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </nav>

    {{-- HEADER --}}
    <header class="header-section text-center">
        <div class="container">
            <h1 class="fw-bolder fs-2tx mb-3">Daftar Instansi Terintegrasi</h1>
            <p class="fs-4 opacity-75">Pilih instansi untuk melihat daftar layanan yang tersedia di MPP Deli Serdang.</p>
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <main class="container mb-20">
        {{-- Search bar --}}
        <div class="row justify-content-center mb-10">
            <div class="col-lg-6">
                <div class="position-relative">
                    <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ms-5 text-gray-500"></i>
                    <input type="text" id="searchSkpd" class="form-control form-control-solid ps-15 py-4 rounded-pill" placeholder="Cari Nama Instansi...">
                </div>
            </div>
        </div>

        <div class="row g-6" id="skpdContainer">
            @foreach($skpd as $item)
            <div class="col-md-6 col-lg-3 skpd-item" data-name="{{ strtolower($item->nama_skpd) }}">
                <div class="card card-skpd shadow-sm p-8" onclick="showServices('{{ $item->id }}')">
                    <div class="icon-wrapper">
                        <i class="fa fa-building-columns fs-1"></i>
                    </div>
                    <h3 class="fs-5 fw-bolder text-dark mb-4 line-clamp-2" style="min-height: 3rem;">{{ $item->nama_skpd }}</h3>
                    <div class="d-flex align-items-center justify-content-between mt-auto">
                        @if($item->is_layanan_buka)
                            <span class="badge badge-light-success fs-9 px-3 py-1">BUKA</span>
                        @else
                            <span class="badge badge-light-danger fs-9 px-3 py-1">TUTUP</span>
                        @endif
                        <span class="text-primary fw-bold fs-8">Lihat Layanan <i class="fa fa-arrow-right fs-9 ms-1"></i></span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="footer text-center">
        <div class="container">
            <div class="text-gray-700 fs-8 fw-bold">
                &copy; {{ date('Y') }} MPP KABUPATEN DELI SERDANG.
            </div>
        </div>
    </footer>

    {{-- MODAL LAYANAN --}}
    <div class="modal fade modal-layanan" id="modalLayanan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg">
                <div class="modal-header">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-40px me-4">
                            <span class="symbol-label bg-white bg-opacity-20">
                                <i class="fa fa-building-user text-white"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="modal-title fw-bolder text-white mb-0" id="modalSkpdName">Nama Instansi</h3>
                            <span class="text-white text-opacity-75 fs-8">Daftar Layanan Tersedia</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-8">
                    <div id="layananContainer" class="row g-3">
                        {{-- Ditempel via JS --}}
                    </div>
                </div>
                <div class="modal-footer border-0 p-8 pt-0">
                    <button type="button" class="btn btn-light-primary fw-bold" data-bs-dismiss="modal">Tutup</button>
                    <a href="{{ route('home') }}" class="btn btn-primary fw-bold">Ambil Antrian di Kiosk</a>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>

    <script>
        const skpdData = @json($skpd);

        // Search Logic
        document.getElementById('searchSkpd').addEventListener('input', function(e) {
            const val = e.target.value.toLowerCase();
            document.querySelectorAll('.skpd-item').forEach(item => {
                const name = item.getAttribute('data-name');
                if (name.includes(val)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Show Services Modal
        window.showServices = function(skpdId) {
            const skpd = skpdData.find(s => s.id == skpdId);
            if (!skpd) return;

            document.getElementById('modalSkpdName').innerText = skpd.nama_skpd;
            const container = document.getElementById('layananContainer');
            container.innerHTML = '';

            if (skpd.lokets.length === 0) {
                container.innerHTML = `<div class="col-12 text-center py-10 text-muted fw-bold">Belum ada daftar layanan.</div>`;
            } else {
                skpd.lokets.forEach(loket => {
                    const item = `
                        <div class="col-md-6">
                            <div class="list-layanan-item">
                                <div class="symbol symbol-30px me-4">
                                    <span class="symbol-label bg-primary bg-opacity-10">
                                        <i class="fa fa-check-circle text-primary"></i>
                                    </span>
                                </div>
                                <span class="fw-bold text-gray-800 fs-7">${loket.nama_loket}</span>
                            </div>
                        </div>`;
                    container.insertAdjacentHTML('beforeend', item);
                });
            }

            new bootstrap.Modal(document.getElementById('modalLayanan')).show();
        }
    </script>
</body>

</html>
