<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Antrian Terpadu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/mpp_logo_premium.png') }}" />
    
    {{-- Phosphor Icons --}}
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --surface-color: #ffffff;
            --background-color: #f8f9fc;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --accent-color: #4f46e5;
            --accent-light: #e0e7ff;
            --radius-lg: 24px;
            --shadow-soft: 0 10px 40px -10px rgba(0,0,0,0.08);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--background-color);
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* Top Header */
        .kiosk-header {
            background: var(--surface-color);
            padding: 24px 40px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .brand-logo {
            height: 56px;
            width: 56px;
            object-fit: contain;
            background: #fff;
            border-radius: 16px;
            padding: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        /* Status Bar */
        .status-showcase {
            background: linear-gradient(135deg, #1e1b4b 0%, #4338ca 100%);
            margin: 32px 40px;
            border-radius: 32px;
            padding: 40px;
            color: white;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 32px;
            box-shadow: 0 20px 25px -5px rgba(67, 56, 202, 0.4);
        }

        .status-box {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 32px;
            border: 1px solid rgba(255,255,255,0.2);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .status-box.active-call {
            background: rgba(255,255,255,0.15);
            border-color: #38bdf8;
            box-shadow: 0 0 40px rgba(56, 189, 248, 0.3);
        }

        .status-label {
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            color: rgba(255,255,255,0.8);
            margin-bottom: 16px;
            display: block;
        }

        .status-value {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 12px;
        }

        .clock-display {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .clock-time {
            font-size: 4.5rem;
            font-weight: 700;
            letter-spacing: -2px;
        }

        /* Grid Section */
        .grid-container {
            padding: 0 40px 60px;
        }

        .grid-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 32px;
        }

        .clean-card {
            background: var(--surface-color);
            border-radius: var(--radius-lg);
            border: 1px solid rgba(0,0,0,0.04);
            transition: all 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
            cursor: pointer;
            height: 100%;
        }

        .clean-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-soft);
            border-color: var(--accent-light);
        }

        .icon-box {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: var(--accent-light);
            color: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }

        .clean-card:hover .icon-box {
            background: var(--accent-color);
            color: white;
            transform: scale(1.05);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.4;
        }

        .action-button {
            background: var(--surface-color);
            border: 1px solid rgba(0,0,0,0.1);
            color: var(--text-primary);
            border-radius: 100px;
            padding: 12px 24px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .action-button:hover {
            background: var(--background-color);
        }

        /* Modal Clean Style */
        .modal-clean .modal-content {
            border-radius: 32px;
            border: none;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            overflow: hidden;
        }
        .modal-clean .modal-header {
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 32px 40px 24px;
            background: #fff;
        }
        .modal-clean .modal-body {
            padding: 40px;
            background: #f8f9fc;
        }

        .custom-input {
            background: #fff;
            border: 2px solid transparent;
            border-radius: 16px;
            padding: 16px 24px;
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--text-primary);
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            transition: all 0.2s;
        }
        .custom-input:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 4px var(--accent-light);
        }

        @media print {
            body * { visibility: hidden; }
            #area-struk, #area-struk * { visibility: visible; }
            #area-struk { position: absolute; left: 0; top: 0; width: 79mm; padding: 2mm 0; background: white; color: black; }
        }
    </style>
</head>

<body>

    <header class="kiosk-header">
        <div class="brand-section">
            <img src="{{ asset('assets/media/logos/mpp_logo_premium.png') }}" alt="Logo" class="brand-logo">
            <div>
                <h1 class="fs-2 fw-black mb-0 text-gray-900" style="letter-spacing: -0.5px;">Pusat Layanan</h1>
                <span class="fs-6 fw-medium text-gray-500">Sistem Antrian Terpadu</span>
            </div>
        </div>
        <div class="d-flex gap-4">
            <button class="action-button" onclick="window.location.reload();">
                <i class="ph ph-arrows-clockwise fs-4"></i> Segarkan
            </button>
            <button class="action-button" data-bs-toggle="modal" data-bs-target="#modalLokasi">
                <i class="ph ph-map-pin fs-4"></i> Peta Lokasi
            </button>
        </div>
    </header>

    <div class="status-showcase">
        <div class="status-box active-call" id="notifikasiPanggilan">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <span class="status-label text-info"><i class="ph-fill ph-speaker-high me-2"></i> Panggilan Aktif</span>
                <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold" id="panggilanLoket">LOKET -</span>
            </div>
            <div class="status-value text-white" id="panggilanNo">---</div>
            <div class="fs-5 fw-medium text-white opacity-75 text-truncate" id="panggilanSkpd">Menunggu Antrian...</div>
        </div>

        <div class="status-box">
            <span class="status-label"><i class="ph-fill ph-queue me-2"></i> Giliran Berikutnya</span>
            <div class="status-value text-white opacity-75" id="nextNo">---</div>
            <div class="fs-5 fw-medium text-white opacity-50 text-truncate" id="nextSkpd">Belum Ada Antrian</div>
        </div>

        <div class="status-box clock-display">
            <div class="clock-time d-flex align-items-center gap-2">
                <span id="jam-jam">00</span><span class="opacity-50">:</span><span id="jam-menit">00</span>
            </div>
            <div class="fs-5 fw-medium mt-4 text-info" id="jam-tanggal">Memuat...</div>
            <div class="mt-4 px-4 py-2 rounded-pill bg-white bg-opacity-10 fs-7 fw-bold">
                <i class="ph-fill ph-check-circle text-success me-1"></i> Sistem Online
            </div>
        </div>
    </div>

    <main class="grid-container">
        <div class="grid-header">
            <div>
                <h2 class="fs-1 fw-bold text-gray-900 mb-2">Pilih Layanan</h2>
                <p class="fs-5 text-gray-500 mb-0">Sentuh instansi yang ingin Anda tuju untuk mengambil nomor.</p>
            </div>
        </div>

        <div class="row g-6" id="gridSkpdContainer">
            @include('kios_grid')
        </div>
    </main>

    <div class="modal fade modal-clean" id="modalLayanan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-black fs-2 mb-1" id="judulSkpd">Nama Instansi</h3>
                        <span class="text-gray-500">Pilih jenis layanan yang tersedia</span>
                    </div>
                    <button class="btn btn-icon btn-light rounded-circle" data-bs-dismiss="modal">
                        <i class="ph ph-x fs-3"></i>
                    </button>
                </div>
                <div class="modal-body" id="daftarLayanan"></div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-clean" id="modalForm" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center gap-4">
                    <button class="btn btn-icon btn-light rounded-circle" onclick="backToLayanan()">
                        <i class="ph ph-arrow-left fs-3"></i>
                    </button>
                    <div>
                        <h3 class="fw-black fs-2 mb-1" id="judulLayanan">Lengkapi Data</h3>
                        <span class="text-gray-500">Informasi ini diperlukan untuk pencetakan tiket</span>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="formAmbilAntrian" method="POST" autocomplete="off">
                        @csrf
                        <input type="hidden" name="skpd_id" id="inputSkpd">
                        <input type="hidden" name="loket_id" id="inputLoket">

                        <div class="row g-5">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-gray-700 ms-1">Nomor Induk Kependudukan (NIK)</label>
                                <input type="text" name="nik" id="inputNik" class="form-control custom-input" placeholder="16 digit angka KTP" maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <div class="invalid-feedback ms-1 mt-2 fw-medium" id="error-nik"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-gray-700 ms-1">Nama Lengkap</label>
                                <input type="text" name="nama" id="inputNama" class="form-control custom-input text-uppercase" placeholder="Sesuai identitas resmi">
                                <div class="invalid-feedback ms-1 mt-2 fw-medium" id="error-nama"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-gray-700 ms-1">Jenis Kelamin</label>
                                <div class="d-flex gap-4 mt-2">
                                    <label class="d-flex align-items-center p-3 bg-white border rounded-3 cursor-pointer flex-fill transition-all hover-border-primary">
                                        <input class="form-check-input me-3" type="radio" name="jk" value="L" required>
                                        <span class="fw-bold">Laki-laki</span>
                                    </label>
                                    <label class="d-flex align-items-center p-3 bg-white border rounded-3 cursor-pointer flex-fill transition-all hover-border-primary">
                                        <input class="form-check-input me-3" type="radio" name="jk" value="P" required>
                                        <span class="fw-bold">Perempuan</span>
                                    </label>
                                </div>
                                <div class="invalid-feedback ms-1 mt-2 fw-medium" id="error-jk" style="display: block;"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-gray-700 ms-1">Nomor WhatsApp / HP</label>
                                <input type="text" name="no_hp" id="inputHp" class="form-control custom-input" placeholder="08..." maxlength="14" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <div class="invalid-feedback ms-1 mt-2 fw-medium" id="error-no_hp"></div>
                            </div>
                        </div>

                        <div class="mt-10">
                            <button type="submit" id="btnCetak" class="btn btn-primary w-100 py-4 rounded-4 fs-4 fw-bold shadow-sm" style="background: var(--accent-color); border:none;">
                                <span id="btnText"><i class="ph-fill ph-printer me-2"></i> Cetak Tiket Antrian</span>
                                <span id="btnLoading" class="d-none">
                                    <span class="spinner-border spinner-border-sm align-middle me-2"></span> Memproses...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-clean" id="modalLokasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-black fs-2 mb-1">Denah & Lokasi Stand</h3>
                        <span class="text-gray-500">Temukan posisi instansi tujuan Anda</span>
                    </div>
                    <button class="btn btn-icon btn-light rounded-circle" data-bs-dismiss="modal">
                        <i class="ph ph-x fs-3"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        @forelse ($skpd as $item)
                            @if (!empty($item->lokasi))
                                <div class="col-md-6 col-lg-4">
                                    <div class="bg-white p-4 rounded-4 border shadow-sm d-flex align-items-center gap-4 h-100">
                                        <div class="bg-light-primary text-primary p-3 rounded-3">
                                            <i class="ph-fill ph-storefront fs-1"></i>
                                        </div>
                                        <div>
                                            <h4 class="fs-6 fw-bold text-gray-900 mb-1 line-clamp-2">{{ $item->nama_skpd }}</h4>
                                            <span class="badge bg-light-info text-info fw-bold"><i class="ph-fill ph-map-pin me-1"></i> {{ $item->lokasi }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="col-12 text-center py-10">
                                <i class="ph ph-map-trifold fs-5x text-gray-300 mb-4 block"></i>
                                <div class="text-gray-500 fw-bold fs-5">Informasi lokasi belum tersedia.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-clean" id="modalSukses" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-10">
                    <div class="mb-6"><i class="ph-fill ph-check-circle fs-5x text-success"></i></div>
                    <h1 class="fw-black text-gray-900 fs-3x mb-4 py-4 border rounded-4 bg-light" id="tiketBerhasil">---</h1>
                    <div class="fw-medium text-gray-500 fs-6 mb-8">Silakan menunggu nomor antrian Anda dipanggil petugas.</div>
                    <button type="button" class="btn btn-primary w-100 py-3 rounded-pill fw-bold" data-bs-dismiss="modal" style="background: var(--accent-color); border:none;">SELESAI</button>
                </div>
            </div>
        </div>
    </div>

    <div id="area-struk" style="display: none;">
        <div style="width: 100%; font-family: 'Courier New', monospace; text-align: center; font-size: 16px; font-weight: bold; line-height: 1.2;">
            <div style="font-size: 18px;">ANTRIAN</div>
            <div style="font-size: 16px;">MAL PELAYANAN PUBLIK</div>
            <div style="border-bottom: 2px dashed black; margin: 10px 0;"></div>
            <div style="margin-top: 10px; font-size: 14px;">NOMOR ANTRIAN</div>
            <div id="struk-nomor" style="font-size: 64px; font-weight: 800; margin: 5px 0; line-height: 1;">---</div>
            <div style="margin-top: 10px; font-size: 14px;">LOKET</div>
            <div id="struk-layanan" style="font-size: 18px; text-transform: uppercase; padding: 0 5px;">---</div>
            <div style="border-bottom: 2px dashed black; margin: 10px 0;"></div>
            <div id="struk-waktu" style="font-size: 14px;">---</div>
            <div style="border-bottom: 2px dashed black; margin: 10px 0;"></div>
            <div style="margin-top: 15px; font-style: italic; font-size: 12px; font-weight: normal;">
                Silakan menunggu dipanggil<br>Terima Kasih
            </div>
            <br><br>.
        </div>
    </div>

    <audio id="tingtung" src="{{ asset('assets/audio/tingtung.mp3') }}"></audio>

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>

    <script type="module">
        const skpdData = @json($skpd);
        const bell = document.getElementById('tingtung');
        let speechQueue = [];

        window.cetakStruk = function(data) {
            document.getElementById('struk-nomor').innerText = data.tiket;
            document.getElementById('struk-layanan').innerText = data.layanan.toUpperCase();
            document.getElementById('struk-waktu').innerText = 'Tgl : ' + data.tgl;

            const areaStruk = document.getElementById('area-struk');
            areaStruk.style.display = 'block';
            window.print();

            setTimeout(() => {
                areaStruk.style.display = 'none';
            }, 1000);
        }

        function refreshGridSkpd() {
            fetch("{{ route('kios.grid') }}")
                .then(res => res.text())
                .then(html => {
                    document.getElementById('gridSkpdContainer').innerHTML = html;
                })
                .catch(e => console.error("Gagal refresh grid", e));
        }

        function updateClock() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

            document.getElementById('jam-jam').innerText = String(now.getHours()).padStart(2, '0');
            document.getElementById('jam-menit').innerText = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('jam-tanggal').innerText = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;

            if (now.getSeconds() === 0) {
                refreshGridSkpd();
            }
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
                        refreshGridSkpd();
                    })
                    .listen('.status-tenant-updated', (e) => {
                        refreshGridSkpd();
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
                    }
                })
                .catch(e => console.log("Fetch error", e));
        }
        fetchAntrianData();

        function updateCardCurrent(nomor, loket, skpd) {
            document.getElementById('panggilanNo').innerText = nomor;
            document.getElementById('panggilanLoket').innerText = loket;
            document.getElementById('panggilanSkpd').innerText = skpd;
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
                container.innerHTML = `<div class="text-center py-10"><i class="ph ph-file-dashed fs-4x text-gray-300 mb-3 block"></i><div class="text-gray-500 fw-bold">Belum ada layanan tersedia.</div></div>`;
            }
            skpd.lokets.forEach(loket => {
                const btn = document.createElement('button');
                btn.className = 'btn btn-outline border-gray-200 text-gray-800 p-4 w-100 mb-3 d-flex justify-content-between align-items-center rounded-3 fs-5 bg-white shadow-sm';
                btn.innerHTML = `<span class="fw-bold">${loket.nama_loket}</span><i class="ph ph-caret-right text-gray-400 fs-3"></i>`;
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

            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.innerText = '');

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
                    window.cetakStruk(result);
                } else {
                    if (result.errors) {
                        for (const [field, messages] of Object.entries(result.errors)) {
                            const inputField = document.querySelector(`[name="${field}"]`);
                            const errorDiv = document.getElementById(`error-${field}`);
                            if (inputField && errorDiv) {
                                inputField.classList.add('is-invalid');
                                errorDiv.innerText = messages[0];
                            }
                        }
                    } else {
                        Swal.fire("Gagal", result.message || "Terjadi kesalahan", "error");
                    }
                }
            } catch (error) {
                console.error(error);
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
        }, { once: true });
    </script>
</body>
</html>
