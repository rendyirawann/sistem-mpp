<!DOCTYPE html>
<html>

<head>
    <title>Daftar Layanan (Loket)</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        @page {
            margin: 20px 30px 60px 30px;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header-table td {
            border: none;
            vertical-align: middle;
        }

        .header-title {
            text-align: center;
        }

        .header-title h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        .meta {
            margin-bottom: 12px;
            font-size: 11px;
        }

        .meta b {
            display: inline-block;
            min-width: 90px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: middle;
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

        .badge-aktif {
            color: #166534;
            font-weight: bold;
        }

        .badge-nonaktif {
            color: #991b1b;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: right;
            font-size: 10px;
            color: #555;
        }

        .pagenum:before {
            content: counter(page);
        }
    </style>
</head>

<body>

    <div class="footer">
        <div>Halaman <span class="pagenum"></span></div>
        <div style="font-size: 9px; font-style: italic; margin-top: 2px;">
            Source: https://mppantrian-deliserdangsehat.deliserdangkab.go.id
        </div>
    </div>

    <table class="header-table">
        <tr>
            <td width="100%" class="header-title">
                <img src="{{ public_path('assets/media/logos/mpp_login.png') }}" style="height: 70px; width: auto;">
                <h2>Daftar Layanan (Loket)<br>Mal Pelayanan Publik</h2>
            </td>
        </tr>
    </table>

    <div class="meta">
        <div><b>Instansi</b>: {{ $namaInstansi }}</div>
        <div><b>Total</b>: {{ $lokets->count() }} layanan</div>
        <div><b>Dicetak</b>: {{ $tanggalCetak }} WIB</div>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Nama Instansi</th>
                <th>Nama Layanan</th>
                <th width="14%">Kode Tenant</th>
                <th width="10%">Prefix</th>
                <th width="12%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lokets as $row)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $row->skpd->nama_skpd ?? '-' }}</td>
                    <td>{{ $row->nama_loket ?? '-' }}</td>
                    <td class="text-center">{{ $row->kode_tenant ?? '-' }}</td>
                    <td class="text-center">{{ $row->prefix_tenant ?? '-' }}</td>
                    <td class="text-center">
                        @if ($row->isaktif)
                            <span class="badge-aktif">Aktif</span>
                        @else
                            <span class="badge-nonaktif">Nonaktif</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data layanan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
