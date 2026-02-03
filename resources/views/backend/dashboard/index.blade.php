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
                <div class="card card-flush shadow-sm h-100 cursor-pointer"onclick="openDetail('antrian_all')">
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
                <div class="card card-flush shadow-sm h-100 cursor-pointer"onclick="openDetail('antrian_today')">
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

            <!-- LOKET NONAKTIF -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-flush shadow-sm h-100 cursor-pointer"onclick="openDetail('loket_nonaktif')">
                    <div class="card-body text-center">
                        <i class="ki-outline ki-cross fs-2tx text-danger mb-3"></i>
                        <div class="fs-2hx fw-bold text-danger">
                            {{ $data['loket_nonaktif'] ?? 0 }}
                        </div>
                        <div class="fw-semibold text-gray-500">
                            Layanan Nonaktif
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOTAL SKPD -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-flush shadow-sm h-100 bg-light-primary">
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

                            {{-- 1. Filter SKPD (Hanya Superadmin) --}}
                            @role('Superadmin')
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Pilih Instansi</label>
                                    <select name="skpd_id" class="form-select form-select-solid" data-control="select2"
                                        data-placeholder="Semua Instansi">
                                        <option value="">Semua Instansi</option>
                                        @foreach ($listSkpd as $skpd)
                                            <option value="{{ $skpd->id }}"
                                                {{ request('skpd_id') == $skpd->id ? 'selected' : '' }}>
                                                {{ $skpd->nama_skpd }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endrole

                            {{-- 2. Jenis Filter --}}
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Periode Filter</label>
                                <select name="filter_type" id="filter_type" class="form-select form-select-solid">
                                    <option value="hari_ini" {{ $filterType == 'hari_ini' ? 'selected' : '' }}>Hari Ini
                                    </option>
                                    <option value="per_tanggal" {{ $filterType == 'per_tanggal' ? 'selected' : '' }}>Per
                                        Tanggal</option>
                                    <option value="per_bulan" {{ $filterType == 'per_bulan' ? 'selected' : '' }}>Per Bulan
                                    </option>
                                    <option value="range" {{ $filterType == 'range' ? 'selected' : '' }}>Range / Per
                                        Minggu</option>
                                </select>
                            </div>

                            {{-- 3. Input Dinamis --}}
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

                            {{-- 4. Tombol Action --}}
                            <div class="col-md-3">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i class="ki-outline ki-filter fs-2"></i> Terapkan
                                    </button>

                                    {{-- Dropdown Export --}}
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-light-success dropdown-toggle"
                                            data-bs-toggle="dropdown">
                                            <i class="ki-outline ki-file-down fs-2"></i> Export
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <button type="submit" name="export_type" value="pdf"
                                                    formaction="{{ route('dashboard.export') }}" formtarget="_blank"
                                                    class="dropdown-item">
                                                    <i class="fa fa-file-pdf text-danger me-2"></i> PDF
                                                </button>
                                            </li>
                                            <li>
                                                <button type="submit" name="export_type" value="excel"
                                                    formaction="{{ route('dashboard.export') }}" formtarget="_blank"
                                                    class="dropdown-item">
                                                    <i class="fa fa-file-excel text-success me-2"></i> Excel
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
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
                                            <td class="text-center fw-bolder fs-5">{{ $row->total }}</td>
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
    <div class="modal fade" id="modalDetailDashboard" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailTitle">Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="modalDetailBody">
                    <div class="text-center py-10">
                        <span class="spinner-border"></span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!--end::Content-->
@endsection
@push('scripts')
    <script>
        function openDetail(type) {
            $('#modalDetailDashboard').modal('show');
            $('#modalDetailBody').html(`
        <div class="text-center py-10">
            <span class="spinner-border"></span>
        </div>
    `);

            let titleMap = {
                antrian_all: 'Detail Total Antrian',
                antrian_today: 'Detail Antrian Hari Ini',
                antrian_menunggu: 'Detail Antrian Menunggu',
                antrian_dipanggil: 'Detail Antrian Dipanggil',
                loket_all: 'Detail Semua Layanan',
                loket_aktif: 'Detail Layanan Aktif',
                loket_nonaktif: 'Detail Layanan Nonaktif',
            };

            $('#modalDetailTitle').text(titleMap[type] ?? 'Detail');

            $.get("{{ route('dashboard.detail') }}", {
                type
            }, function(res) {
                $('#modalDetailBody').html(res);
            }).fail(function() {
                $('#modalDetailBody').html(`
            <div class="alert alert-danger">
                Gagal memuat data
            </div>
        `);
            });
        }
    </script>

    <script>
        // Script Sederhana untuk Show/Hide Input Tanggal
        $(document).ready(function() {
            const toggleInputs = () => {
                const type = $('#filter_type').val();

                // Sembunyikan semua
                $('#input_per_tanggal, #input_per_bulan, #input_range').addClass('d-none');
                // Disable input agar tidak terkirim di URL yang tidak perlu
                $('#input_per_tanggal input, #input_per_bulan input, #input_range input').prop('disabled',
                true);

                // Tampilkan yang sesuai
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
            toggleInputs(); // Jalankan saat load
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
