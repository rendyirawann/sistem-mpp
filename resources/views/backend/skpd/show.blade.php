@extends('backend.layout.app')
@section('title', 'Detail SKPD')
@section('content')

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div class="card border border-gray-300">
        <div class="card-header border-bottom border-gray-300">
            <h2 class="fw-bold">Detail SKPD</h2>
        </div>

        <div class="card-body">
            <table class="table table-striped">
                <tr>
                    <th width="200">Nama SKPD</th>
                    <td>{{ $data->nama_skpd }}</td>
                </tr>
                <tr>
                    <th>Kepala SKPD</th>
                    <td>{{ $data->kepala_skpd ?? '-' }}</td>
                </tr>
                <tr>
                    <th>NIP Kepala</th>
                    <td>{{ $data->nip_kepala ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        @if ($data->isaktif)
                            <span class="badge badge-light-success">Aktif</span>
                        @else
                            <span class="badge badge-light-danger">Nonaktif</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Dibuat</th>
                    <td>{{ $data->created_at->translatedFormat('d F Y, H:i') }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

@endsection
