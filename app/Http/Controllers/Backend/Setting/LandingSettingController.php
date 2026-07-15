<?php

namespace App\Http\Controllers\Backend\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use App\Models\Skpd;

class LandingSettingController extends Controller
{
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

    public function index()
    {
        $settings = SiteSetting::allKeyed();
        $socials  = SocialLink::orderBy('order_index')->get();

        // Tenant yang relevan (aktif + punya loket aktif), prioritas Capil
        $tenants = Skpd::query()
            ->where('isaktif', 1)
            ->whereRaw("EXISTS (
                SELECT * FROM lokets
                WHERE skpd.id COLLATE utf8mb4_unicode_ci = lokets.skpd_id COLLATE utf8mb4_unicode_ci
                AND isaktif = 1
            )")
            ->orderByRaw("CASE WHEN nama_skpd LIKE '%Catatan Sipil%'
                              OR nama_skpd LIKE '%Pencatatan Sipil%'
                              OR nama_skpd LIKE '%Kependudukan%' THEN 0 ELSE 1 END")
            ->orderByDesc('is_antrianonline')
            ->orderBy('nama_skpd')
            ->get();

        return view('backend.setting.landing', compact('settings', 'socials', 'tenants'));
    }

    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand_name'         => 'required|string|max:100',
            'hero_title'         => 'required|string|max:255',
            'hero_subtitle'      => 'nullable|string|max:1000',
            'hero_button_label'  => 'nullable|string|max:50',
            'hero_button_link'   => 'nullable|string|max:500',
            'footer_brand'       => 'nullable|string|max:255',
            'footer_description' => 'nullable|string|max:1000',
            'footer_email'       => 'nullable|string|max:150',
            'footer_phone'       => 'nullable|string|max:100',
            'footer_address'     => 'nullable|string|max:500',
            'footer_copyright'   => 'nullable|string|max:255',
            'online_cutoff'      => 'nullable|date_format:H:i',
        ], [
            'online_cutoff.date_format' => 'Jam cutoff harus format HH:MM (mis. 14:00).',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $keys = [
            'brand_name', 'hero_title', 'hero_subtitle', 'hero_button_label', 'hero_button_link',
            'footer_brand', 'footer_description', 'footer_email', 'footer_phone', 'footer_address', 'footer_copyright',
            'online_cutoff',
        ];
        foreach ($keys as $k) {
            SiteSetting::set($k, $request->input($k));
        }

        // Hari operasional antrian online (checkbox array ISO 1..7) -> comma separated
        $hari = array_values(array_filter(
            array_map('intval', (array) $request->input('online_hari', [])),
            fn ($d) => $d >= 1 && $d <= 7
        ));
        SiteSetting::set('online_hari', implode(',', $hari ?: [1, 2, 3, 4, 5]));

        return redirect()->back()->with('success', 'Pengaturan landing berhasil diperbarui.');
    }

    public function storeSocial(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'nullable|string|max:50',
            // hanya izinkan http(s)/mailto agar aman dari javascript: injection
            'url'   => ['required', 'string', 'max:500', 'regex:/^(https?:\/\/|mailto:)/i'],
        ], [
            'url.required' => 'URL wajib diisi.',
            'url.regex'    => 'URL harus diawali http://, https://, atau mailto:.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'social')->withInput();
        }

        SocialLink::create([
            'label'       => $request->label,
            'url'         => $request->url,
            'order_index' => (SocialLink::max('order_index') ?? 0) + 1,
            'is_active'   => true,
        ]);

        return redirect()->back()->with('success', 'Tautan media sosial ditambahkan.');
    }

    public function toggleSocial($id)
    {
        $social = SocialLink::findOrFail($id);
        $social->is_active = !$social->is_active;
        $social->save();

        return redirect()->back()->with('success', 'Status tautan media sosial diperbarui.');
    }

    public function deleteSocial($id)
    {
        SocialLink::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Tautan media sosial dihapus.');
    }

    public function toggleTenant(Request $request, $id)
    {
        $skpd = Skpd::findOrFail($id);
        $skpd->is_antrianonline = !$skpd->is_antrianonline;
        $skpd->save();

        return response()->json([
            'success'          => true,
            'is_antrianonline' => (bool) $skpd->is_antrianonline,
            'message'          => $skpd->is_antrianonline ? 'Tenant diaktifkan untuk antrian online.' : 'Tenant dinonaktifkan dari antrian online.',
        ]);
    }
}
