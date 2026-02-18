<div class="table-responsive">
    <table class="table table-striped table-hover gy-5 gs-7">
        <tr>
            <th class="fw-bold text-muted w-150px">Logo</th>
            <td>
                @if ($data->logo_skpd)
                    <div class="symbol symbol-60px">
                        <img src="{{ asset('storage/user/logo_skpd/' . $data->logo_skpd) }}" alt="Logo"
                            class="object-fit-cover">
                    </div>
                @else
                    <span class="text-muted fs-7">Tidak ada logo</span>
                @endif
            </td>
        </tr>
        <tr>
            <th class="fw-bold text-muted">Nama SKPD</th>
            <td class="fw-bold text-gray-800">{{ $data->nama_skpd }}</td>
        </tr>
        <tr>
            <th class="fw-bold text-muted">Lokasi Stand</th>
            <td class="fw-bold text-gray-800">{{ $data->lokasi ?? '-' }}</td>
        </tr>
        <tr>
            <th class="fw-bold text-muted">Kepala SKPD</th>
            <td>{{ $data->kepala_skpd ?? '-' }}</td>
        </tr>
        <tr>
            <th class="fw-bold text-muted">NIP Kepala</th>
            <td>{{ $data->nip_kepala ?? '-' }}</td>
        </tr>
        <tr>
            <th class="fw-bold text-muted">ID Sukma Deli</th>
            <td>
                @if ($data->external_id_sukma)
                    <span class="badge badge-light-info fw-bold">{{ $data->external_id_sukma }}</span>
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>
        </tr>
        <tr>
            <th class="fw-bold text-muted">Status</th>
            <td>
                @if ($data->isaktif)
                    <span class="badge badge-light-success fw-bold">Aktif</span>
                @else
                    <span class="badge badge-light-danger fw-bold">Nonaktif</span>
                @endif
            </td>
        </tr>
        <tr>
            <th class="fw-bold text-muted">Dibuat Pada</th>
            <td>{{ $data->created_at ? $data->created_at->translatedFormat('d F Y, H:i') : '-' }}</td>
        </tr>
    </table>
</div>
