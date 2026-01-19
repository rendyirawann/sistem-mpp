@extends('backend.layout.app')
@section('title', 'Loket Management')
@section('content')


<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar d-flex flex-stack py-4 py-lg-8">
    <!--begin::Toolbar wrapper-->
    <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10" id="kt_toolbar">
        <!--begin::Page title-->
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <!--begin::Title-->
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Loket
                List</h1>
            <!--end::Title-->
            <!--begin::Breadcrumb-->
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <!--begin::Item-->
                <li class="breadcrumb-item text-muted">
                    <a class="text-muted text-hover-primary">Home</a>
                </li>
                <!--end::Item-->
                <!--begin::Item-->
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <!--end::Item-->

                <!--begin::Item-->
                <li class="breadcrumb-item text-muted">Layanan Management</li>
                <!--end::Item-->
                <!--begin::Item-->
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <!--end::Item-->
                <!--begin::Item-->
                <li class="breadcrumb-item text-gray-900">Layanan List</li>
                <!--end::Item-->
            </ul>
            <!--end::Breadcrumb-->
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center pt-4 pb-7 pt-lg-1 pb-lg-2">
            <!--begin::Wrapper-->
            <div class="me-3">
                <!--begin::Menu-->
                <!-- <a href="#" class="btn btn-sm btn-flex btn-dark fw-bold" data-kt-menu-trigger="click"
                    data-kt-menu-placement="bottom-end">
                    <i class="ki-outline ki-filter fs-2  me-1"></i>Filter</a> -->
                <!--begin::Menu 1-->
                <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true"
                    id="kt_menu_66b9aa0df2f28">
                    <!--begin::Header-->
                    <div class="px-7 py-5">
                        <div class="fs-5 text-gray-900 fw-bold">Filter Options</div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Menu separator-->
                    <div class="separator border-gray-200"></div>
                    <!--end::Menu separator-->
                    <!--begin::Form-->
                    <!--end::Form-->
                </div>
                <!--end::Menu 1-->
                <!--end::Menu-->
            </div>
            <!--end::Wrapper-->
            <!--begin::Button-->
            @can('loket.create')
            <button type="button" id="btn_tambah_data" class="btn btn-sm btn-primary">
                <i class="ki-outline ki-plus fs-2"></i>Add</button>
            @endcan
            <!--end::Button-->
        </div>
        <!--end::Actions-->
    </div>
    <!--end::Toolbar wrapper-->
</div>
<!--end::Toolbar-->

<div id="kt_app_content" class="app-content flex-column-fluid">
    <!--begin::Card-->
    <div class="card border border-gray-300">
        <!--begin::Card header-->
        <div class="card-header border-bottom border-gray-300">
            <!--begin::Card title-->
            <div class="card-title">
                <!--begin::Search-->
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                    <input type="text" data-kt-loket-table-filter="search" id="search"
                        class="form-control  w-250px ps-13" placeholder="Search loket" />
                </div>
                <!--end::Search-->
            </div>
            <!--begin::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <!--begin::Group actions-->
                <div class="d-flex justify-content-end align-items-center d-none me-3"
                    data-kt-loket-table-toolbar="selected">
                    <div class="fw-bold me-5">
                        <span class="me-2" data-kt-loket-table-select="selected_count"></span>Selected
                    </div>
                    <button type="button" class="btn btn-sm btn-danger" data-kt-loket-table-select="delete_selected"> <i
                            class="ki-outline ki-trash  me-2"></i>Delete
                        Selected</button>
                </div>
                <!--end::Group actions-->
                <!--begin::Toolbar-->
                <div class="d-flex justify-content-end" data-kt-loket-table-toolbar="base">
                    <!--begin::Reload Data-->
                    <button type="button" class="btn btn-sm btn-primary " id="refresh-table-btn">
                        <span class="indicator-label">
                            <i class="ki-outline ki-arrows-loop  me-2"></i> Refresh
                        </span>
                        <span class="indicator-progress">
                            Please Wait ... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                    <!--end::Reload Data-->
                </div>
                <!--end::Toolbar-->



            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body py-4">

            <table class="table align-middle table-row-dashed fs-6 gy-5 chimox" id="chimox">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        @can('loket.massdelete')
                        <th class="w-10px pe-2">
                            <div class="form-check form-check-sm form-check-custom  me-3">
                                <input class="form-check-input" type="checkbox" data-kt-check="true"
                                    data-kt-check-target="#chimox .form-check-input" value="1" />
                            </div>
                        </th>
                        @endcan
                        <th class="min-w-150px ">Nama Instansi</th>
                        <th class="min-w-150px ">Nama Layanan</th>
                        <th class="min-w-120px ">Kode Tenant</th>
                        <th class="min-w-140px text-center">Prefix</th>
                        <th class="min-w-100px ">Status</th>
                        @canany(['loket.show', 'loket.edit', 'loket.delete'])
                        <th class="min-w-120px text-center">Action</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                </tbody>
            </table>


        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
</div>



<!--begin::Modal - Add-->
<div class="modal fade" id="Modal_Tambah_Data" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <!--begin::Modal content-->
        <div class="modal-content" id="tambah-modal-content">
            <!--begin::Modal header-->
            <div class="modal-header border-gray-300" id="kt_modal_add_loket_header">
                <!--begin::Modal title-->
                <h2 class="fw-bold">Add Loket</h2>
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"
                    onclick="resetForm()">
                    <i class="ki-outline ki-cross fs-1 text-dark"></i>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body px-5 my-7">
                <!--begin::Form-->
                <form method="post" id="FormTambahModalID" class="form" enctype="multipart/form-data">
                    @csrf
                    <!--begin::Scroll-->
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add_loket_scroll"
                        data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto"
                        data-kt-scroll-dependencies="#kt_modal_add_loket_header"
                        data-kt-scroll-wrappers="#kt_modal_add_loket_scroll" data-kt-scroll-offset="300px">
                        <!--begin::Input group-->
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">Nama Layanan</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" name="nama_loket" id="nama_loket" class="form-control"
                                placeholder="Dinas/Badan" />
                            <span class="text-danger error-text nama_loket_error_add"></span>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">Kode Tenant</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" name="kode_tenant" id="kode_tenant" class="form-control"
                                placeholder="Ade Guna" />
                            <span class="text-danger error-text kode_tenant_error_add"></span>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label for="prefix" class="required fw-semibold fs-6 mb-2 text-left d-block">Prefix</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" name="prefix_tenant" id="prefix_tenant" class="form-control"
                                placeholder="A" />
                            <span class="text-danger error-text prefix_tenant_error_add"></span>
                            <!--end::Input-->
                        </div>
                        <div class="mb-5">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-5">Instansi</label>
                            <!--end::Label-->
                            <select class="form-control mb-3 mb-lg-0" name="skpd_id" id="skpd_id">
                                <option selected="selected" disabled>Pilih Instansi</option>
                                @foreach ($skpd as $sk)
                                    <option value="{{ $sk->id }}">{{ $sk->nama_skpd }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error-text skpd_error_add"></span>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">Status</label>
                            <!--end::Label-->

                            <!--begin::Select-->
                            <select name="isaktif" id="isaktif" class="form-select mb-3 mb-lg-0">
                                <option value="" selected disabled>Pilih Status</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                            <!--end::Select-->

                            <span class="text-danger error-text is_aktif_error_add"></span>
                        </div>

                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <!--end::Input group-->

                    </div>
                    <!--end::Scroll-->
                    <!--begin::Actions-->
                    <div class="text-center pt-10">
                        <button type="reset" class="btn btn-sm btn-secondary me-3" data-bs-dismiss="modal"
                            onclick="resetForm()">Discard</button>
                        <button type="submit" class="btn btn-sm btn-primary" id="btn-add-data">
                            <span class="indicator-label add-data-label">Submit</span>
                            <span class="indicator-progress add-data-progress" style="display: none;">Please Wait ...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                    <!--end::Actions-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - Add task-->


<!-- Begin Modal Edit -->
<div class="modal fade" id="Modal_Edit_Data" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content" id="edit-modal-content">
            <div class="modal-header border-gray-300" id="kt_modal_edit_loket_header">
                <h2 class="fw-bold">Edit loket</h2>
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-1 text-dark"></i>
                </div>
                <!--end::Close-->
            </div>
            <div class="modal-body px-5 my-7">
                <form id="FormEditModalID" class="form" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <!--begin::Scroll-->
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_edit_loket_scroll"
                        data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto"
                        data-kt-scroll-dependencies="#kt_modal_edit_loket_header"
                        data-kt-scroll-wrappers="#kt_modal_edit_loket_scroll" data-kt-scroll-offset="300px">
                        <div class="fv-row mb-7" id="EditRowModalBody"></div>
                        <input type="hidden" name="action" id="action" />
                    </div>
                    <div class="text-center pt-10">
                        <button type="button" class="btn btn-sm btn-secondary me-3"
                            data-bs-dismiss="modal">Discard</button>
                        <button type="submit" class="btn btn-sm btn-primary" id="btn-edit-data" value="submit">
                            <span class="indicator-label edit-data-label">Submit</span>
                            <span class="indicator-progress edit-data-progress" style="display: none;">Please Wait ...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Modal Edit -->


<!--begin modal hapus-->
<div class="modal fade" id="Modal_Hapus_Data" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" id="hapus-modal-content">
            <div class="modal-header border-gray-300">
                <h2 class="modal-title">Delete loket</h2>
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-1 text-dark"></i>
                </div>
                <!--end::Close-->
            </div>
            <div class="modal-body">

                <p>Apakah Anda Yakin ingin menghapusnya ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Discard</button>
                <button type="button" class="btn btn-sm btn-primary" id="SubmitDeleteRowForm">
                    <span class="indicator-label delete-data-label">Submit</span>
                    <span class="indicator-progress delete-data-progress" style="display: none;">Please Wait ...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </div>
    </div>
</div>
<!--end modal hapus-->



<!-- Modal Ban loket -->
<div class="modal fade" id="ModalBanloket" tabindex="-1" aria-labelledby="ModalBanloketLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header border-gray-300">
                <h3 class="modal-title" id="ModalBanloketLabel">Ban loket</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- ID loket -->
                <input type="hidden" id="ban_loket_id">

                <!-- Alasan Ban -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Alasan</label>
                    <textarea id="ban_reason" class="form-control" rows="3" placeholder="Tuliskan alasan ban"></textarea>
                </div>

                <!-- Durasi Ban -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Durasi Ban</label>
                    <select id="ban_duration" class="form-select">
                        <option value="permanent">Ban Permanen</option>
                        <option value="1h">1 Jam</option>
                        <option value="24h">1 Hari</option>
                        <option value="1w">1 Minggu</option>
                    </select>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-sm btn-danger" id="btnBanloket">Ban loket</button>
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
<script>
    function resetForm() {
        // Reset the form fields
        $("#FormTambahModalID").trigger('reset');

        // Reset avatar input
        $("#avatar").val('');

        // Clear error messages
        $(".error-text").text("");

        // Reset the background image for avatar (optional)
        $('#default-image').css('background-image', 'url({{ URL::to('
            assets / media / svg / files / blank - image.svg ') }})');


    }
</script>

<script>
    $('#avatar').on('change', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#default-image').css('background-image', 'url(' + e.target.result + ')');
            };
            reader.onerror = function() {
                alert("Failed to load image.");
            };
            reader.readAsDataURL(file);
        }
    });
</script>
<script>
    let table;
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }
    $(document).ready(function () {
        const canShow = @json(auth()->user()->can('loket.show'));
        const canEdit = @json(auth()->user()->can('loket.edit'));
        const canDelete = @json(auth()->user()->can('loket.delete'));
        const canMassDelete = @json(auth()->user()->can('loket.massdelete'));

        table = $('#chimox').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            ajax: {
                url: "{{ route('get-loket') }}",
                type: "GET"
            },
            columns: [
                canMassDelete ? {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        return `
                            <div class="form-check form-check-sm form-check-custom">
                                <input class="form-check-input row-check"
                                    type="checkbox"
                                    value="${data.id}">
                            </div>`;
                    }

                } : null,
                { data: 'nama_instansi' },
                { data: 'nama_loket' },
                { data: 'kode_tenant' },
                {data: 'prefix_tenant'},
                { data: 'isaktif' },

                (canShow || canEdit || canDelete) ? {
                    data: 'action',
                    orderable: false,
                    searchable: false
                } : null
            ].filter(Boolean)
        });


        // 🔄 Refresh button
        $('#refresh-table-btn').on('click', function () {
            table.ajax.reload(null, false);
        });

        // 🔍 Search input (native DataTables, JANGAN debounce)
        $('#search').on('keyup', debounce(function() {
            var table = $('.chimox').DataTable();
            table.search($(this).val()).draw();
        }, 500));

        $('#btnResetSearch').click(function() {
            $('#filterrole').val(null).trigger('change');
            table.draw(true);
        });

        $('#btnFiterSubmitSearch').click(function() {
            table.draw(true);
        });

    });
    // SHOW MODAL TAMBAH DATA
    $('#btn_tambah_data').on('click', function () {
        $('#Modal_Tambah_Data').modal('show');

        // reset form setiap buka modal
        $('#FormTambahModalID')[0].reset();
        $('.error-text').text('');
    });

    $('#FormTambahModalID').on('submit', function (e) {
        e.preventDefault();

        let form = this;

        $('#btn-add-data').prop('disabled', true);

        $.ajax({
            url: "{{ route('loket.store') }}", // ⬅️ ROUTE STORE loket
            method: "POST",
            data: new FormData(form),
            processData: false,
            contentType: false,
            dataType: "json",

            beforeSend: function () {
                $('.error-text').text('');
            },

            success: function (res) {
                if (res.errors) {
                    $.each(res.errors, function (key, val) {
                        $('.' + key + '_error_add').text(val[0]);
                    });

                    $('#btn-add-data').prop('disabled', false);
                    return;
                }

                // SUCCESS
                $('#Modal_Tambah_Data').modal('hide');
                $('.chimox').DataTable().ajax.reload();

                Swal.fire({
                    title: 'Berhasil',
                    text: res.success,
                    icon: 'success',
                    timer: 1500
                });

                $('#btn-add-data').prop('disabled', false);
            },

            error: function () {
                $('#btn-add-data').prop('disabled', false);

                Swal.fire({
                    title: 'Error',
                    text: 'Gagal menyimpan data',
                    icon: 'error'
                });
            }
        });
    });


    function updateToolbar() {
        let count = $('#chimox tbody .row-check:checked').length;

        $('[data-kt-loket-table-select="selected_count"]').text(count);

        if (count > 0) {
            $('[data-kt-loket-table-toolbar="selected"]').removeClass('d-none');
        } else {
            $('[data-kt-loket-table-toolbar="selected"]').addClass('d-none');
        }
    }


    // checkbox per row
    // $('#chimox').on('change', 'input.form-check-input', function () {
    //     updateToolbar();

    //     let allChecked =
    //         $('#chimox tbody input.form-check-input').length ===
    //         $('#chimox tbody input.form-check-input:checked').length;

    //     $('[data-kt-check]').prop('checked', allChecked);
    // });
    $(document).on('change', '.row-check', function () {
        updateToolbar();

        let allChecked =
            $('.row-check').length === $('.row-check:checked').length;

        $('[data-kt-check]').prop('checked', allChecked);
    });

    $(document).on('change', '[data-kt-check]', function () {
        let checked = $(this).is(':checked');
        let target = $(this).data('kt-check-target');
        $(target).prop('checked', checked);
        updateToolbar();
    });

    // select all
    $('[data-kt-check]').on('change', function () {
        let checked = $(this).is(':checked');
        let target = $(this).data('kt-check-target');
        $(target).prop('checked', checked);
        updateToolbar();
    });

    $(document).on('click', '[data-kt-loket-table-select="delete_selected"]', function () {

    let ids = [];

    $('#chimox tbody .row-check:checked').each(function () {
        ids.push($(this).val());
    });

    console.log('IDS LOKET:', ids); // 🔥 WAJIB ADA

    if (ids.length === 0) {
        Swal.fire('Warning', 'Tidak ada data dipilih', 'warning');
        return;
    }

    Swal.fire({
        title: 'Yakin hapus?',
        text: `Anda akan menghapus ${ids.length} Loket`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {

        if (result.isConfirmed) {

            $.ajax({
                url: "{{ route('loket.mass-delete') }}",
                type: "POST",
                data: {
                    ids: ids,
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {

                    Swal.fire('Berhasil', res.message, 'success');

                    table.ajax.reload(null, false); // 🔥 PENTING
                    $('[data-kt-check]').prop('checked', false);
                    updateToolbar();
                },
                error: function (xhr) {
                    Swal.fire(
                        'Error',
                        xhr.responseJSON?.message ?? 'Gagal hapus data',
                        'error'
                    );
                }
            });
        }
    });
});


    let editId = null;

    // KLIK TOMBOL EDIT (DARI DROPDOWN)
    $(document).on('click', '#getEditRowData', function () {
        editId = $(this).data('id');

        $.ajax({
            url: "{{ route('loket.edit', ':id') }}".replace(':id', editId),
            type: 'GET',
            success: function (res) {

                // isi body modal dengan form edit
                $('#EditRowModalBody').html(res.html);

                // set action form update
                $('#FormEditModalID').attr(
                    'action',
                    "{{ route('loket.update', ':id') }}".replace(':id', editId)
                );

                // tampilkan modal
                $('#Modal_Edit_Data').modal('show');
            },
            error: function () {
                Swal.fire('Error', 'Gagal mengambil data edit', 'error');
            }
        });
    });
        $(document).on('submit', '#FormEditModalID', function (e) {
        e.preventDefault(); // ⛔ STOP submit normal

        let form = this;
        let actionUrl = $(this).attr('action');

        $('#btn-edit-data').prop('disabled', true);

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: new FormData(form),
            processData: false,
            contentType: false,
            dataType: 'json',

            beforeSend: function () {
                $('.error-text').text('');
            },

            success: function (res) {
                if (res.errors) {
                    $.each(res.errors, function (key, val) {
                        $('.' + key + '_error_edit').text(val[0]);
                    });
                    $('#btn-edit-data').prop('disabled', false);
                    return;
                }

                // ✅ SUCCESS
                $('#Modal_Edit_Data').modal('hide');
                $('.chimox').DataTable().ajax.reload(null, false);

                Swal.fire({
                    icon: 'success',
                    title: res.judul ?? 'Berhasil',
                    text: res.success,
                    timer: 1500,
                    showConfirmButton: false
                });

                $('#btn-edit-data').prop('disabled', false);
            },

            error: function (xhr) {
                $('#btn-edit-data').prop('disabled', false);

                Swal.fire(
                    'Error',
                    xhr.responseJSON?.error ?? 'Gagal memperbarui data',
                    'error'
                );
            }
        });
    });

    let deleteId = null;

    $(document).on('click', '#getDeleteId', function () {
        deleteId = $(this).data('id');
    });

    $('#SubmitDeleteRowForm').on('click', function () {

        if (!deleteId) {
            Swal.fire('Error', 'ID tidak ditemukan', 'error');
            return;
        }

        $.ajax({
            url: "{{ route('loket.destroy', ':id') }}".replace(':id', deleteId),
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                $('#Modal_Hapus_Data').modal('hide');
                $('.chimox').DataTable().ajax.reload();

                Swal.fire('Berhasil', res.success, 'success');
            },
            error: function (xhr) {
                Swal.fire('Error', xhr.responseText ?? 'Gagal menghapus data', 'error');
            }
        });
    });



</script>



<script>
    function openBanModal(id) {
        $('#ban_loket_id').val(id);
        $('#ban_reason').val('');
        $('#ban_duration').val('permanent');
        $('#ModalBanloket').modal('show');
    }



    $('#btnBanloket').click(function() {

        let id = $('#ban_loket_id').val();
        let reason = $('#ban_reason').val();
        let duration = $('#ban_duration').val();

        if (reason.trim() === "") {
            Swal.fire("Peringatan!", "Alasan ban wajib diisi.", "warning");
            return;
        }

        $.ajax({
            url: `/loket/${id}/ban`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                reason: reason,
                duration: duration
            },
            success: function(res) {
                Swal.fire("Berhasil", res.success, "success");
                $('#ModalBanloket').modal('hide');
                $('#datatable').DataTable().ajax.reload();
            },
            error: function(xhr) {
                Swal.fire("Error", "Gagal melakukan ban loket!", "error");
            }
        });

    });


    function unbanloket(id) {

        Swal.fire({
            title: "Unban loket?",
            icon: "info",
            showCancelButton: true,
            confirmButtonText: "Ya, Unban",
            cancelButtonText: "Batal",
            customClass: {
                confirmButton: "btn btn-primary btn-sm me-2",
                cancelButton: "btn btn-secondary btn-sm"
            }
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: `/loket/${id}/unban`,
                    method: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        Swal.fire("Berhasil", res.success, "success");
                        $('#datatable').DataTable().ajax.reload();
                    },
                    error: function() {
                        Swal.fire("Error", "Gagal unban loket!", "error");
                    }
                });

            }
        });
    }
</script>
@endpush
@endsection
