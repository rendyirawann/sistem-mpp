<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\{
    Skpd,
    Loket,
    Antrian,
    Customer
};
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class FrontController extends Controller
{
    /**
     * HALAMAN KIOS
     */
    public function index()
    {

        // Ambil SKPD aktif + loket aktif
        $skpd = Skpd::with(['lokets' => function ($q) {
            $q->where('isaktif', 1);
        }])
            ->where('isAktif', true)
            ->get();

        return view('kios', compact('skpd'));
    }

    /**
     * AMBIL ANTRIAN
     */
    public function ambilAntrian(Request $request)
    {

        $validator = Validator::make($request->all(), [
            // === RULES (Aturannya) ===
            'skpd_id'  => 'required|exists:skpd,id',
            'loket_id' => 'required|exists:lokets,id',
            'nik'      => 'required|numeric|digits:16',
            'nama'     => 'required|string|max:100',
            'no_hp'    => 'required|numeric',
        ], [
            // === MESSAGES (Kata-kata Errornya) ===
            'required' => 'Kolom :attribute wajib diisi.',
            'numeric'  => 'Kolom :attribute harus berupa angka.',
            'digits'   => 'Kolom :attribute harus berisi :digits digit.',
            'exists'   => 'Data :attribute tidak ditemukan di sistem.',
            'max'      => 'Kolom :attribute maksimal :max karakter.',
            'string'   => 'Kolom :attribute harus berupa teks.',
        ], [
            // === ATTRIBUTES (Alias Nama Kolom Biar Cakep) ===
            // Biar errornya "NIK harus angka", bukan "nik harus angka" (huruf kecil)
            'skpd_id'  => 'SKPD',
            'loket_id' => 'Loket',
            'nik'      => 'NIK',
            'nama'     => 'Nama Lengkap',
            'no_hp'    => 'Nomor HP',
        ]);
        // 2. Cek Jika Gagal
        if ($validator->fails()) {
            // Kalau Request datang dari AJAX, balikin JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors'  => $validator->errors()
                ], 422);
            }

            // Kalau Request biasa, balikin redirect kayak biasa
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // CUSTOMER
        $customer = Customer::firstOrCreate(
            ['nik' => $request->nik],
            [
                'nama'  => $request->nama,
                'no_hp' => $request->no_hp,
            ]
        );

        // NOMOR URUT HARI INI PER LOKET
        $tanggal = Carbon::today();

        $lastUrut = Antrian::where('loket_id', $request->loket_id)
            ->whereDate('tanggal', $tanggal)
            ->max('no_urut');

        $nomorUrut = $lastUrut ? $lastUrut + 1 : 1;

        // PREFIX DARI DB (prefix_tenant)
        $loket = Loket::findOrFail($request->loket_id);
        $kodeTiket = $loket->prefix_tenant . '-' . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);

        // SIMPAN ANTRIAN
        Antrian::create([
            'skpd_id'       => $request->skpd_id,
            'loket_id'      => $request->loket_id,
            'customer_id'   => $customer->id,
            'no_urut'    => $nomorUrut,
            'no_antrian' => $kodeTiket,
            'tanggal'       => $tanggal,
            'status'        => 0,
            'waktu_ambil'   => now(),
        ]);

        return redirect()->back()->with('tiket', $kodeTiket);
    }

    public function checkLastPanggilan()
    {
        // Ambil data panggilan terakhir hari ini
        // Kita gunakan DB::raw pada JOIN untuk menghindari error Collation (Error 500)
        $last = Antrian::select(
            'antrians.*',
            'lokets.nama_loket',
            'skpd.nama_skpd'
        )
            ->leftJoin('lokets', function ($join) {
                $join->on(
                    DB::raw('lokets.id COLLATE utf8mb4_unicode_ci'),
                    '=',
                    DB::raw('antrians.loket_id COLLATE utf8mb4_unicode_ci')
                );
            })
            ->leftJoin('skpd', function ($join) {
                $join->on(
                    DB::raw('skpd.id COLLATE utf8mb4_unicode_ci'),
                    '=',
                    DB::raw('antrians.skpd_id COLLATE utf8mb4_unicode_ci')
                );
            })
            ->whereDate('antrians.tanggal', Carbon::today())
            ->where('antrians.status', 1) // Status Dipanggil
            ->orderBy('antrians.waktu_panggil', 'desc')
            ->first();

        // Return kosong jika tidak ada data
        if (!$last) {
            return response()->json(null);
        }

        return response()->json($last);
    }
}
