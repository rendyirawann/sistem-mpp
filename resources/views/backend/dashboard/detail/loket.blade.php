<table class="table table-striped">
    <thead>
        <tr>
            <th>Nama Layanan</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $row)
            <tr>
                <td>{{ $row->nama_loket }}</td>
                <td>
                    @if($row->isaktif)
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-danger">Nonaktif</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="2" class="text-center text-muted">
                    Tidak ada data
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
