@extends('backend.layout.app')
@section('title', 'Intansi Management')
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
                    <li class="breadcrumb-item text-muted">Intansi Management</li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">Intansi List</li>
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

                @can('skpd.edit')
                    <button type="button" id="btn_batch_jam" class="btn btn-sm btn-warning me-2">
                        <i class="ki-outline ki-time fs-2"></i> Set Jam Masal
                    </button>
                @endcan

                @can('skpd.create')
                    {{-- <button type="button" id="btn_sync_sukma" class="btn btn-sm btn-info me-2">
                        <i class="ki-outline ki-arrows-circle fs-2"></i> Sync Sukma
                    </button> --}}
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
                            <th class="min-w-150px ">Nama Intansi</th>
                            <th class="min-w-120px ">Kepala Intansi</th>
                            <th class="min-w-140px text-center">NIP Kepala</th>
                            <th class="min-w-100px ">Status</th>
                            @canany(['skpd.show', 'skpd.edit', 'skpd.delete'])
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
                <div class="modal-header border-gray-300" id="kt_modal_add_skpd_header">
                    <!--begin::Modal title-->
                    <h2 class="fw-bold">Add Skpd</h2>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" onclick="resetForm()">
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
                                <label class="d-block fw-semibold fs-6 mb-5">Logo Instansi</label>
                                <!--end::Label-->
                                <!--begin::Image placeholder-->
                                <style>
                                    .image-input-placeholder {
                                        background-image: url('{{ URL::to('assets/media/svg/files/blank-image.svg') }}');
                                    }

                                    [data-bs-theme="dark"] .image-input-placeholder {
                                        background-image: url('{{ URL::to('assets/media/svg/files/blank-image-dark.svg') }}');
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
                                        title="Change Logo Instansi">
                                        <i class="ki-outline ki-pencil fs-7"></i>
                                        <!--begin::Inputs-->
                                        <input type="file" name="logo_skpd" id="logo_skpd"
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
                                <span class="text-danger error-text logo_skpd_error_add"></span>

                                <!--end::Hint-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="required fw-semibold fs-6 mb-2">Nama Instansi</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="nama_skpd" id="nama_skpd" class="form-control"
                                    placeholder="Dinas/Badan" />
                                <span class="text-danger error-text nama_skpd_error_add"></span>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="required fw-semibold fs-6 mb-2">Nama Kadis</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="kepala_skpd" id="kepala_skpd" class="form-control"
                                    placeholder="Ade Guna" />
                                <span class="text-danger error-text kepala_skpd_error_add"></span>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label for="password" class="required fw-semibold fs-6 mb-2">Nip Kadis</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="nip_kepala" id="nip_kepala" class="form-control"
                                    placeholder="198909152025211075" />
                                <span class="text-danger error-text nip_kepala_error_add"></span>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">ID Sukma Deli (External ID)</label>
                                <input type="number" name="external_id_sukma" id="external_id_sukma"
                                    class="form-control" placeholder="Contoh: 14" />
                                <div class="form-text">Biarkan kosong jika tidak terintegrasi dengan Sukma Deli.</div>
                                <span class="text-danger error-text external_id_sukma_error_add"></span>
                            </div>
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

                                <span class="text-danger error-text isaktif_error_add"></span>
                            </div>
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Lokasi Stand</label>
                                <input type="text" name="lokasi" id="lokasi" class="form-control"
                                    placeholder="Contoh: Gedung A, Lantai 1, Sebelah Kiri" />
                                <span class="text-danger error-text lokasi_error_add"></span>
                            </div>

                            <div class="row mb-5">
                                <div class="col-md-6">
                                    <label class="required fw-semibold fs-6 mb-2">Buka (Senin-Kamis)</label>
                                    <input type="time" name="buka_senin_kamis" class="form-control" value="08:00"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="required fw-semibold fs-6 mb-2">Tutup (Senin-Kamis)</label>
                                    <input type="time" name="tutup_senin_kamis" class="form-control" value="15:00"
                                        required>
                                </div>
                            </div>
                            <div class="row mb-5">
                                <div class="col-md-6">
                                    <label class="required fw-semibold fs-6 mb-2">Buka (Jumat)</label>
                                    <input type="time" name="buka_jumat" class="form-control" value="08:00"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="required fw-semibold fs-6 mb-2">Tutup (Jumat)</label>
                                    <input type="time" name="tutup_jumat" class="form-control" value="15:30"
                                        required>
                                </div>
                            </div>
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Kuota Antrian Harian</label>
                                <input type="number" name="kuota_harian" class="form-control" value="0" required>
                                <div class="form-text">Isi 0 jika tidak ada batasan kuota.</div>
                            </div>
                            <div class="form-check form-switch form-check-custom form-check-solid mb-7">
                                <input class="form-check-input" type="checkbox" name="is_force_close" value="1"
                                    id="forceCloseAdd" />
                                <label class="form-check-label text-danger fw-bold" for="forceCloseAdd">
                                    Tutup Paksa Layanan Ini Sekarang (Force Close)
                                </label>
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

    <div class="modal fade" id="Modal_Show_Data" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header border-gray-300">
                    <h2 class="fw-bold">Detail SKPD</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1 text-dark"></i>
                    </div>
                </div>
                <div class="modal-body px-5 my-7">
                    <div id="ShowRowModalBody"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>


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
                $('#default-image').css('background-image',
                    "url('{{ asset('assets/media/svg/files/blank-image.svg') }}')"
                );


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
            function debounce(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }
            $(document).ready(function() {

                const canShow = @json(auth()->user()->can('skpd.show'));
                const canEdit = @json(auth()->user()->can('skpd.edit'));
                const canDelete = @json(auth()->user()->can('skpd.delete'));
                const canMassDelete = @json(auth()->user()->can('skpd.massdelete'));

                var table = $('#chimox').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: false,
                    ajax: {
                        url: "{{ route('get-skpd') }}",
                        type: "GET"
                    },
                    columns: [
                        canMassDelete ? {
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(data) {
                                return `
                            <div class="form-check form-check-sm form-check-custom">
                                <input class="form-check-input"
                                    type="checkbox"
                                    value="${data.id}">
                            </div>`;
                            }
                        } : null,
                        {
                            data: 'nama_skpd'
                        },
                        {
                            data: 'kepala_skpd'
                        },
                        {
                            data: 'nip_kepala',
                            className: 'text-center nip-cell',
                            render: function(data) {
                                if (!data) return '-';
                                return data.match(/.{1,6}/g).join(' ');
                            }
                        },
                        {
                            data: 'isaktif'
                        },

                        (canShow || canEdit || canDelete) ? {
                            data: 'action',
                            orderable: false,
                            searchable: false
                        } : null
                    ].filter(Boolean)
                });


                // 🔄 Refresh button
                $('#refresh-table-btn').on('click', function() {
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
            $('#btn_tambah_data').on('click', function() {
                $('#Modal_Tambah_Data').modal('show');

                // reset form setiap buka modal
                $('#FormTambahModalID')[0].reset();
                $('.error-text').text('');
            });

            $('#FormTambahModalID').on('submit', function(e) {
                e.preventDefault();

                let form = this;

                $('#btn-add-data').prop('disabled', true);

                $.ajax({
                    url: "{{ route('skpd.store') }}", // ⬅️ ROUTE STORE SKPD
                    method: "POST",
                    data: new FormData(form),
                    processData: false,
                    contentType: false,
                    dataType: "json",

                    beforeSend: function() {
                        $('.error-text').text('');
                    },

                    success: function(res) {
                        if (res.errors) {
                            $.each(res.errors, function(key, val) {
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

                    error: function() {
                        $('#btn-add-data').prop('disabled', false);

                        Swal.fire({
                            title: 'Error',
                            text: 'Gagal menyimpan data',
                            icon: 'error'
                        });
                    }
                });
            });

            // EVENT KLIK TOMBOL SYNC
            $('#btn_sync_sukma').click(function() {
                Swal.fire({
                    title: 'Sinkronisasi Data?',
                    text: "Data SKPD akan diperbarui sesuai database Sukma Deli.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Sinkronkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Tampilkan Loading
                        Swal.fire({
                            title: 'Sedang Memproses...',
                            text: 'Mohon tunggu sebentar.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Request ke Server
                        $.ajax({
                            url: "{{ route('skpd.sync-sukma') }}",
                            method: "POST",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                Swal.fire("Berhasil", res.message, "success");
                                $('.chimox').DataTable().ajax.reload(); // Reload tabel otomatis
                            },
                            error: function(xhr) {
                                Swal.fire("Gagal", xhr.responseJSON?.message || "Terjadi kesalahan",
                                    "error");
                            }
                        });
                    }
                });
            });

            $('#btn_batch_jam').click(function() {
                Swal.fire({
                    title: 'Ubah Jam Operasional Masal',
                    text: "Pilih mode jam operasional untuk SELURUH instansi:",
                    icon: 'question',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa fa-sun text-white"></i> Mode Normal',
                    confirmButtonColor: '#009ef7', // Warna Biru Metronic
                    denyButtonText: '<i class="fa fa-moon text-white"></i> Mode Ramadhan',
                    denyButtonColor: '#ffc700', // Warna Kuning/Warning
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-primary btn-sm me-2',
                        denyButton: 'btn btn-warning btn-sm me-2',
                        cancelButton: 'btn btn-secondary btn-sm'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        sendBatchJam('normal');
                    } else if (result.isDenied) {
                        sendBatchJam('ramadan');
                    }
                });
            });

            function sendBatchJam(mode) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang mengatur jam untuk seluruh tenant.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "{{ route('skpd.batch-jam') }}",
                    method: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        mode: mode
                    },
                    success: function(res) {
                        Swal.fire("Berhasil", res.success, "success");
                        $('.chimox').DataTable().ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        Swal.fire("Gagal", xhr.responseJSON?.error || "Terjadi kesalahan sistem", "error");
                    }
                });
            }


            function updateToolbar() {
                let count = $('#chimox tbody input.form-check-input:checked').length;

                $('[data-kt-skpd-table-select="selected_count"]').text(count);

                if (count > 0) {
                    $('[data-kt-skpd-table-toolbar="selected"]').removeClass('d-none');
                } else {
                    $('[data-kt-skpd-table-toolbar="selected"]').addClass('d-none');
                }
            }

            // checkbox per row
            $('#chimox').on('change', 'input.form-check-input', function() {
                updateToolbar();

                let allChecked =
                    $('#chimox tbody input.form-check-input').length ===
                    $('#chimox tbody input.form-check-input:checked').length;

                $('[data-kt-check]').prop('checked', allChecked);
            });

            // select all
            $('[data-kt-check]').on('change', function() {
                let checked = $(this).is(':checked');
                let target = $(this).data('kt-check-target');
                $(target).prop('checked', checked);
                updateToolbar();
            });

            $('button[data-kt-skpd-table-select="delete_selected"]').on('click', function() {

                let ids = [];

                $('#chimox tbody input.form-check-input:checked').each(function() {
                    ids.push($(this).val());
                });

                if (ids.length === 0) {
                    Swal.fire('Warning', 'Tidak ada data dipilih', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Yakin hapus?',
                    text: `Anda akan menghapus ${ids.length} SKPD`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger btn-sm',
                        cancelButton: 'btn btn-secondary btn-sm'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('skpd.mass-delete') }}",
                            method: "POST",
                            data: {
                                ids: ids,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                Swal.fire('Berhasil', res.message, 'success');
                                table.ajax.reload();
                                $('[data-kt-check]').prop('checked', false);
                                updateToolbar();
                            },
                            error: function() {
                                Swal.fire('Error', 'Gagal hapus data', 'error');
                            }
                        });
                    }
                });
            });

            let editId = null;

            // KLIK TOMBOL EDIT (DARI DROPDOWN)
            $(document).on('click', '#getEditRowData', function() {
                editId = $(this).data('id');

                $.ajax({
                    url: "{{ route('skpd.edit', ':id') }}".replace(':id', editId),
                    type: 'GET',
                    success: function(res) {

                        // isi body modal dengan form edit
                        $('#EditRowModalBody').html(res.html);

                        // set action form update
                        $('#FormEditModalID').attr(
                            'action',
                            "{{ route('skpd.update', ':id') }}".replace(':id', editId)
                        );

                        // tampilkan modal
                        $('#Modal_Edit_Data').modal('show');
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal mengambil data edit', 'error');
                    }
                });
            });

            $(document).on('click', '#getShowRowData', function() {
                let id = $(this).data('id');

                // Tampilkan loading di modal body sebelum data masuk
                $('#ShowRowModalBody').html(
                    '<div class="text-center py-5"><span class="spinner-border text-primary"></span> Memuat data...</div>'
                );
                $('#Modal_Show_Data').modal('show');

                $.ajax({
                    url: "{{ route('skpd.show', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function(res) {
                        // Masukkan HTML dari controller ke dalam body modal
                        $('#ShowRowModalBody').html(res.html);
                    },
                    error: function() {
                        $('#ShowRowModalBody').html(
                            '<div class="text-danger text-center">Gagal mengambil data</div>');
                        Swal.fire('Error', 'Gagal mengambil data detail', 'error');
                    }
                });
            });

            $(document).on('submit', '#FormEditModalID', function(e) {
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

                    beforeSend: function() {
                        $('.error-text').text('');
                    },

                    success: function(res) {
                        if (res.errors) {
                            $.each(res.errors, function(key, val) {
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

                    error: function(xhr) {
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

            $(document).on('click', '#getDeleteId', function() {
                deleteId = $(this).data('id');
            });

            $('#SubmitDeleteRowForm').on('click', function() {

                if (!deleteId) {
                    Swal.fire('Error', 'ID tidak ditemukan', 'error');
                    return;
                }

                $.ajax({
                    url: "{{ route('skpd.destroy', ':id') }}".replace(':id', deleteId),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        $('#Modal_Hapus_Data').modal('hide');
                        $('.chimox').DataTable().ajax.reload(); // Reload tabel

                        Swal.fire('Berhasil', res.success, 'success');
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Gagal menghapus data', 'error');
                    }
                });
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
