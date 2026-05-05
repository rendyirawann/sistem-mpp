<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey Kepuasan Masyarakat</title>

    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/mpp_logo_premium.png') }}" />

    {{-- Phosphor Icons & Fonts --}}
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
            --radius-lg: 32px;
            --shadow-soft: 0 20px 40px -20px rgba(0,0,0,0.08);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--background);
            background-image: radial-gradient(circle at 50% 0%, rgba(79, 70, 229, 0.05) 0%, transparent 70%);
            min-height: 100vh;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .step-content { display: none; }
        .step-content.active { display: block; animation: fadeIn 0.5s ease-out; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .main-card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            border: 1px solid rgba(0,0,0,0.04);
            width: 100%;
            max-width: 800px;
            overflow: hidden;
        }

        .header-section {
            padding: 48px;
            text-align: center;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .brand-logo {
            height: 64px;
            width: 64px;
            object-fit: contain;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 24px;
        }

        .custom-input {
            background: var(--background);
            border: 2px solid transparent;
            border-radius: 16px;
            padding: 16px 24px;
            font-size: 1.125rem;
            font-weight: 500;
            color: var(--primary);
            box-shadow: 0 2px 10px rgba(0,0,0,0.02) inset;
            transition: all 0.2s;
        }
        
        .custom-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-light);
            background: var(--surface);
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

        .info-card {
            background: var(--accent-light);
            border-radius: 20px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 32px;
        }

        .question-card {
            background: var(--background);
            border-radius: 20px;
            padding: 32px;
            margin-bottom: 24px;
            border: 1px solid rgba(0,0,0,0.03);
        }

        .btn-check:checked+.btn-outline-dashed {
            background: var(--accent-light) !important;
            border-color: var(--accent) !important;
            color: var(--accent) !important;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
        }
        
        .option-btn {
            background: var(--surface);
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 16px;
            padding: 16px;
            font-weight: 600;
            color: var(--secondary);
            transition: all 0.2s;
        }
        
        .option-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

    </style>
</head>

<body id="kt_body" class="p-5 p-lg-10">

    <div class="main-card">
        <div class="header-section">
            <img src="{{ asset('assets/media/logos/mpp_logo_premium.png') }}" class="brand-logo" alt="Logo">
            <h1 class="fw-black fs-2x mb-2 text-gray-900" style="letter-spacing: -1px;">Survey Kepuasan Masyarakat</h1>
            <span class="text-gray-500 fs-5 fw-medium">Sistem Informasi Layanan Publik Terpadu</span>
        </div>

        <div class="p-8 p-lg-12">
            {{-- STEP 1: INPUT NOMOR ANTRIAN --}}
            <div id="step-1" class="step-content active text-center py-5">
                <div class="mb-10">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light-primary text-primary rounded-circle mb-6" style="width: 80px; height: 80px;">
                        <i class="ph-fill ph-ticket fs-4x"></i>
                    </div>
                    <h2 class="fw-black text-gray-900 fs-1 mb-3">Selamat Datang</h2>
                    <p class="text-gray-500 fw-medium fs-5">Silakan masukkan nomor antrian Anda untuk memulai pengisian survey.</p>
                </div>

                <div class="mw-500px mx-auto">
                    <div class="d-flex flex-column gap-4 mb-5">
                        <input type="text" id="no_antrian" class="form-control custom-input text-center fs-3 fw-bold text-uppercase" placeholder="Contoh: A-001" maxlength="25" autocomplete="off" />
                        <button type="button" onclick="cekAntrian()" class="btn-apple primary" id="btn-cek">
                            <i class="ph-bold ph-magnifying-glass"></i> Cari Nomor Antrian
                        </button>
                    </div>
                    
                    <div id="error-msg" class="alert alert-dismissible bg-light-danger border border-danger border-dashed d-flex align-items-center p-5 mb-10 d-none rounded-4">
                        <i class="ph-fill ph-warning-circle fs-2x text-danger me-4"></i>
                        <div class="d-flex flex-column text-start">
                            <h5 class="mb-1 fw-bold text-danger">Gagal Ditemukan</h5>
                            <span id="text-error" class="text-danger opacity-75 fw-medium">Nomor antrian tidak terdaftar.</span>
                        </div>
                    </div>
                </div>
            </div>

            <form id="formSkm" method="POST" action="{{ route('skm.store') }}">
                @csrf
                <input type="hidden" name="antrian_id" id="antrian_id">

                {{-- INFO BAR --}}
                <div id="info-bar" class="info-card d-none">
                    <i class="ph-fill ph-user-circle fs-3x text-primary"></i>
                    <div>
                        <h4 class="text-gray-900 fw-bold fs-4 mb-1" id="cust-nama">-</h4>
                        <div class="text-gray-500 fw-medium fs-7 mb-1">
                            NIK: <span id="cust-nik" class="fw-bold text-gray-700">-</span> &bull; 
                            Jenis Kelamin: <span id="cust-jk" class="fw-bold text-gray-700">-</span>
                        </div>
                        <div class="text-primary fw-bold fs-7 d-flex align-items-center gap-1">
                            <i class="ph-fill ph-buildings"></i> <span id="cust-instansi">-</span>
                        </div>
                    </div>
                </div>

                {{-- STEP 2: DATA DIRI --}}
                <div id="step-2" class="step-content">
                    <div class="mb-10 border-bottom pb-6">
                        <h2 class="fw-black text-gray-900 fs-2">Data Responden</h2>
                        <p class="text-gray-500 fw-medium">Lengkapi informasi dasar Anda berikut ini.</p>
                    </div>

                    <div class="row g-6 mb-10">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-gray-700 ms-1">No. Antrian</label>
                            <input type="text" id="disp_no_antrian" class="form-control custom-input bg-light text-gray-500" readonly />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required fw-bold text-gray-700 ms-1">Umur (Tahun)</label>
                            <input type="number" name="umur" id="umur" class="form-control custom-input save-local" placeholder="Contoh: 25" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required fw-bold text-gray-700 ms-1">Pendidikan Terakhir</label>
                            <select name="pendidikan" id="pendidikan" class="form-select custom-input save-local" required>
                                <option value="" disabled selected>Pilih Pendidikan</option>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA">SMA</option>
                                <option value="D1-D3">D1 - D3</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                                <option value="S3">S3</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required fw-bold text-gray-700 ms-1">Pekerjaan Utama</label>
                            <select name="pekerjaan" id="pekerjaan" class="form-select custom-input save-local" required>
                                <option value="" disabled selected>Pilih Pekerjaan</option>
                                <option value="PNS">PNS / TNI / Polri</option>
                                <option value="Pegawai Swasta">Pegawai Swasta</option>
                                <option value="Wiraswasta">Wiraswasta / Pengusaha</option>
                                <option value="Pelajar">Pelajar / Mahasiswa</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-10 pt-6 border-top">
                        <button type="button" class="btn btn-light fw-bold px-6 py-3 rounded-pill" onclick="window.location.reload();">Batalkan</button>
                        <button type="button" class="btn-apple" onclick="nextStep(3)">Lanjutkan Penilaian <i class="ph-bold ph-arrow-right"></i></button>
                    </div>
                </div>

                {{-- STEP 3: PENILAIAN --}}
                <div id="step-3" class="step-content">
                    <div class="mb-10 border-bottom pb-6">
                        <h2 class="fw-black text-gray-900 fs-2">Penilaian Pelayanan</h2>
                        <p class="text-gray-500 fw-medium">Berikan penilaian jujur Anda untuk kualitas pelayanan yang lebih baik.</p>
                    </div>

                    <div class="mb-8">
                        @foreach ($pertanyaan as $key => $p)
                            <div class="question-card">
                                <div class="d-flex gap-4 mb-6 align-items-start">
                                    <span class="badge bg-primary text-white fs-6 px-3 py-2 rounded-3 mt-1">{{ strtoupper($key) }}</span>
                                    <span class="text-gray-900 fw-bold fs-4 lh-sm">{{ $p['tanya'] }}</span>
                                </div>
                                <div class="row g-3">
                                    @foreach ($p['opsi'] as $nilai => $opsi)
                                        <div class="col-md-6 col-lg-3">
                                            <input type="radio" class="btn-check save-local-radio" name="{{ $key }}" value="{{ $nilai }}" id="{{ $key }}_{{ $nilai }}" required />
                                            <label class="btn btn-outline btn-outline-dashed option-btn w-100 h-100 d-flex align-items-center justify-content-center text-center" for="{{ $key }}_{{ $nilai }}">
                                                {{ $opsi }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="question-card border-danger bg-light-danger border-opacity-50">
                            <div class="d-flex gap-4 mb-6 align-items-start">
                                <i class="ph-fill ph-warning fs-1 text-danger mt-1"></i>
                                <span class="text-danger fw-bold fs-4 lh-sm">Apakah Anda pernah diminta biaya tambahan di luar ketentuan resmi (Pungli)?</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <input type="radio" class="btn-check save-local-radio" name="ada_pungli" value="Tidak" id="pungli_tdk" required />
                                    <label class="btn btn-outline btn-outline-dashed border-danger border-opacity-25 text-danger option-btn w-100 h-100" for="pungli_tdk">Tidak Pernah</label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check save-local-radio" name="ada_pungli" value="Ya" id="pungli_ya" required />
                                    <label class="btn btn-outline btn-outline-dashed border-danger border-opacity-25 text-danger option-btn w-100 h-100" for="pungli_ya">Pernah</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8">
                            <label class="form-label fw-bold text-gray-700 ms-1">Kritik / Saran Membangun (Opsional)</label>
                            <textarea name="saran" id="saran" class="form-control custom-input save-local" rows="4" placeholder="Tuliskan saran Anda di sini..."></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-10 pt-6 border-top">
                        <button type="button" class="btn btn-light fw-bold px-6 py-3 rounded-pill" onclick="nextStep(2)"><i class="ph-bold ph-arrow-left me-2"></i> Kembali</button>
                        <button type="submit" class="btn-apple primary" id="btn-submit">
                            <span id="btnText"><i class="ph-fill ph-paper-plane-tilt me-2"></i> Kirim Survey</span>
                            <span id="btnLoading" class="d-none">
                                <span class="spinner-border spinner-border-sm align-middle me-2"></span> Mengirim...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>

    <script>
        const btnCek = document.getElementById('btn-cek');
        const inputAntrian = document.getElementById('no_antrian');
        const errorMsg = document.getElementById('error-msg');
        const textError = document.getElementById('text-error');

        inputAntrian.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                cekAntrian();
            }
        });

        function cekAntrian() {
            let no = inputAntrian.value.trim();
            if (!no) {
                Swal.fire("Peringatan", "Silakan isi nomor antrian terlebih dahulu", "warning");
                return;
            }

            btnCek.disabled = true;
            btnCek.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            errorMsg.classList.add('d-none');

            fetch(`{{ url('/skm/check-antrian') }}?no_antrian=${no}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('antrian_id').value = data.data.id;
                        document.getElementById('disp_no_antrian').value = data.data.no_antrian;
                        
                        document.getElementById('cust-nama').innerText = data.data.nama;
                        document.getElementById('cust-nik').innerText = data.data.nik;
                        document.getElementById('cust-jk').innerText = data.data.jk == 'L' ? 'Laki-Laki' : 'Perempuan';
                        document.getElementById('cust-instansi').innerText = data.data.skpd + " - " + data.data.loket;

                        document.getElementById('info-bar').classList.remove('d-none');
                        nextStep(2);
                    } else {
                        errorMsg.classList.remove('d-none');
                        textError.innerText = data.message;
                        document.getElementById('info-bar').classList.add('d-none');
                    }
                })
                .catch(err => {
                    Swal.fire("Error", "Gagal menghubungi server", "error");
                })
                .finally(() => {
                    btnCek.disabled = false;
                    btnCek.innerHTML = '<i class="ph-bold ph-magnifying-glass"></i> Cari Nomor Antrian';
                });
        }

        function nextStep(step) {
            document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
            document.getElementById('step-' + step).classList.add('active');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Auto Save LocalStorage
        const storageKey = 'skm_draft_data';
        function saveDraft() {
            let draft = {};
            document.querySelectorAll('.save-local').forEach(el => { draft[el.id] = el.value; });
            document.querySelectorAll('.save-local-radio:checked').forEach(el => { draft[el.name] = el.value; });
            localStorage.setItem(storageKey, JSON.stringify(draft));
        }

        function loadDraft() {
            let draft = JSON.parse(localStorage.getItem(storageKey) || '{}');
            for (let key in draft) {
                let el = document.getElementById(key);
                if (el && !el.classList.contains('save-local-radio')) { el.value = draft[key]; }
                else {
                    let radio = document.querySelector(`input[name="${key}"][value="${draft[key]}"]`);
                    if (radio) radio.checked = true;
                }
            }
        }

        document.querySelectorAll('.save-local, .save-local-radio').forEach(el => {
            el.addEventListener('change', saveDraft);
            el.addEventListener('keyup', saveDraft);
        });

        document.addEventListener('DOMContentLoaded', loadDraft);

        document.getElementById('formSkm').addEventListener('submit', function(e) {
            const btnSubmit = document.getElementById('btn-submit');
            btnSubmit.disabled = true;
            document.getElementById('btnText').classList.add('d-none');
            document.getElementById('btnLoading').classList.remove('d-none');
            localStorage.removeItem(storageKey); 
        });

        @if(session('success'))
            Swal.fire({
                title: "Terima Kasih!",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonText: "Tutup",
                customClass: { confirmButton: "btn btn-primary rounded-pill px-8" }
            }).then(() => { window.location.href = "{{ route('skm.index') }}"; });
        @endif
    </script>
</body>
</html>
