<div class="table-responsive">
    <table class="table table-striped table-hover gy-5 gs-7">
        <tr>
            <th class="fw-bold text-muted w-200px">Logo</th>
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
            <th class="fw-bold text-muted">Status Aktif Tenant</th>
            <td>
                @if ($data->isaktif)
                    <span class="badge badge-light-success fw-bold">Aktif</span>
                @else
                    <span class="badge badge-light-danger fw-bold">Nonaktif</span>
                @endif
            </td>
        </tr>

        <tr>
            <th class="fw-bold text-primary pt-8" colspan="2">
                <i class="fa fa-clock text-primary me-2"></i> PENGATURAN WAKTU & LAYANAN
            </th>
        </tr>
        <tr>
            <th class="fw-bold text-muted">Jam Operasional <br><small class="fw-normal">(Senin - Kamis)</small></th>
            <td class="fw-bold text-gray-800">
                {{ substr($data->buka_senin_kamis, 0, 5) }} WIB <span class="text-muted mx-2">s/d</span>
                {{ substr($data->tutup_senin_kamis, 0, 5) }} WIB
            </td>
        </tr>
        <tr>
            <th class="fw-bold text-muted">Jam Operasional <br><small class="fw-normal">(Jumat)</small></th>
            <td class="fw-bold text-gray-800">
                {{ substr($data->buka_jumat, 0, 5) }} WIB <span class="text-muted mx-2">s/d</span>
                {{ substr($data->tutup_jumat, 0, 5) }} WIB
            </td>
        </tr>
        <tr>
            <th class="fw-bold text-muted">Jam Operasional <br><small class="fw-normal">(Sabtu)</small></th>
            <td>
                @if ($data->is_sabtu_buka)
                    <span class="fw-bold text-gray-800">
                        {{ substr($data->buka_sabtu, 0, 5) }} WIB <span class="text-muted mx-2">s/d</span>
                        {{ substr($data->tutup_sabtu, 0, 5) }} WIB
                    </span>
                    <span class="badge badge-light-success ms-2">BUKA</span>
                @else
                    <span class="badge badge-light-danger fw-bold">LIBUR / TUTUP</span>
                @endif
            </td>
        </tr>
        <tr>
            <th class="fw-bold text-muted">Batas Kuota Harian</th>
            <td>
                @if ($data->kuota_harian > 0)
                    <span class="badge badge-warning fw-bolder fs-7">Maksimal {{ $data->kuota_harian }} Antrian</span>
                @else
                    <span class="badge badge-success fw-bolder fs-7">Tanpa Batas (0)</span>
                @endif
            </td>
        </tr>
        <tr>
            <th class="fw-bold text-muted">Status Tutup Paksa <br><small class="fw-normal">(Force Close)</small></th>
            <td>
                @if ($data->is_force_close)
                    <span class="badge badge-danger fw-bold"><i class="fa fa-lock text-white me-1"></i> Sedang Ditutup
                        Paksa</span>
                @else
                    <span class="badge badge-light fw-bold text-gray-600">Berjalan Normal</span>
                @endif
            </td>
        </tr>
        <tr>
            <th class="fw-bold text-muted pt-8">Dibuat Pada</th>
            <td class="pt-8">{{ $data->created_at ? $data->created_at->translatedFormat('d F Y, H:i') : '-' }}</td>
        </tr>
    </table>
</div>
