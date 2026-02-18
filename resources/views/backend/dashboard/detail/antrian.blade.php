<table class="table align-middle table-row-dashed fs-6 gy-5" style="width:100%">
    <thead>
        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
            <th class="min-w-100px">No Antrian</th>
            <th class="min-w-150px">Instansi</th>
            <th class="min-w-150px">Layanan</th>
            <th class="min-w-150px">Nama Masyarakat</th>
            <th class="text-center min-w-100px">Status</th>
        </tr>
    </thead>
    <tbody class="fw-semibold text-gray-600">
        @foreach ($data as $row)
            <tr>
                <td>
                    <span class="text-gray-800 fw-bolder fs-5">{{ $row->no_antrian }}</span>
                </td>

                <td>
                    <span class="text-gray-800 fw-bold d-block fs-6">{{ $row->loket->skpd->nama_skpd ?? '-' }}</span>
                    <span class="text-muted fs-8">Kode: {{ $row->loket->skpd->kode_skpd ?? '-' }}</span>
                </td>

                <td>{{ $row->loket->nama_loket ?? '-' }}</td>

                <td>
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 fw-bold">{{ $row->customer->nama ?? ($row->nama ?? 'Tamu') }}</span>
                        @if (!empty($row->customer->nik) || !empty($row->nik))
                            <span class="text-muted fs-8">NIK: {{ $row->customer->nik ?? $row->nik }}</span>
                        @endif
                    </div>
                </td>

                <td class="text-center">
                    @if ($row->status == 0)
                        <span class="badge badge-light-warning fw-bolder px-4 py-3">Menunggu</span>
                    @elseif($row->status == 1)
                        <span class="badge badge-light-primary fw-bolder px-4 py-3">Dipanggil</span>
                    @elseif($row->status == 2)
                        <span class="badge badge-light-success fw-bolder px-4 py-3">Selesai</span>
                    @elseif($row->status == 3)
                        <span class="badge badge-light-danger fw-bolder px-4 py-3">Batal</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
