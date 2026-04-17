<div class="row mb-5">
    <div class="col-md-6">
        <h5 class="fw-bold mb-3 border-bottom pb-2">Informasi Responden</h5>
        <table class="table table-borderless fs-7 gy-1">
            <tr>
                <td class="fw-bold w-150px">Nama</td>
                <td>: {{ $data->antrian?->customer?->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="fw-bold">NIK</td>
                <td>: {{ $data->antrian?->customer?->nik ?? '-' }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Umur</td>
                <td>: {{ $data->umur }} Tahun</td>
            </tr>
            <tr>
                <td class="fw-bold">Jenis Kelamin</td>
                <td>: {{ $data->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Pendidikan</td>
                <td>: {{ $data->pendidikan }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Pekerjaan</td>
                <td>: {{ $data->pekerjaan }}</td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h5 class="fw-bold mb-3 border-bottom pb-2">Informasi Layanan</h5>
        <table class="table table-borderless fs-7 gy-1">
            <tr>
                <td class="fw-bold w-150px">Instansi</td>
                <td>: {{ $data->antrian?->skpd?->nama_skpd ?? '-' }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Layanan</td>
                <td>: {{ $data->antrian?->loket?->nama_loket ?? '-' }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Waktu Survey</td>
                <td>: {{ $data->created_at->format('d-m-Y H:i') }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Status Sync</td>
                <td>: 
                    @if($data->is_synced)
                        <span class="badge badge-success">Synced</span>
                    @else
                        <span class="badge badge-danger">Pending</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="mb-5">
    <h5 class="fw-bold mb-3 border-bottom pb-2">Nilai Kepuasan (Skala 1-4)</h5>
    <div class="row g-3">
        @php
            $pertanyaan = [
                'u1' => 'Persyaratan',
                'u2' => 'Prosedur',
                'u3' => 'Waktu Pelayanan',
                'u4' => 'Biaya/Tarif',
                'u5' => 'Produk Spesifik',
                'u6' => 'Kompetensi Pelaksana',
                'u7' => 'Perilaku Pelaksana',
                'u8' => 'Sarana Prasarana',
                'u9' => 'Penanganan Pengaduan',
            ];
        @endphp
        @foreach($pertanyaan as $key => $label)
            <div class="col-md-4">
                <div class="border rounded p-3 text-center bg-light">
                    <div class="fs-8 text-muted mb-1">{{ $label }}</div>
                    <div class="fs-4 fw-bold text-primary">{{ $data->$key }}</div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-4 p-4 border rounded bg-light-primary text-center">
        <div class="fs-6 fw-bold">Total Nilai: <span class="fs-2 text-primary ms-2">{{ $data->nilai }}</span></div>
    </div>
</div>

<div class="mb-5">
    <h5 class="fw-bold mb-3 border-bottom pb-2">Integritas & Feedback</h5>
    <div class="row">
        <div class="col-md-6">
            <div class="p-3 border rounded @if($data->is_pungli) bg-light-danger @else bg-light-success @endif">
                <div class="fw-bold mb-1">Potensi Pungli/Gratifikasi:</div>
                <div class="fs-6 fw-bold">@if($data->is_pungli) YA (⚠️) @else TIDAK @endif</div>
                @if($data->is_pungli)
                    <div class="mt-2 text-muted fs-8">
                        <strong>Kontak:</strong> {{ $data->pungli_kontak ?? '-' }}<br>
                        <strong>Keterangan:</strong> {{ $data->pungli_keterangan ?? '-' }}
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-3 border rounded bg-light">
                <div class="fw-bold mb-1">Kritik & Saran:</div>
                <div class="fs-7 text-gray-800">{{ $data->kritik_saran ?? 'Tidak ada saran.' }}</div>
            </div>
        </div>
    </div>
</div>
