<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>MPP - Antrian & Monitor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- 1. ASSET METRONIC (Mandatory) --}}
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />

    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    {{-- 🔥 WAJIB: Load Vite untuk Reverb/WebSocket --}}
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

        /* 🔥 SINKRONISASI TINGGI LAYAR AGAR RESPONSIVE */
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

        /* 🔥 OPTIMASI UNTUK HP LAYAR KECIL / PENDEK */
        @media (max-width: 991px) {
            .sidebar-gradient {
                border-radius: 0 0 1.5rem 1.5rem;
                margin-bottom: 1.5rem;
                padding: 1.25rem !important;
                /* Padding diperkecil */
            }

            /* Kecilkan teks di mobile agar muat */
            #panggilanNo {
                font-size: 3rem !important;
            }

            #nextNo {
                font-size: 2rem !important;
            }

            .fs-2hx {
                font-size: 1.75rem !important;
            }
        }
    </style>
</head>

<body class="bg-body d-flex flex-column flex-lg-row h-lg-100vh overflow-lg-hidden">

    {{-- ================= KIRI: SIDEBAR INFORMASI ================= --}}
    <div
        class="d-flex flex-column justify-content-between w-100 w-lg-compact sidebar-gradient text-white p-6 p-lg-4 shadow-lg z-index-2 position-relative overflow-hidden">

        <div class="position-absolute top-0 end-0 p-5 opacity-10">
            <i class="ki-outline ki-abstract-26 fs-5x text-white"></i>
        </div>

        {{-- HEADER --}}
        <div class="text-center mb-2 position-relative z-index-1">
            <div class="symbol symbol-40px symbol-lg-50px mb-2">
                <img src="{{ asset('images/logo_pemda.png') }}" alt="Logo" class="img-fluid"
                    style="filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));">
            </div>
            <h1 class="fw-bolder text-white fs-4 fs-lg-5 mb-0 text-uppercase">Mall Pelayanan Publik</h1>
            <span class="text-white opacity-75 fs-9 fw-bold ls-2">DELI SERDANG</span>

            {{-- JAM DIGITAL --}}
            <div class="glass-card rounded-3 p-2 mt-3 text-center">
                <div class="d-flex justify-content-center align-items-baseline gap-1">
                    <h1 class="fs-1 fs-lg-1 fw-bolder text-white mb-0" id="jam-jam">00</h1>
                    <span class="fs-2 fw-bold text-white blink">:</span>
                    <h1 class="fs-1 fs-lg-1 fw-bolder text-white mb-0" id="jam-menit">00</h1>
                    <span class="fs-5 fw-bold text-warning ms-1" id="jam-detik">00</span>
                </div>
                <div class="separator separator-dashed separator-content border-white opacity-25 my-1"></div>
                <span id="jam-tanggal" class="fw-semibold fs-9 text-white opacity-90">-</span>
            </div>
        </div>

        {{-- STATUS PANGGILAN --}}
        <div class="d-flex flex-column gap-3 my-4 my-lg-0 position-relative z-index-1">

            {{-- CARD 1: SEDANG DIPANGGIL --}}
            <div id="notifikasiPanggilan" class="card border-0 shadow-sm bg-warning position-relative overflow-hidden">
                <div class="card-body p-4 p-lg-3 text-center d-flex flex-column align-items-center">
                    <span class="badge badge-white text-white fw-bolder fs-9 px-2 py-1 mb-1 shadow-sm">
                        <i class="fa fa-volume-high text-white me-1 animate-pulse"></i>SEDANG MEMANGGIL ANTRIAN
                    </span>
                    <h1 id="panggilanNo" class="fs-3hx fw-black text-white mb-0 lh-1">---</h1>
                    <div class="fw-bold text-white fs-7 text-uppercase mb-1 text-truncate w-100 px-1"
                        id="panggilanSkpd">---</div>
                    <div class="bg-white bg-opacity-20 rounded-pill px-3 py-1 w-100">
                        <span id="panggilanLoket"
                            class="fw-bold text-white fs-8 text-uppercase d-block text-truncate">Menunggu...</span>
                    </div>
                </div>
            </div>

            {{-- CARD 2: GILIRAN BERIKUTNYA --}}
            <div class="card border-0 shadow-sm bg-dark position-relative overflow-hidden">
                <div class="card-body p-3 p-lg-3 text-center d-flex flex-column align-items-center">
                    <span class="text-gray-500 fw-bold fs-9 text-uppercase ls-1">ANTRIAN BERIKUTNYA</span>
                    <h1 id="nextNo" class="fs-2 fw-black text-white mb-0">---</h1>
                    <div class="fw-semibold text-gray-400 fs-9 text-uppercase w-100 text-truncate" id="nextSkpd">---
                    </div>
                    <div class="separator border-gray-700 w-100 my-1"></div>
                    <span id="nextLoket" class="text-gray-500 fs-9 fw-bold">Belum ada antrian</span>
                </div>
            </div>
        </div>

        {{-- FOOTER INFO --}}
        <div class="mt-2 position-relative z-index-1">
            <div class="glass-card rounded-3 p-2 d-flex flex-column gap-1">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-20px me-2">
                        <div class="symbol-label bg-white bg-opacity-20 text-white"><i class="fa fa-clock fs-9"></i>
                        </div>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="fs-10 fw-bold text-white opacity-75 text-uppercase text-nowrap">Jam
                            Operasional</span>
                        <span class="fs-9 fw-bolder text-white">08.00 – 15.00 WIB</span>
                    </div>
                </div>
                <div class="d-flex align-items-center d-lg-none d-xl-flex"> {{-- Disembunyikan di sidebar kecil jika layar pas-pasan --}}
                    <div class="symbol symbol-20px me-2">
                        <div class="symbol-label bg-white bg-opacity-20 text-white"><i class="fa fa-clock fs-9"></i>
                        </div>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="fs-10 fw-bold text-white opacity-75 text-uppercase text-nowrap">Hari
                            Kerja</span>
                        <span class="fs-9 fw-bolder text-white">Senin – Jumat</span>
                    </div>
                </div>
            </div>
            <div class="text-center mt-2">
                <span class="text-white opacity-50 fs-10">&copy; {{ date('Y') }} MPP DELI SERDANG</span>
            </div>
        </div>
    </div>

    {{-- ================= KANAN: MENU UTAMA ================= --}}
    <div class="d-flex flex-column flex-lg-row-fluid bg-light overflow-hidden">

        {{-- Header Kanan --}}
        <div class="d-flex flex-stack px-6 py-4 bg-white shadow-sm z-index-1">
            <div class="d-flex flex-column">
                <h1 class="text-dark fw-bolder fs-3 mb-0">Daftar Layanan</h1>
                <span class="text-muted fw-bold fs-8">Pilih instansi tujuan Anda</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-icon btn-light-primary btn-sm w-35px h-35px rounded-circle"
                    data-bs-toggle="modal" data-bs-target="#modalLokasi">
                    <i class="fa fa-map-location-dot fs-4"></i>
                </button>
                <div
                    class="d-flex align-items-center bg-light-success rounded-pill px-3 py-1 border border-success border-dashed">
                    <span class="bullet bullet-dot bg-success h-6px w-6px me-2 animation-blink"></span>
                    <span class="text-success fw-bold fs-9 text-nowrap">Online</span>
                </div>
            </div>
        </div>

        {{-- Grid SKPD --}}
        <div class="p-4 p-lg-6 scroll-smooth overflow-auto flex-grow-1 pb-15">
            <div class="row g-4">
                @foreach ($skpd as $item)
                <div class="col-6 col-sm-6 col-md-4 col-xl-3">
                    <div class="card card-flush h-100 border-0 shadow-sm card-service"
                        onclick="openLayanan('{{ $item->id }}')">
                        <div
                            class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                            <div
                                class="icon-wrapper symbol symbol-45px symbol-lg-50px symbol-circle bg-light-primary mb-3 d-flex justify-content-center align-items-center transition-all">
                                @if ($item->logo_skpd)
                                <i class="fa fa-building-columns fs-2 text-primary"></i>
                                @else
                                <i class="fa fa-building fs-2 text-primary"></i>
                                @endif
                            </div>
                            <span
                                class="text-gray-800 fw-bold fs-7 fs-lg-6 mb-0 lh-sm line-clamp-2">{{ $item->nama_skpd }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
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
                <div class="modal-body px-4 py-4" id="daftarLayanan">
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Modal Form Input --}}
    <div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header bg-primary py-3">
                    <h3 class="modal-title fw-bolder text-white fs-5" id="judulLayanan">Isi Data Diri</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" onclick="backToLayanan()">
                        <i class="fa fa-arrow-left fs-2 text-white"></i>
                    </div>
                </div>
                <div class="modal-body px-5 py-5">
                    <form id="formAmbilAntrian" method="POST" action="{{ route('ambil.antrian') }}">
                        @csrf
                        <input type="hidden" name="skpd_id" id="inputSkpd">
                        <input type="hidden" name="loket_id" id="inputLoket">

                        <div class="fv-row mb-3">
                            <label class="required form-label fw-bold fs-7">NIK (Sesuai KTP)</label>
                            <input type="number" name="nik" required class="form-control form-control-solid"
                                placeholder="16 digit NIK" />
                        </div>

                        <div class="fv-row mb-3">
                            <label class="required form-label fw-bold fs-7">Nama Lengkap</label>
                            <input type="text" name="nama" required class="form-control form-control-solid"
                                placeholder="Nama Lengkap" />
                        </div>

                        <div class="fv-row mb-5">
                            <label class="required form-label fw-bold fs-7">No. HP / WhatsApp</label>
                            <input type="number" name="no_hp" required class="form-control form-control-solid"
                                placeholder="0812..." />
                        </div>

                        <button type="submit" id="btnCetak" class="btn btn-primary w-100">
                            <span id="btnText"><i class="fa fa-print me-2"></i> CETAK TIKET</span>
                            <span id="btnLoading" class="d-none">
                                <span class="spinner-border spinner-border-sm align-middle me-2"></span>
                                Memproses...
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
                    <div class="mb-4">
                        <i class="ki-outline ki-check-circle fs-4x text-success"></i>
                    </div>
                    <h1 class="fw-black text-primary fs-2x mb-2 border border-dashed border-primary rounded p-2 bg-light-primary"
                        id="tiketBerhasil">---</h1>
                    <div class="fw-bold text-gray-600 fs-7 mb-5">Silakan menunggu nomor antrian Anda dipanggil petugas.
                    </div>
                    <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">SELESAI</button>
                </div>
            </div>
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

        function updateClock() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

            document.getElementById('jam-jam').innerText = String(now.getHours()).padStart(2, '0');
            document.getElementById('jam-menit').innerText = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('jam-detik').innerText = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('jam-tanggal').innerText =
                `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

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
                        document.getElementById('nextLoket').innerText = data.next.nama_loket;
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

        const form = document.getElementById('formAmbilAntrian');
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnCetak');
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
                    bootstrap.Modal.getInstance(document.getElementById('modalForm'))?.hide();
                    document.getElementById('tiketBerhasil').innerText = result.tiket;
                    new bootstrap.Modal(document.getElementById('modalSukses')).show();
                    form.reset();
                } else {
                    Swal.fire("Gagal", result.message || "Periksa data Anda", "error");
                }
            } catch (error) {
                Swal.fire("Error", "Gagal menghubungi server", "error");
            } finally {
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