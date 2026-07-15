@extends('backend.layout.app')
@section('title', 'Pengaturan Landing Antrian Online')
@section('content')

    <div id="kt_app_toolbar" class="app-toolbar py-4">
        <div class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column">
                <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">Landing Antrian Online</h1>
                <ul class="breadcrumb fw-semibold fs-7 my-1">
                    <li class="breadcrumb-item text-muted">Pengaturan</li>
                    <li class="breadcrumb-item text-gray-900">Halaman /antrian-online</li>
                </ul>
            </div>
            <a href="{{ route('antrian-online') }}" target="_blank" class="btn btn-sm btn-light-primary">
                <i class="ki-outline ki-eye fs-3"></i> Lihat Halaman
            </a>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="app-container container-fluid">

            <div class="row g-5">
                {{-- ============ KONTEN HERO & FOOTER ============ --}}
                <div class="col-lg-8">
                    <div class="card border border-gray-300 mb-5">
                        <div class="card-header"><h3 class="card-title fw-bold">Konten Hero &amp; Footer</h3></div>
                        <div class="card-body">
                            <form action="{{ route('landing-setting.update') }}" method="POST">
                                @csrf
                                <div class="fw-bold text-primary mb-3">Navbar &amp; Hero</div>
                                <div class="mb-4">
                                    <label class="form-label required">Nama Brand (Navbar)</label>
                                    <input type="text" name="brand_name" class="form-control"
                                        value="{{ old('brand_name', $settings['brand_name'] ?? 'Portal Antrian MPP') }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label required">Judul Hero</label>
                                    <input type="text" name="hero_title" class="form-control"
                                        value="{{ old('hero_title', $settings['hero_title'] ?? '') }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Subjudul Hero</label>
                                    <textarea name="hero_subtitle" rows="3" class="form-control">{{ old('hero_subtitle', $settings['hero_subtitle'] ?? '') }}</textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-5 mb-4">
                                        <label class="form-label">Label Tombol Hero</label>
                                        <input type="text" name="hero_button_label" class="form-control"
                                            value="{{ old('hero_button_label', $settings['hero_button_label'] ?? 'Panduan') }}">
                                    </div>
                                    <div class="col-md-7 mb-4">
                                        <label class="form-label">Link Tombol Hero (URL atau #)</label>
                                        <input type="text" name="hero_button_link" class="form-control"
                                            value="{{ old('hero_button_link', $settings['hero_button_link'] ?? '#') }}">
                                    </div>
                                </div>

                                <div class="separator my-5"></div>
                                <div class="fw-bold text-primary mb-3">Antrian Online</div>
                                @php
                                    $hariAktif = array_filter(array_map('intval', explode(',', $settings['online_hari'] ?? '1,2,3,4,5')));
                                    $namaHari = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
                                @endphp
                                <div class="mb-4">
                                    <label class="form-label d-block">Hari Operasional (jendela mingguan)</label>
                                    <div class="d-flex flex-wrap gap-4">
                                        @foreach ($namaHari as $iso => $label)
                                            <label class="form-check form-check-custom form-check-sm">
                                                <input class="form-check-input" type="checkbox" name="online_hari[]"
                                                    value="{{ $iso }}" {{ in_array($iso, $hariAktif) ? 'checked' : '' }}>
                                                <span class="form-check-label ms-2">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="form-text fs-8">Antrian online hanya bisa diambil pada hari yang dicentang. Jika kuota minggu berjalan penuh, tombol nonaktif hingga Senin berikutnya. Kuota harian diatur per-instansi di menu Instansi (kolom Kuota Harian).</div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Batas Jam Ambil (Hari Ini)</label>
                                    <input type="time" name="online_cutoff" class="form-control w-200px"
                                        value="{{ $settings['online_cutoff'] ?? '14:00' }}">
                                    <div class="form-text fs-8">Lewat jam ini, warga tidak bisa memilih <b>hari ini</b> lagi (hanya hari berikutnya), meski kuota hari ini masih ada.</div>
                                </div>

                                <div class="separator my-5"></div>
                                <div class="fw-bold text-primary mb-3">Footer</div>
                                <div class="mb-4">
                                    <label class="form-label">Nama Instansi (Footer)</label>
                                    <input type="text" name="footer_brand" class="form-control"
                                        value="{{ old('footer_brand', $settings['footer_brand'] ?? '') }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Deskripsi Footer</label>
                                    <textarea name="footer_description" rows="2" class="form-control">{{ old('footer_description', $settings['footer_description'] ?? '') }}</textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Email</label>
                                        <input type="text" name="footer_email" class="form-control"
                                            value="{{ old('footer_email', $settings['footer_email'] ?? '') }}">
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Telepon</label>
                                        <input type="text" name="footer_phone" class="form-control"
                                            value="{{ old('footer_phone', $settings['footer_phone'] ?? '') }}">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Alamat (boleh beberapa baris)</label>
                                    <textarea name="footer_address" rows="3" class="form-control">{{ old('footer_address', $settings['footer_address'] ?? '') }}</textarea>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Teks Copyright</label>
                                    <input type="text" name="footer_copyright" class="form-control"
                                        value="{{ old('footer_copyright', $settings['footer_copyright'] ?? '') }}">
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ki-outline ki-check fs-3"></i> Simpan Pengaturan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ============ MEDIA SOSIAL ============ --}}
                <div class="col-lg-4">
                    <div class="card border border-gray-300 mb-5">
                        <div class="card-header"><h3 class="card-title fw-bold">Media Sosial Footer</h3></div>
                        <div class="card-body">
                            <form action="{{ route('landing-setting.social.store') }}" method="POST" class="mb-5">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Label (opsional)</label>
                                    <input type="text" name="label" class="form-control form-control-sm"
                                        placeholder="Instagram" value="{{ old('label') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label required">URL</label>
                                    <input type="text" name="url" class="form-control form-control-sm"
                                        placeholder="https://instagram.com/akun" value="{{ old('url') }}">
                                    @error('url', 'social')<div class="text-danger fs-8 mt-1">{{ $message }}</div>@enderror
                                    <div class="form-text fs-8">Ikon terdeteksi otomatis dari URL (IG, TikTok, FB, dll).</div>
                                </div>
                                <button type="submit" class="btn btn-sm btn-light-primary w-100">
                                    <i class="ki-outline ki-plus fs-4"></i> Tambah Tautan
                                </button>
                            </form>

                            <div class="separator mb-4"></div>

                            @forelse ($socials as $soc)
                                <div class="d-flex align-items-center justify-content-between mb-3 p-2 rounded bg-light-{{ $soc->is_active ? 'success' : 'secondary' }}">
                                    <div class="d-flex align-items-center gap-2 text-truncate">
                                        <span class="text-primary">@include('antrian_online.partials.social_icon', ['platform' => $soc->platform])</span>
                                        <div class="text-truncate">
                                            <div class="fw-bold fs-7 text-truncate">{{ $soc->label ?? ucfirst($soc->platform) }}</div>
                                            <div class="text-muted fs-8 text-truncate" style="max-width:160px;">{{ $soc->url }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <form action="{{ route('landing-setting.social.toggle', $soc->id) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-icon btn-sm btn-light" title="Aktif/Nonaktif">
                                                <i class="ki-outline ki-{{ $soc->is_active ? 'eye' : 'eye-slash' }} fs-5 text-{{ $soc->is_active ? 'success' : 'muted' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('landing-setting.social.delete', $soc->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus tautan ini?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-icon btn-sm btn-light"><i class="ki-outline ki-trash fs-5 text-danger"></i></button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-muted text-center fs-7">Belum ada tautan.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ TENANT ANTRIAN ONLINE ============ --}}
            <div class="card border border-gray-300">
                <div class="card-header">
                    <h3 class="card-title fw-bold">Tenant Antrian Online</h3>
                    <div class="card-toolbar">
                        <input type="text" id="tenantSearch" class="form-control form-control-sm w-250px"
                            placeholder="Cari instansi...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-muted fs-7 mb-4">Aktifkan tenant agar tombol <b>Pilih Tenan</b> dapat diklik di halaman publik. Jika nonaktif, tampil sebagai <b>Available Soon</b>.</div>
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle">
                            <thead>
                                <tr class="fw-bold text-muted fs-7 text-uppercase">
                                    <th class="w-50px">No</th>
                                    <th>Instansi</th>
                                    <th class="w-150px text-center">Antrian Online</th>
                                </tr>
                            </thead>
                            <tbody id="tenantBody">
                                @foreach ($tenants as $i => $t)
                                    <tr class="tenant-row">
                                        <td>{{ $i + 1 }}</td>
                                        <td class="tenant-name fw-semibold">{{ $t->nama_skpd }}</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch form-check-custom d-inline-flex">
                                                <input class="form-check-input tenant-toggle" type="checkbox"
                                                    data-id="{{ $t->id }}" {{ $t->is_antrianonline ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            @if (session('success'))
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 2500, timerProgressBar: true });
            @endif
            @if ($errors->any() && !$errors->getBag('social')->any())
                Swal.fire({ icon: 'error', title: 'Periksa Input', text: 'Beberapa isian belum valid.' });
            @endif

            // Pencarian tenant (client-side)
            document.getElementById('tenantSearch').addEventListener('keyup', function () {
                var q = this.value.toLowerCase();
                document.querySelectorAll('#tenantBody .tenant-row').forEach(function (row) {
                    var name = row.querySelector('.tenant-name').textContent.toLowerCase();
                    row.style.display = name.indexOf(q) > -1 ? '' : 'none';
                });
            });

            // Toggle tenant online (AJAX)
            document.querySelectorAll('.tenant-toggle').forEach(function (el) {
                el.addEventListener('change', function () {
                    var id = this.dataset.id;
                    var input = this;
                    fetch("{{ url('landing-setting/tenant') }}/" + id + "/toggle", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: res.message, showConfirmButton: false, timer: 2000 });
                    })
                    .catch(function () {
                        input.checked = !input.checked; // rollback
                        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat memperbarui status.' });
                    });
                });
            });
        </script>
    @endpush
@endsection
