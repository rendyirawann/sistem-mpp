<?php

namespace App\Http\Controllers\Backend\Display;

use App\Http\Controllers\Controller;
use App\Models\DisplayAnnouncement;
use App\Events\PanggilanPengumuman;
use App\Events\StopPengumuman;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AnnouncementController extends Controller
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
     * Tampilkan Halaman Pengumuman Suara TV
     */
    public function index()
    {
        return view('backend.announcement.index');
    }

    /**
     * Ambil data untuk DataTables
     */
    public function getAnnouncement()
    {
        $query = DisplayAnnouncement::query()->orderBy('order_index', 'asc');
        return DataTables::of($query)
            ->addColumn('is_active_label', function($row) {
                return $row->is_active 
                    ? '<span class="badge badge-light-success">Aktif</span>' 
                    : '<span class="badge badge-light-danger">Nonaktif</span>';
            })
            ->addColumn('action', function($row) {
                return '
                <div class="text-center">
                    <button class="btn btn-sm btn-icon btn-light-success btn-play me-2" data-id="'.$row->id.'" title="Putar Pengumuman Ini">
                        <i class="ki-outline ki-notification-on fs-3"></i>
                    </button>
                    <button class="btn btn-sm btn-icon btn-light-warning btn-edit me-2" data-id="'.$row->id.'" title="Edit">
                        <i class="ki-outline ki-pencil fs-3"></i>
                    </button>
                    <button class="btn btn-sm btn-icon btn-light-danger btn-delete" data-id="'.$row->id.'" title="Hapus">
                        <i class="ki-outline ki-trash fs-3"></i>
                    </button>
                </div>';
            })
            ->rawColumns(['is_active_label', 'action'])
            ->make(true);
    }

    /**
     * Memainkan satu pengumuman secara utuh
     */
    public function play(Request $request)
    {
        $request->validate(['id' => 'required|exists:display_announcements,id']);
        $announcement = DisplayAnnouncement::findOrFail($request->id);

        try {
            PanggilanPengumuman::dispatch($announcement->text);
        } catch (\Throwable $e) {
            \Log::error("Gagal mengirim suara pengumuman ke Reverb: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman suara berhasil dikirim ke layar display TV.'
        ]);
    }

    /**
     * Memainkan semua pengumuman aktif berurutan
     * Menghapus kalimat penutup di tengah-tengah, dan hanya menyisipkannya satu kali di paling akhir.
     */
    public function playAll()
    {
        $announcements = DisplayAnnouncement::where('is_active', true)
            ->orderBy('order_index', 'asc')
            ->get();

        if ($announcements->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada pengumuman aktif yang bisa diputar.'
            ], 422);
        }

        $phrases = [];
        $closingPhrase = "ATAS KERJASAMANYA KAMI UCAPKAN TERIMA KASIH";

        foreach ($announcements as $announcement) {
            // Regex untuk membuang kalimat penutup (case-insensitive, spasi ganda, titik/koma/tanda seru/whitespace di sekitarnya)
            $cleaned = preg_replace('/\bATAS\s+KERJASAMANYA\s+KAMI\s+UCAPKAN\s+TERIMA\s+KASIH[\.\,\!\s]*/i', '', $announcement->text);
            $cleaned = trim($cleaned);
            if (!empty($cleaned)) {
                $phrases[] = $cleaned;
            }
        }

        if (empty($phrases)) {
            // Jika semuanya terhapus (misal hanya berisi kalimat penutup), cukup putar kalimat penutup saja
            $combinedText = $closingPhrase . ".";
        } else {
            // Gabungkan kalimat-kalimat dengan jeda elipsis untuk natural pause di SpeechSynthesis
            $combinedText = implode(" ... ", $phrases);
            
            // Tambahkan elipsis dan kalimat penutup di akhir rangkaian
            // Pastikan format rapi (contoh: "... ATAS KERJASAMANYA KAMI UCAPKAN TERIMA KASIH.")
            $combinedText = rtrim($combinedText, '.') . " ... " . $closingPhrase . ".";
        }

        try {
            PanggilanPengumuman::dispatch($combinedText);
        } catch (\Throwable $e) {
            \Log::error("Gagal mengirim Play All pengumuman ke Reverb: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Seluruh pengumuman berhasil digabungkan dan diputar di TV.'
        ]);
    }

    /**
     * Menghentikan semua pengumuman yang sedang berbunyi di TV
     */
    public function stop()
    {
        try {
            StopPengumuman::dispatch();
        } catch (\Throwable $e) {
            \Log::error("Gagal mengirim event stop pengumuman ke Reverb: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Sinyal penghentian berhasil dikirim ke layar display TV.'
        ]);
    }

    /**
     * Simpan pengumuman baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'text' => 'required|string',
            'order_index' => 'required|numeric',
        ]);

        DisplayAnnouncement::create([
            'title' => $request->title,
            'text' => $request->text,
            'order_index' => $request->order_index,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return response()->json(['success' => true, 'message' => 'Pengumuman berhasil ditambahkan.']);
    }

    /**
     * Tampilkan detail pengumuman
     */
    public function show($id)
    {
        $announcement = DisplayAnnouncement::findOrFail($id);
        return response()->json($announcement);
    }

    /**
     * Perbarui pengumuman
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'text' => 'required|string',
            'order_index' => 'required|numeric',
        ]);

        $announcement = DisplayAnnouncement::findOrFail($id);
        $announcement->update([
            'title' => $request->title,
            'text' => $request->text,
            'order_index' => $request->order_index,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return response()->json(['success' => true, 'message' => 'Pengumuman berhasil diperbarui.']);
    }

    /**
     * Hapus pengumuman
     */
    public function destroy($id)
    {
        $announcement = DisplayAnnouncement::findOrFail($id);
        $announcement->delete();
        return response()->json(['success' => true, 'message' => 'Pengumuman berhasil dihapus.']);
    }
}
