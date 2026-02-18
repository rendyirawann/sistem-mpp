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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Events\AntrianBaru; // <--- 1. TAMBAHKAN INI DI ATAS

// ESC/POS (DISIAPKAN, BELUM DIPAKAI)
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

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

        // ================= EKSEKUSI CETAK (BAGIAN PENTING) =================
        // ================= EKSEKUSI CETAK =================
        try {
            $namaPrinter = "smb://localhost/printer_kios";
            // AMBIL NAMA TENANT (SKPD) DARI RELASI
            // Pastikan $loket->skpd ada isinya (biasanya otomatis terambil karena relasi belongsTo)
            $namaTenant = $loket->skpd->nama_skpd;
            // $this->printTiket($kodeTiket, $loket->nama_loket, $namaPrinter);
            $this->printTiket($kodeTiket, $namaTenant, $namaPrinter);
        } catch (\Exception $e) {
            Log::error("Gagal Cetak Tiket: " . $e->getMessage());
        }

        // Wrap Event di Try-Catch agar jika Reverb error, aplikasi tidak crash
        try {
            AntrianBaru::dispatch();
        } catch (\Exception $e) {
            Log::error("Gagal Broadcast WebSocket: " . $e->getMessage());
        }

        // 🔥 UBAH BAGIAN RETURN INI
        // Jika request dari AJAX (Javascript), kembalikan JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'tiket'   => $kodeTiket,
                'message' => 'Berhasil mengambil antrian'
            ]);
        }

        // Fallback untuk request biasa
        return redirect()->back()->with('tiket', $kodeTiket);
    }

    // public function checkLastPanggilan()
    // {
    //     // Ambil data panggilan terakhir hari ini
    //     // Kita gunakan DB::raw pada JOIN untuk menghindari error Collation (Error 500)
    //     $last = Antrian::select(
    //         'antrians.*',
    //         'lokets.nama_loket',
    //         'skpd.nama_skpd'
    //     )
    //         ->leftJoin('lokets', function ($join) {
    //             $join->on(
    //                 DB::raw('lokets.id COLLATE utf8mb4_unicode_ci'),
    //                 '=',
    //                 DB::raw('antrians.loket_id COLLATE utf8mb4_unicode_ci')
    //             );
    //         })
    //         ->leftJoin('skpd', function ($join) {
    //             $join->on(
    //                 DB::raw('skpd.id COLLATE utf8mb4_unicode_ci'),
    //                 '=',
    //                 DB::raw('antrians.skpd_id COLLATE utf8mb4_unicode_ci')
    //             );
    //         })
    //         ->whereDate('antrians.tanggal', Carbon::today())
    //         ->where('antrians.status', 1) // Status Dipanggil
    //         ->orderBy('antrians.waktu_panggil', 'desc')
    //         ->first();

    //     // Return kosong jika tidak ada data
    //     if (!$last) {
    //         return response()->json(null);
    //     }

    //     return response()->json($last);
    // }

    public function checkLastPanggilan()
    {
        // ==============================================================================
        // 🔥 FIX SINKRONISASI: LANGSUNG BACA DATABASE (CACHE DI-BYPASS)
        // Agar data di Kios 100% sama dengan Admin, kita tidak menggunakan Cache::get()
        // ==============================================================================

        // 1. AMBIL YANG "SEDANG DIPANGGIL" (Status 1, Waktu Panggil Paling Baru)
        $current = Antrian::select('antrians.*', 'lokets.nama_loket', 'skpd.nama_skpd')
            ->leftJoin('lokets', function ($join) {
                $join->on(DB::raw('lokets.id COLLATE utf8mb4_unicode_ci'), '=', DB::raw('antrians.loket_id COLLATE utf8mb4_unicode_ci'));
            })
            ->leftJoin('skpd', function ($join) {
                $join->on(DB::raw('skpd.id COLLATE utf8mb4_unicode_ci'), '=', DB::raw('antrians.skpd_id COLLATE utf8mb4_unicode_ci'));
            })
            ->whereDate('antrians.tanggal', Carbon::today())
            ->where('antrians.status', 1) // Wajib Status 1 (Dipanggil)
            ->orderBy('antrians.waktu_panggil', 'desc') // Ambil yang baru saja dipanggil
            ->first();

        // 2. AMBIL "GILIRAN BERIKUTNYA" (Status 0, Paling Lama Menunggu)
        $next = Antrian::select('antrians.no_antrian', 'lokets.nama_loket', 'skpd.nama_skpd')
            ->leftJoin('lokets', function ($join) {
                $join->on(DB::raw('lokets.id COLLATE utf8mb4_unicode_ci'), '=', DB::raw('antrians.loket_id COLLATE utf8mb4_unicode_ci'));
            })
            ->leftJoin('skpd', function ($join) {
                $join->on(DB::raw('skpd.id COLLATE utf8mb4_unicode_ci'), '=', DB::raw('antrians.skpd_id COLLATE utf8mb4_unicode_ci'));
            })
            ->whereDate('antrians.tanggal', Carbon::today())
            ->where('antrians.status', 0) // Status 0 (Menunggu)
            ->orderBy('antrians.waktu_ambil', 'asc') // First In First Out
            ->orderBy('antrians.no_urut', 'asc')
            ->first();

        // (Opsional) Update cache biar backend lain bisa baca, tapi Kios sendiri pakai data DB
        if ($current) {
            Cache::forever('panggilan_terakhir', $current);
        }

        return response()->json([
            'current' => $current,
            'next'    => $next
        ]);
    }



    /**
     * FUNGSI CETAK TIKET
     */
    private function printTiket($kodeTiket, $namaSkpd, $printerName)
    {
        $connector = new WindowsPrintConnector($printerName);
        $printer   = new Printer($connector);

        $printer->setJustification(Printer::JUSTIFY_CENTER);

        // Header
        $printer->text("MPP\n");
        $printer->text("KABUPATEN DELI SERDANG\n");
        $printer->text("--------------------------------\n");

        // Nomor Antrian
        $printer->feed(1);
        $printer->text("NOMOR ANTRIAN\n");
        $printer->feed(1);

        $printer->setTextSize(3, 3);
        $printer->text($kodeTiket . "\n");
        $printer->setTextSize(1, 1);

        // Layanan
        $printer->feed(1);
        $printer->text("LOKET\n");
        // $printer->text(strtoupper($namaLoket) . "\n");
        // === LOGIKA TEXT WRAPPING ===
        $namaSkpdUpper = strtoupper($namaSkpd);

        // Angka 30 adalah batas aman karakter per baris untuk kertas 58mm (biasanya max 32)
        // Parameter "\n" memaksa pindah baris
        // Parameter false artinya jangan potong kata di tengah jalan (tunggu spasi)
        $namaSkpdWrapped = wordwrap($namaSkpdUpper, 30, "\n", false);

        $printer->text($namaSkpdWrapped . "\n");
        // Waktu
        $printer->text("--------------------------------\n");
        $printer->text("Tgl : " . now()->format('d-m-Y H:i') . "\n");
        $printer->text("--------------------------------\n");

        // Footer
        $printer->feed(1);
        $printer->text("Silakan menunggu dipanggil\n");
        $printer->feed(2);

        // Cut
        $printer->cut();
        $printer->close();
    }
}
