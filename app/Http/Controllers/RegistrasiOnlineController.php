<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Loket;
use App\Models\Skpd;
use App\Models\Customer;
use App\Models\Antrian;
use App\Models\FormPersyaratanValue;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use App\Services\AntrianService;
use App\Events\AntrianBaru;

class RegistrasiOnlineController extends Controller
{
    public function __construct(private AntrianService $antrian) {}

    private function draftKey(string $loketId): string
    {
        return 'regdraft:' . session()->getId() . ':' . $loketId;
    }

    private function commonData(): array
    {
        return [
            'settings' => SiteSetting::allKeyed(),
            'socials'  => SocialLink::where('is_active', true)->orderBy('order_index')->get(),
        ];
    }

    /**
     * True jika NIK sudah punya antrean AKTIF untuk LAYANAN (loket) yang sama,
     * yaitu tanggalnya belum lewat (hari ini / akan datang) & belum dibatalkan.
     * NIK tetap bebas mengambil layanan (loket) lain.
     */
    private function nikSudahAntre(string $loketId, string $nik): bool
    {
        return Antrian::where('loket_id', $loketId)
            ->where('status', '!=', 3) // abaikan antrean yang dibatalkan
            ->whereDate('tanggal', '>=', now()->toDateString()) // "hari A belum lewat"
            ->whereHas('customer', fn ($q) => $q->where('nik', $nik))
            ->exists();
    }

    /** Halaman wizard registrasi online untuk sebuah loket/layanan. */
    public function show($loketId)
    {
        $loket = Loket::with(['skpd', 'formPersyaratan'])->where('isaktif', 1)->findOrFail($loketId);
        $skpd  = $loket->skpd;

        if (!$skpd || !$skpd->is_antrianonline) {
            return redirect()->route('antrian-online')
                ->with('error', 'Layanan antrean online untuk instansi ini belum tersedia.');
        }

        $hariTersedia = $this->antrian->hariTersediaOnline($skpd);
        $adaSlot      = collect($hariTersedia)->where('penuh', false)->isNotEmpty();
        $draft        = Cache::get($this->draftKey($loketId), []);

        // Alasan tutup: 'penuh' = ada hari tapi kuota habis; 'jam' = tak ada hari tersisa (lewat jam cutoff / hari kerja habis)
        $noticeType  = (!$adaSlot && !empty($hariTersedia)) ? 'penuh' : 'jam';
        $cutoffLabel = (string) SiteSetting::get('online_cutoff', '14:00');

        // Form persyaratan (Dukcapil) yang wajib diisi untuk layanan ini (0..N)
        $forms = $loket->formPersyaratan
            ->where('is_active', true)
            ->map(fn ($f) => [
                'id'        => $f->id,
                'kode'      => $f->kode,
                'nama'      => $f->nama,
                'deskripsi' => $f->deskripsi,
                'skema'     => $f->skema,
            ])->values();

        return view('antrian_online.registrasi', array_merge($this->commonData(), [
            'loket'        => $loket,
            'skpd'         => $skpd,
            'hariTersedia' => $hariTersedia,
            'draft'        => $draft,
            'kuotaHabis'   => !$adaSlot,
            'noticeType'   => $noticeType,
            'cutoffLabel'  => $cutoffLabel,
            'forms'        => $forms,
        ]));
    }

    /** Simpan progres (step) ke draft Redis dengan TTL 10 menit (sliding). */
    public function saveStep(Request $request, $loketId)
    {
        $loket = Loket::where('isaktif', 1)->findOrFail($loketId);
        $key   = $this->draftKey($loketId);
        $draft = Cache::get($key, []);
        $step  = (int) $request->input('step', 1);

        if ($step === 1) {
            $validator = Validator::make($request->all(), [
                'nik'   => 'required|numeric|digits:16',
                'nama'  => 'required|string|max:100',
                'jk'    => 'required|in:L,P',
                'no_hp' => 'required|numeric|digits_between:10,14',
            ], [
                'nik.required' => 'NIK wajib diisi', 'nik.digits' => 'NIK harus 16 digit',
                'nama.required' => 'Nama wajib diisi', 'jk.required' => 'Jenis Kelamin wajib dipilih',
                'no_hp.required' => 'Nomor HP/WA wajib diisi',
                'no_hp.digits_between' => 'Nomor HP/WA 10-14 digit',
            ]);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            // Cegah NIK yang sama mengambil layanan yang sama selagi antrean belum lewat
            if ($this->nikSudahAntre($loketId, $request->nik)) {
                return response()->json([
                    'success' => false,
                    'errors'  => ['nik' => ['NIK ini sudah punya antrean aktif untuk layanan ini. Ajukan lagi setelah tanggal antrean Anda lewat, atau pilih layanan lain.']],
                ], 422);
            }

            $draft = array_merge($draft, $request->only(['nik', 'nama', 'jk', 'no_hp']));
        }

        if ($step === 2) {
            $dataUrl = $request->input('foto');
            $bytes   = $this->decodeDataUrl($dataUrl);
            if (!$bytes || !$this->imageQualityOk($bytes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Foto tidak valid (terlalu gelap/terang atau kosong). Silakan ambil ulang.',
                ], 422);
            }
            $compressed = $this->compress($bytes);
            if (!$compressed) {
                return response()->json(['success' => false, 'message' => 'Gagal memproses foto.'], 422);
            }
            $draft['foto'] = base64_encode($compressed); // simpan terkompres di draft
        }

        // Cache isian form persyaratan (AJAX per-halaman) — tahan refresh dari sisi server juga
        if ($request->filled('form_values')) {
            $draft['form_values'] = $request->input('form_values');
        }

        Cache::put($key, $draft, now()->addMinutes(10));

        return response()->json(['success' => true]);
    }

    /** Final submit -> buat antrian online + tiket. */
    public function submit(Request $request, $loketId)
    {
        $loket = Loket::with('skpd')->where('isaktif', 1)->findOrFail($loketId);
        $skpd  = $loket->skpd;
        // Submit MANDIRI: baca semua data langsung dari request (tidak bergantung draft/session).
        $validator = Validator::make($request->all(), [
            'nik'    => 'required|numeric|digits:16',
            'nama'   => 'required|string|max:100',
            'jk'     => 'required|in:L,P',
            'no_hp'  => 'required|numeric|digits_between:10,14',
            'foto'   => 'required|string',
            'setuju' => 'accepted',
        ], [
            'setuju.accepted' => 'Anda harus menyetujui pernyataan data.',
            'foto.required'   => 'Foto verifikasi wajah belum ada. Ulangi langkah verifikasi.',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        // Cegah NIK ganda untuk layanan yang sama selagi antrean belum lewat
        if ($this->nikSudahAntre($loketId, $request->nik)) {
            return response()->json([
                'success' => false,
                'message' => 'NIK ini sudah memiliki antrean aktif untuk layanan ini. Silakan ajukan kembali setelah tanggal antrean Anda lewat, atau pilih layanan lain.',
            ], 422);
        }

        // Validasi & kompres foto wajah (server-side, backup deteksi client)
        $bytes = $this->decodeDataUrl($request->input('foto'));
        if (!$bytes || !$this->imageQualityOk($bytes)) {
            return response()->json(['success' => false, 'message' => 'Foto tidak valid (terlalu gelap/terang/kosong). Ulangi verifikasi wajah.'], 422);
        }
        $compressed = $this->compress($bytes);
        if (!$compressed) {
            return response()->json(['success' => false, 'message' => 'Gagal memproses foto.'], 422);
        }

        // Tanggal yang DIPILIH warga harus valid: hari pelayanan, bukan libur, belum penuh
        $pilih = $request->input('tanggal');
        $slot  = collect($this->antrian->hariTersediaOnline($skpd))->firstWhere('tanggal', $pilih);
        if (!$pilih || !$slot) {
            return response()->json(['success' => false, 'message' => 'Tanggal yang dipilih tidak valid atau bukan hari pelayanan online.'], 422);
        }
        if ($slot['penuh']) {
            return response()->json(['success' => false, 'penuh' => true, 'message' => 'Kuota untuk tanggal tersebut sudah penuh. Silakan pilih hari lain.'], 422);
        }
        $tanggal = Carbon::parse($pilih);

        try {
            DB::beginTransaction();

            $customer = Customer::create([
                'nik'   => $request->nik,
                'nama'  => $request->nama,
                'jk'    => $request->jk,
                'no_hp' => $request->no_hp,
            ]);

            $nomor = $this->antrian->generateNomor($loket, $tanggal, 'online');

            $antrian = new Antrian();
            $antrian->skpd_id     = $skpd->id;
            $antrian->loket_id    = $loket->id;
            $antrian->customer_id = $customer->id;
            $antrian->no_urut     = $nomor['no_urut'];
            $antrian->no_antrian  = $nomor['no_antrian'];
            $antrian->status      = 0;
            $antrian->sumber      = 'online';
            $antrian->tanggal     = $tanggal->toDateString();
            $antrian->waktu_ambil = now();
            $antrian->save();

            // Simpan foto wajah ke folder per-minggu (mudah dibersihkan)
            $folder = 'antrian-wajah/' . $tanggal->isoFormat('GGGG-[W]WW');
            $path   = $folder . '/' . $antrian->id . '.jpg';
            Storage::disk('public')->put($path, $compressed);
            $antrian->foto_wajah = $path;
            $antrian->save();

            // Simpan isian form persyaratan (jika layanan memilikinya)
            $this->simpanFormValues($antrian, $loket, $request, $tanggal);

            DB::commit();

            Cache::forget($this->draftKey($loketId));

            try { AntrianBaru::dispatch(); } catch (\Exception $e) { Log::error('Broadcast gagal: ' . $e->getMessage()); }

            return response()->json([
                'success'   => true,
                'no_antrian' => $antrian->no_antrian,
                'tanggal'   => $tanggal->locale('id')->translatedFormat('d F Y'),
                'is_hari_ini' => $tanggal->isToday(),
                'skpd'      => $skpd->nama_skpd,
                'layanan'   => $loket->nama_loket,
                'pemohon'   => $request->nama,
                'lokasi'    => $skpd->lokasi ?? '-',
                'qr'        => (string) $antrian->id, // ringkas (UUID) agar QR tidak padat & mudah dipindai
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Submit antrian online gagal: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem. Coba lagi.'], 500);
        }
    }

    /**
     * Simpan isian form persyaratan warga ke form_persyaratan_values.
     * Payload `form_values` = JSON { "<form_id>": { field: value, fileKey: {name,dataUrl}, repeaterKey: [ {..} ] } }.
     * File (dataUrl) disimpan ke storage, value diganti { file: path, name: originalName }.
     */
    private function simpanFormValues(Antrian $antrian, Loket $loket, Request $request, Carbon $tanggal): void
    {
        $raw = $request->input('form_values');
        if (!$raw) return;
        $all = is_array($raw) ? $raw : json_decode($raw, true);
        if (!is_array($all) || empty($all)) return;

        $forms  = $loket->formPersyaratan()->where('is_active', true)->get()->keyBy('id');
        $folder = 'form-dokumen/' . $tanggal->isoFormat('GGGG-[W]WW') . '/' . $antrian->id;

        foreach ($all as $formId => $nilai) {
            if (!isset($forms[$formId]) || !is_array($nilai)) continue;
            $form = $forms[$formId];

            // Simpan field file (jika ada { dataUrl })
            foreach ($nilai as $key => $val) {
                if (is_array($val) && isset($val['dataUrl'])) {
                    $bytes = $this->decodeDataUrl($val['dataUrl']);
                    if ($bytes && strlen($bytes) <= 4 * 1024 * 1024) {
                        $ext  = $this->extFromDataUrl($val['dataUrl']) ?: 'bin';
                        $fpath = $folder . '/' . $formId . '_' . $key . '.' . $ext;
                        Storage::disk('public')->put($fpath, $bytes);
                        $nilai[$key] = ['file' => $fpath, 'name' => $val['name'] ?? basename($fpath)];
                    } else {
                        $nilai[$key] = null;
                    }
                }
            }

            FormPersyaratanValue::create([
                'antrian_id'          => $antrian->id,
                'form_persyaratan_id' => $form->id,
                'kode'                => $form->kode,
                'nama_form'           => $form->nama,
                'nilai'               => $nilai,
            ]);
        }
    }

    private function extFromDataUrl(?string $d): ?string
    {
        if (!$d || !preg_match('#^data:([^;]+);#', $d, $m)) return null;
        return [
            'image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/png' => 'png',
            'image/webp' => 'webp', 'application/pdf' => 'pdf',
        ][$m[1]] ?? 'bin';
    }

    // ===== Helpers gambar =====
    private function decodeDataUrl(?string $dataUrl): ?string
    {
        if (!$dataUrl) return null;
        $data  = strpos($dataUrl, ',') !== false ? substr($dataUrl, strpos($dataUrl, ',') + 1) : $dataUrl;
        $bytes = base64_decode($data, true);
        return $bytes ?: null;
    }

    private function compress(string $bytes, int $max = 640, int $quality = 82): ?string
    {
        $img = @imagecreatefromstring($bytes);
        if (!$img) return null;
        $w = imagesx($img); $h = imagesy($img);
        $scale = min(1, $max / max($w, $h));
        $nw = max(1, (int) round($w * $scale));
        $nh = max(1, (int) round($h * $scale));
        $dst = imagecreatetruecolor($nw, $nh);
        imagecopyresampled($dst, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
        ob_start();
        imagejpeg($dst, null, $quality);
        $out = ob_get_clean();
        imagedestroy($img); imagedestroy($dst);
        return $out ?: null;
    }

    private function imageQualityOk(string $bytes): bool
    {
        $img = @imagecreatefromstring($bytes);
        if (!$img) return false;
        $w = imagesx($img); $h = imagesy($img);
        if ($w < 120 || $h < 120) { imagedestroy($img); return false; }

        $stepX = max(1, (int) ($w / 40));
        $stepY = max(1, (int) ($h / 40));
        $n = 0; $sum = 0; $sumSq = 0;
        for ($x = 0; $x < $w; $x += $stepX) {
            for ($y = 0; $y < $h; $y += $stepY) {
                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF; $g = ($rgb >> 8) & 0xFF; $b = $rgb & 0xFF;
                $lum = 0.299 * $r + 0.587 * $g + 0.114 * $b;
                $sum += $lum; $sumSq += $lum * $lum; $n++;
            }
        }
        imagedestroy($img);
        if ($n === 0) return false;
        $mean = $sum / $n;
        $std  = sqrt(max(0, $sumSq / $n - $mean * $mean));

        // tolak: terlalu gelap, terlalu putih/terang, atau hampir polos (kosong)
        if ($mean < 25 || $mean > 235) return false;
        if ($std < 12) return false;
        return true;
    }
}
