@extends('backend.layout.app')
@section('title', 'Form Persyaratan')

@push('stylesheets')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endpush

@section('content')
    <div id="kt_app_toolbar" class="app-toolbar py-4">
        <div class="app-container container-fluid d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">Form Persyaratan</h1>
                <ul class="breadcrumb fw-semibold fs-7 my-1">
                    <li class="breadcrumb-item text-muted">Antrian Online</li>
                    <li class="breadcrumb-item text-gray-900">Form Persyaratan</li>
                </ul>
            </div>
            <a href="{{ route('form-persyaratan.create') }}" class="btn btn-sm btn-primary">
                <i class="ki-outline ki-plus fs-2"></i> Tambah Form
            </a>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="app-container container-fluid">
            <div class="card border border-gray-300">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-row-bordered align-middle gy-3" id="tblForm" style="width:100%">
                            <thead>
                                <tr class="fw-bold fs-7 text-gray-600 text-uppercase">
                                    <th>Kode</th>
                                    <th>Nama Form</th>
                                    <th>Jumlah Field</th>
                                    <th>Tertaut</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const CSRF = document.querySelector('meta[name="csrf-token"]').content;
            const table = $('#tblForm').DataTable({
                processing: true, serverSide: true,
                ajax: "{{ route('form-persyaratan.data') }}",
                columns: [
                    { data: 'kode', name: 'kode' },
                    { data: 'nama', name: 'nama' },
                    { data: 'field_count', name: 'field_count', searchable: false },
                    { data: 'taut', name: 'taut', orderable: false, searchable: false },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' },
                ],
                language: { processing: 'Memuat...', emptyTable: 'Belum ada form.', search: 'Cari:', paginate: { next: '›', previous: '‹' } },
            });

            $('#tblForm').on('click', '.btn-del', function () {
                const id = $(this).data('id'), nama = $(this).data('nama');
                Swal.fire({
                    title: 'Hapus Form?', text: nama, icon: 'warning',
                    showCancelButton: true, confirmButtonText: 'Ya, hapus', cancelButtonText: 'Batal',
                }).then(res => {
                    if (!res.isConfirmed) return;
                    fetch("{{ url('form-persyaratan') }}/" + id, {
                        method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                    }).then(async r => ({ ok: r.ok, j: await r.json().catch(() => ({})) }))
                        .then(({ ok, j }) => {
                            if (ok && j.success) { Swal.fire('Berhasil', j.message, 'success'); table.ajax.reload(); }
                            else Swal.fire('Gagal', j.message || 'Terjadi kesalahan', 'error');
                        });
                });
            });
        })();
    </script>
@endpush
