@extends('backend.layout.app')
@section('title', 'Panggilan Antrian')
@section('content')

    <div id="kt_app_toolbar" class="app-toolbar d-flex flex-stack py-3">
        <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2">
            <div class="page-title d-flex flex-column justify-content-center me-3">
                <h1 class="page-heading fw-bold fs-3 my-0">Panggilan Antrian</h1>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">

        {{-- ROW ATAS: MONITOR & STATISTIK (Dibuat Lebih Kecil/Compact) --}}
        <div class="row g-4 mb-5">

            {{-- 1. KOLOM KIRI: STATISTIK (Lebih Kecil) --}}
            <div class="col-xl-4">
                <div class="row g-3">
                    <div class="col-12">
                        {{-- Total Masuk --}}
                        <div class="card card-flush shadow-sm bg-light-primary border-primary border-start border-4"
                            style="min-height: 80px">
                            <div class="card-body d-flex align-items-center py-3 px-4">
                                <i class="ki-outline ki-user-tick fs-2 text-primary me-3"></i>
                                <div>
                                    <div id="jumlah-antrian" class="fs-3 fw-bold text-gray-800">-</div>
                                    <div class="fw-semibold text-gray-600 fs-8">Total Masuk</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        {{-- Menunggu --}}
                        <div class="card card-flush shadow-sm bg-light-warning border-warning border-start border-4"
                            style="min-height: 80px">
                            <div class="card-body py-3 px-2 text-center">
                                <div id="sisa-antrian" class="fs-3 fw-bold text-gray-800">-</div>
                                <div class="fw-semibold text-gray-600 fs-8">Menunggu</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        {{-- Selesai --}}
                        <div class="card card-flush shadow-sm bg-light-success border-success border-start border-4"
                            style="min-height: 80px">
                            <div class="card-body py-3 px-2 text-center">
                                <div id="antrian-selesai" class="fs-3 fw-bold text-gray-800">-</div>
                                <div class="fw-semibold text-gray-600 fs-8">Selesai</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. KOLOM KANAN: MONITOR (Lebih Kecil/Compact) --}}
            {{-- 2. KOLOM KANAN: MONITOR (Compact & Posisi SKPD Diatas) --}}
            <div class="col-xl-8">
                <div class="row h-100 g-3">

                    {{-- KARTU 1: SEDANG DIPANGGIL --}}
                    <div class="col-md-6">
                        <div class="card card-flush shadow-sm h-100 text-white position-relative overflow-hidden"
                            style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: 1px solid #34d399; min-height: 180px;">

                            <div class="position-absolute top-0 end-0 opacity-10 pe-2 pt-2">
                                <i class="ki-outline ki-notification-on fs-3x text-white"></i>
                            </div>

                            <div class="card-body d-flex flex-column justify-content-center text-center py-3">
                                <h3 class="text-white opacity-90 text-uppercase fw-bold ls-1 mb-0 fs-8">
                                    <i
                                        class="ki-outline ki-sound me-1 animate__animated animate__flash animate__infinite"></i>
                                    Sedang Dipanggil
                                </h3>

                                <div id="current-loading" class="my-3"><span
                                        class="spinner-border spinner-border-sm text-white"></span></div>

                                <div id="current-content" style="display:none;">
                                    <h1 id="current-nomor" class="fw-black text-white mb-0 mt-1"
                                        style="font-size: 3.5rem; line-height: 1;">---</h1>

                                    {{-- POSISI BARU: SKPD DI ATAS, LOKET DI BAWAH --}}
                                    <div class="mt-2 d-flex flex-column align-items-center">
                                        <span id="current-skpd"
                                            class="fw-bold fs-6 text-white text-uppercase lh-sm px-2">---</span>
                                        <div class="bg-white bg-opacity-20 rounded px-2 py-0 backdrop-blur-sm mt-1">
                                            <span id="current-loket" class="fs-8 fw-semibold">---</span>
                                        </div>
                                    </div>
                                </div>

                                <div id="current-empty" style="display:none;" class="py-3 opacity-50">
                                    <span class="fs-6">Belum ada panggilan</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- KARTU 2: GILIRAN BERIKUTNYA --}}
                    {{-- KARTU 2: DAFTAR STANDBY BERIKUTNYA (SIMPLE LIST) --}}
                    <div class="col-md-6">
                        <div class="card card-flush shadow-sm h-100 text-white"
                            style="background: linear-gradient(135deg, #1e1e2f 0%, #2b2b40 100%); border: 1px solid #444; min-height: 180px;">

                            <div class="card-body d-flex flex-column justify-content-center text-center py-3">
                                <h3 class="text-white opacity-50 text-uppercase fw-bold ls-1 mb-2 fs-8">
                                    <i class="ki-outline ki-category me-1"></i> Antrian Berikutnya
                                </h3>

                                <div id="next-loading" class="my-3"><span
                                        class="spinner-border spinner-border-sm text-white"></span></div>

                                {{-- KONTAINER LIST NOMOR --}}
                                <div id="next-list-container" style="display:none;"
                                    class="animate__animated animate__fadeIn">

                                    {{-- List Nomor Antrian (Dipisah Koma) --}}
                                    <h1 id="next-nomor-list" class="fw-black text-white mb-0 opacity-90 lh-sm text-break"
                                        style="font-size: 2.5rem;">
                                        ---
                                    </h1>

                                    <div class="mt-2 text-gray-500 fs-9 fst-italic">
                                        *Menunggu giliran panggil
                                    </div>
                                </div>

                                <div id="next-empty" style="display:none;" class="py-3 opacity-50">
                                    <i class="ki-outline ki-check-circle fs-1 mb-1"></i><br>
                                    <span class="fs-8">Semua Antrian Bersih</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- TABEL ANTRIAN --}}
        <div class="card border border-gray-300 shadow-sm">
            <div class="card-header border-bottom border-gray-300 min-h-60px">
                <div class="card-title">
                    <h3 class="fw-bold m-0 fs-5">Daftar Antrian</h3>
                </div>
                <div class="card-toolbar gap-2">
                    {{-- TOMBOL HISTORY (MODAL) --}}
                    <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal"
                        data-bs-target="#modalHistory" onclick="loadHistoryInModal()">
                        <i class="ki-outline ki-time-history fs-4 me-1"></i> Riwayat
                    </button>

                    <button type="button" class="btn btn-sm btn-primary" id="refresh-table-btn">
                        <span class="indicator-label"><i class="ki-outline ki-arrows-loop me-1"></i> Refresh</span>
                        <span class="indicator-progress" style="display:none">Wait... <span
                                class="spinner-border spinner-border-sm ms-2"></span></span>
                    </button>
                </div>
            </div>
            <div class="card-body py-4">
                <table id="tabel-antrian" class="table align-middle table-row-dashed fs-6 gy-4">
                    <thead>
                        <tr class="text-muted fw-bold fs-7 text-uppercase bg-light">
                            <th class="text-center ps-4 rounded-start">Nomor</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Layanan</th>
                            <th class="text-center rounded-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600"></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL RIWAYAT PANGGILAN --}}
    <div class="modal fade" id="modalHistory" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-500px">
            <div class="modal-content">
                <div class="modal-header pb-0 border-0 justify-content-end">
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                    <div class="mb-13 text-center">
                        <h1 class="mb-3">Riwayat Panggilan</h1>
                        <div class="text-muted fw-semibold fs-5">Daftar panggilan antrian terakhir hari ini</div>
                    </div>

                    <div id="modal-history-list" class="hover-scroll-overlay-y pe-2" style="max-height: 300px">
                        <div class="text-center text-muted py-5">
                            <span class="spinner-border spinner-border-sm"></span> Memuat data...
                        </div>
                    </div>

                    <div class="text-center mt-5">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    {{-- WAJIB: Load Javascript Module --}}
    <script type="module">
        let table;
        let isGlobalCooldown = false;
        const COOLDOWN_TIME = 30000;

        // Buat Global Function agar bisa dipanggil onclick HTML
        window.loadHistoryInModal = function() {
            $('#modal-history-list').html(
                '<div class="text-center text-muted py-5"><span class="spinner-border spinner-border-sm"></span> Memuat data...</div>'
            );

            $.ajax({
                url: "{{ route('antrian.history') }}",
                type: "GET",
                success: function(data) {
                    let html = '';
                    if (data.length > 0) {
                        data.forEach((item, index) => {
                            let bgClass = index === 0 ? 'bg-light-primary border-primary' :
                                'bg-light border-gray-200';
                            let textClass = index === 0 ? 'text-primary' : 'text-gray-800';
                            let jam = item.waktu_panggil ? item.waktu_panggil : '-';

                            html += `
                                <div class="d-flex align-items-center mb-3 p-3 border rounded ${bgClass}">
                                    <div class="me-4 text-center" style="min-width: 60px;">
                                        <span class="fs-2 fw-bold ${textClass} d-block">${item.no_antrian}</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        {{-- SKPD DI ATAS (BOLD), LOKET DI BAWAH (KECIL) --}}
                                        <div class="fw-bold text-gray-800 fs-7">${item.nama_skpd}</div>
                                        <div class="text-muted fs-8">${item.nama_loket}</div>
                                        <div class="text-muted fs-9 mt-1">Dipanggil: <span class="fw-bold text-dark">${jam}</span></div>
                                    </div>
                                    ${index === 0 ? '<span class="badge badge-sm badge-primary">Baru</span>' : ''}
                                </div>
                            `;
                        });
                    } else {
                        html =
                            `<div class="text-center text-muted fs-6 py-10"><i class="ki-outline ki-file-sheet fs-1 mb-2"></i><br>Belum ada riwayat panggilan</div>`;
                    }
                    $('#modal-history-list').html(html);
                },
                error: function() {
                    $('#modal-history-list').html(
                        '<div class="text-center text-danger py-5">Gagal memuat data.</div>');
                }
            });
        }

        $(document).ready(function() {

            // --- 1. Load Info (Statistik & Hero) ---
            function loadInfo() {
                $('#jumlah-antrian').load("{{ route('antrian.jumlah') }}");
                $('#sisa-antrian').load("{{ route('antrian.sisa') }}");
                $('#antrian-selesai').load("{{ route('antrian.selesai') }}");
                loadHeroCard();
                // loadHistoryInModal(); // Tidak perlu diload otomatis biar ringan, user klik dulu baru load
            }

            let nextCandidatesList = [];
            let currentCarouselIndex = 0;
            let carouselInterval = null;

            function startCarousel() {
                if (carouselInterval) clearInterval(carouselInterval);
                if (nextCandidatesList.length === 0) return;

                // Fungsi ganti slide
                const showSlide = (index) => {
                    const item = nextCandidatesList[index];
                    const display = $('#next-item-display');

                    // Efek Fade Out
                    display.addClass('animate__fadeOut');

                    setTimeout(() => {
                        $('#next-nomor').text(item.no_antrian);
                        $('#next-skpd').text(item.skpd);
                        $('#next-loket').text(item.loket);
                        $('#next-waktu').text(item.waktu);

                        // Update Dots
                        $('#carousel-dots .bullet').removeClass('bg-white opacity-100').addClass(
                            'bg-secondary opacity-50');
                        $(`#dot-${index}`).removeClass('bg-secondary opacity-50').addClass(
                            'bg-white opacity-100');

                        // Efek Fade In
                        display.removeClass('animate__fadeOut').addClass('animate__fadeIn');
                    }, 300); // Tunggu setengah detik
                };

                // Tampilkan slide pertama langsung
                showSlide(0);

                // Jika lebih dari 1, jalankan loop
                if (nextCandidatesList.length > 1) {
                    carouselInterval = setInterval(() => {
                        currentCarouselIndex = (currentCarouselIndex + 1) % nextCandidatesList.length;
                        showSlide(currentCarouselIndex);
                    }, 4000); // Ganti setiap 4 detik
                }
            }

            function loadHeroCard() {
                $.ajax({
                    url: "{{ route('antrian.global-info') }}",
                    type: "GET",
                    success: function(res) {
                        $('#current-loading, #next-loading').hide();
                        // ==========================================
                        if (res.cooldown_remaining > 0) {
                            console.log("Server sedang cooldown, sisa:", res.cooldown_remaining);

                            // Hanya jalankan jika timer belum berjalan di browser ini
                            if (!isGlobalCooldown) {
                                startCooldownTimer(res.cooldown_remaining);
                            }
                        }

                        // UPDATE CARD "SEDANG DIPANGGIL"
                        // UPDATE CARD SEDANG DIPANGGIL
                        if (res.current.status === 'exist') {
                            $('#current-empty').hide();
                            $('#current-content').show();
                            if ($('#current-nomor').text() !== res.current.no_antrian) {
                                $('#current-nomor').text(res.current.no_antrian);
                                $('#current-nomor').removeClass('animate__animated animate__heartBeat');
                                void document.getElementById("current-nomor").offsetWidth;
                                $('#current-nomor').addClass('animate__animated animate__heartBeat');
                            }
                            // Data SKPD & Loket sudah ditukar posisinya di HTML
                            $('#current-loket').text(res.current.loket);
                            $('#current-skpd').text(res.current.skpd);
                        } else {
                            $('#current-content').hide();
                            $('#current-empty').show();
                        }

                        // UPDATE CARD BERIKUTNYA
                        // if (res.next.status === 'exist') {
                        //     $('#next-empty').hide();
                        //     $('#next-content').show();
                        //     $('#next-nomor').text(res.next.no_antrian);
                        //     $('#next-loket').text(res.next.loket);
                        //     $('#next-skpd').text(res.next.skpd);
                        //     $('#next-waktu').text(res.next.waktu);
                        // } else {
                        //     $('#next-content').hide();
                        //     $('#next-empty').show();
                        // }
                        if (res.next_list && res.next_list.length > 0) {
                            $('#next-empty').hide();
                            $('#next-list-container').show();

                            // Ambil hanya No Antrian, lalu gabung dengan koma
                            // Contoh Hasil: "A-001, B-005, C-003"
                            let nomorListString = res.next_list.map(item => item.no_antrian).join(', ');

                            // Tampilkan ke HTML
                            $('#next-nomor-list').text(nomorListString);

                        } else {
                            $('#next-list-container').hide();
                            $('#next-empty').show();
                        }

                    }
                });
            }

            // Init Load
            loadInfo();

            // --- 2. WEBSOCKET LISTENER ---
            setTimeout(() => {
                if (window.Echo) {
                    const channel = window.Echo.channel('antrian-channel');

                    channel.listen('.panggilan-baru', (e) => {
                        console.log("🔔 Ada panggilan:", e);
                        table.ajax.reload(null, false);
                        loadInfo();
                    });

                    channel.listen('.antrian-baru', (e) => {
                        console.log("🎫 Tiket baru dicetak!");
                        table.ajax.reload(null, false);
                        loadInfo();
                    });

                    // 🔥 LISTENER BARU: GLOBAL COOLDOWN
                    channel.listen('.cooldown-started', (e) => {
                        console.log("⏳ Cooldown dimulai global:", e.duration);

                        // Jalankan fungsi timer (walaupun bukan kita yang klik)
                        // Pastikan timer tidak jalan dobel
                        if (!isGlobalCooldown) {
                            startCooldownTimer(e.duration);
                        }
                    });

                } else {
                    console.error("Reverb tidak terdeteksi");
                }
            }, 1000);

            // --- 3. DATATABLES ---
            // --- UPDATE KOLOM LAYANAN DI TABEL ---
            table = $('#tabel-antrian').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ordering: false,
                ajax: "{{ route('antrian.get') }}",
                columns: [{
                        data: 'no_antrian',
                        className: 'text-center fw-bold fs-4 text-dark'
                    },
                    {
                        data: 'status_label',
                        className: 'text-center'
                    },
                    {
                        data: 'nama_loket',
                        className: 'text-center',
                        // CUSTOM RENDER UNTUK KOLOM LAYANAN
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex flex-column align-items-center">
                                    <span class="fw-bold text-gray-800 fs-7">${row.nama_skpd}</span>
                                    <span class="text-muted fs-8">${row.nama_loket}</span>
                                </div>
                            `;
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(d) {
                            if (d.status == 2)
                                return `<button class="btn btn-light btn-sm text-gray-500" disabled><i class="ki-outline ki-check-circle fs-4"></i> Selesai</button>`;
                            if (d.status == 1)
                                return `<button class="btn btn-secondary btn-sm btn-call"><i class="ki-outline ki-notification fs-4"></i> Recall</button>`;
                            if (d.status == 0 && !d.is_first)
                                return `<button class="btn btn-light btn-sm" disabled><i class="ki-outline ki-lock fs-4"></i></button>`;
                            return `<button class="btn btn-success btn-sm btn-call"><i class="ki-outline ki-notification-on"></i> Panggil</button>`;
                        }
                    }
                ],
                drawCallback: function(settings) {
                    if (isGlobalCooldown) $('.btn-call').prop('disabled', true).addClass('disabled');
                }
            });

            // --- 4. LOGIC KLIK TOMBOL PANGGIL ---
            $('#tabel-antrian').on('click', '.btn-call', function() {
                let btn = $(this);
                let data = table.row(btn.closest('tr')).data();

                if (isGlobalCooldown) return;
                if (!data) return;

                isGlobalCooldown = true;
                btn.html('<i class="spinner-border spinner-border-sm"></i>').prop('disabled', true);
                $('.btn-call').prop('disabled', true).addClass('disabled');

                $.ajax({
                    url: "{{ route('antrian.panggil') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: data.id
                    },
                    success: function(res) {
                        loadInfo();
                        table.ajax.reload(null, false);
                        startCooldownTimer();
                    },
                    error: function(err) {
                        alert(err.responseJSON ? err.responseJSON.message : 'Gagal memanggil');
                        isGlobalCooldown = false;
                        table.ajax.reload(null, false);
                    }
                });
            });

            // Ubah function ini agar menerima parameter duration (default 27)
            function startCooldownTimer(durationSec = null) {
                // Jika durationSec tidak dikirim, pakai default COOLDOWN_TIME
                let timeLeft = durationSec ? durationSec : (COOLDOWN_TIME / 1000);

                // Set flag global biar tombol gak bisa diklik
                isGlobalCooldown = true;

                let refreshBtn = $('#refresh-table-btn');
                refreshBtn.prop('disabled', true);

                // Matikan semua tombol panggil
                $('.btn-call').prop('disabled', true).addClass('disabled').html(
                    '<i class="ki-outline ki-lock fs-4"></i>');

                // Clear interval lama jika ada (biar gak tabrakan)
                if (window.cooldownInterval) clearInterval(window.cooldownInterval);

                window.cooldownInterval = setInterval(() => {
                    timeLeft--;

                    // Update teks tombol refresh sebagai indikator
                    refreshBtn.find('.indicator-label').html(
                        `<span class="text-danger fw-bold"><i class="ki-outline ki-time text-danger me-2"></i> ${timeLeft}s</span>`
                    );

                    if (timeLeft <= 0) {
                        clearInterval(window.cooldownInterval);
                        isGlobalCooldown = false;

                        // Kembalikan tombol refresh
                        refreshBtn.find('.indicator-label').html(
                            `<i class="ki-outline ki-arrows-loop me-1"></i> Refresh`);
                        refreshBtn.prop('disabled', false);

                        // Refresh tabel otomatis agar tombol panggil muncul lagi (status disabled hilang)
                        table.ajax.reload(null, false);
                    }
                }, 1000);
            }

            $('#refresh-table-btn').on('click', function() {
                if (isGlobalCooldown) return;
                loadInfo();
                table.ajax.reload(null, false);
            });
        });
    </script>
@endpush
