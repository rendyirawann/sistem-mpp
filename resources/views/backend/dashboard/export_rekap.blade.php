<!DOCTYPE html>
<html>

<head>
    <title>Laporan Rekapitulasi Pelayanan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        /* 1. SETUP MARGIN HALAMAN (Penting agar footer muat) */
        @page {
            margin: 20px 30px 60px 30px;
            /* Atas Kanan Bawah Kiri */
        }

        /* KOP SURAT */
        .header-table {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-table td {
            border: none;
            vertical-align: middle;
        }

        .header-title h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
            text-align: center;
        }

        .header-title p {
            margin: 5px 0 0 0;
            font-size: 12px;
            text-align: center;
        }

        /* TABEL DATA */
        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 6px;
        }

        table.data th {
            background-color: #eee;
            text-align: center;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* 2. STYLE FOOTER (Letak di Kanan Bawah) */
        .footer {
            position: fixed;
            bottom: -40px;
            /* Tarik ke area margin bawah */
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: right;
            /* Rata Kanan */
            font-size: 10px;
            color: #555;
        }

        /* CSS Counter untuk Nomor Halaman */
        .pagenum:before {
            content: counter(page);
        }
    </style>
</head>

<body>

    <div class="footer">
        <div>Halaman <span class="pagenum"></span></div>
        <div style="font-size: 9px; font-style: italic; margin-top: 2px;">
            {{-- Source: {{ request()->fullUrl() }} --}}
            Source: https://mppantrian-deliserdangsehat.deliserdangkab.go.id
        </div>
    </div>

    <table class="header-table">
        <tr>
            <td width="100%" class="header-title text-center">
                {{-- Pastikan path gambar benar --}}
                <img src="{{ public_path('assets/media/logos/mpp_login.png') }}" style="height: 80px; width: auto;">
                <h2>Laporan Rekapitulasi Pelayanan<br>Periode: {{ $labelPeriode }}</h2>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Instansi (SKPD)</th>
                <th>Layanan (Loket)</th>
                <th width="15%">Jumlah Antrian</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach ($data as $row)
                @php $grandTotal += $row->total; @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $row->loket->skpd->nama_skpd ?? 'Tanpa Instansi' }}</td>
                    <td>{{ $row->loket->nama_loket ?? 'Tanpa Layanan' }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ number_format($row->total) }}</td>
                </tr>
            @endforeach

            <tr style="background-color: #f8f9fa;">
                <td colspan="3" class="text-right" style="font-weight: bold;">TOTAL KESELURUHAN</td>
                <td class="text-center" style="font-weight: bold; font-size: 14px;">{{ number_format($grandTotal) }}
                </td>
            </tr>
        </tbody>
    </table>

</body>

</html>
