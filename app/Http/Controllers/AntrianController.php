<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Loket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AntrianController extends Controller
{
    /**
     * Ambil nomor antrian (tombol antrian)
     */
    public function ambilAntrian(Request $request, $kodeLoket)
    {
        DB::beginTransaction();

        try {
            $loket = Loket::where('kode_loket', $kodeLoket)
                ->where('status', 'aktif')
                ->firstOrFail();

            // ambil antrian terakhir hari ini
            $lastQueue = Antrian::where('loket_id', $loket->id)
                ->whereDate('tanggal', now())
                ->lockForUpdate()
                ->orderByDesc('nomor_urut')
                ->first();

            $nextNumber = ($lastQueue->nomor_urut ?? 0) + 1;

            $antrian = Antrian::create([
                'loket_id' => $loket->id,
                'nomor_urut' => $nextNumber,
                'nomor_antrian' => $loket->prefix_antrian . str_pad($nextNumber, 3, '0', STR_PAD_LEFT),
                'tanggal' => now()->toDateString(),
                'status' => 'menunggu',
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Antrian berhasil diambil',
                'data' => $antrian,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil antrian',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List antrian hari ini per loket
     */
    public function listHariIni($kodeLoket)
    {
        $loket = Loket::where('kode_loket', $kodeLoket)->firstOrFail();

        $antrians = Antrian::where('loket_id', $loket->id)
            ->whereDate('tanggal', now())
            ->orderBy('nomor_urut')
            ->get();

        return response()->json([
            'status' => true,
            'loket' => $loket->nama_loket,
            'data' => $antrians,
        ]);
    }

    /**
     * Panggil antrian berikutnya
     */
    public function panggilAntrian($kodeLoket)
    {
        $loket = Loket::where('kode_loket', $kodeLoket)->firstOrFail();

        $antrian = Antrian::where('loket_id', $loket->id)
            ->whereDate('tanggal', now())
            ->where('status', 'menunggu')
            ->orderBy('nomor_urut')
            ->first();

        if (!$antrian) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada antrian menunggu',
            ]);
        }

        $antrian->update([
            'status' => 'dipanggil',
            'waktu_panggil' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Antrian dipanggil',
            'data' => $antrian,
        ]);
    }

    /**
     * Selesaikan antrian
     */
    public function selesaiAntrian($id)
    {
        $antrian = Antrian::findOrFail($id);

        $antrian->update([
            'status' => 'selesai',
            'waktu_selesai' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Antrian selesai',
            'data' => $antrian,
        ]);
    }
}
