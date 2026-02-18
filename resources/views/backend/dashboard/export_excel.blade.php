<table>
    <thead>
        <tr>
            <th style="font-weight: bold; text-align: center;">No Antrian</th>
            <th style="font-weight: bold; text-align: center;">Tanggal</th>
            <th style="font-weight: bold; text-align: center;">Waktu Ambil</th>
            <th style="font-weight: bold; text-align: center;">Waktu Panggil</th>
            <th style="font-weight: bold; text-align: center;">Waktu Selesai</th>
            <th style="font-weight: bold; text-align: center;">Nama Instansi (SKPD)</th>
            <th style="font-weight: bold; text-align: center;">Nama Layanan (Loket)</th>
            <th style="font-weight: bold; text-align: center;">Nama Customer</th>
            <th style="font-weight: bold; text-align: center;">NIK</th>
            <th style="font-weight: bold; text-align: center;">No HP</th>
            <th style="font-weight: bold; text-align: center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $row)
            <tr>
                <td>{{ $row->no_antrian }}</td>

                {{-- PERBAIKAN: Format Tanggal saja (d-m-Y) --}}
                <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>

                <td>{{ \Carbon\Carbon::parse($row->created_at)->format('H:i:s') }}</td>
                <td>{{ $row->waktu_panggil ? \Carbon\Carbon::parse($row->waktu_panggil)->format('H:i:s') : '-' }}</td>
                <td>{{ $row->status == 2 ? \Carbon\Carbon::parse($row->updated_at)->format('H:i:s') : '-' }}</td>
                <td>{{ $row->loket->skpd->nama_skpd ?? '-' }}</td>
                <td>{{ $row->loket->nama_loket ?? '-' }}</td>
                <td>{{ $row->customer->nama ?? ($row->nama ?? '-') }}</td>
                <td>'{{ $row->customer->nik ?? ($row->nik ?? '-') }}</td>
                <td>'{{ $row->customer->no_hp ?? ($row->no_hp ?? '-') }}</td>
                <td>
                    @if ($row->status == 0)
                        Menunggu
                    @elseif($row->status == 1)
                        Dipanggil
                    @elseif($row->status == 2)
                        Selesai
                    @elseif($row->status == 3)
                        Batal
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
