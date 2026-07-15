<?php

namespace App\Http\Controllers\Backend\Display;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DisplaySetting;
use App\Models\DisplayBanner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DisplaySettingController extends Controller
{
    /**
     * Konstruktor: Batasi hanya untuk user ber-role Superadmin saja.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->hasRole('Superadmin')) {
                abort(403, 'Akses ditolak. Menu ini hanya dapat diakses oleh Superadmin.');
            }
            return $next($request);
        });
    }

    /**
     * Tampilkan Halaman Pengaturan Display Monitor TV
     */
    public function index()
    {
        // Dapatkan setting pertama, atau buat jika kosong
        $setting = DisplaySetting::firstOrCreate([], [
            'video_youtube_id' => 'qK65r2c462I',
            'ticker_text' => 'Selamat Datang di Mal Pelayanan Publik Kabupaten Deli Serdang. Mari melayani dengan ramah, cepat, transparan, dan prima. Silakan tunggu giliran nomor antrian Anda dipanggil. NIK Anda terdaftar dengan aman di database MPP. Sukseskan Mal Pelayanan Publik Deli Serdang!'
        ]);

        $banners = DisplayBanner::orderBy('order_index', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return view('backend.display.index', compact('setting', 'banners'));
    }

    /**
     * Perbarui Pengaturan Video YouTube dan Teks Ticker
     */
    public function updateSetting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_youtube_id' => 'required|string|max:50',
            'ticker_text' => 'nullable|string|max:1000',
        ], [
            'video_youtube_id.required' => 'ID Video YouTube wajib diisi.',
            'video_youtube_id.max' => 'ID Video YouTube maksimal 50 karakter.',
            'ticker_text.max' => 'Teks Informasi/Ticker maksimal 1000 karakter.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $setting = DisplaySetting::first();
        if (!$setting) {
            $setting = new DisplaySetting();
        }

        $setting->video_youtube_id = $request->video_youtube_id;
        $setting->ticker_text = $request->ticker_text;
        $setting->save();

        return redirect()->back()->with('success', 'Pengaturan display berhasil diperbarui.');
    }

    /**
     * Simpan Gambar Banner Iklan Baru (Mendukung Multi-Upload / Form Repeater)
     */
    public function storeBanner(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'banners' => 'required|array|min:1',
            'banners.*.image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'banners.*.title' => 'nullable|string|max:150',
            'banners.*.order_index' => 'nullable|integer',
        ], [
            'banners.required' => 'Wajib menambahkan minimal satu baris banner.',
            'banners.*.image.required' => 'Gambar banner pada setiap baris wajib diunggah.',
            'banners.*.image.image' => 'File harus berupa gambar.',
            'banners.*.image.mimes' => 'Format gambar harus jpeg, jpg, png, gif, atau webp.',
            'banners.*.image.max' => 'Ukuran gambar maksimal 2 MB.',
            'banners.*.title.max' => 'Judul iklan maksimal 150 karakter.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $uploadedCount = 0;
        if ($request->has('banners')) {
            foreach ($request->banners as $row) {
                if (isset($row['image']) && $row['image']->isValid()) {
                    $file = $row['image'];
                    
                    // Simpan gambar ke storage/app/public/iklan/
                    $path = $file->store('iklan', 'public');

                    $banner = new DisplayBanner();
                    $banner->image_path = 'storage/' . $path; // Menyimpan path publik "storage/iklan/filename.ext"
                    $banner->title = $row['title'] ?? null;
                    $banner->order_index = $row['order_index'] ?? 0;
                    $banner->is_active = true;
                    $banner->save();

                    $uploadedCount++;
                }
            }

            return redirect()->back()->with('success', "{$uploadedCount} banner iklan baru berhasil ditambahkan.");
        }

        return redirect()->back()->with('error', 'Gagal mengunggah banner.');
    }

    /**
     * Toggle Aktif/Nonaktif Gambar Banner
     */
    public function toggleBanner($id)
    {
        $banner = DisplayBanner::findOrFail($id);
        $banner->is_active = !$banner->is_active;
        $banner->save();

        $status = $banner->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Banner iklan berhasil {$status}.");
    }

    /**
     * Hapus Gambar Banner
     */
    public function deleteBanner($id)
    {
        $banner = DisplayBanner::findOrFail($id);

        // Hapus file fisik dari storage jika ada
        // Mengubah "storage/iklan/filename.ext" menjadi "iklan/filename.ext" untuk pencarian storage disk
        $relativePath = str_replace('storage/', '', $banner->image_path);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }

        $banner->delete();

        return redirect()->back()->with('success', 'Banner iklan berhasil dihapus.');
    }
}
