<?php

namespace App\Http\Controllers;

use App\Models\WilayahProvinsi;
use App\Models\WilayahKabupaten;
use App\Models\WilayahKecamatan;
use App\Models\WilayahDesa;

/**
 * Endpoint publik untuk dropdown wilayah bertingkat pada wizard antrian online.
 */
class WilayahController extends Controller
{
    public function provinsi()
    {
        return response()->json(WilayahProvinsi::orderBy('nama')->get(['id', 'nama']));
    }

    public function kabupaten($provinsi)
    {
        return response()->json(
            WilayahKabupaten::where('wilayah_provinsi_id', $provinsi)->orderBy('nama')->get(['id', 'nama'])
        );
    }

    public function kecamatan($kabupaten)
    {
        return response()->json(
            WilayahKecamatan::where('wilayah_kabupaten_id', $kabupaten)->orderBy('nama')->get(['id', 'nama'])
        );
    }

    public function desa($kecamatan)
    {
        return response()->json(
            WilayahDesa::where('wilayah_kecamatan_id', $kecamatan)->orderBy('nama')->get(['id', 'nama'])
        );
    }
}
