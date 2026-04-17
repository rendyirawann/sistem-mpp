@extends('backend.layout.app')
@section('title', 'Layanan SKM List')
@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar d-flex flex-stack py-4 py-lg-8">
        <!--begin::Toolbar wrapper-->
        <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10" id="kt_toolbar">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Layanan SKM List</h1>
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
                    <li class="breadcrumb-item text-gray-900">Layanan SKM List</li>
                </ul>
            </div>
            <!--begin::Actions-->
            <div class="d-flex align-items-center pt-4 pb-7 pt-lg-1 pb-lg-2">
                @can('layanan_skm.create')
                    <button type="button" id="btn_tambah_data" class="btn btn-sm btn-primary">
                        <i class="ki-outline ki-plus fs-2"></i>Add Layanan</button>
                @endcan
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
                    <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
                        <div class="fw-bold me-5">
                            <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected
                        </div>
                        <button type="button" class="btn btn-danger btn-sm me-3" data-kt-user-table-select="delete_selected">Delete Selected</button>
                    </div>
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
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
                <table class="table align-middle table-row-dashed fs-7 gy-2 chimox" id="chimox">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            @can('layanan_skm.massdelete')
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm form-check-custom me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true"
                                            data-kt-check-target="#chimox .form-check-input" value="1" />
                                    </div>
                                </th>
                            @endcan
                            <th class="min-w-150px">Layanan</th>
                            <th class="min-w-200px">OPD</th>
                            <th class="min-w-50px">ID Layanan</th>
                            <th class="min-w-100px">Created At</th>
                            @canany(['layanan_skm.show', 'layanan_skm.edit', 'layanan_skm.delete'])
                                <th class="text-end min-w-100px ps-4 rounded-end">Actions</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal - Add Layanan -->
    <div class="modal fade" id="Modal_Tambah_Data" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-550px">
            <div class="modal-content" id="tambah-modal-content">
                <div class="modal-header align-items-center py-6 border-gray-300">
                    <h4 class="fw-bold">Add Layanan</h4>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1 text-dark"></i>
                    </div>
                </div>
                <form method="post" id="FormTambahModalID" class="form" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body px-2 my-2">
                        <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add__scroll">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-7 mb-2">OPD (SKPD)</label>
                                <select name="id_opd" id="id_opd" class="form-select form-select-sm" data-control="select2" data-placeholder="Pilih OPD" data-dropdown-parent="#Modal_Tambah_Data">
                                    <option></option>
                                    @php $skpds = \App\Models\Skpd::all(); @endphp
                                    @foreach($skpds as $skpd)
                                        <option value="{{ $skpd->external_id_sukma }}">{{ $skpd->nama_skpd }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text id_opd_error_add"></span>
                            </div>
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-7 mb-2">ID Layanan (Internal)</label>
                                <input type="number" name="id_layanan" class="form-control mb-3 mb-lg-0" placeholder="Contoh: 123" />
                                <span class="text-danger error-text id_layanan_error_add"></span>
                            </div>
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-7 mb-2">Nama Layanan</label>
                                <input type="text" name="layanan" class="form-control mb-3 mb-lg-0" placeholder="Nama Layanan" />
                                <span class="text-danger error-text layanan_error_add"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-4">
                        <button type="reset" class="btn btn-sm btn-secondary me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary" id="btn-add-data">
                            <span class="indicator-label add-data-label">Simpan</span>
                            <span class="indicator-progress add-data-progress" style="display: none;">Please Wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="Modal_Edit_Data" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-550px">
            <div class="modal-content" id="edit-modal-content">
                <div class="modal-header align-items-center py-6 border-gray-300">
                    <h4 class="fw-bold">Edit Layanan</h4>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1 text-dark"></i>
                    </div>
                </div>
                <form id="FormEditModalID" class="form" enctype="multipart/form-data">
                    <div class="modal-body px-2 my-2">
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

    <!-- Modal Hapus -->
    <div class="modal fade" id="Modal_Hapus_Data" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-550px">
            <div class="modal-content" id="hapus-modal-content">
                <div class="modal-header align-items-center py-6 border-gray-300">
                    <h4 class="modal-title">Delete Data</h4>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1 text-dark"></i>
                    </div>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda Yakin ingin menghapusnya?</p>
                </div>
                <div class="modal-footer py-4">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Discard</button>
                    <button type="button" class="btn btn-sm btn-primary" id="SubmitDeleteRowForm">
                        <span class="indicator-label delete-data-label">Submit</span>
                        <span class="indicator-progress delete-data-progress" style="display: none;">Please Wait ... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Show -->
    <div class="modal fade shadow-sm" id="Modal_Show_Data" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-550px">
            <div class="modal-content" id="show-modal-content">
                <div class="modal-header align-items-center py-6 border-gray-300">
                    <h4 class="fw-bold m-0">Detail Layanan</h4>
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
                var canShow = @json(auth()->user()->can('layanan_skm.show'));
                var canEdit = @json(auth()->user()->can('layanan_skm.edit'));
                var canDelete = @json(auth()->user()->can('layanan_skm.delete'));
                var canMassDelete = @json(auth()->user()->can('layanan_skm.massdelete'));

                var table = $('.chimox').DataTable({
                    processing: true,
                    language: {
                        processing: "Please Wait ...",
                        zeroRecords: "Tidak ada data yang ditemukan",
                        emptyTable: "Tidak ada data tersedia",
                        search: "Cari:",
                    },
                    serverSide: true,
                    ajax: "{{ route('get.master.layanan-skm.data') }}",
                    columns: [
                        canMassDelete ? {
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, full, meta) {
                                return '<div class="form-check form-check-sm form-check-custom pe-2"><input class="form-check-input" type="checkbox" value="' + full.id + '" /></div>';
                            }
                        } : null,
                        { data: 'layanan', name: 'layanan' },
                        { data: 'skpd_name', name: 'skpd_name' },
                        { data: 'id_layanan', name: 'id_layanan' },
                        { data: 'created_at', name: 'created_at' },
                        (canShow || canEdit || canDelete) ? { data: 'action', name: 'action', orderable: false, searchable: false } : null
                    ].filter(column => column !== null)
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

                $('#btn_tambah_data').click(function() {
                    $('#Modal_Tambah_Data').modal('show');
                });

                $('#FormTambahModalID').on('submit', function(e) {
                    e.preventDefault();
                    var formData = new FormData(this);
                    $('#btn-add-data .add-data-label').hide();
                    $('#btn-add-data .add-data-progress').show();
                    $('#btn-add-data').prop('disabled', true);

                    $.ajax({
                        url: "{{ route('layanan-skm.store') }}",
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            if (res.errors) {
                                $.each(res.errors, function(k, v) {
                                    $('.' + k + '_error_add').text(v[0]);
                                });
                            } else {
                                $('#Modal_Tambah_Data').modal('hide');
                                table.ajax.reload();
                                Swal.fire("Berhasil", res.success, "success");
                                $('#FormTambahModalID').trigger('reset');
                                $('#id_opd').val(null).trigger('change');
                            }
                        },
                        complete: function() {
                            $('#btn-add-data .add-data-label').show();
                            $('#btn-add-data .add-data-progress').hide();
                            $('#btn-add-data').prop('disabled', false);
                        }
                    });
                });

                var id;
                $('body').on('click', '#getEditRowData', function() {
                    id = $(this).data('id');
                    $.get("layanan-skm/" + id + "/edit", function(res) {
                        $('#EditRowModalBody').html(res.html);
                        $('#Modal_Edit_Data').modal('show');
                        // Re-init Select2 in edit modal if needed
                        $('#EditRowModalBody select').select2({
                            dropdownParent: $('#Modal_Edit_Data')
                        });
                    });
                });

                $('#FormEditModalID').on('submit', function(e) {
                    e.preventDefault();
                    var formData = new FormData(this);
                    $.ajax({
                        url: "layanan-skm/" + id,
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
                        }
                    });
                });

                var deleteID;
                $('body').on('click', '#getDeleteId', function() {
                    deleteID = $(this).data('id');
                });

                $('#SubmitDeleteRowForm').click(function() {
                    $.ajax({
                        url: "layanan-skm/" + deleteID,
                        method: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function(res) {
                            $('#Modal_Hapus_Data').modal('hide');
                            table.ajax.reload();
                            Swal.fire("Berhasil", res.success, "success");
                        }
                    });
                });

            });
        </script>
    @endpush
@endsection
