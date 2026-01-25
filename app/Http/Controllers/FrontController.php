<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Antrian;
use App\Models\Loket;
use App\Models\Skpd;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

// ESC/POS (DISIAPKAN, BELUM DIPAKAI)
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class FrontController extends Controller
{
    /**
     * Halaman kios
     */
    public function index()
    {
        $skpd = Skpd::with('lokets')->get();
        return view('kios', compact('skpd'));
    }

    /**
     * Proses ambil antrian
     */
    public function ambilAntrian(Request $request)
    {
        // ================= VALIDASI =================
        $request->validate([
            'skpd_id'  => 'required',
            'loket_id' => 'required',
            'nik'      => 'required',
            'nama'     => 'required',
            'no_hp'    => 'required',
        ]);

        // ================= SIMPAN CUSTOMER =================
        $customer = Customer::create([
            'id'    => (string) Str::uuid(),
            'nik'   => $request->nik,
            'nama'  => $request->nama,
            'no_hp' => $request->no_hp,
        ]);

        // ================= AMBIL LOKET =================
        $loket = Loket::findOrFail($request->loket_id);

        // ================= HITUNG NOMOR URUT =================
        $today = Carbon::today();

        $lastNumber = Antrian::where('loket_id', $loket->id)
            ->whereDate('created_at', $today)
            ->max('no_urut');

        $nextNumber = ($lastNumber ?? 0) + 1;

        // ================= SIMPAN ANTRIAN =================
        Antrian::create([
            'id'          => (string) Str::uuid(),
            'customer_id' => $customer->id,
            'skpd_id'     => $request->skpd_id,
            'loket_id'    => $loket->id,
            'no_urut'  => $nextNumber,
            'status'      => 0, // 0 = menunggu (SESUAI DATABASE)
        ]);

   // ================= FORMAT TIKET =================
        $kodeTiket = $loket->kode_tenant . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // ================= EKSEKUSI CETAK (BAGIAN PENTING) =================
        try {
            // Pastikan Printer sudah di-SHARE dengan nama "printer_kios" di Windows
            // Menggunakan smb://localhost agar lebih stabil di XAMPP
            $namaPrinter = "smb://localhost/printer_kios";
            
            $this->printTiket($kodeTiket, $loket->nama_loket, $namaPrinter);

        } catch (\Exception $e) {
            // Jika error, catat di log tapi JANGAN hentikan aplikasi
            Log::error("Gagal Cetak Tiket: " . $e->getMessage());
        }

        // ================= KEMBALI KE KIOS + TAMPILKAN TIKET =================
        return redirect('/')
            ->with('tiket', $kodeTiket);
    }

    /**
     * FUNGSI CETAK TIKET
     */
    private function printTiket($kodeTiket, $namaLoket, $printerName)
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
        $printer->text("LAYANAN\n");
        $printer->text(strtoupper($namaLoket) . "\n");

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