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
        $request->validate([
            'skpd_id'  => 'required|exists:skpd,id',
            'loket_id' => 'required|exists:lokets,id',
            'nik'      => 'required|min:16',
            'nama'     => 'required',
            'no_hp'    => 'required',
        ]);

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
            ->max('nomor_urut');

        $nomorUrut = $lastUrut ? $lastUrut + 1 : 1;

        // PREFIX DARI DB (prefix_tenant)
        $loket = Loket::findOrFail($request->loket_id);
        $kodeTiket = $loket->prefix_tenant . '-' . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);

        // SIMPAN ANTRIAN
        Antrian::create([
            'skpd_id'       => $request->skpd_id,
            'loket_id'      => $request->loket_id,
            'customer_id'   => $customer->id,
            'nomor_urut'    => $nomorUrut,
            'nomor_antrian' => $kodeTiket,
            'tanggal'       => $tanggal,
            'status'        => 0,
            'waktu_ambil'   => now(),
        ]);

        return redirect()->back()->with('tiket', $kodeTiket);
    }
}
