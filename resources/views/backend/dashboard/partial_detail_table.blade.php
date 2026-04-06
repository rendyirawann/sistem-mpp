{{-- TIDAK PERLU DIV TABLE-RESPONSIVE (DataTables akan handle) --}}
<table class="table align-middle table-row-dashed fs-6 gy-5" style="width:100%">
    <thead>
        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
            <th class="min-w-50px text-center">No</th>
            <th class="min-w-100px text-center">No. Antrian</th>
            <th class="min-w-100px">Waktu Ambil</th>
            <th class="min-w-100px text-center">Status</th>
        </tr>
    </thead>
    <tbody class="fw-semibold text-gray-600">
        @forelse($antrian as $item)
            <tr>
                <td class="text-center">
                    <span class="text-gray-800 fw-bold">{{ $loop->iteration }}</span>
                </td>

                <td class="text-center">
                    <span class="text-gray-800 fw-bolder fs-5">{{ $item->no_antrian }}</span>
                    @if(isset($item->customer->jk))
                        <span class="badge badge-light-secondary ms-1">{{ $item->customer->jk }}</span>
                    @endif
                </td>

                <td>
                    <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }}</span>
                    <span
                        class="text-muted fs-8 d-block">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</span>
                </td>

                <td class="text-center">
                    @if ($item->status == 0)
                        <span class="badge badge-light-warning fw-bolder px-3 py-2">Menunggu</span>
                    @elseif($item->status == 1)
                        <span class="badge badge-light-primary fw-bolder px-3 py-2">Dipanggil</span>
                    @elseif($item->status == 2)
                        <span class="badge badge-light-success fw-bolder px-3 py-2">Selesai</span>
                    @elseif($item->status == 3)
                        <span class="badge badge-light-danger fw-bolder px-3 py-2">Batal</span>
                    @endif
                </td>
            </tr>
        @empty
            {{-- Bagian ini sebenarnya tidak akan tampil jika logic di controller benar, tapi untuk jaga-jaga --}}
            <tr>
                <td colspan="4" class="text-center text-muted">Tidak ada data antrian</td>
            </tr>
        @endforelse
    </tbody>
</table>
