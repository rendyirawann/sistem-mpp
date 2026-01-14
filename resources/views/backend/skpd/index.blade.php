@extends('backend.layout.app')
@section('title', 'skpd Management')
@section('content')


<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar d-flex flex-stack py-4 py-lg-8">
    <!--begin::Toolbar wrapper-->
    <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10" id="kt_toolbar">
        <!--begin::Page title-->
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <!--begin::Title-->
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Skpd
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
                <li class="breadcrumb-item text-muted">Skpd Management</li>
                <!--end::Item-->
                <!--begin::Item-->
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <!--end::Item-->
                <!--begin::Item-->
                <li class="breadcrumb-item text-gray-900">Skpd List</li>
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
                <a href="#" class="btn btn-sm btn-flex btn-dark fw-bold" data-kt-menu-trigger="click"
                    data-kt-menu-placement="bottom-end">
                    <i class="ki-outline ki-filter fs-2  me-1"></i>Filter</a>
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
            @can('skpd.create')
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
                    <input type="text" data-kt-skpd-table-filter="search" id="search"
                        class="form-control  w-250px ps-13" placeholder="Search skpd" />
                </div>
                <!--end::Search-->
            </div>
            <!--begin::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <!--begin::Group actions-->
                <div class="d-flex justify-content-end align-items-center d-none me-3"
                    data-kt-skpd-table-toolbar="selected">
                    <div class="fw-bold me-5">
                        <span class="me-2" data-kt-skpd-table-select="selected_count"></span>Selected
                    </div>
                    <button type="button" class="btn btn-sm btn-danger" data-kt-skpd-table-select="delete_selected"> <i
                            class="ki-outline ki-trash  me-2"></i>Delete
                        Selected</button>
                </div>
                <!--end::Group actions-->
                <!--begin::Toolbar-->
                <div class="d-flex justify-content-end" data-kt-skpd-table-toolbar="base">
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
                        @can('skpd.massdelete')
                        <th class="w-10px pe-2">
                            <div class="form-check form-check-sm form-check-custom  me-3">
                                <input class="form-check-input" type="checkbox" data-kt-check="true"
                                    data-kt-check-target="#chimox .form-check-input" value="1" />
                            </div>
                        </th>
                        @endcan
                        <th class="min-w-125px">Nama SKPD</th>
                        <th class="min-w-100px">Kepala SKPD</th>
                        <th class="min-w-100px">NIP Kepala</th>
                        <th class="min-w-100px">Status</th>
                        @canany(['skpd.show', 'skpd.edit', 'skpd.delete'])
                        <th class="text-end min-w-100px">Action</th>
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
            <div class="modal-header border-gray-300" id="kt_modal_add_skpd_header">
                <!--begin::Modal title-->
                <h2 class="fw-bold">Add skpd</h2>
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
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add_skpd_scroll"
                        data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto"
                        data-kt-scroll-dependencies="#kt_modal_add_skpd_header"
                        data-kt-scroll-wrappers="#kt_modal_add_skpd_scroll" data-kt-scroll-offset="300px">
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="d-block fw-semibold fs-6 mb-5">Avatar</label>
                            <!--end::Label-->
                            <!--begin::Image placeholder-->
                            <style>
                                .image-input-placeholder {
                                    background-image: url('{{ URL::to(' assets/media/svg/files/blank-image.svg') }}');
                                }

                                [data-bs-theme="dark"] .image-input-placeholder {
                                    background-image: url('{{ URL::to(' assets/media/svg/files/blank-image-dark.svg') }}');
                                }
                            </style>
                            <!--end::Image placeholder-->
                            <!--begin::Image input-->
                            <div class="image-input image-input-outline image-input-placeholder"
                                data-kt-image-input="true">
                                <!--begin::Preview existing avatar-->
                                <div class="image-input-wrapper w-125px h-125px" id="default-image"
                                    style="background-image: url({{ URL::to('assets/media/svg/files/blank-image.svg') }});">
                                </div>
                                <!--end::Preview existing avatar-->
                                <!--begin::Label-->
                                <label
                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                    title="Change avatar">
                                    <i class="ki-outline ki-pencil fs-7"></i>
                                    <!--begin::Inputs-->
                                    <input type="file" name="avatar" id="avatar"
                                        accept=".png, .jpg, .jpeg" />
                                    <input type="hidden" name="avatar_remove" />
                                    <!--end::Inputs-->
                                </label>
                                <!--end::Label-->
                                <!--begin::Cancel-->
                                <span
                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                    title="Cancel avatar">
                                    <i class="ki-outline ki-cross fs-2"></i>
                                </span>
                                <!--end::Cancel-->
                                <!--begin::Remove-->
                                <span
                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                    title="Remove avatar">
                                    <i class="ki-outline ki-cross fs-2"></i>
                                </span>
                                <!--end::Remove-->
                            </div>
                            <!--end::Image input-->
                            <!--begin::Hint-->
                            <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
                            <span class="text-danger error-text avatar_error_add"></span>

                            <!--end::Hint-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">Full Name</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" name="name" id="name" class="form-control mb-3 mb-lg-0"
                                placeholder="Full name" />
                            <span class="text-danger error-text name_error_add"></span>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->








                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">Email</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="email" name="email" id="email" class="form-control mb-3 mb-lg-0"
                                placeholder="example@domain.com" />
                            <span class="text-danger error-text email_error_add"></span>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">WhatsApp</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="teks" name="no_wa" id="no_wa" class="form-control mb-3 mb-lg-0"
                                placeholder="081273812533" />
                            <span class="text-danger error-text no_wa_error_add"></span>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label for="password" class="required fw-semibold fs-6 mb-2">Password</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="password" name="password" id="password" class="form-control  mb-3 mb-lg-0"
                                placeholder="Password" />
                            <span class="text-danger error-text password_error_add"></span>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label for="password_confirmation" class="required fw-semibold fs-6 mb-2">Confirm
                                Password</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control  mb-3 mb-lg-0" placeholder="Confirm Password" />
                            <span class="text-danger error-text password_confirmation_error_add"></span>
                            <!--end::Input-->
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
            <div class="modal-header border-gray-300" id="kt_modal_edit_skpd_header">
                <h2 class="fw-bold">Edit skpd</h2>
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
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_edit_skpd_scroll"
                        data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto"
                        data-kt-scroll-dependencies="#kt_modal_edit_skpd_header"
                        data-kt-scroll-wrappers="#kt_modal_edit_skpd_scroll" data-kt-scroll-offset="300px">
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
                <h2 class="modal-title">Delete skpd</h2>
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



<!-- Modal Ban skpd -->
<div class="modal fade" id="ModalBanskpd" tabindex="-1" aria-labelledby="ModalBanskpdLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header border-gray-300">
                <h3 class="modal-title" id="ModalBanskpdLabel">Ban skpd</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- ID skpd -->
                <input type="hidden" id="ban_skpd_id">

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
                <button class="btn btn-sm btn-danger" id="btnBanskpd">Ban skpd</button>
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
<script type="text/javascript">
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }
    $(document).ready(function() {
        var canShow = @json(auth()->user()->can('skpd.show'));
        var canEdit = @json(auth()->user()->can('skpd.edit'));
        var canDelete = @json(auth()->user()->can('skpd.delete'));
        var canMassDelete = @json(auth()->user()->can('skpd.massdelete'));


        var table = $('.chimox').DataTable({
            processing: true,
            language: {
                processing: "Please Wait ...",
                loadingRecords: false,
                zeroRecords: "Tidak ada data yang ditemukan",
                emptyTable: "Tidak ada data yang tersedia di tabel ini",
                search: "Cari:",
            },
            serverSide: true,
            order: false,
            ajax: {
                url: "{{ route('get-skpd') }}",
                type: 'GET',
                data: function(d) {
                    d.filterrole = $('#filterrole').val(); // Parameter tambahan untuk filtering
                }
            },
            columns: [
                canMassDelete ? {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        return `<input type="checkbox" value="${data.id}">`;
                    }
                } : null,
                { data: 'kode_skpd' },
                { data: 'nama_skpd' },
                { data: 'kepala_skpd' },
                { data: 'nip_kepala' },
                { data: 'is_aktif' },
                (canShow || canEdit || canDelete) ? {
                    data: 'action',
                    orderable: false,
                    searchable: false
                } : null
            ].filter(Boolean)

        });




        $(document).ready(function() {
            var button = document.querySelector("#refresh-table-btn");

            $('#refresh-table-btn').on('click', function() {
                // Disable the button to prevent further clicks
                button.setAttribute("data-kt-indicator", "on");
                button.disabled = true; // Disable the button

                // Reload the DataTable
                table.ajax.reload(function() {
                    // Re-enable the button after table is refreshed
                    button.removeAttribute("data-kt-indicator");
                    button.disabled = false; // Enable the button again
                });
            });
        });

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




        // SHOW MODAL TAMBAH DATA
        $('#btn_tambah_data').click(function() {
            $('#Modal_Tambah_Data').modal('show');

            // Perbarui gambar default
            $('#default-image').css('background-image',
                'url({{ URL::to('
                assets / media / svg / files / blank - image.svg ') }})');

        });

        var target = document.querySelector("#tambah-modal-content");
        var blockUI = new KTBlockUI(target, {
            message: '<div class="blockui-message"><span class="spinner-border text-primary"></span> <span class="text-white">Please Wait ...</span></div>',
            overlayClass: "bg-dark bg-opacity-50",
        });

        $('#FormTambahModalID').on('submit', function(event) {
            event.preventDefault();
            blockUI.block();

            $('#btn-add-data .add-data-label').hide();
            $('#btn-add-data .add-data-progress').show();
            $('#btn-add-data').prop('disabled', true);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('skpd.store') }}",
                method: 'post',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                    $(document).find("span.error-text").text("");
                },
                success: function(result) {
                    if (result.errors) {
                        setTimeout(function() {
                            $.each(result.errors, function(prefix, val) {
                                $("span." + prefix + "_error_add").text(val[
                                    0]);
                            });
                            blockUI.release();

                            Swal.fire({
                                title: "Gagal",
                                text: "Terjadi kesalahan validasi, periksa kembali input Anda.",
                                icon: "error",
                                timer: 1500,
                                confirmButtonText: "Oke",
                            });
                            $('#btn-add-data .add-data-label').show();
                            $('#btn-add-data .add-data-progress').hide();
                            $('#btn-add-data').prop('disabled', false);
                        }, 1000);
                    } else if (result.error) {
                        setTimeout(function() {
                            $("#Modal_Tambah_Data").modal("hide");
                            blockUI.release();

                            Swal.fire({
                                title: result.judul,
                                text: result.error,
                                icon: "error",
                                timer: 1500,
                                confirmButtonText: "Oke",
                            });

                            $('#btn-add-data .add-data-label').show();
                            $('#btn-add-data .add-data-progress').hide();
                            $('#btn-add-data').prop('disabled', false);


                        }, 1000);
                    } else {

                        setTimeout(function() {
                            $("#Modal_Tambah_Data").modal("hide");
                            $(".chimox").DataTable().ajax.reload();
                            blockUI.release();
                            Swal.fire({
                                title: "Berhasil",
                                text: result.success,
                                icon: "success",
                                timer: 1500,
                                confirmButtonText: "Oke",
                            });

                            $('#btn-add-data .add-data-label').show();
                            $('#btn-add-data .add-data-progress').hide();
                            $('#btn-add-data').prop('disabled', false);

                        }, 1000);
                    }
                },
            });
        });


        // Tombol "Batal"
        $("#Modal_Tambah_Data").on("hidden.bs.modal", function() {
            resetForm();
        });



        var targetedit = document.querySelector("#edit-modal-content");
        var blockUIEdit = new KTBlockUI(targetedit, {
            message: '<div class="blockui-message"><span class="spinner-border text-danger"></span> <span class="text-white">Please Wait ...</span></div>',
            overlayClass: "bg-dark bg-opacity-50"
        });

        // EDIT MODAL

        var id;
        $('body').on('click', '#getEditRowData', function(e) {

            id = $(this).data('id');
            $.ajax({
                url: "skpd/" + id + "/edit",
                dataType: "json",
                success: function(result) {
                    console.log(result);
                    $('#EditRowModalBody').html(result.html);
                    $('#Modal_Edit_Data').modal('show');
                }
            });
        });

        // UPDATE MODAL
        $('#FormEditModalID').on('submit', function(e) {
            e.preventDefault();
            blockUIEdit.block();
            $('#btn-edit-data .edit-data-label').hide();
            $('#btn-edit-data .edit-data-progress').show();
            $('#btn-edit-data').prop('disabled', true);
            var id = $('#hidden_id').val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "skpd/" + id,
                method: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                    $(document).find("span.error-text").text("");
                },
                success: function(result) {
                    if (result.errors) {
                        setTimeout(function() {
                            blockUIEdit.release();
                            $.each(result.errors, function(prefix, val) {
                                $("span." + prefix + "_error_edit").text(
                                    val[0]);
                            });

                            Swal.fire({
                                title: "Error",
                                text: "Terjadi kesalahan validasi, periksa kembali input Anda.",
                                icon: "error",
                                timer: 1500,
                                confirmButtonText: "Ok",
                            });
                            $('#btn-edit-data .edit-data-label').show();
                            $('#btn-edit-data .edit-data-progress').hide();
                            $('#btn-edit-data').prop('disabled', false);
                        }, 1000);
                    } else if (result.error) {

                        setTimeout(function() {
                            $("#Modal_Edit_Data").modal("hide");
                            blockUIEdit.release();

                            Swal.fire({
                                title: result.judul,
                                text: result.error,
                                icon: "error",
                                timer: 1500,
                                confirmButtonText: "Oke",
                            });
                            $('#btn-edit-data .edit-data-label').show();
                            $('#btn-edit-data .edit-data-progress').hide();
                            $('#btn-edit-data').prop('disabled', false);



                        }, 1000);


                    } else {
                        setTimeout(function() {
                            $("#Modal_Edit_Data").modal("hide");
                            $(".chimox").DataTable().ajax.reload();
                            blockUIEdit.release();

                            Swal.fire({
                                text: result.success,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                timer: 1500,
                                customClass: {
                                    confirmButton: "btn btn-primary",
                                },
                            });
                            $('#btn-edit-data .edit-data-label').show();
                            $('#btn-edit-data .edit-data-progress').hide();
                            $('#btn-edit-data').prop('disabled', false);


                        }, 1000);
                    }
                },
            });
        });






        var targethapus = document.querySelector("#hapus-modal-content");
        var blockUIHapus = new KTBlockUI(targethapus, {
            message: '<div class="blockui-message"><span class="spinner-border text-primary"></span> <span class="text-white">Please Wait ...</span></div>',
            overlayClass: "bg-dark bg-opacity-50"
        });

        // Delete article Ajax request.
        var deleteID;
        $('body').on('click', '#getDeleteId', function() {
            deleteID = $(this).data('id');
        })
        $('#SubmitDeleteRowForm').click(function(e) {
            e.preventDefault();
            blockUIHapus.block();

            $('#SubmitDeleteRowForm .delete-data-label').hide();
            $('#SubmitDeleteRowForm .delete-data-progress').show();
            $('#SubmitDeleteRowForm').prop('disabled', true);

            var id = deleteID;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "skpd/" + id,
                method: 'DELETE',
                success: function(result) {
                    if (result.error) {

                        setTimeout(function() {
                            $("#Modal_Hapus_Data").modal("hide");
                            blockUIHapus.release();

                            Swal.fire({
                                title: result.judul,
                                text: result.error,
                                icon: "error",
                                timer: 1500,
                                confirmButtonText: "Oke",
                            });
                            $('#SubmitDeleteRowForm .delete-data-label').show();
                            $('#SubmitDeleteRowForm .delete-data-progress').hide();
                            $('#SubmitDeleteRowForm').prop('disabled', false);

                        }, 1000);


                    } else {

                        setTimeout(function() {
                            $("#Modal_Hapus_Data").modal("hide");
                            $(".chimox").DataTable().ajax.reload();
                            blockUIHapus.release();
                            Swal.fire({
                                text: result.success,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                timer: 1500,
                                customClass: {
                                    confirmButton: "btn btn-primary",
                                }
                            });
                            $('#SubmitDeleteRowForm .delete-data-label').show();
                            $('#SubmitDeleteRowForm .delete-data-progress').hide();
                            $('#SubmitDeleteRowForm').prop('disabled', false);

                        }, 1000);
                    }
                },
            });

        });

        // Function to handle individual checkbox change event
        $('.chimox').on('change', 'input.form-check-input', function() {
            updateToolbar();

            // Check if all checkboxes are selected
            var allChecked = $('.chimox tbody input.form-check-input').length === $(
                '.chimox tbody input.form-check-input:checked').length;

            // Update the "Select All" checkbox
            $('[data-kt-check]').prop('checked', allChecked);
        });

        // Function to handle the "Select All" checkbox
        $('[data-kt-check]').on('change', function() {
            var isChecked = $(this).is(':checked');
            var target = $(this).data('kt-check-target');

            // Check/uncheck all checkboxes in the target
            $(target).prop('checked', isChecked);

            // Update toolbar display
            updateToolbar();
        });

        // Function to update the toolbar based on the selected checkboxes
        function updateToolbar() {
            var selectedCount = $('.chimox tbody input.form-check-input:checked').length;

            // Update the count in the toolbar
            $('[data-kt-skpd-table-select="selected_count"]').text(selectedCount);

            if (selectedCount > 0) {
                // Show the toolbar if there are selected checkboxes
                $('[data-kt-skpd-table-toolbar="selected"]').removeClass('d-none');
            } else {
                // Hide the toolbar if no checkbox is selected
                $('[data-kt-skpd-table-toolbar="selected"]').addClass('d-none');
            }
        }




        // Function to handle checkbox change event
        $('.chimox').on('change', 'input.form-check-input', function() {
            var selectedCount = $('.chimox tbody input.form-check-input:checked').length;

            // Update selected count
            $('[data-kt-skpd-table-select="selected_count"]').text(selectedCount);

            if (selectedCount > 0) {
                // Remove the d-none class to show the toolbar if any checkbox is selected
                $('[data-kt-skpd-table-toolbar="selected"]').removeClass('d-none');
            } else {
                // Add the d-none class to hide the toolbar if no checkbox is selected
                $('[data-kt-skpd-table-toolbar="selected"]').addClass('d-none');
            }
        });



        $('button[data-kt-skpd-table-select="delete_selected"]').on('click', function() {
            var selectedIds = [];

            // Get all selected checkboxes
            $('.chimox tbody input.form-check-input:checked').each(function() {
                selectedIds.push($(this).val()); // Collect the skpd IDs
            });

            if (selectedIds.length > 0) {
                // Confirm before deleting
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You are about to delete ' + selectedIds.length + ' skpd.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete!',
                    cancelButtonText: 'No, cancel!',

                    customClass: {
                        confirmButton: "btn btn-sm btn-primary",
                        cancelButton: "btn btn-sm btn-secondary",
                    }

                }).then(function(result) {
                    if (result.isConfirmed) {
                        // Make an AJAX call to mass delete the skpd
                        $.ajax({
                            url: "{{ route('skpd.mass-delete') }}", // Pastikan route ini ada
                            type: 'POST',
                            data: {
                                ids: selectedIds,
                                _token: '{{ csrf_token() }}' // CSRF token for security
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 1500, // Timer harus ditempatkan di dalam objek konfigurasi

                                    });


                                    // Reload the DataTable to reflect changes
                                    table.ajax.reload();

                                    // Reset the toolbar and uncheck the "Select All" checkbox
                                    $('[data-kt-skpd-table-toolbar="selected"]')
                                        .addClass('d-none');
                                    $('[data-kt-skpd-table-select="selected_count"]')
                                        .text(0);

                                    // Uncheck "Select All" checkbox
                                    $('[data-kt-check]').prop('checked', false);
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!',
                                    'An error occurred while deleting skpd.',
                                    'error');
                            }
                        });
                    }
                });
            } else {
                Swal.fire('Warning!', 'No skpd selected for deletion.', 'warning');
            }
        });







    });
    // Make the DIV element draggable:
    var elements = document.querySelectorAll('#Modal_Tambah_Data, #Modal_Edit_Data, #Modal_Hapus_Data');
    elements.forEach(function(element) {
        dragElement(element);

        function dragElement(elmnt) {
            var pos1 = 0,
                pos2 = 0,
                pos3 = 0,
                pos4 = 0;
            if (elmnt.querySelector('.modal-header')) {
                // if present, the header is where you move the DIV from:
                elmnt.querySelector('.modal-header').onmousedown = dragMouseDown;
            } else {
                // otherwise, move the DIV from anywhere inside the DIV:
                elmnt.onmousedown = dragMouseDown;
            }

            function dragMouseDown(e) {
                e = e || window.event;
                // get the mouse cursor position at startup:
                pos3 = e.clientX;
                pos4 = e.clientY;
                document.onmouseup = closeDragElement;
                // call a function whenever the cursor moves:
                document.onmousemove = elementDrag;
            }

            function elementDrag(e) {
                e = e || window.event;
                // calculate the new cursor position:
                pos1 = pos3 - e.clientX;
                pos2 = pos4 - e.clientY;
                pos3 = e.clientX;
                pos4 = e.clientY;
                // set the element's new position:
                elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
                elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
            }

            function closeDragElement() {
                // stop moving when mouse button is released:
                document.onmouseup = null;
                document.onmousemove = null;
            }
        }
    });
</script>


<script>
    function openBanModal(id) {
        $('#ban_skpd_id').val(id);
        $('#ban_reason').val('');
        $('#ban_duration').val('permanent');
        $('#ModalBanskpd').modal('show');
    }



    $('#btnBanskpd').click(function() {

        let id = $('#ban_skpd_id').val();
        let reason = $('#ban_reason').val();
        let duration = $('#ban_duration').val();

        if (reason.trim() === "") {
            Swal.fire("Peringatan!", "Alasan ban wajib diisi.", "warning");
            return;
        }

        $.ajax({
            url: `/skpd/${id}/ban`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                reason: reason,
                duration: duration
            },
            success: function(res) {
                Swal.fire("Berhasil", res.success, "success");
                $('#ModalBanskpd').modal('hide');
                $('#datatable').DataTable().ajax.reload();
            },
            error: function(xhr) {
                Swal.fire("Error", "Gagal melakukan ban skpd!", "error");
            }
        });

    });


    function unbanskpd(id) {

        Swal.fire({
            title: "Unban skpd?",
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
                    url: `/skpd/${id}/unban`,
                    method: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        Swal.fire("Berhasil", res.success, "success");
                        $('#datatable').DataTable().ajax.reload();
                    },
                    error: function() {
                        Swal.fire("Error", "Gagal unban skpd!", "error");
                    }
                });

            }
        });
    }
</script>
@endpush
@endsection
