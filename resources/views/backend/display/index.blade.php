@extends('backend.layout.app')
@section('title', 'Display Monitor TV Settings')
@section('content')

    {{-- Toolbar --}}
    <div id="kt_app_toolbar" class="app-toolbar d-flex flex-stack py-4 py-lg-8">
        <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10" id="kt_toolbar">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Display Monitor TV
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Display</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-gray-900">TV Settings</li>
                </ul>
            </div>

            <div class="d-flex align-items-center pt-4 pb-7 pt-lg-1 pb-lg-2">
                {{-- Quick Link to Open public display page in new tab --}}
                <a href="{{ route('display.monitor') }}" target="_blank" class="btn btn-sm btn-info fw-bold">
                    <i class="ki-outline ki-screen fs-2 me-1"></i> Buka Layar Monitor TV
                </a>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div id="kt_app_content" class="app-content flex-column-fluid">
        
        {{-- Alert notification --}}
        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center p-5 mb-8">
                <i class="ki-outline ki-shield-tick fs-2hx text-success me-4"></i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-dark fw-bold">Berhasil</h4>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger d-flex align-items-center p-5 mb-8">
                <i class="ki-outline ki-shield-cross fs-2hx text-danger me-4"></i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-dark fw-bold">Gagal</h4>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger d-flex align-items-center p-5 mb-8">
                <i class="ki-outline ki-information-3 fs-2hx text-danger me-4"></i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-dark fw-bold">Terjadi Kesalahan Validasi</h4>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="row g-7">
            {{-- KIRI: Global Settings (YouTube Video & Ticker Text) --}}
            <div class="col-lg-5">
                <div class="card border border-gray-300 shadow-sm mb-7">
                    <div class="card-header border-bottom border-gray-300">
                        <div class="card-title">
                            <h3 class="fw-boldest text-gray-900 fs-4">
                                <i class="ki-outline ki-setting-2 text-primary fs-3 me-2"></i> Pengaturan Konten Global
                            </h3>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('display-setting.update') }}" method="POST" id="form-global-setting">
                            @csrf

                            {{-- YouTube Video ID --}}
                            <div class="mb-8">
                                <label class="required fw-bold fs-6 mb-2">YouTube Video ID</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text bg-light text-gray-600">https://youtube.com/watch?v=</span>
                                    <input type="text" name="video_youtube_id" value="{{ old('video_youtube_id', $setting->video_youtube_id) }}" class="form-control" placeholder="qK65r2c462I" required>
                                </div>
                                <div class="text-muted fs-8">
                                    Masukkan 11 digit ID video dari YouTube. Contoh: <code>qK65r2c462I</code>
                                </div>
                            </div>

                            {{-- Running Ticker Text --}}
                            <div class="mb-8">
                                <label class="required fw-bold fs-6 mb-2">Teks Pengumuman Berjalan (Running Text)</label>
                                <textarea name="ticker_text" rows="8" class="form-control" placeholder="Tuliskan teks informasi di sini..." required>{{ old('ticker_text', $setting->ticker_text) }}</textarea>
                                <div class="text-muted fs-8 mt-2">
                                    Informasi/pengumuman ini akan berjalan secara bergulir di bagian paling bawah layar TV display.
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary" id="btn-submit-setting">
                                    <span class="indicator-label">
                                        <i class="ki-outline ki-check fs-2 me-1"></i> Simpan Perubahan
                                    </span>
                                    <span class="indicator-progress d-none">
                                        Mohon Tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Live Preview Embed Video --}}
                <div class="card border border-gray-300 shadow-sm">
                    <div class="card-header border-bottom border-gray-300">
                        <div class="card-title">
                            <h3 class="fw-boldest text-gray-900 fs-4">
                                <i class="ki-outline ki-video text-success fs-3 me-2"></i> Preview Video
                            </h3>
                        </div>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm">
                            <iframe src="https://www.youtube.com/embed/{{ $setting->video_youtube_id }}?autoplay=0&mute=1" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KANAN: Iklan Banner Slider --}}
            <div class="col-lg-7">
                {{-- Form Upload Banner (DENGAN FORM REPEATER MULTI-UPLOAD) --}}
                <div class="card border border-gray-300 shadow-sm mb-7">
                    <div class="card-header border-bottom border-gray-300">
                        <div class="card-title">
                            <h3 class="fw-boldest text-gray-900 fs-4">
                                <i class="ki-outline ki-plus text-primary fs-3 me-2"></i> Tambah Banner Iklan Baru (Multi-Upload)
                            </h3>
                        </div>
                        <div class="card-toolbar">
                            <button type="button" class="btn btn-light-primary btn-sm fw-bold" onclick="addRepeaterRow()">
                                <i class="ki-outline ki-plus-square fs-3 me-1"></i> Tambah Baris
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('display-setting.banner.store') }}" method="POST" enctype="multipart/form-data" id="form-upload-banner">
                            @csrf
                            
                            {{-- Repeater Container --}}
                            <div id="banner-repeater-container" class="d-flex flex-column gap-6 mb-6">
                                {{-- Baris Pertama Default --}}
                                <div class="repeater-row border border-dashed border-gray-400 rounded p-5 bg-light bg-opacity-30 position-relative">
                                    <button type="button" class="btn btn-icon btn-light-danger btn-sm rounded-circle position-absolute end-0 top-0 mt-n3 me-n3 shadow-xs d-none btn-remove-row" onclick="removeRepeaterRow(this)">
                                        <i class="ki-outline ki-trash fs-4"></i>
                                    </button>
                                    <div class="row g-4">
                                        {{-- Choose Image --}}
                                        <div class="col-md-7">
                                            <label class="required fw-bold fs-7 mb-1">Pilih File Gambar</label>
                                            <input type="file" name="banners[0][image]" class="form-control form-control-sm input-banner-image" accept="image/*" required>
                                        </div>

                                        {{-- Order Index --}}
                                        <div class="col-md-5">
                                            <label class="fw-bold fs-7 mb-1">Urutan Tampil (Order)</label>
                                            <input type="number" name="banners[0][order_index]" value="0" min="0" class="form-control form-control-sm">
                                        </div>

                                        {{-- Title --}}
                                        <div class="col-md-12">
                                            <label class="fw-bold fs-7 mb-1">Judul/Deskripsi Singkat (Opsional)</label>
                                            <input type="text" name="banners[0][title]" class="form-control form-control-sm" placeholder="Contoh: Banner Layanan Dukcapil Deli Serdang">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-muted fs-8 mt-1 mb-6">
                                <i class="ki-outline ki-information-2 text-warning fs-6 me-1"></i>
                                Format Gambar: JPG, JPEG, PNG, WEBP. Maks: 2 MB per file. Gunakan rasio lanskap/lebar untuk tampilan TV yang presisi.
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <button type="submit" class="btn btn-success" id="btn-submit-banner">
                                    <span class="indicator-label">
                                        <i class="ki-outline ki-file-up fs-2 me-1"></i> Unggah & Pasang Banner
                                    </span>
                                    <span class="indicator-progress d-none">
                                        Mengunggah... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Banner List --}}
                <div class="card border border-gray-300 shadow-sm">
                    <div class="card-header border-bottom border-gray-300">
                        <div class="card-title">
                            <h3 class="fw-boldest text-gray-900 fs-4">
                                <i class="ki-outline ki-folder text-warning fs-3 me-2"></i> Daftar Banner Aktif (Siklus Iklan Slider)
                            </h3>
                        </div>
                    </div>

                    <div class="card-body">
                        @if ($banners->isEmpty())
                            <div class="text-center py-10">
                                <i class="ki-outline ki-emoji-sad fs-3x text-muted mb-3 d-block"></i>
                                <span class="text-muted fw-bold fs-6">Belum ada banner iklan dinamis yang diunggah.</span>
                                <div class="text-muted fs-8 mt-1">Display Monitor TV akan otomatis menampilkan gambar fallback bawaan sistem di folder <code>public/images/iklan/</code>.</div>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-row-bordered table-row-dashed align-middle gy-4">
                                    <thead>
                                        <tr class="fw-bold fs-7 text-muted text-uppercase">
                                            <th class="min-w-100px">Gambar Preview</th>
                                            <th class="min-w-150px">Detail Info</th>
                                            <th class="text-center min-w-80px">Urutan</th>
                                            <th class="text-center min-w-100px">Status</th>
                                            <th class="text-center min-w-100px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-800 fw-semibold">
                                        @foreach ($banners as $banner)
                                            <tr>
                                                {{-- Preview Image --}}
                                                <td>
                                                    <div class="symbol symbol-70px symbol-2by3">
                                                        <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="rounded shadow-xs" style="object-fit: cover;">
                                                    </div>
                                                </td>

                                                {{-- Info --}}
                                                <td>
                                                    <span class="text-gray-900 fw-bold fs-6 d-block mb-1">
                                                        {{ $banner->title ?: 'Tanpa Judul' }}
                                                    </span>
                                                    <span class="text-muted fs-8 text-truncate d-block" style="max-width: 250px;">
                                                        Path: <code>{{ $banner->image_path }}</code>
                                                    </span>
                                                </td>

                                                {{-- Order Index --}}
                                                <td class="text-center">
                                                    <span class="badge badge-light-dark fw-boldest fs-7">{{ $banner->order_index }}</span>
                                                </td>

                                                {{-- Toggle Active Status --}}
                                                <td class="text-center">
                                                    <form action="{{ route('display-setting.banner.toggle', $banner->id) }}" method="POST" class="form-toggle-banner">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm {{ $banner->is_active ? 'btn-light-success text-success' : 'btn-light-danger text-danger' }} fw-boldest btn-submit-toggle">
                                                            <span class="indicator-label">
                                                                <i class="ki-outline {{ $banner->is_active ? 'ki-eye text-success' : 'ki-eye-slash text-danger' }} fs-5 me-1"></i>
                                                                {{ $banner->is_active ? 'Aktif' : 'Nonaktif' }}
                                                            </span>
                                                            <span class="indicator-progress d-none">
                                                                <span class="spinner-border spinner-border-sm align-middle"></span>
                                                            </span>
                                                        </button>
                                                    </form>
                                                </td>

                                                {{-- Delete Action --}}
                                                <td class="text-center">
                                                    <form action="{{ route('display-setting.banner.delete', $banner->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus banner iklan ini secara permanen?')" class="form-delete-banner">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm btn-submit-delete">
                                                            <span class="indicator-label">
                                                                <i class="ki-outline ki-trash fs-2 text-danger"></i>
                                                            </span>
                                                            <span class="indicator-progress d-none">
                                                                <span class="spinner-border spinner-border-sm align-middle text-danger"></span>
                                                            </span>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Javascript Form Repeater & Button Spinners --}}
    <script>
        let rowIndex = 0;

        /**
         * Tambah Baris Form Repeater Baru
         */
        function addRepeaterRow() {
            rowIndex++;
            const container = document.getElementById('banner-repeater-container');
            
            const newRow = document.createElement('div');
            newRow.className = 'repeater-row border border-dashed border-gray-400 rounded p-5 bg-light bg-opacity-30 position-relative animate__animated animate__fadeIn';
            
            newRow.innerHTML = `
                <button type="button" class="btn btn-icon btn-light-danger btn-sm rounded-circle position-absolute end-0 top-0 mt-n3 me-n3 shadow-xs btn-remove-row" onclick="removeRepeaterRow(this)">
                    <i class="ki-outline ki-trash fs-4"></i>
                </button>
                <div class="row g-4">
                    {{-- Choose Image --}}
                    <div class="col-md-7">
                        <label class="required fw-bold fs-7 mb-1">Pilih File Gambar</label>
                        <input type="file" name="banners[${rowIndex}][image]" class="form-control form-control-sm input-banner-image" accept="image/*" required>
                    </div>

                    {{-- Order Index --}}
                    <div class="col-md-5">
                        <label class="fw-bold fs-7 mb-1">Urutan Tampil (Order)</label>
                        <input type="number" name="banners[${rowIndex}][order_index]" value="0" min="0" class="form-control form-control-sm">
                    </div>

                    {{-- Title --}}
                    <div class="col-md-12">
                        <label class="fw-bold fs-7 mb-1">Judul/Deskripsi Singkat (Opsional)</label>
                        <input type="text" name="banners[${rowIndex}][title]" class="form-control form-control-sm" placeholder="Contoh: Banner Layanan Dukcapil Deli Serdang">
                    </div>
                </div>
            `;
            
            container.appendChild(newRow);
            updateRemoveButtons();
        }

        /**
         * Hapus Baris Form Repeater
         */
        function removeRepeaterRow(button) {
            const row = button.closest('.repeater-row');
            row.classList.remove('animate__fadeIn');
            row.classList.add('animate__fadeOut');
            
            setTimeout(() => {
                row.remove();
                updateRemoveButtons();
            }, 300);
        }

        /**
         * Update status visibilitas tombol hapus jika baris > 1
         */
        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.repeater-row');
            const removeButtons = document.querySelectorAll('.btn-remove-row');
            
            if (rows.length <= 1) {
                removeButtons.forEach(btn => btn.classList.add('d-none'));
            } else {
                removeButtons.forEach(btn => btn.classList.remove('d-none'));
            }
        }

        // ==========================================
        // DYNAMIC FILE SIZE VALIDATION (2 MB LIMIT)
        // ==========================================
        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('input-banner-image')) {
                const file = e.target.files[0];
                const maxSize = 2 * 1024 * 1024; // 2 MB
                
                if (file && file.size > maxSize) {
                    // Reset input value agar tidak bisa diupload
                    e.target.value = '';
                    
                    // Notifikasi Swal warning (Metronic default)
                    Swal.fire({
                        title: 'Berkas Terlalu Besar!',
                        text: `Berkas "${file.name}" memiliki ukuran ${(file.size / (1024 * 1024)).toFixed(2)} MB. Batas maksimal ukuran berkas adalah 2.00 MB. Silakan pilih berkas yang lebih kecil.`,
                        icon: 'warning',
                        buttonsStyling: false,
                        confirmButtonText: 'Pilih File Lain',
                        customClass: {
                            confirmButton: 'btn btn-sm btn-warning fw-bold'
                        }
                    });
                }
            }
        });

        // ==========================================
        // SPINNER SUBMIT BUTTONS HANDLER (METRONIC STYLE)
        // ==========================================
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                // Jangan cegah jika validasi gagal bawaan browser
                if (!this.checkValidity()) return;

                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.setAttribute('data-kt-indicator', 'on');
                    
                    const label = btn.querySelector('.indicator-label');
                    const progress = btn.querySelector('.indicator-progress');
                    
                    if (label && progress) {
                        label.classList.add('d-none');
                        progress.classList.remove('d-none');
                    }
                }
            });
        });
    </script>

@endsection
