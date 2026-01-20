<table class="table table-striped">
    <thead>
        <tr>
            <th>Intansi</th>
            <th>Layanan</th>
            <th>Nama Masyarakat</th>
            <th>No Antrian</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $row)
            <tr>
                <td>{{ $row->no_antrian }}</td>
                <td>
                    @if($row->status == 0)
                        <span class="badge bg-warning">Menunggu</span>
                    @elseif($row->status == 1)
                        <span class="badge bg-success">Dipanggil</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted">
                    Tidak ada data
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
