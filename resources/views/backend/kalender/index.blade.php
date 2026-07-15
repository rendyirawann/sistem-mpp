@extends('backend.layout.app')
@section('title', 'Kalender Antrian')
@section('content')
    <div id="kt_app_toolbar" class="app-toolbar py-4">
        <div class="app-container container-fluid">
            <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">Kalender Kuota Antrian</h1>
            <ul class="breadcrumb fw-semibold fs-7 my-1">
                <li class="breadcrumb-item text-muted">Antrian</li>
                <li class="breadcrumb-item text-gray-900">Kalender</li>
            </ul>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="app-container container-fluid">
            <div class="card border border-gray-300">
                <div class="card-header">
                    <h3 class="card-title fw-bold">Pilih Instansi</h3>
                    <div class="card-toolbar">
                        <input type="text" id="kalSearch" class="form-control form-control-sm w-250px" placeholder="Cari instansi...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-muted fs-7 mb-4">Klik instansi untuk membuka kalender & mengatur kuota antrean per tanggal.</div>
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle">
                            <thead>
                                <tr class="fw-bold text-muted fs-7 text-uppercase">
                                    <th class="w-50px">No</th>
                                    <th>Instansi</th>
                                    <th class="w-150px text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="kalBody">
                                @foreach ($skpds as $i => $s)
                                    <tr class="kal-row">
                                        <td>{{ $i + 1 }}</td>
                                        <td class="kal-name fw-semibold">{{ $s->nama_skpd }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('kalender.show', $s->id) }}" class="btn btn-sm btn-light-primary">
                                                <i class="ki-outline ki-calendar fs-4"></i> Buka Kalender
                                            </a>
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
            document.getElementById('kalSearch').addEventListener('keyup', function () {
                var q = this.value.toLowerCase();
                document.querySelectorAll('#kalBody .kal-row').forEach(function (row) {
                    var name = row.querySelector('.kal-name').textContent.toLowerCase();
                    row.style.display = name.indexOf(q) > -1 ? '' : 'none';
                });
            });
        </script>
    @endpush
@endsection
