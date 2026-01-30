<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey Kepuasan Masyarakat</title>

    {{-- 1. ASSET METRONIC (Mandatory) --}}
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/logo_deliserdang.png') }}" />

    {{-- Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f5f8fa;
            /* Background Metronic Default */
        }

        /* Animasi Transisi Sederhana */
        .step-content {
            display: none;
        }

        .step-content.active {
            display: block;
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom untuk Radio Button agar terlihat seperti Card tombol */
        .btn-check:checked+.btn.btn-outline.btn-outline-dashed {
            background-color: #f1faff;
            /* Light Blue */
            border-color: #009ef7;
            /* Primary */
            color: #009ef7;
        }
    </style>
</head>

<body id="kt_body" class="app-blank bgi-size-cover bgi-position-center bgi-no-repeat">

    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-column flex-column-fluid flex-center p-10">

            {{-- CONTAINER UTAMA --}}
            <div class="card card-flush w-100 mw-800px shadow-lg border-0 rounded-4">

                {{-- HEADER BIRU --}}
                <div
                    class="card-header bg-primary py-7 d-flex justify-content-center align-items-center flex-column rounded-top-4">
                    <h1 class="text-white fw-bolder fs-2x mb-1">SURVEY KEPUASAN</h1>
                    <span class="text-white opacity-75 fs-6 fw-bold">MPP Kabupaten Deli Serdang</span>
                </div>

                <div class="card-body p-lg-10 p-5">

                    {{-- STEP 1: INPUT NOMOR ANTRIAN --}}
                    <div id="step-1" class="step-content active text-center py-5">
                        <div class="mb-10">
                            <h2 class="fw-bolder text-dark mb-3">Selamat Datang</h2>
                            <div class="text-muted fw-bold fs-5">Silakan masukkan nomor antrian Anda untuk memulai.
                            </div>
                        </div>

                        <div class="mw-400px mx-auto">
                            <div class="input-group input-group-lg input-group-solid mb-5">
                                <input type="text" id="no_antrian"
                                    class="form-control form-control-solid text-center fs-2 fw-bolder text-uppercase"
                                    placeholder="A-001" maxlength="25" autocomplete="off" />

                                <button type="button" onclick="cekAntrian()" class="btn btn-primary" id="btn-cek">
                                    <i class="ki-outline ki-magnifier fs-2"></i> Cari
                                </button>
                            </div>
                            <div id="error-msg"
                                class="alert alert-dismissible bg-light-danger border border-danger border-dashed d-flex flex-column flex-sm-row p-5 mb-10 d-none">
                                <i class="ki-outline ki-message-text-2 fs-2hx text-danger me-4 mb-5 mb-sm-0"></i>
                                <div class="d-flex flex-column pe-0 pe-sm-10">
                                    <h5 class="mb-1">Gagal</h5>
                                    <span id="text-error">Nomor tidak ditemukan.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- FORM UTAMA (STEP 2 & 3) --}}
                    <form id="mainForm" action="{{ route('skm.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="antrian_id" id="antrian_id">

                        {{-- INFO BAR (HIDDEN DEFAULT) --}}
                        <div id="info-bar"
                            class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-8 d-none">
                            <i class="ki-outline ki-user-square fs-2tx text-primary me-4"></i>
                            <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                <div class="mb-3 mb-md-0 fw-semibold">
                                    <h4 class="text-gray-900 fw-bold" id="cust-nama">-</h4>
                                    <div class="fs-6 text-gray-700 pe-7">NIK: <span id="cust-nik"
                                            class="fw-bold font-monospace">-</span></div>
                                    <div class="fs-6 text-primary mt-1"><i class="fa fa-building me-1 text-primary"></i>
                                        <span id="cust-instansi">-</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span id="cust-layanan"
                                        class="badge badge-lg badge-primary fw-bolder fs-6 px-4 py-2">-</span>
                                </div>
                            </div>
                        </div>

                        {{-- STEP 2: DATA DIRI --}}
                        <div id="step-2" class="step-content">
                            <div class="pb-5 pb-lg-10">
                                <h2 class="fw-bolder text-dark">Data Diri Responden</h2>
                                <div class="text-muted fw-bold fs-6">Mohon lengkapi data diri Anda.</div>
                            </div>

                            <div class="row g-5 mb-8">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">No. Antrian</label>
                                    <input type="text" id="disp_no_antrian"
                                        class="form-control form-control-solid bg-secondary" readonly />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">Umur (Tahun)</label>
                                    <input type="number" name="umur" id="umur"
                                        class="form-control form-control-solid save-local" placeholder="Contoh: 25"
                                        required />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">Jenis Kelamin</label>
                                    {{-- Menggunakan Metronic Select2 --}}
                                    <select name="jk" id="jk"
                                        class="form-select form-select-solid save-local" data-control="select2"
                                        data-placeholder="Pilih Jenis Kelamin" data-hide-search="true" required>
                                        <option></option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">Pendidikan Terakhir</label>
                                    <select name="pendidikan" id="pendidikan"
                                        class="form-select form-select-solid save-local" data-control="select2"
                                        data-placeholder="Pilih Pendidikan" required>
                                        <option></option>
                                        @foreach ($pendidikan as $item)
                                            <option value="{{ $item['name'] ?? $item['nama'] }}">
                                                {{ $item['name'] ?? $item['nama'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">Pekerjaan</label>
                                    <select name="pekerjaan" id="pekerjaan"
                                        class="form-select form-select-solid save-local" data-control="select2"
                                        data-placeholder="Pilih Pekerjaan" required>
                                        <option></option>
                                        @foreach ($pekerjaan as $item)
                                            <option value="{{ $item['name'] ?? $item['nama'] }}">
                                                {{ $item['name'] ?? $item['nama'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">Penyandang Disabilitas?</label>
                                    <select name="disabilitas" id="disabilitas"
                                        class="form-select form-select-solid save-local" data-control="select2"
                                        data-placeholder="Pilih Status" data-hide-search="true" required>
                                        <option></option>
                                        @foreach ($disabilitas as $item)
                                            <option value="{{ $item['name'] ?? $item['nama'] }}">
                                                {{ $item['name'] ?? $item['nama'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label required fw-bold">Jenis Layanan</label>
                                    <select name="id_pelayanan" id="id_pelayanan"
                                        class="form-select form-select-solid save-local" data-control="select2"
                                        data-placeholder="Pilih Jenis Layanan" required>
                                        <option></option>
                                        {{-- OPTION AKAN DIISI OTOMATIS OLEH JAVASCRIPT --}}
                                    </select>
                                    <div class="form-text text-muted" id="loading-layanan"></div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between pt-5">
                                <button type="button" onclick="resetSurvey()" class="btn btn-light-danger">
                                    <i class="ki-outline ki-cross fs-2"></i> Batal
                                </button>
                                <button type="button" onclick="goToStep(3)" class="btn btn-primary">
                                    Selanjutnya <i class="ki-outline ki-arrow-right fs-2 ms-2"></i>
                                </button>
                            </div>
                        </div>

                        {{-- STEP 3: PENILAIAN --}}
                        <div id="step-3" class="step-content">
                            <div class="pb-5">
                                <h2 class="fw-bolder text-dark">Penilaian Kualitas</h2>
                                <div class="text-muted fw-bold fs-6">Berikan penilaian Anda terhadap pelayanan kami.
                                </div>
                            </div>

                            {{-- Looping Pertanyaan dengan Style Metronic --}}
                            @foreach ($pertanyaan as $key => $p)
                                <div class="card card-dashed border-gray-300 bg-lighten mb-5">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <span
                                                class="badge badge-light-primary fw-bolder me-3">{{ strtoupper($key) }}</span>
                                            <span class="text-gray-800 fw-bold fs-6">{{ $p['tanya'] }}</span>
                                        </div>

                                        <div class="row g-3">
                                            @foreach ($p['opsi'] as $index => $opsi)
                                                @php $nilai = $index + 1; @endphp
                                                <div class="col-md-6">
                                                    <input type="radio" class="btn-check save-local"
                                                        name="{{ $key }}" value="{{ $nilai }}"
                                                        id="{{ $key }}_{{ $nilai }}" required />
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-active-light-primary p-3 d-flex align-items-center justify-content-center w-100"
                                                        for="{{ $key }}_{{ $nilai }}">
                                                        <span class="fw-bold fs-7">{{ $opsi }}</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- PUNGLI SECTION --}}
                            <div
                                class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-6 mb-8 flex-column">
                                <div class="d-flex flex-stack mb-4">
                                    <div class="fw-bold fs-5 text-gray-800">
                                        Apakah Anda pernah diminta biaya tambahan di luar ketentuan resmi (Pungli)?
                                    </div>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <input type="radio" class="btn-check save-local" name="is_pungli"
                                            value="1" id="pungli_ya" onchange="togglePungli(this)" required />
                                        <label
                                            class="btn btn-outline btn-outline-dashed btn-active-light-danger w-100 p-4"
                                            for="pungli_ya">
                                            <span class="fw-bolder fs-4">YA</span>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check save-local" name="is_pungli"
                                            value="0" id="pungli_tidak" onchange="togglePungli(this)"
                                            required />
                                        <label
                                            class="btn btn-outline btn-outline-dashed btn-active-light-success w-100 p-4"
                                            for="pungli_tidak">
                                            <span class="fw-bolder fs-4">TIDAK</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Hidden Form Pungli --}}
                                <div id="form-pungli" class="d-none bg-white rounded p-4 border border-gray-300">
                                    <div class="alert alert-warning d-flex align-items-center p-3 mb-3">
                                        <i class="ki-outline ki-information-2 fs-2 text-warning me-3"></i>
                                        <span class="text-gray-700 fw-bold fs-7">Identitas Anda akan kami
                                            rahasiakan.</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label required fw-bold fs-7">Kontak (HP/Email)</label>
                                        <input type="text" name="pungli_kontak" id="inp_pungli_kontak"
                                            class="form-control form-control-solid save-local" />
                                    </div>
                                    <div>
                                        <label class="form-label required fw-bold fs-7">Kronologi Singkat</label>
                                        <textarea name="pungli_keterangan" id="inp_pungli_ket" class="form-control form-control-solid save-local"
                                            rows="3"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-10">
                                <label class="form-label fw-bold">Kritik & Saran</label>
                                <textarea name="kritik_saran" id="kritik_saran" class="form-control form-control-solid save-local" rows="3"
                                    placeholder="Masukan saran Anda..."></textarea>
                            </div>

                            <div class="d-flex justify-content-between border-top border-gray-300 pt-6">
                                <button type="button" onclick="resetSurvey()"
                                    class="btn btn-light-danger me-2 w-25">Batal</button>
                                <button type="button" onclick="goToStep(2)"
                                    class="btn btn-light-primary me-2 w-25">Kembali</button>
                                <button type="submit" onclick="clearLocal()" class="btn btn-primary w-50">
                                    <span class="indicator-label">Kirim Penilaian <i
                                            class="ki-outline ki-send fs-2 ms-1"></i></span>
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. SCRIPTS METRONIC (Wajib) --}}
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Restore session jika ada
            restoreSession();
        });

        // 1. INPUT MASKING (A-001)
        $('#no_antrian').on('input', function() {
            let val = $(this).val().toUpperCase().replace(/[^A-Z0-9]/g, '');
            let match = val.match(/^([A-Z]+)(\d*)$/);
            if (match) {
                let prefix = match[1];
                let number = match[2];
                if (number.length > 0) {
                    $(this).val(prefix + '-' + number);
                } else {
                    $(this).val(prefix);
                }
            } else {
                $(this).val(val);
            }
        });

        // 2. NAVIGASI STEP
        function goToStep(step) {
            if (step === 3) {
                // Validasi Step 2 (Bootstrap style)
                let valid = true;
                $('#step-2 input[required], #step-2 select[required]').each(function() {
                    if ($(this).val() === "" || $(this).val() === null) {
                        valid = false;
                        $(this).addClass('is-invalid');
                        // Khusus Select2, tambahkan border merah ke containernya jika perlu
                        $(this).next('.select2').find('.select2-selection').addClass('border-danger');
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).next('.select2').find('.select2-selection').removeClass('border-danger');
                    }
                });

                if (!valid) {
                    Swal.fire({
                        text: "Mohon lengkapi Data Diri terlebih dahulu.",
                        icon: "warning",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, Mengerti!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                    return;
                }
            }

            $('.step-content').removeClass('active').addClass('d-none');
            $('#step-' + step).removeClass('d-none').addClass('active');
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // 3. CEK ANTRIAN
        // 3. CEK ANTRIAN
        function cekAntrian() {
            let no = $('#no_antrian').val();
            let btn = $('#btn-cek');

            $('#error-msg').addClass('d-none');

            if (!no) return;

            // Loading State Metronic
            btn.attr('data-kt-indicator', 'on');
            btn.prop('disabled', true);

            $.ajax({
                url: "{{ route('skm.check') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    no_antrian: no
                },
                success: function(res) {
                    btn.removeAttr('data-kt-indicator');
                    btn.prop('disabled', false);

                    if (res.status === 'success') {
                        localStorage.setItem('skm_session', JSON.stringify({
                            no_antrian: no,
                            data: res.data,
                            services: res.services // Simpan layanan di localstorage juga
                        }));

                        applyData(res.data, no);

                        // 🔥 POPULASI DROPDOWN LAYANAN DARI API
                        populateServices(res.services);

                        goToStep(2);
                    } else {
                        $('#text-error').text(res.message);
                        $('#error-msg').removeClass('d-none');
                    }
                },
                error: function() {
                    btn.removeAttr('data-kt-indicator');
                    btn.prop('disabled', false);
                    $('#text-error').text("Terjadi kesalahan koneksi.");
                    $('#error-msg').removeClass('d-none');
                }
            });
        }

        // FUNGSI BARU: Render Option Layanan
        // FUNGSI BARU: Render Option Layanan (Versi Auto-Detect)
        function populateServices(services) {
            let select = $('#id_pelayanan');
            select.empty();
            select.append('<option></option>');

            console.log("🔥 DEBUG DATA API:", services); // Cek ini di Console Browser

            if (Array.isArray(services) && services.length > 0) {
                services.forEach(function(item) {

                    // 1. Coba tebak nama field yang umum digunakan
                    let text = item.nama_layanan ||
                        item.jenis_layanan ||
                        item.layanan ||
                        item.opd ||
                        item.nama;

                    // 2. JIKA MASIH KOSONG, Kita cari manual field yang isinya huruf (String)
                    if (!text) {
                        // Ambil semua key (misal: ['id', 'nm_pelayanan'])
                        let keys = Object.keys(item);
                        // Cari key yang BUKAN 'id' dan isinya adalah TEXT
                        let foundKey = keys.find(k => k !== 'id' && typeof item[k] === 'string');
                        if (foundKey) {
                            text = item[foundKey];
                        }
                    }

                    // 3. Fallback terakhir jika benar-benar tidak ketemu
                    text = text || "Layanan Tidak Bernama (Cek Console)";

                    select.append(new Option(text, item.id));
                });

                $('#loading-layanan').html(
                    '<span class="text-success"><i class="ki-outline ki-check-circle fs-7"></i> Data layanan berhasil ditarik (' +
                    services.length + ' item).</span>'
                );
            } else {
                $('#loading-layanan').html(
                    '<span class="text-danger fw-bold"><i class="ki-outline ki-cross-circle fs-7"></i> Data layanan kosong atau ID Sukma salah.</span>'
                );
            }

            select.trigger('change');
        }

        // Helper Apply Data
        function applyData(data, no) {
            $('#antrian_id').val(data.id);
            $('#disp_no_antrian').val(no.toUpperCase());
            $('#cust-nama').text(data.nama);
            $('#cust-nik').text(data.nik);
            $('#cust-instansi').text(data.instansi);
            $('#cust-layanan').text(data.layanan);
            $('#info-bar').removeClass('d-none');
        }

        // 4. DATA PERSISTENCE
        $(document).on('change input', '.save-local', function() {
            let name = $(this).attr('name');
            let val = $(this).val();

            // Khusus Radio
            if ($(this).attr('type') === 'radio') {
                if ($(this).is(':checked')) {
                    localStorage.setItem('skm_form_' + name, val);
                }
            } else {
                localStorage.setItem('skm_form_' + name, val);
            }
        });

        // UPDATE FUNGSI RESTORE SESSION
        function restoreSession() {
            let session = localStorage.getItem('skm_session');
            if (session) {
                let sessData = JSON.parse(session);
                $('#no_antrian').val(sessData.no_antrian);
                applyData(sessData.data, sessData.no_antrian);

                // Restore Layanan Dulu sebelum restore value yang dipilih
                if (sessData.services) {
                    populateServices(sessData.services);
                }

                goToStep(2);

                $('.save-local').each(function() {
                    let name = $(this).attr('name');
                    let storedVal = localStorage.getItem('skm_form_' + name);
                    if (storedVal) {
                        if ($(this).attr('type') === 'radio') {
                            if ($(this).val() == storedVal) {
                                $(this).prop('checked', true);
                                if (name === 'is_pungli') togglePungli(this);
                            }
                        } else {
                            $(this).val(storedVal);
                            if ($(this).is('select')) {
                                $(this).trigger('change.select2');
                            }
                        }
                    }
                });
            }
        }

        // 6. RESET SURVEY
        function resetSurvey() {
            Swal.fire({
                text: "Batalkan pengisian survey?",
                icon: 'warning',
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Tidak',
                customClass: {
                    confirmButton: "btn btn-danger",
                    cancelButton: "btn btn-active-light"
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    localStorage.removeItem('skm_session');
                    Object.keys(localStorage).forEach((key) => {
                        if (key.startsWith('skm_form_')) localStorage.removeItem(key);
                    });

                    document.getElementById('mainForm').reset();
                    // Reset Select2
                    $('.form-select').val(null).trigger('change');

                    $('#no_antrian').val('');
                    $('.step-content').removeClass('active').addClass('d-none');
                    $('#step-1').removeClass('d-none').addClass('active');
                    $('#info-bar').addClass('d-none');
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            });
        }

        function clearLocal() {
            // Biarkan submit berjalan, nanti dibersihkan via session success
        }

        // 6. MENAMPILKAN ALERT SUKSES & DEBUG KE CONSOLE
        @if (session('success'))
            localStorage.clear();

            // 🔥 CETAK DATA KE CONSOLE F12
            console.log("%c🚀 DEBUG API SUKMADELI",
                "color: white; background: #009ef7; font-weight: bold; padding: 4px 8px; border-radius: 4px;");

            @if (session('api_debug'))
                console.log("Status Code:", {{ session('api_debug.status_code') }});
                console.log("Payload Terkirim:", @json(session('api_debug.payload')));
                console.log("Respon API Sukmadeli:", @json(session('api_debug.response')));

                // Notifikasi visual di console
                if ({{ session('api_debug.status_code') }} == 200) {
                    console.log("%c✅ DATA BERHASIL TERKIRIM KE SERVER SUKMADELI", "color: green; font-weight: bold;");
                } else {
                    console.log("%c❌ GAGAL TERKIRIM KE SERVER SUKMADELI", "color: red; font-weight: bold;");
                }
            @endif

            Swal.fire({
                text: "{{ session('success') }}",
                icon: "success",
                buttonsStyling: false,
                confirmButtonText: "Selesai",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
        @endif

        @if (session('error'))
            Swal.fire({
                text: "{{ session('error') }}",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok",
                customClass: {
                    confirmButton: "btn btn-danger"
                }
            });
        @endif

        // Logic Toggle Pungli
        function togglePungli(radio) {
            const formContainer = document.getElementById('form-pungli');
            const inputKontak = document.getElementById('inp_pungli_kontak');
            const inputKet = document.getElementById('inp_pungli_ket');

            if (radio.value == '1') {
                $(formContainer).removeClass('d-none');
                inputKontak.required = true;
                inputKet.required = true;
            } else {
                $(formContainer).addClass('d-none');
                inputKontak.required = false;
                inputKet.required = false;
                inputKontak.value = "";
                inputKet.value = "";
                localStorage.removeItem('skm_form_pungli_kontak');
                localStorage.removeItem('skm_form_pungli_keterangan');
            }
        }
    </script>
</body>

</html>
