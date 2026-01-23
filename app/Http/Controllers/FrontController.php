<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Antrian;
use App\Models\Loket;
use Carbon\Carbon;
use App\Models\Skpd;

// ESC/POS
// use Mike42\Escpos\Printer;
// use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector; // GANTI INI


class FrontController extends Controller
{
   public function index()
{
    $skpd = Skpd::with('lokets')->get();

    return view('kios', compact('skpd'));
}   

    public function ambilAntrian(Request $request)
    {
        // =========================
        // VALIDASI
        // =========================
        $request->validate([
            'nik'        => 'required',
            'nama'       => 'required',
            'no_hp'      => 'required',
            'loket_id'   => 'required',
            'skpd_id'    => 'required',
        ]);

        $tanggal = Carbon::today()->toDateString();

        // =========================
        // SIMPAN / UPDATE CUSTOMER
        // =========================
        $customer = Customer::updateOrCreate(
            ['nik' => $request->nik],
            [
                'nama'  => $request->nama,
                'no_hp' => $request->no_hp,
            ]
        );

        // =========================
        // AMBIL LOKET
        // =========================
        $loket = Loket::findOrFail($request->loket_id);

        // =========================
        // GENERATE NOMOR ANTRIAN
        // =========================
        $last = Antrian::where('loket_id', $loket->id)
            ->whereDate('tanggal', $tanggal)
            ->orderByDesc('id')
            ->first();

        $nomorUrut = $last ? $last->no_urut + 1 : 1;
        $kodeTiket = $loket->kode . '-' . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);

        // =========================
        // SIMPAN ANTRIAN
        // =========================
        Antrian::create([
            'skpd_id'     => $request->skpd_id,
            'loket_id'    => $loket->id,
            'customer_id' => $customer->id,
            'no_urut'     => $nomorUrut,
            'no_antrian'  => $kodeTiket,
            'tanggal'     => $tanggal,
            'status'      => 0,
            'waktu_ambil' => now(),
        ]);

       try {
            // Pastikan nama printer sesuai dengan yang di-share di Windows (Langkah 1)
            $namaPrinterShared = "printer_kios"; 
            $this->printTiket($kodeTiket, $loket->nama_loket, $namaPrinterShared);
            
        } catch (\Exception $e) {
            // Jika print gagal, jangan hentikan aplikasi, tapi catat errornya
            Log::error("Gagal Print: " . $e->getMessage());
            // Opsional: Anda bisa return dengan pesan warning jika perlu
        }

        // KEMBALI KE KIOS
        return redirect()->back()->with('tiket', $kodeTiket);
    }

//     /**
//      * =========================
//      * FUNGSI CETAK TIKET
//      * =========================
//      * NANTI TINGGAL GANTI COM PORT
//      */
// private function printTiket($kodeTiket, $namaLoket)
// {
//     // SESUAIKAN COM PORT WINDOWS
//     $connector = new FilePrintConnector("COM6");
//     $printer   = new Printer($connector);

//     // ================= HEADER =================
//     $printer->setJustification(Printer::JUSTIFY_CENTER);
//     $printer->setTextSize(1, 1);

//     $printer->text("MPP\n");
//     $printer->text("KABUPATEN XXXXX\n");
//     $printer->text("--------------------------------\n\n");

//     // ================= JUDUL =================
//     $printer->text("NOMOR ANTRIAN\n\n");

//     // ================= NOMOR BESAR =================
//     $printer->setTextSize(3, 3);
//     $printer->text($kodeTiket . "\n\n");

//     // ================= LAYANAN =================
//     $printer->setTextSize(1, 1);
//     $printer->text("LAYANAN\n");
//     $printer->text(strtoupper($namaLoket) . "\n\n");

//     // ================= WAKTU =================
//     $printer->text("--------------------------------\n");
//     $printer->text("Tanggal : " . now()->format('d-m-Y') . "\n");
//     $printer->text("Waktu   : " . now()->format('H:i') . " WIB\n");
//     $printer->text("--------------------------------\n\n");

//     // ================= FOOTER =================
//     $printer->text("Silakan menunggu\n");
//     $printer->text("Nomor Anda akan dipanggil\n");
//     $printer->text("melalui layar antrian\n\n");

//     // ================= CUT =================
//     $printer->cut();
//     $printer->close();
// }

/**
     * FUNGSI CETAK TIKET
     */
    private function printTiket($kodeTiket, $namaLoket, $printerName)
    {
        // Gunakan WindowsPrintConnector
        $connector = new WindowsPrintConnector($printerName);
        $printer   = new Printer($connector);

        // ================= HEADER =================
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        
        // Header
        $printer->text("MPP\n");
        $printer->text("KABUPATEN XXXXX\n");
        $printer->text("--------------------------------\n");

        // Judul
        $printer->feed(1); // Jarak 1 baris
        $printer->text("NOMOR ANTRIAN\n");
        $printer->feed(1);

        // ================= NOMOR BESAR =================
        $printer->setTextSize(3, 3);
        $printer->text($kodeTiket . "\n");
        $printer->setTextSize(1, 1); // Reset size
        $printer->feed(1);

        // ================= LAYANAN =================
        $printer->text("LAYANAN\n");
        $printer->setTextSize(1, 2); // Agak tinggi sedikit
        $printer->text(strtoupper($namaLoket) . "\n");
        $printer->setTextSize(1, 1); // Reset
        $printer->feed(1);

        // ================= WAKTU =================
        $printer->text("--------------------------------\n");
        $printer->text("Tgl : " . now()->format('d-m-Y') . " " . now()->format('H:i') . "\n");
        $printer->text("--------------------------------\n");

        // ================= FOOTER =================
        $printer->feed(1);
        $printer->text("Silakan menunggu dipanggil\n");
        $printer->feed(2); // Space sebelum potong

        // ================= CUT =================
        $printer->cut();
        $printer->close();
    }

}
