<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Antrian;
use App\Models\Skm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

class SkmController extends Controller
{
    /**
     * KONFIGURASI PERTANYAAN IKM (Private Helper)
     * Disimpan di sini agar Controller tetap bersih & mudah diedit.
     */
    private function getDaftarPertanyaan()
    {
        return [
            'u1' => [
                'tanya' => 'Bagaimana pendapat Saudara tentang kesesuaian persyaratan pelayanan dengan jenis pelayanannya?',
                'opsi'  => ['Tidak Sesuai', 'Kurang Sesuai', 'Sesuai', 'Sangat Sesuai']
            ],
            'u2' => [
                'tanya' => 'Bagaimana pemahaman Saudara tentang kemudahan prosedur pelayanan di unit ini?',
                'opsi'  => ['Tidak Mudah', 'Kurang Mudah', 'Mudah', 'Sangat Mudah']
            ],
            'u3' => [
                'tanya' => 'Bagaimana pendapat Saudara tentang kecepatan waktu dalam memberikan pelayanan?',
                'opsi'  => ['Tidak Cepat', 'Kurang Cepat', 'Cepat', 'Sangat Cepat']
            ],
            'u4' => [
                'tanya' => 'Bagaimana pendapat Saudara tentang kewajaran biaya/tarif dalam pelayanan?',
                'opsi'  => ['Sangat Mahal', 'Cukup Mahal', 'Murah', 'Gratis']
            ],
            'u5' => [
                'tanya' => 'Bagaimana pendapat Saudara tentang kesesuaian produk pelayanan antara yang tercantum dalam standar pelayanan dengan hasil yang diberikan?',
                'opsi'  => ['Tidak Sesuai', 'Kurang Sesuai', 'Sesuai', 'Sangat Sesuai']
            ],
            'u6' => [
                'tanya' => 'Bagaimana pendapat Saudara tentang kompetensi/kemampuan petugas dalam pelayanan?',
                'opsi'  => ['Tidak Kompeten', 'Kurang Kompeten', 'Kompeten', 'Sangat Kompeten']
            ],
            'u7' => [
                'tanya' => 'Bagaimana pendapat Saudara tentang perilaku petugas dalam pelayanan terkait kesopanan dan keramahan?',
                'opsi'  => ['Tidak Sopan', 'Kurang Sopan', 'Sopan', 'Sangat Sopan']
            ],
            'u8' => [
                'tanya' => 'Bagaimana pendapat Saudara tentang kualitas sarana dan prasarana?',
                'opsi'  => ['Buruk', 'Cukup', 'Baik', 'Sangat Baik']
            ],
            'u9' => [
                'tanya' => 'Bagaimana pendapat Saudara tentang penanganan pengaduan pengguna layanan?',
                'opsi'  => ['Tidak Ada', 'Ada tapi tidak berfungsi', 'Berfungsi kurang maksimal', 'Dikelola dengan baik']
            ],
        ];
    }

    // 1. Tampilkan Halaman Form SKM
    public function index()
    {
        $baseUrl = 'https://sukmadeli.deliserdangkab.go.id/api/v1';
        $token   = '694e5225-d868-8323-8e10-21b0d3774720';

        // Logika: Kita bisa mengambil ID OPD dari session antrian jika user sudah input nomor antrian di awal,
        // namun karena di multi-step ini nomor antrian baru dicek di client-side, 
        // maka untuk pengambilan data awal (pooling) kita gunakan ID 18 sebagai default utama.
        $defaultOpdId = 18;

        $responses = Http::pool(fn(Pool $pool) => [
            $pool->as('pendidikan')->withHeaders(['X-API-TOKEN' => $token])->get("{$baseUrl}/pendidikan"),
            $pool->as('pekerjaan')->withHeaders(['X-API-TOKEN' => $token])->get("{$baseUrl}/pekerjaan"),
            $pool->as('disabilitas')->withHeaders(['X-API-TOKEN' => $token])->get("{$baseUrl}/disabilitas"),
            // Gunakan defaultOpdId
            $pool->as('pelayanan')->withHeaders(['X-API-TOKEN' => $token])->get("{$baseUrl}/pelayanan?opd={$defaultOpdId}"),
        ]);

        $pendidikan  = $responses['pendidikan']->ok() ? $responses['pendidikan']->json() : [];
        $pekerjaan   = $responses['pekerjaan']->ok()  ? $responses['pekerjaan']->json()  : [];
        $disabilitas = $responses['disabilitas']->ok() ? $responses['disabilitas']->json() : [];
        $pelayananRaw = $responses['pelayanan']->ok() ? $responses['pelayanan']->json() : [];
        $pelayanan    = $pelayananRaw['layanan_list'] ?? [];
        $pertanyaan = $this->getDaftarPertanyaan();

        return view('skm.index', compact('pendidikan', 'pekerjaan', 'disabilitas', 'pelayanan', 'pertanyaan'));
    }

    // 2. API: Cek Nomor Antrian
    public function checkAntrian(Request $request)
    {
        $request->validate(['no_antrian' => 'required']);

        // 1. Cari Antrian
        $antrian = Antrian::where('no_antrian', $request->no_antrian)
            ->whereDate('tanggal', now())
            ->with(['customer', 'loket', 'skpd'])
            ->first();

        if (!$antrian) {
            return response()->json(['status' => 'error', 'message' => 'Nomor antrian tidak ditemukan hari ini.']);
        }

        if ($antrian->status == 0) {
            return response()->json(['status' => 'error', 'message' => 'Antrian belum dipanggil petugas.']);
        }
        if ($antrian->status == 2) {
            return response()->json(['status' => 'error', 'message' => 'Survey sudah diisi sebelumnya.']);
        }

        // 2. AMBIL LAYANAN BERDASARKAN ID SUKMA DARI DATABASE SKPD
        $externalId = $antrian->skpd->external_id_sukma; // <--- Ambil field baru
        $daftarLayanan = [];

        if ($externalId) {
            try {
                $url_api = "https://sukmadeli.deliserdangkab.go.id/api/v1/pelayanan?opd={$externalId}";
                $token   = "694e5225-d868-8323-8e10-21b0d3774720";

                $response = Http::withOptions(['verify' => false])
                    ->withHeaders(['X-API-TOKEN' => $token])
                    ->timeout(5)
                    ->get($url_api);

                if ($response->successful()) {
                    $json = $response->json();
                    $daftarLayanan = $json['layanan_list'] ?? [];
                }
            } catch (\Exception $e) {
                Log::error("Gagal ambil layanan Sukma: " . $e->getMessage());
                // Tetap lanjut, nanti list layanan kosong
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id'       => $antrian->id,
                'nama'     => $antrian->customer->nama,
                'nik'      => $antrian->customer->nik,
                'layanan'  => $antrian->loket->nama_loket,
                'instansi' => $antrian->skpd->nama_skpd,
                'external_id' => $externalId // Info tambahan
            ],
            'services' => $daftarLayanan // <--- Kirim List Layanan ke Frontend
        ]);
    }

    // 3. Simpan Survey & Tutup Antrian
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'antrian_id' => 'required|exists:antrians,id',
    //         'umur'       => 'required|numeric',
    //         'jk'         => 'required|in:L,P',
    //         'pendidikan' => 'required|string',
    //         'pekerjaan'  => 'required|string',
    //         'disabilitas' => 'required|in:YA,TIDAK',
    //         'id_pelayanan' => 'required|integer',
    //         'u1' => 'required|integer|between:1,4',
    //         'u2' => 'required|integer|between:1,4',
    //         'u3' => 'required|integer|between:1,4',
    //         'u4' => 'required|integer|between:1,4',
    //         'u5' => 'required|integer|between:1,4',
    //         'u6' => 'required|integer|between:1,4',
    //         'u7' => 'required|integer|between:1,4',
    //         'u8' => 'required|integer|between:1,4',
    //         'u9' => 'required|integer|between:1,4',
    //         'is_pungli' => 'required|in:0,1',
    //         'pungli_kontak'     => 'required_if:is_pungli,1',
    //         'pungli_keterangan' => 'required_if:is_pungli,1',
    //         'kritik_saran' => 'nullable|string'
    //     ]);

    //     // Ambil data antrian beserta relasi SKPD untuk mendapatkan external_id_sukma
    //     $antrian = Antrian::with('skpd')->findOrFail($request->antrian_id);
    //     // 🔥 LOGIKA DEFAULT: Jika external_id_sukma kosong, gunakan 18
    //     $externalIdSkpd = $antrian->skpd->external_id_sukma ?? 18;

    //     if (!$externalIdSkpd) {
    //         return redirect()->back()->with('error', 'Konfigurasi ID SKPD Sukmadeli belum diatur pada data instansi.');
    //     }

    //     DB::beginTransaction();
    //     try {
    //         // 1. Simpan ke Database Lokal
    //         $skm = Skm::create([
    //             'antrian_id' => $request->antrian_id,
    //             'nilai'      => round($request->u1 + $request->u2 + $request->u3 + $request->u4 + $request->u5 + $request->u6 + $request->u7 + $request->u8 + $request->u9),
    //             'umur'       => $request->umur,
    //             'jk'         => $request->jk,
    //             'pendidikan' => $request->pendidikan,
    //             'pekerjaan'  => $request->pekerjaan,
    //             'disabilitas' => $request->disabilitas,
    //             'jenis_layanan_id' => $request->id_pelayanan,
    //             'u1' => $request->u1,
    //             'u2' => $request->u2,
    //             'u3' => $request->u3,
    //             'u4' => $request->u4,
    //             'u5' => $request->u5,
    //             'u6' => $request->u6,
    //             'u7' => $request->u7,
    //             'u8' => $request->u8,
    //             'u9' => $request->u9,
    //             'is_pungli'         => $request->is_pungli,
    //             'pungli_kontak'     => $request->pungli_kontak,
    //             'pungli_keterangan' => $request->pungli_keterangan,
    //             'kritik_saran' => $request->kritik_saran
    //         ]);

    //         // 2. Update Status Antrian
    //         $antrian->update(['status' => 2, 'waktu_selesai' => now()]);

    //         // 3. KIRIM DATA KE API SUKMADELI
    //         $url_api = "https://sukmadeli.deliserdangkab.go.id/api/v1/survey";
    //         $token   = "694e5225-d868-8323-8e10-21b0d3774720";

    //         $payload = [
    //             "id_skpd"      => (int)$externalIdSkpd,
    //             "umur"         => (int)$request->umur,
    //             "jk"           => $request->jk,
    //             "pendidikan"   => $request->pendidikan,
    //             "pekerjaan"    => $request->pekerjaan,
    //             "disabilitas"  => $request->disabilitas,
    //             "id_pelayanan" => (int)$request->id_pelayanan,
    //             "u1" => (int)$request->u1,
    //             "u2" => (int)$request->u2,
    //             "u3" => (int)$request->u3,
    //             "u4" => (int)$request->u4,
    //             "u5" => (int)$request->u5,
    //             "u6" => (int)$request->u6,
    //             "u7" => (int)$request->u7,
    //             "u8" => (int)$request->u8,
    //             "u9" => (int)$request->u9
    //         ];

    //         // PERBAIKAN: Tambahkan 'verify' => false untuk bypass SSL Error
    //         $response = Http::withOptions(['verify' => false])
    //             ->withHeaders(['X-API-TOKEN' => $token])
    //             ->timeout(20) // Naikkan timeout jaga-jaga koneksi lambat
    //             ->post($url_api, $payload);

    //         // Cek jika API Sukmadeli mengembalikan Error (Bukan 200 OK)
    //         if ($response->failed()) {
    //             Log::error("API SUKMA ERROR: " . $response->body());
    //             // Kita tidak rollback DB lokal agar data tetap tersimpan meski gagal kirim ke pusat
    //             // Tapi kita beri notifikasi ke user (opsional)
    //         }

    //         DB::commit();

    //         return redirect()->back()->with([
    //             'success' => 'Terima kasih atas penilaian Anda!',
    //             'api_debug' => [
    //                 'status_code' => $response->status(),
    //                 'payload'     => $payload,
    //                 'response'    => $response->json()
    //             ]
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         // CATAT LOG ERROR ASLINYA
    //         Log::error("SKM STORE ERROR: " . $e->getMessage() . " | Line: " . $e->getLine());

    //         // TAMPILKAN ERROR ASLI KE LAYAR (UNTUK DEBUGGING)
    //         return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
    //     }
    // }

    public function store(Request $request)
    {
        $request->validate([
            'antrian_id'   => 'required|exists:antrians,id',
            'umur'         => 'required|numeric',
            'jk'           => 'required|in:L,P',
            'pendidikan'   => 'required|string',
            'pekerjaan'    => 'required|string',
            'disabilitas'  => 'required|in:YA,TIDAK',
            'id_pelayanan' => 'required|integer',
            'u1' => 'required|integer|between:1,4',
            'u2' => 'required|integer|between:1,4',
            'u3' => 'required|integer|between:1,4',
            'u4' => 'required|integer|between:1,4',
            'u5' => 'required|integer|between:1,4',
            'u6' => 'required|integer|between:1,4',
            'u7' => 'required|integer|between:1,4',
            'u8' => 'required|integer|between:1,4',
            'u9' => 'required|integer|between:1,4',
            'is_pungli'         => 'required|in:0,1',
            'pungli_kontak'     => 'required_if:is_pungli,1',
            'pungli_keterangan' => 'required_if:is_pungli,1',
            'kritik_saran'      => 'nullable|string'
        ]);

        $antrian = Antrian::with('skpd')->findOrFail($request->antrian_id);
        // Default ID 18 jika tidak ada di database
        $externalIdSkpd = $antrian->skpd->external_id_sukma ?? 18;

        DB::beginTransaction();
        try {
            // 1. Simpan ke Database Lokal
            Skm::create([
                'antrian_id' => $request->antrian_id,
                'nilai'      => round($request->u1 + $request->u2 + $request->u3 + $request->u4 + $request->u5 + $request->u6 + $request->u7 + $request->u8 + $request->u9),
                'umur'       => $request->umur,
                'jk'         => $request->jk,
                'pendidikan' => $request->pendidikan,
                'pekerjaan'  => $request->pekerjaan,
                'disabilitas' => $request->disabilitas,
                'jenis_layanan_id' => $request->id_pelayanan,
                'u1' => $request->u1,
                'u2' => $request->u2,
                'u3' => $request->u3,
                'u4' => $request->u4,
                'u5' => $request->u5,
                'u6' => $request->u6,
                'u7' => $request->u7,
                'u8' => $request->u8,
                'u9' => $request->u9,
                'is_pungli'         => $request->is_pungli,
                'pungli_kontak'     => $request->pungli_kontak,
                'pungli_keterangan' => $request->pungli_keterangan,
                'kritik_saran'      => $request->kritik_saran
            ]);

            // 2. Update Status Antrian
            $antrian->update(['status' => 2, 'waktu_selesai' => now()]);

            // 3. KIRIM DATA KE API SUKMADELI
            $url_api = "https://sukmadeli.deliserdangkab.go.id/api/v1/survey";
            $token   = "694e5225-d868-8323-8e10-21b0d3774720";

            $payload = [
                "id_skpd"      => (int)$externalIdSkpd,
                "umur"         => (int)$request->umur,
                "jk"           => $request->jk,
                "pendidikan"   => $request->pendidikan,
                "pekerjaan"    => $request->pekerjaan,
                "disabilitas"  => $request->disabilitas,
                "id_pelayanan" => (int)$request->id_pelayanan,
                "u1" => (int)$request->u1,
                "u2" => (int)$request->u2,
                "u3" => (int)$request->u3,
                "u4" => (int)$request->u4,
                "u5" => (int)$request->u5,
                "u6" => (int)$request->u6,
                "u7" => (int)$request->u7,
                "u8" => (int)$request->u8,
                "u9" => (int)$request->u9
            ];

            // [PENTING] Tetap gunakan verify false untuk menangani SSL Intranet
            $response = Http::withOptions(['verify' => false])
                ->withHeaders(['X-API-TOKEN' => $token])
                ->timeout(10) // Timeout standar
                ->post($url_api, $payload);

            // Log jika API Pusat menolak data (untuk admin saja, user tidak perlu tahu detailnya)
            if ($response->failed()) {
                Log::error("GAGAL KIRIM SUKMADELI: " . $response->body());
            }

            DB::commit();

            // Respon bersih tanpa 'api_debug'
            return redirect()->back()->with('success', 'Terima kasih, penilaian Anda telah tersimpan!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Catat error asli di Log file server (storage/logs/laravel.log)
            Log::error("SKM SYSTEM ERROR: " . $e->getMessage() . " | Line: " . $e->getLine());

            // Tampilkan pesan umum ke User (Clean)
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi petugas.');
        }
    }
}
