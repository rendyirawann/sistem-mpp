@extends('backend.layout.app')
@section('title', 'Antrian Online')

@push('stylesheets')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <style>
        #modalDetailAO .ao-foto { width: 100%; max-width: 240px; aspect-ratio: 4/3; object-fit: cover; border-radius: 12px; background: #e2e8f0; }
        #modalDetailAO .d-row { display: flex; gap: 8px; padding: 6px 0; border-bottom: 1px dashed #e3e8f0; font-size: 13px; }
        #modalDetailAO .d-row .k { width: 120px; color: #64748b; flex: 0 0 auto; }
        #modalDetailAO .d-row .v { font-weight: 600; word-break: break-word; }
        #modalDetailAO .sec-title { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: .3px; margin: 10px 0 2px; }
    </style>
@endpush

@section('content')
    <div id="kt_app_toolbar" class="app-toolbar py-4">
        <div class="app-container container-fluid">
            <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">Antrian Online</h1>
            <ul class="breadcrumb fw-semibold fs-7 my-1">
                <li class="breadcrumb-item text-muted">Antrian Online</li>
                <li class="breadcrumb-item text-gray-900">Daftar Antrean</li>
            </ul>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="app-container container-fluid">
            <div class="card border border-gray-300">
                <div class="card-header border-bottom border-gray-300 d-flex align-items-center">
                    <ul class="nav nav-pills" id="aoTabs">
                        <li class="nav-item"><a class="nav-link active" data-tab="berjalan" href="#">
                            <i class="ki-outline ki-time fs-5 me-1"></i> Sedang Berjalan</a></li>
                        <li class="nav-item"><a class="nav-link" data-tab="selesai" href="#">
                            <i class="ki-outline ki-check-circle fs-5 me-1"></i> Selesai (Lewat Hari)</a></li>
                    </ul>
                    <div class="card-toolbar ms-auto">
                        <span class="text-muted fs-8" id="aoTabHint">Hari ini &amp; akan datang — reset otomatis tiap hari.</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-row-bordered align-middle gy-3" id="tblAntrianOnline" style="width:100%">
                            <thead>
                                <tr class="fw-bold fs-7 text-gray-600 text-uppercase">
                                    <th>No. Antrean</th>
                                    <th>Tanggal</th>
                                    <th>Nama</th>
                                    <th>NIK</th>
                                    @if ($isSuper)<th>Instansi</th>@endif
                                    <th>Layanan</th>
                                    <th>Status</th>
                                    <th>Form</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL --}}
    <div class="modal fade" id="modalDetailAO" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fw-bold">Detail Antrean Online</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"><i class="ki-outline ki-cross fs-2"></i></div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            <img id="dFoto" class="ao-foto" alt="Foto Wajah">
                            <div class="mt-3">
                                <div class="fw-bolder text-primary" style="font-size:26px;" id="dNo">-</div>
                                <div class="fw-bold fs-7" id="dTanggal">-</div>
                                <span class="badge badge-light-primary mt-1" id="dStatus"></span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="d-row"><span class="k">Instansi</span><span class="v" id="dSkpd">-</span></div>
                            <div class="d-row"><span class="k">Layanan</span><span class="v" id="dLayanan">-</span></div>
                            <div class="d-row"><span class="k">NIK</span><span class="v" id="dNik">-</span></div>
                            <div class="d-row"><span class="k">Nama</span><span class="v" id="dNama">-</span></div>
                            <div class="d-row"><span class="k">Jenis Kelamin</span><span class="v" id="dJk">-</span></div>
                            <div class="d-row"><span class="k">No. HP/WA</span><span class="v" id="dHp">-</span></div>
                        </div>
                    </div>
                    <div id="dForms" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const CSRF = document.querySelector('meta[name="csrf-token"]').content;
            const DATA_URL = "{{ route('antrian-online-list.data') }}";
            const DETAIL_URL = "{{ url('list-antrian-online') }}";
            const DEFAULT_FOTO = "{{ asset('assets/media/logos/logo_deliserdang.png') }}";
            const IS_SUPER = @json($isSuper);
            let currentTab = 'berjalan';

            function esc(s) { return String(s == null ? '' : s).replace(/[&<>]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[c])); }

            let columns = [
                { data: 'no_antrian', name: 'no_antrian' },
                { data: 'tanggal_f', name: 'tanggal' },
                { data: 'nama', name: 'nama' },
                { data: 'nik', name: 'nik' },
            ];
            if (IS_SUPER) columns.push({ data: 'instansi', name: 'instansi' });
            columns.push(
                { data: 'layanan', name: 'layanan' },
                { data: 'status', name: 'status' },
                { data: 'form', name: 'form', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
            );

            const table = $('#tblAntrianOnline').DataTable({
                processing: true,
                serverSide: true,
                ajax: { url: DATA_URL, data: function (d) { d.tab = currentTab; } },
                columns: columns,
                order: [],
                language: {
                    processing: 'Memuat...', emptyTable: 'Belum ada antrean online.',
                    zeroRecords: 'Data tidak ditemukan.', info: 'Menampilkan _START_–_END_ dari _TOTAL_',
                    infoEmpty: 'Tidak ada data', infoFiltered: '(disaring dari _MAX_)',
                    lengthMenu: 'Tampilkan _MENU_', search: 'Cari:',
                    paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                },
            });

            $('#aoTabs .nav-link').on('click', function (e) {
                e.preventDefault();
                $('#aoTabs .nav-link').removeClass('active');
                $(this).addClass('active');
                currentTab = $(this).data('tab');
                $('#aoTabHint').text(currentTab === 'selesai'
                    ? 'Antrean yang tanggalnya sudah lewat.'
                    : 'Hari ini & akan datang — reset otomatis tiap hari.');
                table.ajax.reload();
            });

            // Detail
            $('#tblAntrianOnline').on('click', '.btn-detail', function () {
                const id = $(this).data('id');
                Swal.fire({ title: 'Memuat...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                fetch(DETAIL_URL + '/' + id, { headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(function (d) {
                        Swal.close();
                        if (!d.success) { Swal.fire('Gagal', d.message || 'Tidak ditemukan', 'error'); return; }
                        renderDetail(d);
                        new bootstrap.Modal(document.getElementById('modalDetailAO')).show();
                    })
                    .catch(() => { Swal.close(); Swal.fire('Error', 'Gagal memuat detail.', 'error'); });
            });

            function renderDetail(d) {
                document.getElementById('dNo').textContent = d.no_antrian;
                document.getElementById('dTanggal').textContent = d.tanggal;
                document.getElementById('dStatus').textContent = d.status;
                document.getElementById('dSkpd').textContent = d.skpd;
                document.getElementById('dLayanan').textContent = d.layanan;
                document.getElementById('dNik').textContent = d.nik;
                document.getElementById('dNama').textContent = d.nama;
                document.getElementById('dJk').textContent = d.jk;
                document.getElementById('dHp').textContent = d.no_hp;
                const foto = document.getElementById('dFoto');
                foto.onerror = function () { this.onerror = null; this.src = DEFAULT_FOTO; };
                foto.src = d.foto || DEFAULT_FOTO;

                const host = document.getElementById('dForms');
                host.innerHTML = '';
                if (!d.forms || !d.forms.length) { host.innerHTML = '<div class="text-muted fs-8">Layanan ini tidak memakai form persyaratan.</div>'; return; }
                let html = '<div class="separator my-4"></div><h4 class="fw-bold fs-6 mb-2">Form Persyaratan</h4>';
                d.forms.forEach(function (f) {
                    html += '<div class="border border-gray-300 rounded p-4 mb-3">';
                    html += '<div class="fw-bold text-primary mb-1">' + esc(f.nama) + ' <span class="text-muted fs-8">(' + esc(f.kode) + ')</span></div>';
                    if (!f.sections || !f.sections.length) { html += '<div class="text-muted fs-8">Tidak ada isian.</div></div>'; return; }
                    f.sections.forEach(function (sec) {
                        if (sec.title) html += '<div class="sec-title">' + esc(sec.title) + '</div>';
                        sec.items.forEach(function (it) {
                            const v = it.fileUrl
                                ? '<a href="' + it.fileUrl + '" target="_blank" class="text-primary fw-bold">' + esc(it.value) + ' &#8599;</a>'
                                : esc(it.value).replace(/\n/g, '<br>');
                            html += '<div class="d-row"><span class="k">' + esc(it.label) + '</span><span class="v">' + v + '</span></div>';
                        });
                    });
                    html += '</div>';
                });
                host.innerHTML = html;
            }
        })();
    </script>
@endpush
