@extends('backend.layout.app')
@section('title', 'Manajemen Hasil Survey SKM')
@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar d-flex flex-stack py-4 py-lg-8">
        <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10" id="kt_toolbar">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Manajemen Hasil Survey SKM</h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Master</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-gray-900">Manajemen SKM</li>
                </ul>
            </div>
            <div class="d-flex align-items-center pt-4 pb-7 pt-lg-1 pb-lg-2">
                <!-- No Add Button for results -->
            </div>
        </div>
    </div>
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="card border border-gray-300">
            <div class="card-header border-bottom border-gray-300">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                        <input type="text" data-kt-table-filter="search" id="search"
                            class="form-control w-250px ps-13" placeholder="Search" />
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                        @if ($unsyncedCount > 0)
                            <button type="button" class="btn btn-sm btn-danger me-3" id="btn-sync-data">
                                <span class="indicator-label">
                                    <i class="ki-outline ki-cloud-change me-2"></i> Sync Survey ({{ $unsyncedCount }})
                                </span>
                                <span class="indicator-progress">
                                    Syncing... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        @endif
                        <button type="button" class="btn btn-sm btn-primary" id="refresh-table-btn">
                            <span class="indicator-label">
                                <i class="ki-outline ki-arrows-loop me-2"></i> Refresh Table
                            </span>
                            <span class="indicator-progress">
                                Please Wait ... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body py-4">
                <table class="table align-middle table-row-dashed fs-7 gy-2" id="table_skm">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-10px pe-2">#</th>
                            <th class="min-w-150px">Nama Customer</th>
                            <th class="min-w-200px">Instansi</th>
                            <th class="min-w-50px">Nilai</th>
                            <th class="min-w-100px">Status Sync</th>
                            <th class="min-w-100px">Tanggal</th>
                            <th class="text-end min-w-100px ps-4 rounded-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="Modal_Edit_Data" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header align-items-center py-6 border-gray-300">
                    <h4 class="fw-bold">Edit Hasil Survey</h4>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1 text-dark"></i>
                    </div>
                </div>
                <form id="FormEditModalID" class="form">
                    <div class="modal-body px-5 my-2">
                        @method('PUT')
                        @csrf
                        <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="EditRowModalBody"></div>
                    </div>
                    <div class="modal-footer py-4">
                        <button type="button" class="btn btn-sm btn-secondary me-3" data-bs-dismiss="modal">Discard</button>
                        <button type="submit" class="btn btn-sm btn-primary" id="btn-edit-data">
                            <span class="indicator-label edit-data-label">Submit</span>
                            <span class="indicator-progress edit-data-progress" style="display: none;">Please Wait ... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Show -->
    <div class="modal fade shadow-sm" id="Modal_Show_Data" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-750px">
            <div class="modal-content">
                <div class="modal-header align-items-center py-6 border-gray-300">
                    <h4 class="fw-bold m-0">Detail Hasil Survey</h4>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1 text-dark"></i>
                    </div>
                </div>
                <div class="modal-body" id="ShowRowModalBody">
                    <div class="text-center py-10">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-3">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('stylesheets')
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="stylesheet" href="{{ URL::to('assets/plugins/custom/datatables/datatables.bundle.css') }}" />
    @endpush

    @push('scripts')
        <script src="{{ URL::to('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                var table = $('#table_skm').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('master.skm.getData') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'nama_customer', name: 'nama_customer' },
                        { data: 'instansi', name: 'instansi' },
                        { data: 'nilai', name: 'nilai' },
                        { data: 'status_sync', name: 'status_sync' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]
                });

                $('#search').on('keyup', function() {
                    table.search($(this).val()).draw();
                });

                $('#refresh-table-btn').on('click', function() {
                    var button = this;
                    button.setAttribute("data-kt-indicator", "on");
                    button.disabled = true;
                    table.ajax.reload(function() {
                        button.removeAttribute("data-kt-indicator");
                        button.disabled = false;
                    });
                });

                var id;
                $('body').on('click', '#getEditRowData', function() {
                    id = $(this).data('id');
                    $.get("skm/" + id + "/edit", function(res) {
                        $('#EditRowModalBody').html(res.html);
                        $('#Modal_Edit_Data').modal('show');
                    });
                });

                $('#FormEditModalID').on('submit', function(e) {
                    e.preventDefault();
                    var formData = new FormData(this);
                    $('#btn-edit-data .edit-data-label').hide();
                    $('#btn-edit-data .edit-data-progress').show();
                    
                    $.ajax({
                        url: "skm/" + id,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            if (res.errors) {
                                // handle errors
                            } else {
                                $('#Modal_Edit_Data').modal('hide');
                                table.ajax.reload();
                                Swal.fire("Berhasil", res.success, "success");
                            }
                        },
                        complete: function() {
                            $('#btn-edit-data .edit-data-label').show();
                            $('#btn-edit-data .edit-data-progress').hide();
                        }
                    });
                });

                $('body').on('click', '#getShowRowData', function() {
                    var showId = $(this).data('id');
                    $('#ShowRowModalBody').html('<div class="text-center py-10"><div class="spinner-border text-primary"></div><p class="mt-3">Loading...</p></div>');
                    $.get("skm/" + showId, function(res) {
                        $('#ShowRowModalBody').html(res.html);
                    });
                });

                $('#btn-sync-data').click(function() {
                    var button = this;
                    Swal.fire({
                        title: 'Sinkronisasi Data',
                        text: "Lanjutkan pengiriman data survey yang tertunda ke API SukmaDeli?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Sinkronkan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            button.setAttribute("data-kt-indicator", "on");
                            button.disabled = true;

                            $.ajax({
                                url: "{{ route('master.skm.sync') }}",
                                method: 'POST',
                                data: { _token: "{{ csrf_token() }}" },
                                success: function(res) {
                                    if (res.status == 'done') {
                                        Swal.fire("Berhasil", res.message, "success").then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire("Info", res.message, "info");
                                    }
                                },
                                error: function() {
                                    Swal.fire("Gagal", "Terjadi kesalahan sistem.", "error");
                                },
                                complete: function() {
                                    button.removeAttribute("data-kt-indicator");
                                    button.disabled = false;
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
