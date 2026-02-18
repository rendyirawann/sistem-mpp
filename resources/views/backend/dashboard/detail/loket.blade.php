<table class="table align-middle table-row-dashed fs-6 gy-5" style="width:100%">
    <thead>
        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
            @if (isset($data[0]) && $data[0] instanceof \App\Models\Skpd)
                <th class="min-w-100px">Kode SKPD</th>
                <th class="min-w-200px">Nama Instansi</th>
                <th class="text-center min-w-100px">Status</th>
            @else
                <th class="min-w-150px">Nama Layanan</th>
                <th class="min-w-200px">Instansi Induk</th>
                <th class="text-center min-w-100px">Status</th>
            @endif
        </tr>
    </thead>
    <tbody class="fw-semibold text-gray-600">
        @foreach ($data as $row)
            <tr>
                @if ($row instanceof \App\Models\Skpd)
                    {{-- ISI TABEL TOTAL SKPD --}}
                    <td>
                        <span class="badge badge-light text-gray-700 fw-bold">{{ $row->kode_skpd ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="text-gray-800 fw-bold fs-6">{{ $row->nama_skpd }}</span>
                    </td>
                    <td class="text-center">
                        @if ($row->isAktif)
                            <span class="badge badge-light-success fw-bolder px-3 py-2">Aktif</span>
                        @else
                            <span class="badge badge-light-danger fw-bolder px-3 py-2">Nonaktif</span>
                        @endif
                    </td>
                @else
                    {{-- ISI TABEL LAYANAN / LOKET --}}
                    <td>
                        <span class="text-gray-800 fw-bold fs-6">{{ $row->nama_loket }}</span>
                    </td>
                    <td>
                        <span class="text-gray-600 fw-semibold d-block">{{ $row->skpd->nama_skpd ?? '-' }}</span>
                    </td>
                    <td class="text-center">
                        @if ($row->isaktif)
                            <span class="badge badge-light-success fw-bolder px-3 py-2">Aktif</span>
                        @else
                            <span class="badge badge-light-danger fw-bolder px-3 py-2">Nonaktif</span>
                        @endif
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
