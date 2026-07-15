@extends('backend.layout.app')

@section('title', 'Pengumuman Suara TV Display')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Pengumuman Suara TV
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Display Monitor</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-dark">Pengumuman Suara</li>
                </ul>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <!--begin::Card-->
            <div class="card card-flush shadow-sm">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                            <input type="text" id="search_table" class="form-control form-control-solid w-250px ps-13" placeholder="Cari Pengumuman..." />
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end gap-3" data-kt-user-table-toolbar="base">
                            <!--begin::Toggle Play/Stop Button-->
                            <button type="button" id="btn-toggle-play-all" class="btn btn-success d-flex align-items-center" data-state="play">
                                <span class="indicator-label d-flex align-items-center">
                                    <i class="ki-outline ki-notification-on fs-2 me-2"></i> Putar Semua
                                </span>
                                <span class="indicator-progress">
                                    Mohon tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                            <!--end::Toggle Play/Stop Button-->

                            <!--begin::Add announcement-->
                            <button type="button" class="btn btn-warning d-flex align-items-center text-dark" data-bs-toggle="modal" data-bs-target="#modal_add_announcement">
                                <i class="ki-outline ki-plus fs-2 me-2 text-dark"></i> Tambah Pengumuman Baru
                            </button>
                            <!--end::Add announcement-->
                        </div>
                    </div>
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body py-4">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="announcements_table">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-50px">Urutan</th>
                                    <th class="min-w-200px">Judul Pengumuman</th>
                                    <th class="min-w-350px">Kalimat Pengumuman (TTS)</th>
                                    <th class="min-w-100px text-center">Status</th>
                                    <th class="text-end min-w-150px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                <!-- Ajax populated -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
    </div>
    <!--end::Content-->
</div>

<!--begin::Modal - Add Announcement-->
<div class="modal fade" id="modal_add_announcement" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-1"></i>
                </div>
            </div>
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <form id="form_add_announcement" class="form" action="#">
                    @csrf
                    <div class="mb-13 text-center">
                        <h1 class="mb-3">Tambah Pengumuman Baru</h1>
                        <div class="text-muted fw-semibold fs-5">Buat kalimat pengumuman suara yang akan dibacakan di TV Display</div>
                    </div>

                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Judul Pengumuman</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder="Contoh: Himbauan Duduk di Lantai" name="title" required />
                    </div>

                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Kalimat Pengumuman (Text-To-Speech)</span>
                        </label>
                        <textarea class="form-control form-control-solid" rows="5" placeholder="Tuliskan seluruh teks pengumuman di sini dengan jelas..." name="text" required></textarea>
                        <div class="text-muted fs-8 mt-2">
                            Tips: Gunakan tanda baca titik (.), koma (,), atau spasi yang baik agar suara robot pembaca memiliki intonasi jeda yang alami.
                        </div>
                    </div>

                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2 required">Urutan Tampil</label>
                            <input type="number" class="form-control form-control-solid" name="order_index" value="1" min="1" required />
                        </div>
                        <div class="col-md-6 fv-row d-flex align-items-center mt-9">
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="is_active" checked="checked" />
                                <span class="form-check-label fw-semibold text-gray-700">Aktifkan Pengumuman</span>
                            </label>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btn_submit_add" class="btn btn-warning text-dark">
                            <span class="indicator-label text-dark">Simpan Perubahan</span>
                            <span class="indicator-progress">
                                Menyimpan... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Add Announcement-->

<!--begin::Modal - Edit Announcement-->
<div class="modal fade" id="modal_edit_announcement" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <div class="btn btn-sm btn-icon btn-icon-danger btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-1"></i>
                </div>
            </div>
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <form id="form_edit_announcement" class="form" action="#">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_id">

                    <div class="mb-13 text-center">
                        <h1 class="mb-3">Edit Pengumuman</h1>
                        <div class="text-muted fw-semibold fs-5">Perbarui kalimat pengumuman TV Display</div>
                    </div>

                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Judul Pengumuman</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" id="edit_title" name="title" required />
                    </div>

                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Kalimat Pengumuman (Text-To-Speech)</span>
                        </label>
                        <textarea class="form-control form-control-solid" rows="5" id="edit_text" name="text" required></textarea>
                        <div class="text-muted fs-8 mt-2">
                            Tips: Gunakan tanda baca titik (.), koma (,), atau spasi yang baik agar suara robot pembaca memiliki intonasi jeda yang alami.
                        </div>
                    </div>

                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2 required">Urutan Tampil</label>
                            <input type="number" class="form-control form-control-solid" id="edit_order_index" name="order_index" min="1" required />
                        </div>
                        <div class="col-md-6 fv-row d-flex align-items-center mt-9">
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" />
                                <span class="form-check-label fw-semibold text-gray-700">Aktifkan Pengumuman</span>
                            </label>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btn_submit_edit" class="btn btn-warning text-dark">
                            <span class="indicator-label text-dark">Simpan Perubahan</span>
                            <span class="indicator-progress">
                                Memperbarui... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Edit Announcement-->
@endsection

@push('stylesheets')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="{{ URL::to('assets/plugins/custom/datatables/datatables.bundle.css') }}" />
@endpush

@push('scripts')
<script src="{{ URL::to('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialize DataTable
        var table = $('#announcements_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('announcement.data') }}",
            columns: [
                { data: 'order_index', name: 'order_index', className: 'text-center fw-bold text-gray-800' },
                { data: 'title', name: 'title', className: 'fw-bold text-gray-800' },
                { 
                    data: 'text', 
                    name: 'text',
                    render: function(data, type, row) {
                        return data.length > 150 ? data.substr(0, 150) + '...' : data;
                    }
                },
                { data: 'is_active_label', name: 'is_active', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                zeroRecords: "Tidak ditemukan data pengumuman",
                info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                infoEmpty: "Data tidak tersedia",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Lanjut",
                    previous: "Sebelum"
                }
            }
        });

        // Live Search
        $('#search_table').keyup(function() {
            table.search($(this).val()).draw();
        });

        var isAnnouncementPlaying = false;

        function disablePlayButtons() {
            $('.btn-play').prop('disabled', true).addClass('opacity-50').attr('title', 'Sedang memutar pengumuman...');
        }

        function enablePlayButtons() {
            // Only enable if we're not currently playing
            if (!isAnnouncementPlaying) {
                $('.btn-play').prop('disabled', false).removeClass('opacity-50').attr('title', 'Putar Pengumuman Ini');
            }
        }

        // Keep buttons disabled if a redraw happens during playback
        table.on('draw', function() {
            if (isAnnouncementPlaying) {
                disablePlayButtons();
            }
        });

        // ==========================================
        // SINGLE PLAY BUTTON ACTION (Realtime sound)
        // ==========================================
        $(document).on('click', '.btn-play', function() {
            var id = $(this).data('id');
            var btn = $(this);
            
            // Immediately set playing state local to avoid double trigger
            isAnnouncementPlaying = true;
            disablePlayButtons();
            
            var originalHtml = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm align-middle"></span>');

            $.ajax({
                url: "{{ route('announcement.play') }}",
                type: "POST",
                data: { id: id },
                success: function(response) {
                    btn.html(originalHtml);
                    toastr.success(response.message || 'Suara pengumuman dikirim.');
                },
                error: function(xhr) {
                    isAnnouncementPlaying = false;
                    enablePlayButtons();
                    btn.html(originalHtml);
                    toastr.error('Gagal mengirim perintah suara pengumuman.');
                }
            });
        });

        // Helper to change button state to "Play All" (green, Putar Semua)
        function setButtonToPlay() {
            var btn = $('#btn-toggle-play-all');
            if (btn.length) {
                btn.attr('data-state', 'play');
                btn.removeClass('btn-danger').addClass('btn-success');
                btn.find('.indicator-label').html('<i class="ki-outline ki-notification-on fs-2 me-2"></i> Putar Semua');
                btn.removeAttr('data-kt-indicator').prop('disabled', false);
            }
        }

        // Helper to change button state to "Stop All" (red, Hentikan Suara)
        function setButtonToStop() {
            var btn = $('#btn-toggle-play-all');
            if (btn.length) {
                btn.attr('data-state', 'stop');
                btn.removeClass('btn-success').addClass('btn-danger');
                btn.find('.indicator-label').html('<i class="ki-outline ki-cross-circle fs-2 me-2"></i> Hentikan Suara');
                btn.removeAttr('data-kt-indicator').prop('disabled', false);
            }
        }

        // ==========================================
        // TOGGLE PLAY ALL / STOP ALL ACTION
        // ==========================================
        $('#btn-toggle-play-all').on('click', function() {
            var btn = $(this);
            var state = btn.attr('data-state');
            btn.attr('data-kt-indicator', 'on').prop('disabled', true);

            if (state === 'play') {
                $.ajax({
                    url: "{{ route('announcement.play_all') }}",
                    type: "POST",
                    success: function(response) {
                        setButtonToStop();
                        toastr.success(response.message || 'Seluruh pengumuman digabungkan dan diputar.');
                    },
                    error: function(xhr) {
                        setButtonToPlay();
                        var errMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal memutar seluruh pengumuman.';
                        toastr.error(errMsg);
                    }
                });
            } else {
                $.ajax({
                    url: "{{ route('announcement.stop') }}",
                    type: "POST",
                    success: function(response) {
                        setButtonToPlay();
                        toastr.success(response.message || 'Suara pengumuman dihentikan.');
                    },
                    error: function(xhr) {
                        btn.removeAttr('data-kt-indicator').prop('disabled', false);
                        toastr.error('Gagal mengirim perintah penghentian suara.');
                    }
                });
            }
        });

        // ==========================================
        // LARAVEL REVERB WEBSOCKET LISTENER FOR BUTTON SYNC
        // ==========================================
        if (window.Echo) {
            console.log("📢 Admin Announcement Panel: Connecting to Reverb Channel...");
            window.Echo.channel('antrian-channel')
                .listen('.panggilan-pengumuman', (e) => {
                    console.log("📢 WebSocket: Panggilan pengumuman dimulai, mengubah tombol ke Hentikan...");
                    setButtonToStop();
                    isAnnouncementPlaying = true;
                    disablePlayButtons();
                })
                .listen('.stop-pengumuman', (e) => {
                    console.log("🛑 WebSocket: Pengumuman dihentikan, mengubah tombol ke Putar...");
                    setButtonToPlay();
                    isAnnouncementPlaying = false;
                    enablePlayButtons();
                })
                .listen('.pengumuman-selesai', (e) => {
                    console.log("✅ WebSocket: Pengumuman selesai dibacakan, mengubah tombol ke Putar...");
                    setButtonToPlay();
                    isAnnouncementPlaying = false;
                    enablePlayButtons();
                });
        }


        // ==========================================
        // STORE (ADD NEW) ACTION
        // ==========================================
        $('#form_add_announcement').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = $('#btn_submit_add');
            btn.attr('data-kt-indicator', 'on').prop('disabled', true);

            $.ajax({
                url: "{{ route('announcement.store') }}",
                type: "POST",
                data: form.serialize(),
                success: function(response) {
                    btn.removeAttr('data-kt-indicator').prop('disabled', false);
                    if (response.success) {
                        $('#modal_add_announcement').modal('hide');
                        form[0].reset();
                        table.ajax.reload();
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                },
                error: function(xhr) {
                    btn.removeAttr('data-kt-indicator').prop('disabled', false);
                    var errors = xhr.responseJSON.errors;
                    var errorMsg = '';
                    $.each(errors, function(key, val) {
                        errorMsg += val[0] + '<br>';
                    });
                    Swal.fire({
                        html: errorMsg,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Mengerti",
                        customClass: {
                            confirmButton: "btn btn-danger"
                        }
                    });
                }
            });
        });

        // ==========================================
        // SHOW FOR EDIT ACTION
        // ==========================================
        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            
            $.ajax({
                url: "{{ url('display-announcement') }}/" + id,
                type: "GET",
                success: function(data) {
                    $('#edit_id').val(data.id);
                    $('#edit_title').val(data.title);
                    $('#edit_text').val(data.text);
                    $('#edit_order_index').val(data.order_index);
                    $('#edit_is_active').prop('checked', data.is_active ? true : false);
                    
                    $('#modal_edit_announcement').modal('show');
                },
                error: function() {
                    toastr.error('Gagal mengambil data pengumuman.');
                }
            });
        });

        // ==========================================
        // UPDATE (EDIT) ACTION
        // ==========================================
        $('#form_edit_announcement').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var id = $('#edit_id').val();
            var btn = $('#btn_submit_edit');
            btn.attr('data-kt-indicator', 'on').prop('disabled', true);

            $.ajax({
                url: "{{ url('display-announcement') }}/" + id,
                type: "POST", // Method override via serializing _method PUT
                data: form.serialize(),
                success: function(response) {
                    btn.removeAttr('data-kt-indicator').prop('disabled', false);
                    if (response.success) {
                        $('#modal_edit_announcement').modal('hide');
                        table.ajax.reload();
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                },
                error: function(xhr) {
                    btn.removeAttr('data-kt-indicator').prop('disabled', false);
                    var errors = xhr.responseJSON.errors;
                    var errorMsg = '';
                    $.each(errors, function(key, val) {
                        errorMsg += val[0] + '<br>';
                    });
                    Swal.fire({
                        html: errorMsg,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Mengerti",
                        customClass: {
                            confirmButton: "btn btn-danger"
                        }
                    });
                }
            });
        });

        // ==========================================
        // DESTROY (DELETE) ACTION
        // ==========================================
        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            
            Swal.fire({
                text: "Apakah Anda yakin ingin menghapus pengumuman ini?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-danger",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: "{{ url('display-announcement') }}/" + id,
                        type: "DELETE",
                        success: function(response) {
                            table.ajax.reload();
                            Swal.fire({
                                text: response.message,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Selesai",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        },
                        error: function() {
                            Swal.fire({
                                text: "Gagal menghapus pengumuman.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "OK",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
