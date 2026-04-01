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
use App\Events\StatusTenantUpdated;
// ESC/POS (DISIAPKAN, BELUM DIPAKAI)
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class FrontController extends Controller
{
    /**
     * HALAMAN KIOS
     */
    /**
     * HALAMAN KIOS
     */
    // public function index()
    // {
    //     // Ambil SKPD aktif + loket aktif
    //     $skpd = Skpd::with(['lokets' => function ($q) {
    //         $q->where('isaktif', 1);
    //     }])
    //         ->where('isAktif', true)
    //         // GANTI whereHas DENGAN whereRaw UNTUK MENGATASI ERROR COLLATION
    //         ->whereRaw("EXISTS (
    //             SELECT * FROM lokets 
    //             WHERE skpd.id COLLATE utf8mb4_unicode_ci = lokets.skpd_id COLLATE utf8mb4_unicode_ci 
    //             AND isaktif = 1
    //         )")
    //         ->get();

    //     return view('kios', compact('skpd'));
    // }

    // A. BUAT FUNGSI PRIVATE UNTUK MENGAMBIL DATA STATUS SKPD (Mencegah kode berulang)
    private function getSkpdWithStatus()
    {
        $skpd = Skpd::with(['lokets' => function ($q) {
            $q->where('isaktif', 1);
        }])
            ->where('isAktif', true)
            ->whereRaw("EXISTS (
                SELECT * FROM lokets 
                WHERE skpd.id COLLATE utf8mb4_unicode_ci = lokets.skpd_id COLLATE utf8mb4_unicode_ci 
                AND isaktif = 1
            )")
            ->get();

        $now = Carbon::now();
        $hariIni = $now->dayOfWeekIso;
        $waktuSekarang = $now->format('H:i:s');
        $tanggalSekarang = $now->toDateString();

        foreach ($skpd as $item) {
            $item->is_layanan_buka = true;
            $item->pesan_tutup = '';

            if ($item->is_force_close) {
                $item->is_layanan_buka = false;
                $item->pesan_tutup = 'SEDANG DITUTUP';
            } else {
                if (in_array($hariIni, [1, 2, 3, 4])) {
                    if ($waktuSekarang < $item->buka_senin_kamis || $waktuSekarang > $item->tutup_senin_kamis) {
                        $item->is_layanan_buka = false;
                        $item->pesan_tutup = 'JAM PELAYANAN TUTUP';
                    }
                } elseif ($hariIni == 5) {
                    if ($waktuSekarang < $item->buka_jumat || $waktuSekarang > $item->tutup_jumat) {
                        $item->is_layanan_buka = false;
                        $item->pesan_tutup = 'JAM PELAYANAN TUTUP';
                    }
                } elseif ($hariIni == 6) {
                    // SABTU (Cek apakah instansi ini mengizinkan buka Sabtu)
                    if ($item->is_sabtu_buka) {
                        if ($waktuSekarang < $item->buka_sabtu || $waktuSekarang > $item->tutup_sabtu) {
                            $item->is_layanan_buka = false;
                            $item->pesan_tutup = 'JAM PELAYANAN TUTUP';
                        }
                    } else {
                        // Jika tidak diizinkan, maka otomatis libur
                        $item->is_layanan_buka = false;
                        $item->pesan_tutup = 'PELAYANAN LIBUR';
                    }
                } else {
                    $item->is_layanan_buka = false;
                    $item->pesan_tutup = 'PELAYANAN LIBUR';
                }

                if ($item->is_layanan_buka && $item->kuota_harian > 0) {
                    $jumlahAntrianHariIni = Antrian::where('skpd_id', $item->id)->whereDate('tanggal', $tanggalSekarang)->count();
                    if ($jumlahAntrianHariIni >= $item->kuota_harian) {
                        $item->is_layanan_buka = false;
                        $item->pesan_tutup = 'KUOTA PENUH';
                    }
                }
            }
        }
        return $skpd;
    }

    // B. UBAH FUNGSI INDEX MENJADI SANGAT RINGKAS
    public function index()
    {
        $skpd = $this->getSkpdWithStatus();
        return view('kios', compact('skpd'));
    }

    // C. TAMBAHKAN FUNGSI BARU UNTUK RENDER GRID HTML SAJA
    public function getGridSkpd()
    {
        $skpd = $this->getSkpdWithStatus();
        return view('kios_grid', compact('skpd'))->render();
    }

    /**
     * AMBIL ANTRIAN
     */
    // public function ambilAntrian(Request $request)
    // {

    //     $validator = Validator::make($request->all(), [
    //         // === RULES (Aturannya) ===
    //         'skpd_id'  => 'required|exists:skpd,id',
    //         'loket_id' => 'required|exists:lokets,id',
    //         'nik'      => 'required|numeric|digits:16',
    //         'nama'     => 'required|string|max:100',
    //         'no_hp'    => 'required|numeric|digits_between:10,14',
    //     ], [
    //         // === MESSAGES (Kata-kata Errornya) ===
    //         'nik.required' => 'NIK wajib diisi',
    //         'nik.digits'   => 'NIK harus 16 digit',
    //         'nama.required' => 'Nama wajib diisi',
    //         'no_hp.required' => 'Nomor HP wajib diisi',
    //         'no_hp.digits_between' => 'Nomor HP minimal 10 dan maksimal 14 digit',
    //     ], [
    //         // === ATTRIBUTES (Alias Nama Kolom Biar Cakep) ===
    //         // Biar errornya "NIK harus angka", bukan "nik harus angka" (huruf kecil)
    //         'skpd_id'  => 'SKPD',
    //         'loket_id' => 'Loket',
    //         'nik'      => 'NIK',
    //         'nama'     => 'Nama Lengkap',
    //         'no_hp'    => 'Nomor HP',
    //     ]);
    //     // 2. Cek Jika Gagal
    //     if ($validator->fails()) {
    //         // Kalau Request datang dari AJAX, balikin JSON
    //         if ($request->expectsJson() || $request->ajax()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Silakan perbaiki data form',
    //                 'errors'  => $validator->errors()
    //             ], 422);
    //         }

    //         // Kalau Request biasa, balikin redirect kayak biasa
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }

    //     // CUSTOMER
    //     $customer = Customer::firstOrCreate(
    //         ['nik' => $request->nik],
    //         [
    //             'nama'  => $request->nama,
    //             'no_hp' => $request->no_hp,
    //         ]
    //     );

    //     // NOMOR URUT HARI INI PER LOKET
    //     $tanggal = Carbon::today();

    //     $lastUrut = Antrian::where('loket_id', $request->loket_id)
    //         ->whereDate('tanggal', $tanggal)
    //         ->max('no_urut');

    //     $nomorUrut = $lastUrut ? $lastUrut + 1 : 1;

    //     // PREFIX DARI DB (prefix_tenant)
    //     $loket = Loket::findOrFail($request->loket_id);
    //     $kodeTiket = $loket->prefix_tenant . '-' . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);

    //     // SIMPAN ANTRIAN
    //     Antrian::create([
    //         'skpd_id'       => $request->skpd_id,
    //         'loket_id'      => $request->loket_id,
    //         'customer_id'   => $customer->id,
    //         'no_urut'    => $nomorUrut,
    //         'no_antrian' => $kodeTiket,
    //         'tanggal'       => $tanggal,
    //         'status'        => 0,
    //         'waktu_ambil'   => now(),
    //     ]);

    //     // 5. BROADCAST WEBSOCKET (REVERB)
    //     try {
    //         AntrianBaru::dispatch();
    //     } catch (\Exception $e) {
    //         Log::error("Gagal Broadcast WebSocket: " . $e->getMessage());
    //     }

    //     // 6. RESPON KE BROWSER (PENTING!)
    //     // Kita kirim Data Tiket agar Browser yang mencetak via Recta
    //     if ($request->expectsJson() || $request->ajax()) {
    //         return response()->json([
    //             'success'    => true,
    //             'tiket'      => $kodeTiket,
    //             'layanan'    => $loket->skpd->nama_skpd, // Nama Dinas
    //             'loket'      => $loket->nama_loket,      // Nama Loket
    //             'tgl'        => now()->format('d-m-Y H:i'),
    //             'message'    => 'Berhasil mengambil antrian'
    //         ]);
    //     }
    //     // Fallback untuk request biasa
    //     return redirect()->back()->with('tiket', $kodeTiket);
    // }

    public function ambilAntrian(Request $request)
    {
        // 1. VALIDASI
        $validator = Validator::make($request->all(), [
            'skpd_id'  => 'required|exists:skpd,id',
            'loket_id' => 'required|exists:lokets,id',
            'nik'      => 'required|numeric|digits:16',
            'nama'     => 'required|string|max:100',
            'no_hp'    => 'required|numeric|digits_between:10,14',
        ], [
            'nik.required' => 'NIK wajib diisi',
            'nik.digits'   => 'NIK harus 16 digit',
            'nama.required' => 'Nama wajib diisi',
            'no_hp.required' => 'Nomor HP wajib diisi',
            'no_hp.digits_between' => 'Nomor HP minimal 10 dan maksimal 14 digit',
        ], [
            'skpd_id'  => 'SKPD',
            'loket_id' => 'Loket',
            'nik'      => 'NIK',
            'nama'     => 'Nama Lengkap',
            'no_hp'    => 'Nomor HP',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan perbaiki data form',
                    'errors'  => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction(); // Tambahkan Transaction biar aman
        try {
            // 2. SIMPAN / UPDATE CUSTOMER
            $customer = Customer::firstOrCreate(
                ['nik' => $request->nik],
                [
                    'nama'  => $request->nama,
                    'no_hp' => $request->no_hp,
                ]
            );

            // 3. AMBIL DATA LOKET DULU (Untuk Cek Prefix)
            $loket = Loket::findOrFail($request->loket_id); //
            $prefixSama = $loket->prefix_tenant;
            $tanggal = Carbon::today();

            // 4. 🔥 LOGIKA NOMOR URUT BARU (OPSI B) 🔥
            // Cari max nomor urut dari SEMUA loket yang punya prefix sama hari ini
            $lastUrut = Antrian::whereDate('tanggal', $tanggal)
                ->whereHas('loket', function ($q) use ($prefixSama) {
                    $q->where('prefix_tenant', $prefixSama);
                })
                ->max('no_urut'); // Gunakan max() biar urutan tidak reset jika ada data dihapus

            $nomorUrut = $lastUrut ? $lastUrut + 1 : 1;

            // 5. GENERATE KODE TIKET (Contoh: BS-005)
            $kodeTiket = $loket->prefix_tenant . '-' . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);

            // 6. SIMPAN ANTRIAN
            Antrian::create([
                'skpd_id'     => $request->skpd_id,
                'loket_id'    => $request->loket_id,
                'customer_id' => $customer->id,
                'no_urut'     => $nomorUrut, // Angka murni (misal: 5)
                'no_antrian'  => $kodeTiket, // String tiket (misal: BS-005)
                'tanggal'     => $tanggal,
                'status'      => 0,
                'waktu_ambil' => now(),
            ]);

            DB::commit();

            // 7. BROADCAST WEBSOCKET
            try {
                AntrianBaru::dispatch();
            } catch (\Exception $e) {
                Log::error("Gagal Broadcast WebSocket: " . $e->getMessage());
            }

            // 8. RESPON JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success'    => true,
                    'tiket'      => $kodeTiket,
                    'layanan'    => $loket->skpd->nama_skpd,
                    'loket'      => $loket->nama_loket,
                    'tgl'        => now()->format('d-m-Y H:i'),
                    'message'    => 'Berhasil mengambil antrian'
                ]);
            }

            return redirect()->back()->with('tiket', $kodeTiket);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem');
        }
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
        // Angka 45-48 adalah batas aman karakter per baris untuk kertas 80mm
        $namaSkpdWrapped = wordwrap($namaSkpdUpper, 45, "\n", false);

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
