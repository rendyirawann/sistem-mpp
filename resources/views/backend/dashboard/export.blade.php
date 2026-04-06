<!DOCTYPE html>
<html>

<head>
    <title>Laporan Detail Pelayanan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        /* HEADER TABLE STYLE (KHUSUS KOP SURAT) */
        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            /* Garis bawah kop surat */
            padding-bottom: 10px;
        }

        /* Pastikan tabel header tidak ada garis kotaknya */
        .header-table td {
            border: none !important;
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

        .header-title p {
            margin: 5px 0 0 0;
            font-size: 12px;
        }

        /* CSS TABLE DATA (KONTEN) */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            page-break-inside: auto;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: middle;
        }

        .data-table th {
            background-color: #eee;
            text-align: center;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .page-break {
            page-break-before: always;
        }

        .skpd-container {
            width: 100%;
            display: block;
        }

        @media print {
            @page {
                margin: 0.5cm;
                size: A4 landscape;
            }
        }
    </style>
</head>

<body onload="{{ $type == 'pdf' ? 'window.print()' : '' }}">

    <table class="header-table">
        <tr>


            <td width="70%" class="header-title">
                <img src="{{ public_path('assets/media/logos/mpp_login.png') }}" style="height: 60px; width: auto;">
                {{-- <h2>PEMERINTAH KABUPATEN DELI SERDANG</h2>
                <h2>MAL PELAYANAN PUBLIK (MPP)</h2> --}}
                <h3>Laporan Detail Pelayanan Antrian Periode: {{ $labelPeriode }}</h3>
            </td>


        </tr>
    </table>
    {{-- LOOP DATA (SAMA SEPERTI SEBELUMNYA) --}}
    @foreach ($data as $namaSkpd => $lokets)
        <div class="skpd-container {{ !$loop->first ? 'page-break' : '' }}">

            <div
                style="margin-top: 15px; margin-bottom: 5px; font-size: 12px; font-weight: bold; border-bottom: 2px solid #000;">
                INSTANSI: {{ strtoupper($namaSkpd) }}
            </div>

            @foreach ($lokets as $namaLoket => $antrians)
                {{-- Tambahkan class 'data-table' agar style tidak bentrok dengan header --}}
                <table class="data-table" nobr="true">
                    <thead>
                        <tr>
                            <th colspan="11" style="text-align: left; background-color: #dbeafe;">
                                LAYANAN: {{ strtoupper($namaLoket) }}
                            </th>
                        </tr>
                        <tr>
                            <th width="3%">No</th>
                            <th width="8%">No Antrian</th>
                            <th width="8%">Status</th>
                            <th>NIK</th>
                            <th>Nama Customer</th>
                            <th width="5%">JK</th>
                            <th>No HP</th>
                            <th width="8%">Tgl</th>
                            <th width="8%">Ambil</th>
                            <th width="8%">Panggil</th>
                            <th width="8%">Selesai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($antrians as $row)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center" style="font-weight:bold;">{{ $row->no_antrian }}</td>
                                <td class="text-center">
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

                                <td class="text-center">{{ $row->customer->nik ?? ($row->nik ?? '-') }}</td>
                                <td>{{ $row->customer->nama ?? ($row->nama ?? '-') }}</td>
                                <td class="text-center">
                                    @php
                                        $jkVal = $row->customer->jk ?? null;
                                    @endphp
                                    @if($jkVal == 'L')
                                        <strong>L</strong>
                                    @elseif($jkVal == 'P')
                                        <strong>P</strong>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">{{ $row->customer->no_hp ?? ($row->no_hp ?? '-') }}</td>

                                <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/y') }}
                                </td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($row->created_at)->format('H:i:s') }}
                                </td>
                                <td class="text-center">
                                    {{ $row->waktu_panggil ? \Carbon\Carbon::parse($row->waktu_panggil)->format('H:i:s') : '-' }}
                                </td>
                                <td class="text-center">
                                    {{ $row->status == 2 ? \Carbon\Carbon::parse($row->updated_at)->format('H:i:s') : '-' }}
                                </td>
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="11" class="text-right"
                                style="background-color: #f8f9fa; padding-right: 10px;">
                                <strong>Total {{ $namaLoket }}: {{ $antrians->count() }} Antrian</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            @endforeach

            <div style="text-align: right; margin-bottom: 20px; font-size: 11px;">
                Total Instansi {{ $namaSkpd }}: <strong>{{ $lokets->flatten()->count() }}</strong>
            </div>

        </div>
    @endforeach

    <hr>
    <div style="text-align: center; font-weight: bold; font-size: 14px; margin-top: 20px;">
        GRAND TOTAL PERIODE INI: {{ $total }} ANTRIAN
    </div>

</body>

</html>
