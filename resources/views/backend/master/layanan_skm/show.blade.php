<div class="table-responsive">
    <table class="table table-bordered align-middle gs-7 gy-4">
        <tr>
            <th class="fw-bold w-150px">Nama Layanan</th>
            <td>{{ $data->layanan }}</td>
        </tr>
        <tr>
            <th class="fw-bold">OPD (SKPD)</th>
            <td>{{ $data->skpd ? $data->skpd->nama_skpd : $data->opd }}</td>
        </tr>
        <tr>
            <th class="fw-bold">ID Layanan</th>
            <td>{{ $data->id_layanan }}</td>
        </tr>
        <tr>
            <th class="fw-bold">External ID (OPD)</th>
            <td>{{ $data->id_opd }}</td>
        </tr>
        <tr>
            <th class="fw-bold">Dibuat Pada</th>
            <td>{{ $data->created_at ? $data->created_at->format('d F Y H:i') : '-' }}</td>
        </tr>
        <tr>
            <th class="fw-bold">Diperbarui Pada</th>
            <td>{{ $data->updated_at ? $data->updated_at->format('d F Y H:i') : '-' }}</td>
        </tr>
    </table>
</div>
