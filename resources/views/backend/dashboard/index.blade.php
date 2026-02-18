@extends('backend.layout.app')
@section('title', 'Dashboard')
@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar d-flex flex-stack py-4 py-lg-8">
        <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10" id="kt_toolbar">

            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 my-0">
                    Dashboard
                </h1>

                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-gray-900">
                        Dashboard
                    </li>
                </ul>
            </div>
            <!--end::Page title-->

        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">

        <!-- ================= ANTRIAN ================= -->
        <div class="row g-5 g-xl-8">

            <!-- TOTAL ANTRIAN -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-flush shadow-sm h-100 cursor-pointer"onclick="openDetail('antrian_total')">
                    <div class="card-body text-center">
                        <i class="ki-outline ki-chart-simple fs-2tx text-primary mb-3"></i>
                        <div class="fs-2hx fw-bold text-primary">
                            {{ $data['total_antrian'] ?? 0 }}
                        </div>
                        <div class="fw-semibold text-gray-500">
                            Total Antrian/Pengunjung
                        </div>
                    </div>
                </div>
            </div>

            <!-- ANTRIAN HARI INI -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-flush shadow-sm h-100 cursor-pointer"onclick="openDetail('antrian_hari_ini')">
                    <div class="card-body text-center">
                        <i class="ki-outline ki-calendar fs-2tx text-success mb-3"></i>
                        <div class="fs-2hx fw-bold text-success">
                            {{ $data['antrian_hari_ini'] ?? 0 }}
                        </div>
                        <div class="fw-semibold text-gray-500">
                            Total Antrian Hari Ini
                        </div>
                    </div>
                </div>
            </div>

            <!-- ANTRIAN MENUNGGU -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-flush shadow-sm h-100 cursor-pointer"onclick="openDetail('antrian_menunggu')">
                    <div class="card-body text-center">
                        <i class="ki-outline ki-time fs-2tx text-warning mb-3"></i>
                        <div class="fs-2hx fw-bold text-warning">
                            {{ $data['antrian_menunggu'] ?? 0 }}
                        </div>
                        <div class="fw-semibold text-gray-500">
                            Antrian Menunggu
                        </div>
                    </div>
                </div>
            </div>

            <!-- ANTRIAN DIPANGGIL -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-flush shadow-sm h-100 cursor-pointer"onclick="openDetail('antrian_dipanggil')">
                    <div class="card-body text-center">
                        <i class="ki-outline ki-check-circle fs-2tx text-info mb-3"></i>
                        <div class="fs-2hx fw-bold text-info">
                            {{ $data['antrian_dipanggil'] ?? 0 }}
                        </div>
                        <div class="fw-semibold text-gray-500">
                            Antrian Dipanggil
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ================= LOKET ================= -->
        <div class="row g-5 g-xl-8 mt-2">

            <div class="col-xl-3 col-md-6">
                <div class="card card-flush shadow-sm h-100 cursor-pointer"onclick="openDetail('antrian_selesai')">
                    <div class="card-body text-center">
                        <i class="ki-outline ki-check-circle fs-2tx text-info mb-3"></i>
                        <div class="fs-2hx fw-bold text-info">
                            {{ $data['antrian_selesai'] ?? 0 }}
                        </div>
                        <div class="fw-semibold text-gray-500">
                            Antrian Selesai
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOTAL LOKET -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-flush shadow-sm h-100 cursor-pointer"onclick="openDetail('loket_all')">
                    <div class="card-body text-center">
                        <i class="ki-outline ki-abstract-26 fs-2tx text-dark mb-3"></i>
                        <div class="fs-2hx fw-bold text-dark">
                            {{ $data['total_loket'] ?? 0 }}
                        </div>
                        <div class="fw-semibold text-gray-500">
                            Total Layanan
                        </div>
                    </div>
                </div>
            </div>

            <!-- Layanan AKTIF -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-flush shadow-sm h-100 cursor-pointer"onclick="openDetail('loket_aktif')">
                    <div class="card-body text-center">
                        <i class="ki-outline ki-check fs-2tx text-success mb-3"></i>
                        <div class="fs-2hx fw-bold text-success">
                            {{ $data['loket_aktif'] ?? 0 }}
                        </div>
                        <div class="fw-semibold text-gray-500">
                            Layanan Aktif
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOTAL SKPD -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-flush shadow-sm h-100 cursor-pointer"onclick="openDetail('total_skpd')">
                    <div class="card-body text-center">
                        <i class="ki-outline ki-office-bag fs-2tx text-dark mb-3"></i>
                        <div class="fs-2hx fw-bold text-dark">
                            {{ $data['total_skpd'] ?? 0 }}
                        </div>
                        <div class="fw-semibold text-gray-600">
                            Total SKPD
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-5 g-xl-8 mt-5">
            <div class="col-12">

                <div class="card card-flush shadow-sm">
                    <div class="card-header pt-5">
                        <h3 class="card-title fw-bold text-gray-800">
                            <i class="ki-outline ki-document fs-2 me-2"></i> Rekapitulasi Laporan
                        </h3>
                    </div>
                    <div class="card-body">

                        {{-- FORM FILTER --}}
                        <form action="{{ route('dashboard') }}" method="GET" class="row g-3 mb-10 align-items-end">

                            {{-- 1. JENIS LAPORAN (BARU) --}}
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Jenis Laporan</label>
                                {{-- ID ini penting untuk Javascript Toggle --}}
                                <select name="format_laporan" id="format_laporan" class="form-select form-select-solid"
                                    data-control="select2" data-hide-search="true">
                                    <option value="detail" {{ request('format_laporan') == 'detail' ? 'selected' : '' }}>
                                        Detail Pelayanan</option>
                                    <option value="rekap" {{ request('format_laporan') == 'rekap' ? 'selected' : '' }}>
                                        Rekapitulasi (General)</option>
                                </select>
                            </div>

                            {{-- 2. Filter SKPD (Hanya Superadmin) --}}
                            @role('Superadmin')
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Pilih Instansi</label>
                                    <select name="skpd_id" class="form-select form-select-solid" data-control="select2"
                                        data-placeholder="Semua Instansi">
                                        <option value="all" {{ request('skpd_id') == 'all' ? 'selected' : '' }}>Semua
                                            Instansi</option>
                                        @foreach ($listSkpd as $skpd)
                                            <option value="{{ $skpd->id }}"
                                                {{ request('skpd_id') == $skpd->id ? 'selected' : '' }}>
                                                {{ $skpd->nama_skpd }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endrole

                            {{-- 3. Status (Diberi ID filter_status_container agar bisa di-hide via JS) --}}
                            <div class="col-md-2" id="filter_status_container">
                                <label class="form-label fw-bold">Status</label>
                                <select name="status" class="form-select form-select-solid" data-control="select2"
                                    data-hide-search="true">
                                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status
                                    </option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Menunggu (0)
                                    </option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Dipanggil (1)
                                    </option>
                                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Selesai (2)
                                    </option>
                                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Batal (3)
                                    </option>
                                </select>
                            </div>

                            {{-- 4. Periode Filter --}}
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Periode Filter</label>
                                <select name="filter_type" id="filter_type" class="form-select form-select-solid"
                                    data-hide-search="true">
                                    <option value="hari_ini" {{ $filterType == 'hari_ini' ? 'selected' : '' }}>Hari Ini
                                    </option>
                                    <option value="per_tanggal" {{ $filterType == 'per_tanggal' ? 'selected' : '' }}>Per
                                        Tanggal</option>
                                    <option value="per_bulan" {{ $filterType == 'per_bulan' ? 'selected' : '' }}>Per Bulan
                                    </option>
                                    <option value="range" {{ $filterType == 'range' ? 'selected' : '' }}>Range Tanggal
                                    </option>
                                </select>
                            </div>

                            {{-- 5. Input Dinamis --}}
                            <div class="col-md-3">
                                {{-- Input Per Tanggal --}}
                                <div id="input_per_tanggal" class="d-none">
                                    <label class="form-label fw-bold">Tanggal</label>
                                    <input type="date" name="start_date" class="form-control form-control-solid"
                                        value="{{ $startDate }}" {{ $filterType == 'per_tanggal' ? '' : 'disabled' }}>
                                </div>

                                {{-- Input Per Bulan --}}
                                <div id="input_per_bulan" class="d-none">
                                    <label class="form-label fw-bold">Bulan</label>
                                    <input type="month" name="bulan" class="form-control form-control-solid"
                                        value="{{ $bulan }}" {{ $filterType == 'per_bulan' ? '' : 'disabled' }}>
                                </div>

                                {{-- Input Range --}}
                                <div id="input_range" class="d-none">
                                    <label class="form-label fw-bold">Rentang Tanggal</label>
                                    <div class="input-group">
                                        <input type="date" name="start_date" class="form-control form-control-solid"
                                            value="{{ $startDate }}" {{ $filterType == 'range' ? '' : 'disabled' }}>
                                        <span class="input-group-text">-</span>
                                        <input type="date" name="end_date" class="form-control form-control-solid"
                                            value="{{ $endDate }}" {{ $filterType == 'range' ? '' : 'disabled' }}>
                                    </div>
                                </div>
                            </div>

                            {{-- 6. Tombol Action --}}
                            <div class="col-md-auto ms-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <button type="submit" class="btn btn-sm btn-primary fw-bold">
                                        <i class="fa fa-search me-1"></i> Cari
                                    </button>

                                    <a href="{{ url()->current() }}" class="btn btn-sm btn-light-danger fw-bold"
                                        title="Reset Filter">
                                        <i class="fa fa-sync me-1"></i> Reset
                                    </a>

                                    {{-- <button type="submit" name="type" value="excel"
                                        formaction="{{ route('dashboard.export') }}"
                                        class="btn btn-sm btn-success fw-bold">
                                        <i class="fa fa-file-excel me-1"></i> Excel
                                    </button> --}}

                                    <button type="submit" name="type" value="pdf"
                                        formaction="{{ route('dashboard.export') }}" formtarget="_blank"
                                        class="btn btn-sm btn-danger fw-bold">
                                        <i class="fa fa-file-pdf me-1"></i> PDF
                                    </button>
                                </div>
                            </div>
                        </form>

                        {{-- TABEL LAPORAN --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped gs-7 gy-3 align-middle">
                                <thead class="bg-light fw-bold text-center">
                                    <tr>
                                        <th class="w-50px">No</th>
                                        <th>Instansi (SKPD)</th>
                                        <th>Layanan (Loket)</th>
                                        <th class="w-150px">Jumlah Antrian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rekapLayanan as $index => $row)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                @if ($row->loket->skpd->logo_skpd)
                                                    <img src="{{ asset('storage/user/logo_skpd/' . $row->loket->skpd->logo_skpd) }}"
                                                        class="w-25px h-25px me-2 object-fit-cover rounded-circle">
                                                @endif
                                                {{ $row->loket->skpd->nama_skpd ?? '-' }}
                                            </td>
                                            <td>{{ $row->loket->nama_loket }}</td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end align-items-center gap-2">
                                                    <span class="fw-bold fs-6">{{ $row->total }}</span>

                                                    <button type="button"
                                                        class="btn btn-icon btn-sm btn-light-primary w-25px h-25px btn-detail-rekap"
                                                        data-loket-id="{{ $row->loket_id }}"
                                                        data-nama-loket="{{ $row->loket->nama_loket ?? '-' }}"
                                                        title="Lihat Detail">
                                                        <i class="fa fa-eye fs-7"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-5">
                                                Tidak ada data antrian pada periode/filter ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-light-primary fw-bolder fs-5">
                                    <tr>
                                        <td colspan="3" class="text-end pe-5">TOTAL NOMOR ANTRIAN</td>
                                        <td class="text-center text-primary">{{ $totalRekap }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
    <!-- Modal Detail Dashboard -->
    <div class="modal fade" id="modalDetailRekap" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header pb-0 border-0 justify-content-end">
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                    <div class="mb-13 text-center">
                        <h1 class="mb-3">Detail Data</h1>
                        <div class="text-muted fw-semibold fs-5">
                            <span id="modalLoketName" class="text-primary fw-bold">...</span>
                        </div>
                    </div>

                    <div id="loadingModal" class="text-center py-5">
                        <span class="spinner-border text-primary" role="status"></span>
                        <div class="mt-2 text-gray-500">Memuat data...</div>
                    </div>

                    <div id="contentModal"></div>
                </div>
            </div>
        </div>
    </div>

    <!--end::Content-->
    @push('stylesheets')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ URL::to('assets/plugins/custom/datatables/datatables.bundle.css') }}">
    @endpush
    @push('scripts')
        <script src="{{ URL::to('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

        <script>
            // 1. CONFIG BAHASA INDONESIA (LOCAL) - Agar tidak error CDN & Icon sesuai Metronic
            const dtLanguageConfig = {
                "zeroRecords": "Data tidak ditemukan",
                "info": "Hal _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada data",
                "infoFiltered": "(filter dari _MAX_ total data)",
                "lengthMenu": "Tampilkan _MENU_",
                "search": "Cari:",
                "paginate": {
                    "next": "<i class='fa fa-angle-right'></i>",
                    "previous": "<i class='fa fa-angle-left'></i>"
                }
            };

            // 2. FUNGSI UNTUK KLIK CARD DATA UTAMA (ATAS)
            window.openDetail = function(type) {
                let title = '';
                switch (type) {
                    case 'antrian_total':
                        title = 'Total Seluruh Antrian';
                        break;
                    case 'antrian_hari_ini':
                        title = 'Antrian Hari Ini';
                        break;
                    case 'antrian_menunggu':
                        title = 'Antrian Sedang Menunggu';
                        break;
                    case 'antrian_dipanggil':
                        title = 'Antrian Sedang Dipanggil';
                        break;
                    case 'antrian_selesai':
                        title = 'Antrian Selesai Dilayani';
                        break;

                    case 'loket_all':
                        title = 'Daftar Semua Layanan';
                        break;
                    case 'loket_aktif':
                        title = 'Daftar Layanan Aktif';
                        break;
                    case 'loket_nonaktif':
                        title = 'Daftar Layanan Non-Aktif';
                        break;

                    case 'total_skpd':
                        title = 'Daftar Instansi (SKPD)';
                        break;
                    default:
                        title = 'Detail Data';
                }

                // Buka Modal
                $('#modalLoketName').text(title);
                $('#modalDetailRekap').modal('show');
                $('#loadingModal').removeClass('d-none');
                $('#contentModal').html('');

                // AJAX Request
                $.ajax({
                    url: "{{ route('dashboard.detail') }}",
                    type: "GET",
                    data: {
                        type: type
                    },
                    success: function(response) {
                        $('#loadingModal').addClass('d-none');
                        $('#contentModal').html(response.html);

                        // Init DataTable
                        $('#contentModal table').DataTable({
                            "language": dtLanguageConfig,
                            "info": true,
                            "ordering": true,
                            "paging": true,
                            "pageLength": 5,
                            "lengthMenu": [
                                [5, 10, 25, 50, -1],
                                [5, 10, 25, 50, "Semua"]
                            ],
                            "dom": "<'row mb-2'<'col-sm-6 d-flex align-items-center justify-content-start dt-toolbar'l><'col-sm-6 d-flex align-items-center justify-content-end dt-toolbar'f>>" +
                                "<'table-responsive'tr>" +
                                "<'row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>"
                        });
                    },
                    error: function(xhr) {
                        $('#loadingModal').addClass('d-none');
                        $('#contentModal').html(
                            '<div class="alert alert-danger text-center">Gagal memuat data detail.</div>');
                    }
                });
            }
        </script>

        <script>
            $(document).ready(function() {
                // 3. LOGIC SHOW/HIDE FILTER TANGGAL
                const toggleInputs = () => {
                    const type = $('#filter_type').val();
                    $('#input_per_tanggal, #input_per_bulan, #input_range').addClass('d-none');
                    $('#input_per_tanggal input, #input_per_bulan input, #input_range input').prop('disabled',
                        true);

                    if (type === 'per_tanggal') {
                        $('#input_per_tanggal').removeClass('d-none');
                        $('#input_per_tanggal input').prop('disabled', false);
                    } else if (type === 'per_bulan') {
                        $('#input_per_bulan').removeClass('d-none');
                        $('#input_per_bulan input').prop('disabled', false);
                    } else if (type === 'range') {
                        $('#input_range').removeClass('d-none');
                        $('#input_range input').prop('disabled', false);
                    }
                };
                $('#filter_type').change(toggleInputs);
                toggleInputs();

                // 4. LOGIC KLIK TOMBOL MATA (DETAIL TABEL REKAP)
                $('.btn-detail-rekap').click(function() {
                    let loketId = $(this).data('loket-id');
                    let namaLoket = $(this).data('nama-loket');

                    $('#modalLoketName').text(namaLoket);
                    $('#modalDetailRekap').modal('show');
                    $('#loadingModal').removeClass('d-none');
                    $('#contentModal').html('');

                    let params = {
                        loket_id: loketId,
                        status: $('select[name="status"]').val(),
                        filter_type: $('#filter_type').val(),
                        start_date: $('input[name="start_date"]').val(),
                        end_date: $('input[name="end_date"]').val(),
                        bulan: $('input[name="bulan"]').val(),
                        skpd_id: $('select[name="skpd_id"]').val()
                    };

                    $.ajax({
                        url: "{{ route('dashboard.detail_rekap') }}",
                        type: "GET",
                        data: params,
                        success: function(response) {
                            $('#loadingModal').addClass('d-none');
                            $('#contentModal').html(response.html);

                            // Init DataTable
                            $('#contentModal table').DataTable({
                                "language": dtLanguageConfig,
                                "info": true,
                                "ordering": true,
                                "paging": true,
                                "pageLength": 5,
                                "lengthMenu": [
                                    [5, 10, 25, 50, -1],
                                    [5, 10, 25, 50, "Semua"]
                                ],
                                "dom": "<'row mb-2'<'col-sm-6 d-flex align-items-center justify-content-start dt-toolbar'l><'col-sm-6 d-flex align-items-center justify-content-end dt-toolbar'f>>" +
                                    "<'table-responsive'tr>" +
                                    "<'row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>"
                            });
                        },
                        error: function() {
                            $('#loadingModal').addClass('d-none');
                            $('#contentModal').html(
                                '<div class="alert alert-danger">Gagal memuat data.</div>');
                        }
                    });
                });

                // 5. 🔥 LOGIC BARU: HIDE/SHOW STATUS BERDASARKAN JENIS LAPORAN
                // Jika pilih Rekap, sembunyikan dropdown Status
                $('#format_laporan').change(function() {
                    let val = $(this).val();
                    if (val === 'rekap') {
                        $('#filter_status_container').addClass('d-none');
                    } else {
                        $('#filter_status_container').removeClass('d-none');
                    }
                });
                // Jalankan saat load halaman
                $('#format_laporan').trigger('change');
            });
        </script>

        <style>
            .cursor-pointer {
                cursor: pointer;
            }

            .card.cursor-pointer:hover {
                transform: translateY(-3px);
                transition: .2s;
            }
        </style>
    @endpush
@endsection
