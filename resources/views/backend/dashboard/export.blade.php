<!DOCTYPE html>
<html>
<head>
    <title>Laporan Rekapitulasi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #eee; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        @media print {
            @page { margin: 1cm; size: A4; }
        }
    </style>
</head>
{{-- Jika tipe PDF, otomatis trigger window.print() saat load --}}
<body onload="{{ $type == 'pdf' ? 'window.print()' : '' }}">

    <div class="header">
        <h2>LAPORAN REKAPITULASI PELAYANAN</h2>
        <p>Periode: {{ $labelPeriode }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Instansi (SKPD)</th>
                <th>Layanan (Loket)</th>
                <th width="15%">Jumlah Antrian</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($data as $row)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $row->loket->skpd->nama_skpd ?? '-' }}</td>
                <td>{{ $row->loket->nama_loket ?? '-' }}</td>
                <td class="text-center">{{ $row->total }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">TOTAL KESELURUHAN</th>
                <th class="text-center">{{ $total }}</th>
            </tr>
        </tfoot>
    </table>

</body>
</html>