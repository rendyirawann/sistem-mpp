@extends('backend.layout.app')
@section('title', 'Satuan List')
@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar d-flex flex-stack py-4 py-lg-8">
        <!--begin::Toolbar wrapper-->
        <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10" id="kt_toolbar">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Satuan
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
                    <li class="breadcrumb-item text-muted">Master</li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">Satuan List</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center pt-4 pb-7 pt-lg-1 pb-lg-2">
                
                <!--begin::Button-->
                @can('satuan.create')
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
    <!--begin::Content-->
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
                        <input type="text" data-kt-table-filter="search" id="search"
                            class="form-control  w-250px ps-13" placeholder="Search" />
                    </div>
                    <!--end::Search-->
                </div>
                <!--begin::Card title-->
                <!--begin::Card toolbar-->
                <div class="card-toolbar">
                     <!--begin::Group actions-->
                    <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
                        <div class="fw-bold me-5">
                            <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected
                        </div>
                        <button type="button" class="btn btn-danger btn-sm me-3" data-kt-user-table-select="delete_selected">Delete
                            Selected</button>
                    </div>
                    <!--end::Group actions-->
                    <!--begin::Toolbar-->
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                        <!--begin::Reload Data-->
                        <button type="button" class="btn btn-sm btn-primary " id="refresh-table-btn">
                            <span class="indicator-label">
                                <i class="ki-outline ki-arrows-loop  me-2"></i> Refresh Table
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
                <!--begin::Table-->
                <table class="table align-middle table-row-dashed fs-7 gy-2 chimox" id="chimox">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                          @can('satuan.massdelete')
                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm form-check-custom  me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true"
                                            data-kt-check-target="#chimox .form-check-input" value="1" />
                                    </div>
                                </th>
            @endcan
            <th class="min-w-125px">Nama Satuan</th>
            <th class="min-w-125px">Created At</th>
            <th class="min-w-125px">Updated At</th>
            <th class="min-w-125px">Created By</th>
            @canany(['satuan.show', 'satuan.edit', 'satuan.delete'])
                <th class="text-end min-w-100px ps-4 rounded-end">Actions</th>
            @endcanany
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
                <!--end::Table-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Content-->
   
    


<!--begin::Modal - Add Brand-->
<div class="modal fade" id="Modal_Tambah_Data" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-550px">
        <!--begin::Modal content-->
        <div class="modal-content" id="tambah-modal-content">
            <div class="modal-header align-items-center py-6 border-gray-300">
                    <h4 class="fw-bold">Add Data</h4>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary " data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1 text-dark"></i>
                    </div>
                    <!--end::Close-->
                </div>
            <form method="post" id="FormTambahModalID" class="form" enctype="multipart/form-data">
                @csrf
                <!--begin::Modal body-->
                <div class="modal-body px-2 my-2">
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add__scroll"
                        data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto"
                        data-kt-scroll-dependencies="#kt_modal_add_header"
                        data-kt-scroll-wrappers="#kt_modal_add_scroll" data-kt-scroll-offset="300px">

                        <!-- Input group: Nama Brand -->
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-7 mb-2">Nama Satuan</label>
                            <input type="text" name="nama" id="nama" class="form-control mb-3 mb-lg-0"
                                placeholder="Contoh: Box" />
                            <span class="text-danger error-text nama_error_add"></span>
                        </div>

                       

                    </div>
                </div>
                <!--end::Modal body-->

                <div class="modal-footer py-4">
                    <button type="reset" class="btn btn-sm btn-secondary me-3" data-bs-dismiss="modal"
                        onclick="resetForm()">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="btn-add-data">
                        <span class="indicator-label add-data-label">Simpan</span>
                        <span class="indicator-progress add-data-progress" style="display: none;">
                            Please Wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - Add Brand-->



    <!-- Begin Modal Edit -->
    <div class="modal fade" id="Modal_Edit_Data" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-550px">
            <div class="modal-content" id="edit-modal-content">
                <div class="modal-header align-items-center py-6 border-gray-300">
                    <h4 class="fw-bold">Edit Data</h4>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1 text-dark"></i>
                    </div>
                    <!--end::Close-->
                </div>
                <form id="FormEditModalID" class="form" enctype="multipart/form-data">
                <div class="modal-body px-2 my-2">
                    
                        @method('PUT')
                        @csrf
                        <!--begin::Scroll-->
                        <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_edit_user_scroll"
                            data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto"
                            data-kt-scroll-dependencies="#kt_modal_edit_user_header"
                            data-kt-scroll-wrappers="#kt_modal_edit_user_scroll" data-kt-scroll-offset="300px">
                            <div id="EditRowModalBody"></div>
                            <input type="hidden" name="action" id="action" />
                        </div>
                        

                    
                </div>
                <div class="modal-footer py-4">
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
    <!-- End Modal Edit -->


    <!--begin modal hapus-->
    <div class="modal fade" id="Modal_Hapus_Data" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-550px">
            <div class="modal-content" id="hapus-modal-content">
                 <div class="modal-header align-items-center py-6 border-gray-300">
                    <h4 class="modal-title">Delete Data</h4>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1 text-dark"></i>
                    </div>
                    <!--end::Close-->
                </div>
                <div class="modal-body">

                    <p>Apakah Anda Yakin ingin menghapusnya ?</p>
                </div>
                <div class="modal-footer py-4">
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

<!-- Begin Modal Show -->
<div class="modal fade shadow-sm" id="Modal_Show_Data" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-550px">
        <div class="modal-content" id="show-modal-content">
            <div class="modal-header align-items-center py-6 border-gray-300">
    <h4 class="fw-bold m-0">Detail Data</h4>
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
<!-- End Modal Show -->



    @push('stylesheets')
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="stylesheet" href="{{ URL::to('assets/plugins/custom/datatables/datatables.bundle.css') }}" />
    @endpush

    @push('scripts')
        <script src="{{ URL::to('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
        <script>
            function resetForm() {

                $("#FormTambahModalID").trigger('reset');

                $(".error-text").text("");
            }
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
                var canShow = @json(auth()->user()->can('satuan.show'));
                var canEdit = @json(auth()->user()->can('satuan.edit'));
                var canDelete = @json(auth()->user()->can('satuan.delete'));
                var canMassDelete = @json(auth()->user()->can('satuan.massdelete'));

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
                        url: "{{ route('get.master.satuan.data') }}",
                        type: 'GET',
                        data: function(d) {}
                    },
                    columns: [
                        // Kondisi untuk Mass Delete Checkbox
                        canMassDelete ? {
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, full, meta) {
                                return '<div class="form-check form-check-sm form-check-custom pe-2">' +
                                    '<input class="form-check-input" type="checkbox" value="' + full
                                    .id + '" />' +
                                    '</div>';
                            }
                        } : null,
                       
                        {
                            data: 'nama',
                            name: 'nama',
                            orderable: false,
                            searchable: false
                        },
                       

                        {
                            data: 'created_at',
                            name: 'created_at',
                            orderable: false,
                            searchable: false
                        },
                        
                        {
                            data: 'updated_at',
                            name: 'updated_at',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'user_id',
                            name: 'user_id',
                            orderable: false,
                            searchable: false
                        },
                        
                       
                        
                        // Kondisi untuk menampilkan kolom Action
                        (canShow || canEdit || canDelete) ? {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        } : null
                    ].filter(column => column !== null) // Filter untuk menghapus kolom null
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



                ////////////////////////BEGIN STORE DATA///////////////////////

                 // SHOW MODAL TAMBAH DATA
                 $('#btn_tambah_data').click(function() {
                    $('#Modal_Tambah_Data').modal('show');

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
                        url: "{{ route('satuan.store') }}",
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



                ////////////////////////END STORE DATA///////////////////////


////////////////////////BEGIN UPDATE DATA///////////////////////
                var targetedit = document.querySelector("#edit-modal-content");
                var blockUIEdit = new KTBlockUI(targetedit, {
                    message: '<div class="blockui-message"><span class="spinner-border text-primary"></span> <span class="text-white">Please Wait ...</span></div>',
                    overlayClass: "bg-dark bg-opacity-50",
                });

                // EDIT MODAL

                var id;
                $("body").on("click", "#getEditRowData", function(e) {
                    id = $(this).data("id");
                    $.ajax({
                        url: "satuan/" + id + "/edit",
                        dataType: "json",
                        success: function(result) {
                            console.log(result);
                            $("#EditRowModalBody").html(result.html);
                            $("#Modal_Edit_Data").modal("show");
                        },
                    });
                });

                // UPDATE MODAL
                $("#FormEditModalID").on("submit", function(e) {
                    e.preventDefault();
                    blockUIEdit.block();
                    $('#btn-edit-data .edit-data-label').hide();
                    $('#btn-edit-data .edit-data-progress').show();
                    $('#btn-edit-data').prop('disabled', true);
                    var id = $("#hidden_id").val();
                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                    });
                    $.ajax({
                        url: "satuan/" + id,
                        method: "POST",
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
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

////////////////////////END UPDATE DATA///////////////////////

////////////////////////BEGIN DESTROY DATA///////////////////////

var targethapus = document.querySelector("#hapus-modal-content");
                var blockUIHapus = new KTBlockUI(targethapus, {
                    message: '<div class="blockui-message"><span class="spinner-border text-primary"></span> <span class="text-white">Please Wait ...</span></div>',
                    overlayClass: "bg-dark bg-opacity-50",
                });

                // Delete article Ajax request.
                var deleteID;
                $("body").on("click", "#getDeleteId", function() {
                    deleteID = $(this).data("id");
                });
                $("#SubmitDeleteRowForm").click(function(e) {
                    e.preventDefault();
                    blockUIHapus.block();
                    $('#SubmitDeleteRowForm .delete-data-label').hide();
                    $('#SubmitDeleteRowForm .delete-data-progress').show();
                    $('#SubmitDeleteRowForm').prop('disabled', true);
                    var id = deleteID;
                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                    });
                    $.ajax({
                        url: "satuan/" + id,
                        method: "DELETE",
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
                                        },
                                    });
                                    $('#SubmitDeleteRowForm .delete-data-label').show();
                                    $('#SubmitDeleteRowForm .delete-data-progress').hide();
                                    $('#SubmitDeleteRowForm').prop('disabled', false);

                                }, 1000);
                            }
                        },
                    });
                });

////////////////////////END DESTROY DATA///////////////////////
////////////////////////BEGIN MASS DELETE DATA///////////////////////

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
                    $('[data-kt-user-table-select="selected_count"]').text(selectedCount);

                    if (selectedCount > 0) {
                        // Show the toolbar if there are selected checkboxes
                        $('[data-kt-user-table-toolbar="selected"]').removeClass('d-none');
                    } else {
                        // Hide the toolbar if no checkbox is selected
                        $('[data-kt-user-table-toolbar="selected"]').addClass('d-none');
                    }
                }




                // Function to handle checkbox change event
                $('.chimox').on('change', 'input.form-check-input', function() {
                    var selectedCount = $('.chimox tbody input.form-check-input:checked').length;

                    // Update selected count
                    $('[data-kt-user-table-select="selected_count"]').text(selectedCount);

                    if (selectedCount > 0) {
                        // Remove the d-none class to show the toolbar if any checkbox is selected
                        $('[data-kt-user-table-toolbar="selected"]').removeClass('d-none');
                    } else {
                        // Add the d-none class to hide the toolbar if no checkbox is selected
                        $('[data-kt-user-table-toolbar="selected"]').addClass('d-none');
                    }
                });



                $('button[data-kt-user-table-select="delete_selected"]').on('click', function() {
                    var selectedIds = [];

                    // Get all selected checkboxes
                    $('.chimox tbody input.form-check-input:checked').each(function() {
                        selectedIds.push($(this).val()); // Collect the user IDs
                    });

                    if (selectedIds.length > 0) {
                        // Confirm before deleting
                        Swal.fire({
                            title: 'Are you sure?',
                            text: 'You are about to delete ' + selectedIds.length + ' users.',
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
                                // Make an AJAX call to mass delete the users
                                $.ajax({
                                    url: "{{ route('satuan.mass-delete') }}", // Pastikan route ini ada
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
                                            $('[data-kt-user-table-toolbar="selected"]')
                                                .addClass('d-none');
                                            $('[data-kt-user-table-select="selected_count"]')
                                                .text(0);

                                            // Uncheck "Select All" checkbox
                                            $('[data-kt-check]').prop('checked', false);
                                        } else {
                                            Swal.fire('Error!', response.message, 'error');
                                        }
                                    },
                                    error: function() {
                                        Swal.fire('Error!',
                                            'An error occurred while deleting users.',
                                            'error');
                                    }
                                });
                            }
                        });
                    } else {
                        Swal.fire('Warning!', 'No users selected for deletion.', 'warning');
                    }
                });
////////////////////////END MASS DELETE DATA///////////////////////



////////////////////////BEGIN SHOW MODAL DETAIL///////////////////////
$("body").on("click", "#getShowRowData", function(e) {
    e.preventDefault();
    let id = $(this).data("id");
    $("#ShowRowModalBody").html(
        '<div class="text-center py-10"><div class="spinner-border text-primary"></div><p class="mt-3">Loading...</p></div>'
    );

    $.ajax({
        url: "/master/satuan/" + id,
        dataType: "json",
        success: function(result) {
            $("#ShowRowModalBody").html(result.html);
        },
        error: function() {
            $("#ShowRowModalBody").html('<p class="text-danger">Gagal memuat data.</p>');
        }
    });
});
////////////////////////BEGIN SHOW MODAL DETAIL///////////////////////


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
    @endpush
@endsection
