@extends('backend.layout.app')
@section('title', 'Kalender ' . $skpd->nama_skpd)

@push('stylesheets')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <style>
        .kal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; }
        .kal-dow { text-align: center; font-weight: 700; font-size: 12px; color: #6b7280; padding: 6px 0; text-transform: uppercase; }
        .kal-cell { border: 1px solid #e3e8f0; border-radius: 12px; min-height: 110px; padding: 8px; display: flex; flex-direction: column; background: #fff; }
        .kal-cell.empty { border: none; background: transparent; }
        .kal-cell.libur { background: #f8fafc; }
        .kal-cell.today { border-color: #1d4ed8; box-shadow: 0 0 0 2px rgba(29, 78, 216, .15); }
        .kal-cell.editable { cursor: pointer; transition: .15s; }
        .kal-cell.editable:hover { border-color: #1d4ed8; transform: translateY(-2px); }
        .kal-date { font-weight: 700; font-size: 14px; color: #111827; display: flex; justify-content: space-between; align-items: center; }
        .kal-info { font-size: 11px; color: #475569; margin-top: 6px; line-height: 1.6; }
        .kal-info .on { color: #1d4ed8; font-weight: 700; }
        .kal-info .ki { color: #0f766e; font-weight: 700; }
        .kal-edit { margin-top: auto; padding-top: 4px; }
        @media (max-width: 768px) { .kal-cell { min-height: 86px; } .kal-info { font-size: 10px; } }
    </style>
@endpush

@section('content')
    <div id="kt_app_toolbar" class="app-toolbar py-4">
        <div class="app-container container-fluid d-flex flex-stack flex-wrap gap-2">
            <div class="page-title d-flex flex-column">
                <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">Kalender Kuota — {{ $skpd->nama_skpd }}</h1>
                <ul class="breadcrumb fw-semibold fs-7 my-1">
                    <li class="breadcrumb-item text-muted">Antrian</li>
                    <li class="breadcrumb-item text-gray-900">Kalender</li>
                </ul>
            </div>
            @if (auth()->user()->hasRole('Superadmin'))
                <a href="{{ route('kalender.index') }}" class="btn btn-sm btn-light"><i class="ki-outline ki-arrow-left fs-4"></i> Pilih Instansi Lain</a>
            @endif
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="app-container container-fluid">
            <div class="card border border-gray-300">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <a href="{{ route('kalender.show', $skpd->id) }}?bulan={{ $prev }}" class="btn btn-sm btn-icon btn-light"><i class="ki-outline ki-left fs-2"></i></a>
                    <h3 class="card-title fw-bold m-0">{{ $bulanLabel }}</h3>
                    <a href="{{ route('kalender.show', $skpd->id) }}?bulan={{ $next }}" class="btn btn-sm btn-icon btn-light"><i class="ki-outline ki-right fs-2"></i></a>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-4 mb-4 fs-8 text-muted">
                        <span><span class="on text-primary fw-bold">Online</span> = terambil/kuota</span>
                        <span><span class="ki fw-bold" style="color:#0f766e">Kiosk</span> = terambil/kuota</span>
                        <span><i class="ki-outline ki-lock-2"></i> Terkunci (sudah ada antrean)</span>
                        <span><i class="ki-outline ki-pencil text-primary"></i> Klik untuk edit kuota</span>
                    </div>

                    <div class="kal-grid mb-2">
                        @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $dow)
                            <div class="kal-dow">{{ $dow }}</div>
                        @endforeach
                    </div>

                    <div class="kal-grid">
                        @for ($i = 0; $i < $leadBlanks; $i++)
                            <div class="kal-cell empty"></div>
                        @endfor

                        @foreach ($days as $d)
                            <div class="kal-cell {{ $d['libur'] ? 'libur' : '' }} {{ $d['is_today'] ? 'today' : '' }} {{ $d['editable'] ? 'editable' : '' }}"
                                @if ($d['editable']) data-tanggal="{{ $d['tanggal'] }}" data-online="{{ $d['kuota_online'] }}" data-kiosk="{{ $d['kuota_kiosk'] }}" onclick="bukaEdit(this)" @endif>
                                <div class="kal-date">
                                    <span>{{ $d['day'] }}</span>
                                    @if ($d['libur'])
                                        <span class="badge badge-light-secondary fs-9">Libur</span>
                                    @elseif ($d['locked'])
                                        <i class="ki-outline ki-lock-2 fs-6 text-muted" title="Sudah ada antrean"></i>
                                    @elseif ($d['editable'])
                                        <i class="ki-outline ki-pencil fs-7 text-primary"></i>
                                    @endif
                                </div>
                                @unless ($d['libur'])
                                    <div class="kal-info">
                                        <div><span class="on">On</span> {{ $d['taken_online'] }}/{{ $d['kuota_online'] }} <span class="text-muted">(sisa {{ $d['sisa_online'] }})</span></div>
                                        <div><span class="ki">Ki</span> {{ $d['taken_kiosk'] }}/{{ $d['kuota_kiosk'] }} <span class="text-muted">(sisa {{ $d['sisa_kiosk'] }})</span></div>
                                    </div>
                                    @if ($d['locked'])
                                        <div class="kal-edit"><span class="badge badge-light-warning fs-9">Terkunci</span></div>
                                    @endif
                                @endunless
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal edit kuota --}}
    <div class="modal fade" id="modalKuota" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Atur Kuota Tanggal</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal"><i class="ki-outline ki-cross fs-2"></i></div>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="m-tanggal">
                    <div class="mb-4">
                        <label class="form-label">Tanggal</label>
                        <div class="fw-bold fs-5" id="m-tanggal-label">-</div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label required">Kuota Online</label>
                            <input type="number" id="m-online" class="form-control" min="0" value="0">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label required">Kuota Kiosk</label>
                            <input type="number" id="m-kiosk" class="form-control" min="0" value="0">
                        </div>
                    </div>
                    <div class="form-text">Hanya bisa diubah jika belum ada antrean pada tanggal ini.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-sm btn-primary" id="btnSimpanKuota">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function bukaEdit(el) {
                document.getElementById('m-tanggal').value = el.dataset.tanggal;
                document.getElementById('m-tanggal-label').innerText = el.dataset.tanggal;
                document.getElementById('m-online').value = el.dataset.online;
                document.getElementById('m-kiosk').value = el.dataset.kiosk;
                new bootstrap.Modal(document.getElementById('modalKuota')).show();
            }

            $('#btnSimpanKuota').on('click', function () {
                let btn = $(this);
                btn.prop('disabled', true);
                $.ajax({
                    url: "{{ route('kalender.set', $skpd->id) }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        tanggal: $('#m-tanggal').val(),
                        kuota_online: $('#m-online').val(),
                        kuota_kiosk: $('#m-kiosk').val()
                    },
                    success: function (res) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1300, showConfirmButton: false })
                            .then(() => location.reload());
                    },
                    error: function (xhr) {
                        btn.prop('disabled', false);
                        Swal.fire('Gagal', xhr.responseJSON?.message ?? 'Terjadi kesalahan', 'error');
                    }
                });
            });
        </script>
    @endpush
@endsection
