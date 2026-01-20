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

    $.get("{{ route('dashboard.detail') }}", { type }, function (res) {
        $('#modalDetailBody').html(res);
    }).fail(function () {
        $('#modalDetailBody').html(`
            <div class="alert alert-danger">
                Gagal memuat data
            </div>
        `);
    });
}
</script>

<style>
.cursor-pointer { cursor: pointer; }
.card.cursor-pointer:hover {
    transform: translateY(-3px);
    transition: .2s;
}
</style>
@endpush
