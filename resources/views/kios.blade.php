<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>MPP - Antrian & Monitor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- 1. ASSET METRONIC --}}
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/logo_deliserdang.png') }}" />

    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    {{-- 🔥 HAPUS SCRIPT RECTA YANG ERROR --}}
    {{-- <script src="{{ asset('assets/js/recta.js') }}"></script> --}}

    {{-- Load Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f5f8fa;
        }

        /* Gradient Sidebar */
        .sidebar-gradient {
            background: linear-gradient(135deg, #009ef7 0%, #0069d9 100%);
        }

        .blink {
            animation: blinker 1s linear infinite;
        }

        @keyframes blinker {
            50% {
                opacity: 0;
            }
        }

        .card-service {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .card-service:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
            border-color: #009ef7 !important;
        }

        .card-service:hover .icon-wrapper {
            background-color: #009ef7 !important;
            color: #fff !important;
        }

        /* Scrollbar Halus */
        .scroll-smooth::-webkit-scrollbar {
            width: 4px;
        }

        .scroll-smooth::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .scroll-smooth::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* RESPONSIVE LAYOUT */
        @media (min-width: 992px) {
            .h-lg-100vh {
                height: 100vh !important;
            }

            .w-lg-compact {
                width: 280px !important;
                flex: 0 0 280px !important;
            }

            .overflow-lg-hidden {
                overflow: hidden !important;
            }
        }

        @media (max-width: 991px) {
            .sidebar-gradient {
                border-radius: 0 0 1.5rem 1.5rem;
                margin-bottom: 1.5rem;
                padding: 1.25rem !important;
            }

            #panggilanNo {
                font-size: 3rem !important;
            }

            #nextNo {
                font-size: 2rem !important;
            }
        }

        /* =========================================
           🔥 CSS KHUSUS PRINT (POS 58mm)
           ========================================= */
        @media print {

            /* Sembunyikan UI Website */
            body * {
                visibility: hidden;
                height: 0;
                overflow: hidden;
            }

            /* Setting Halaman Browser ke 80mm */
            @page {
                size: 80mm auto;
                /* Lebar 80mm, Tinggi Auto */
                margin: 0mm;
                /* Hilangkan margin browser */
            }

            /* Tampilkan Area Struk */
            #area-struk,
            #area-struk * {
                visibility: visible;
                height: auto;
                overflow: visible;
            }

            #area-struk {
                position: absolute;
                left: 0;
                top: 0;
                width: 79mm;
                /* Gunakan 79mm (Safe Area) agar tidak terpotong */
                padding: 2mm 0;
                margin: 0;
                background-color: white;
            }
        }

        /* Geser Modal ke Atas (10% dari atas layar) */
        .modal-pos-top {
            margin-top: 10vh !important;
        }
    </style>
</head>

<body class="bg-body d-flex flex-column flex-lg-row h-lg-100vh overflow-lg-hidden">

    {{-- ================= WRAPPER UTAMA ================= --}}
    <div class="d-flex flex-column flex-lg-row vh-100 overflow-hidden bg-light">

        {{-- ================= KIRI: SIDEBAR INFORMASI (RESPONSIVE) ================= --}}
        <div class="sidebar-gradient text-white shadow-lg d-flex flex-column z-index-2"
            style="width: 100%; max-height: 100vh; flex: 0 0 auto;">

            <style>
                @media (min-width: 992px) {
                    .sidebar-gradient {
                        width: 350px !important;
                    }
                }

                .glass-card {
                    background: rgba(255, 255, 255, 0.1);
                    backdrop-filter: blur(10px);
                    border: 1px solid rgba(255, 255, 255, 0.2);
                }
            </style>

            <div class="p-6 p-lg-8 d-flex flex-column justify-content-between h-100 overflow-y-auto">

                {{-- HEADER: LOGO DI ATAS TENGAH (SESUAI FIGMA) --}}
                <div class="text-center mb-8">
                    <div class="symbol symbol-50px symbol-lg-70px mb-4">
                        <img src="{{ asset('images/logo_pemda.png') }}" alt="Logo"
                            style="filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));">
                    </div>
                    <div class="mb-2">
                        <h1 class="fw-bolder text-white fs-4 fs-lg-3 mb-0 text-uppercase lh-sm">Mall Pelayanan Publik
                        </h1>
                        <span class="text-white opacity-75 fs-9 fw-bold ls-3 text-uppercase">Kabupaten Deli
                            Serdang</span>
                    </div>

                    {{-- JAM DIGITAL --}}
                    <div class="glass-card rounded-4 p-4 mt-5">
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <h1 class="fs-2hx fs-lg-3hx fw-bolder text-white mb-0" id="jam-jam">00</h1>
                            <span class="fs-1 fw-bold text-white blink">:</span>
                            <h1 class="fs-2hx fs-lg-3hx fw-bolder text-white mb-0" id="jam-menit">00</h1>
                            <div class="d-flex flex-column align-items-start">
                                <span class="fs-3 fw-bold text-warning lh-1" id="jam-detik">00</span>
                            </div>
                        </div>
                        <div class="separator border-white opacity-20 my-2 w-50 mx-auto"></div>
                        <span id="jam-tanggal" class="fw-semibold fs-7 text-white opacity-90">Memuat Tanggal...</span>
                    </div>
                </div>

                {{-- STATUS PANGGILAN --}}
                <div class="d-flex flex-column gap-4 mb-8">
                    {{-- CARD 1: SEDANG DIPANGGIL --}}
                    <div id="notifikasiPanggilan"
                        class="card border-0 shadow-sm bg-warning position-relative overflow-hidden">
                        <div class="card-body p-5 text-center">
                            <span
                                class="badge badge-white text-white fw-bolder fs-9 px-3 py-2 mb-3 shadow-sm rounded-pill">
                                <i class="fa fa-volume-high text-white me-1 animate-pulse"></i> SEDANG MEMANGGIL
                            </span>
                            <h1 id="panggilanNo" class="fs-4hx fw-black text-white mb-0 lh-1">---</h1>
                            <div class="fw-bold text-white fs-6 text-uppercase my-2 text-truncate px-2"
                                id="panggilanSkpd">Menunggu...</div>
                            <div class="bg-dark bg-opacity-10 rounded-pill px-4 py-2 mt-2">
                                <span id="panggilanLoket" class="fw-bolder text-white fs-8 text-uppercase">ANTRIAN
                                    KOSONG</span>
                            </div>
                        </div>
                    </div>

                    {{-- CARD 2: GILIRAN BERIKUTNYA --}}
                    <div class="card border-0 shadow-sm bg-dark position-relative overflow-hidden">
                        <div class="card-body p-4 text-center">
                            <span class="text-gray-500 fw-bold fs-9 text-uppercase ls-2">BERIKUTNYA</span>
                            <h1 id="nextNo" class="fs-1 fw-black text-white mb-1">---</h1>
                            <div class="fw-semibold text-gray-500 fs-9 text-uppercase text-truncate" id="nextSkpd">
                                TIDAK ADA ANTRIAN</div>
                        </div>
                    </div>
                </div>

                {{-- FOOTER INFO: HARI & JAM OPERASIONAL --}}
                <div class="mt-auto">
                    <div class="glass-card rounded-3 p-4 mb-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="symbol symbol-30px">
                                <div class="symbol-label bg-white bg-opacity-20 text-white">
                                    <i class="fa fa-clock fs-6"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="fs-10 fw-bold text-white opacity-75 text-uppercase mb-1">Jam
                                    Operasional</span>
                                <div class="d-flex flex-column gap-1">
                                    <div class="d-flex justify-content-between align-items-center gap-4">
                                        <span class="fs-8 fw-bold text-white">Senin - Kamis</span>
                                        <span class="fs-8 fw-bolder text-white">08.00 - 15.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center gap-4">
                                        <span class="fs-8 fw-bold text-white">Jumat</span>
                                        <span class="fs-8 fw-bolder text-white">08.00 - 15.30</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <span class="text-white opacity-50 fs-10">&copy; {{ date('Y') }} MPP DELI SERDANG</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= KANAN: MENU UTAMA (FLEX GROW) ================= --}}
        <div class="d-flex flex-column flex-row-fluid overflow-hidden">

            {{-- Header Kanan --}}
            <div class="d-flex flex-stack px-8 py-6 bg-white shadow-sm z-index-1">
                <div class="d-flex align-items-center gap-4">
                    {{-- Tombol Refresh Rendy --}}
                    <button type="button" class="btn btn-icon btn-light-info btn-md rounded-circle shadow-sm"
                        onclick="window.location.reload();" title="Refresh">
                        <i class="fa fa-rotate-right fs-4"></i>
                    </button>
                    <div class="d-flex flex-column">
                        <h1 class="text-dark fw-bolder fs-2 mb-0">Daftar Layanan MPP</h1>
                        <span class="text-muted fw-bold fs-7">Silakan pilih Instansi tujuan Anda</span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-flex btn-light-primary px-5 rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modalLokasi">
                        <i class="fa fa-map-location-dot me-2"></i>
                        <span class="fw-bold fs-8">Peta Lokasi</span>
                    </button>
                    <div
                        class="d-flex align-items-center bg-light-success rounded-pill px-4 py-2 border border-success border-dashed">
                        <span class="bullet bullet-dot bg-success h-8px w-8px me-2 animation-blink"></span>
                        <span class="text-success fw-bold fs-8">Sistem Online</span>
                    </div>
                </div>
            </div>

            {{-- Grid SKPD --}}
            <div class="p-6 p-lg-10 scroll-smooth overflow-auto flex-grow-1 bg-gray-100">
                <div class="row g-6" id="gridSkpdContainer">
                    {{-- @foreach ($skpd as $item)
                        <div class="col-6 col-md-4 col-xl-3">
                            <div class="card card-flush h-100 border-0 shadow-sm card-service cursor-pointer"
                                onclick="openLayanan('{{ $item->id }}')">
                                <div
                                    class="card-body d-flex flex-column justify-content-center align-items-center text-center p-6">
                                    <div
                                        class="symbol symbol-60px symbol-circle bg-light-primary mb-5 d-flex justify-content-center align-items-center transition-all">
                                        <div class="symbol-label fs-2hx fw-bold text-primary bg-light-primary">
                                            <i
                                                class="fa {{ $item->logo_skpd ? 'fa-building-columns' : 'fa-building' }} fs-1 text-primary"></i>
                                        </div>
                                    </div>
                                    <h3 class="text-gray-800 fw-bolder fs-6 mb-0 lh-sm line-clamp-2 px-2">
                                        {{ $item->nama_skpd }}</h3>
                                </div>
                            </div>
                        </div>
                    @endforeach --}}

                    @include('kios_grid')
                </div>
                {{-- Spacer Bawah agar tidak mepet --}}
                <div class="h-50px"></div>
            </div>
        </div>
    </div>

    {{-- ================= MODALS ================= --}}

    {{-- 1. Modal Daftar Layanan --}}
    <div class="modal fade" id="modalLayanan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4">
                <div class="modal-header bg-primary py-3">
                    <h3 class="modal-title fw-bolder text-white fs-5" id="judulSkpd">Pilih Layanan</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-2 text-white"></i>
                    </div>
                </div>
                <div class="modal-body px-4 py-4" id="daftarLayanan"></div>
            </div>
        </div>
    </div>

    {{-- 2. Modal Form Input --}}
    {{-- <div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header bg-primary py-3">
                    <h3 class="modal-title fw-bolder text-white fs-5" id="judulLayanan">Isi Data Diri</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" onclick="backToLayanan()">
                        <i class="fa fa-arrow-left fs-2 text-white"></i>
                    </div>
                </div>
                <div class="modal-body px-5 py-5">
                    <form id="formAmbilAntrian" method="POST">
                        @csrf
                        <input type="hidden" name="skpd_id" id="inputSkpd">
                        <input type="hidden" name="loket_id" id="inputLoket">

                        <div class="fv-row mb-3">
                            <label class="required form-label fw-bold fs-7">NIK (Sesuai KTP)</label>
                            <input type="number" maxlength="16" name="nik" required
                                class="form-control form-control-solid" placeholder="16 digit NIK" />
                        </div>

                        <div class="fv-row mb-3">
                            <label class="required form-label fw-bold fs-7">Nama Lengkap</label>
                            <input type="text" name="nama" required class="form-control form-control-solid"
                                placeholder="Nama Lengkap" />
                        </div>

                        <div class="fv-row mb-5">
                            <label class="required form-label fw-bold fs-7">No. HP / WhatsApp</label>
                            <input type="number" maxlength="12" name="no_hp" required
                                class="form-control form-control-solid" placeholder="0812..." />
                        </div>

                        <button type="submit" id="btnCetak" class="btn btn-primary w-100">
                            <span id="btnText"><i class="fa fa-print me-2"></i> CETAK TIKET</span>
                            <span id="btnLoading" class="d-none">
                                <span class="spinner-border spinner-border-sm align-middle me-2"></span> Memproses...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}
    {{-- 2. Modal Form Input (Updated) --}}
    <div class="modal fade" id="modalForm" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-hidden="true">
        {{-- Hapus 'modal-dialog-centered', ganti dengan 'modal-pos-top' --}}
        <div class="modal-dialog modal-pos-top">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-primary py-3">
                    <h3 class="modal-title fw-bolder text-white fs-5" id="judulLayanan">Isi Data Diri</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" onclick="backToLayanan()">
                        <i class="fa fa-arrow-left fs-2 text-white"></i>
                    </div>
                </div>
                <div class="modal-body px-5 py-5">
                    <form id="formAmbilAntrian" method="POST" autocomplete="off">
                        @csrf
                        <input type="hidden" name="skpd_id" id="inputSkpd">
                        <input type="hidden" name="loket_id" id="inputLoket">

                        <div class="fv-row mb-4">
                            <label class="required form-label fw-bold fs-7">NIK (Sesuai KTP)</label>
                            <input type="text" name="nik" id="inputNik"
                                class="form-control form-control-solid fw-bolder text-dark"
                                placeholder="16 Digit Angka" maxlength="16" inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                            {{-- Tempat Pesan Error --}}
                            <div class="invalid-feedback fw-bold fs-7" id="error-nik"></div>
                        </div>

                        <div class="fv-row mb-4">
                            <label class="required form-label fw-bold fs-7">Nama Lengkap</label>
                            <input type="text" name="nama" id="inputNama"
                                class="form-control form-control-solid fw-bold text-uppercase"
                                placeholder="NAMA LENGKAP" />
                            <div class="invalid-feedback fw-bold fs-7" id="error-nama"></div>
                        </div>

                        <div class="fv-row mb-5">
                            <label class="required form-label fw-bold fs-7">No. HP / WhatsApp</label>
                            <input type="text" name="no_hp" id="inputHp"
                                class="form-control form-control-solid fw-bolder" placeholder="0812..."
                                maxlength="14" inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                            <div class="invalid-feedback fw-bold fs-7" id="error-no_hp"></div>
                        </div>

                        <button type="submit" id="btnCetak" class="btn btn-primary w-100 py-3 fs-4 fw-bold">
                            <span id="btnText"><i class="fa fa-print me-2"></i> CETAK TIKET</span>
                            <span id="btnLoading" class="d-none">
                                <span class="spinner-border spinner-border-sm align-middle me-2"></span> Memproses...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Modal Lokasi --}}
    <div class="modal fade" id="modalLokasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content rounded-4">
                <div class="modal-header bg-dark py-3">
                    <h3 class="modal-title fw-bolder text-white fs-5">Lokasi Stand</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-2 text-white"></i>
                    </div>
                </div>
                <div class="modal-body px-4 py-4 bg-light">
                    <div class="row g-3">
                        @forelse ($skpd as $item)
                            @if (!empty($item->lokasi))
                                <div class="col-12 col-md-6">
                                    <div class="card border border-gray-300 shadow-sm h-100">
                                        <div class="card-body d-flex align-items-center p-3">
                                            <div class="symbol symbol-35px me-3">
                                                <span class="symbol-label bg-light-primary text-primary">
                                                    <i class="fa fa-building-user"></i>
                                                </span>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span
                                                    class="text-gray-800 fw-bold fs-7 mb-1">{{ $item->nama_skpd }}</span>
                                                <span class="text-muted fw-semibold fs-9">
                                                    <i class="fa fa-map-pin text-danger me-1"></i> {{ $item->lokasi }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="col-12 text-center py-5">
                                <div class="text-gray-500 fw-bold fs-7">Lokasi belum tersedia.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Modal Sukses --}}
    <div class="modal fade" id="modalSukses" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 shadow-lg border-0">
                <div class="modal-body text-center p-8">
                    <div class="mb-4"><i class="ki-outline ki-check-circle fs-4x text-success"></i></div>
                    <h1 class="fw-black text-primary fs-2x mb-2 border border-dashed border-primary rounded p-2 bg-light-primary"
                        id="tiketBerhasil">---</h1>
                    <div class="fw-bold text-gray-600 fs-7 mb-5">Silakan menunggu nomor antrian Anda dipanggil petugas.
                    </div>
                    <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">SELESAI</button>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================
         🔥 AREA PRINT STRUK (HIDDEN)
         ========================================= --}}
    {{-- =========================================
         🔥 AREA PRINT STRUK (80mm)
         ========================================= --}}
    <div id="area-struk" style="display: none;">
        {{-- Font size dinaikkan sedikit (14px -> 16px) karena kertas lebih lebar --}}
        <div
            style="width: 100%; font-family: 'Courier New', monospace; text-align: center; font-size: 16px; font-weight: bold; line-height: 1.2;">

            <div style="font-size: 18px;">MPP</div>
            <div style="font-size: 16px;">KABUPATEN DELI SERDANG</div>
            <div style="border-bottom: 2px dashed black; margin: 10px 0;"></div>

            <div style="margin-top: 10px; font-size: 14px;">NOMOR ANTRIAN</div>
            {{-- Font Nomor Antrian diperbesar --}}
            <div id="struk-nomor" style="font-size: 64px; font-weight: 800; margin: 5px 0; line-height: 1;">---</div>

            <div style="margin-top: 10px; font-size: 14px;">LOKET</div>
            <div id="struk-layanan" style="font-size: 18px; text-transform: uppercase; padding: 0 5px;">---</div>

            <div style="border-bottom: 2px dashed black; margin: 10px 0;"></div>
            <div id="struk-waktu" style="font-size: 14px;">---</div>
            <div style="border-bottom: 2px dashed black; margin: 10px 0;"></div>

            <div style="margin-top: 15px; font-style: italic; font-size: 12px; font-weight: normal;">
                Silakan menunggu dipanggil<br>
                Terima Kasih
            </div>
            <br><br>.
        </div>
    </div>

    <audio id="tingtung" src="{{ asset('assets/audio/tingtung.mp3') }}"></audio>

    {{-- SCRIPTS --}}
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>

    <script type="module">
        const skpdData = @json($skpd);
        const bell = document.getElementById('tingtung');
        let speechQueue = [];

        // ==========================================
        // FUNGSI CETAK STRUK (WINDOW.PRINT)
        // ==========================================
        window.cetakStruk = function(data) {
            // 1. Isi data ke HTML Struk
            document.getElementById('struk-nomor').innerText = data.tiket;
            document.getElementById('struk-layanan').innerText = data.layanan.toUpperCase();
            document.getElementById('struk-waktu').innerText = 'Tgl : ' + data.tgl;

            // 2. Tampilkan area struk (tapi CSS @media print yang akan handle sisanya)
            const areaStruk = document.getElementById('area-struk');
            areaStruk.style.display = 'block';

            // 3. Eksekusi Print Browser
            window.print();

            // 4. Sembunyikan lagi setelah print dialog tertutup/selesai
            // Delay 1 detik agar proses spooling masuk
            setTimeout(() => {
                areaStruk.style.display = 'none';
            }, 1000);
        }

        // ==========================================
        // FUNGSI REFRESH GRID SKPD TANPA RELOAD PAGE
        // ==========================================
        function refreshGridSkpd() {
            fetch("{{ route('kios.grid') }}")
                .then(res => res.text())
                .then(html => {
                    document.getElementById('gridSkpdContainer').innerHTML = html;
                })
                .catch(e => console.error("Gagal refresh grid", e));
        }

        // ==========================================
        // LOGIKA JAM & PENGECEKAN WAKTU OTOMATIS
        // ==========================================
        function updateClock() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

            document.getElementById('jam-jam').innerText = String(now.getHours()).padStart(2, '0');
            document.getElementById('jam-menit').innerText = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('jam-detik').innerText = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('jam-tanggal').innerText =
                `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;

            // PENTING: Refresh grid SKPD otomatis setiap pergantian menit (detik == 0)
            // Ini menangani jam tutup layanan tanpa harus di-trigger dari server.
            if (now.getSeconds() === 0) {
                refreshGridSkpd();
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        // ==========================================
        // WEBSOCKET LISTENER (REVERB)
        // ==========================================
        setTimeout(() => {
            if (window.Echo) {
                window.Echo.channel('antrian-channel')
                    .listen('.panggilan-baru', (e) => {
                        let data = e.data;
                        updateCardCurrent(data.no_antrian, data.loket, data.skpd);
                        putarAudio(data);
                        fetchAntrianData();
                    })
                    .listen('.antrian-baru', (e) => {
                        fetchAntrianData();
                        refreshGridSkpd(); // Refresh grid karena kuota mungkin sudah habis
                    })
                    .listen('.status-tenant-updated', (e) => {
                        refreshGridSkpd(); // Refresh grid karena Admin mengubah settingan
                    });
            }
        }, 1000);

        function fetchAntrianData() {
            fetch("{{ route('antrian.check') }}")
                .then(res => res.json())
                .then(data => {
                    if (data.current) {
                        updateCardCurrent(data.current.no_antrian, data.current.nama_loket, data.current.nama_skpd);
                    }
                    if (data.next) {
                        document.getElementById('nextNo').innerText = data.next.no_antrian;
                        document.getElementById('nextSkpd').innerText = data.next.nama_skpd;
                        // document.getElementById('nextLoket').innerText = data.next.nama_loket;
                    }
                })
                .catch(e => console.log("Fetch error", e));
        }
        fetchAntrianData();

        function updateCardCurrent(nomor, loket, skpd) {
            document.getElementById('panggilanNo').innerText = nomor;
            document.getElementById('panggilanLoket').innerText = loket;
            document.getElementById('panggilanSkpd').innerText = skpd;
            const card = document.getElementById('notifikasiPanggilan');
            card.classList.add('bg-warning');
        }

        function putarAudio(data) {
            speechSynthesis.cancel();
            speechQueue = [];
            bell.pause();
            bell.currentTime = 0;
            bell.play().catch(e => {});

            let nomorRaw = data.no_antrian.toString().toUpperCase();
            let nomorDieja = nomorRaw.split('').map(char => {
                if (char === '-') return '';
                return char + '. ';
            }).join(' ');

            let textNomor = `Nomor antrian... ${nomorDieja}`;
            let textLoket = `Silakan menuju ke Loket... ${data.skpd}.`;
            speechQueue.push(textNomor, textLoket, textNomor, textLoket);
            setTimeout(() => processSpeechQueue(), 1500);
        }

        function processSpeechQueue() {
            if (speechQueue.length === 0) return;
            let text = speechQueue.shift();
            let utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            utterance.rate = 0.85;
            utterance.onend = () => setTimeout(() => processSpeechQueue(), 500);
            speechSynthesis.speak(utterance);
        }

        window.openLayanan = function(id) {
            const skpd = skpdData.find(s => s.id == id);
            document.getElementById('judulSkpd').innerText = skpd.nama_skpd;
            const container = document.getElementById('daftarLayanan');
            container.innerHTML = '';

            if (skpd.lokets.length === 0) {
                container.innerHTML = `<div class="text-center py-5 text-muted fw-bold">Belum ada layanan.</div>`;
            }
            skpd.lokets.forEach(loket => {
                const btn = document.createElement('button');
                btn.className =
                    'btn btn-outline btn-outline-dashed btn-outline-default p-3 w-100 mb-2 d-flex justify-content-between align-items-center';
                btn.innerHTML =
                    `<span class="fw-bold fs-7 text-gray-800">${loket.nama_loket}</span><i class="fa fa-chevron-right text-gray-400 fs-8"></i>`;
                btn.onclick = () => window.openForm(skpd.id, loket.id, loket.nama_loket);
                container.appendChild(btn);
            });
            new bootstrap.Modal(document.getElementById('modalLayanan')).show();
        }

        window.openForm = function(skpdId, loketId, namaLayanan) {
            bootstrap.Modal.getInstance(document.getElementById('modalLayanan'))?.hide();
            document.getElementById('judulLayanan').innerText = namaLayanan;
            document.getElementById('inputSkpd').value = skpdId;
            document.getElementById('inputLoket').value = loketId;
            new bootstrap.Modal(document.getElementById('modalForm')).show();
        }

        window.backToLayanan = function() {
            bootstrap.Modal.getInstance(document.getElementById('modalForm'))?.hide();
            new bootstrap.Modal(document.getElementById('modalLayanan')).show();
        }

        // ==========================================
        // SUBMIT FORM (AJAX)
        // ==========================================
        const form = document.getElementById('formAmbilAntrian');
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnCetak');

            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.innerText = '');

            // Loading State
            btn.disabled = true;
            document.getElementById('btnText').classList.add('d-none');
            document.getElementById('btnLoading').classList.remove('d-none');

            try {
                const response = await fetch("{{ route('ambil.antrian') }}", {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: new FormData(form)
                });
                const result = await response.json();

                if (response.ok && result.success) {
                    // 1. Tutup Modal Form
                    bootstrap.Modal.getInstance(document.getElementById('modalForm'))?.hide();

                    // 2. Tampilkan Modal Sukses
                    document.getElementById('tiketBerhasil').innerText = result.tiket;
                    new bootstrap.Modal(document.getElementById('modalSukses')).show();

                    // 3. Reset Form
                    form.reset();

                    // 4. 🔥 EKSEKUSI CETAK WINDOW.PRINT 🔥
                    window.cetakStruk(result);

                } else {
                    // GAGAL VALIDASI
                    if (result.errors) {
                        // Loop error dari Controller dan tempel ke field masing-masing
                        for (const [field, messages] of Object.entries(result.errors)) {
                            const inputField = document.querySelector(`[name="${field}"]`);
                            const errorDiv = document.getElementById(`error-${field}`);

                            if (inputField && errorDiv) {
                                inputField.classList.add('is-invalid'); // Tambah border merah
                                errorDiv.innerText = messages[0]; // Tampilkan pesan error pertama
                            }
                        }
                    } else {
                        // Error umum lain (misal server error)
                        Swal.fire("Gagal", result.message || "Terjadi kesalahan", "error");
                    }
                }
            } catch (error) {
                console.error(error);
                Swal.fire("Error", "Gagal menghubungi server", "error");
            } finally {
                // Reset Button State
                btn.disabled = false;
                document.getElementById('btnText').classList.remove('d-none');
                document.getElementById('btnLoading').classList.add('d-none');
            }
        });

        document.body.addEventListener('click', () => {
            bell.play().then(() => {
                bell.pause();
                bell.currentTime = 0;
            }).catch(() => {});
        }, {
            once: true
        });
    </script>
</body>

</html>
