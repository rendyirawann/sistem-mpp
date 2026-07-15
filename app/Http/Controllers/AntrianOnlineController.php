<?php

namespace App\Http\Controllers;

use App\Models\Skpd;
use App\Models\Loket;
use App\Models\SiteSetting;
use App\Models\SocialLink;

class AntrianOnlineController extends Controller
{
    /**
     * Base query tenant untuk halaman publik.
     * - hanya SKPD aktif yang punya loket aktif
     * - prioritas tampil: Catatan Sipil dulu, lalu yang sudah online, lalu alfabet
     * - pakai EXISTS + COLLATE eksplisit (kolom skpd.id beda collation dgn lokets.skpd_id)
     */
    private function tenantQuery()
    {
        return Skpd::query()
            ->where('isaktif', 1)
            ->whereRaw("EXISTS (
                SELECT * FROM lokets
                WHERE skpd.id COLLATE utf8mb4_unicode_ci = lokets.skpd_id COLLATE utf8mb4_unicode_ci
                AND isaktif = 1
            )")
            ->with(['lokets' => function ($q) {
                $q->where('isaktif', 1);
            }])
            ->orderByRaw("CASE WHEN nama_skpd LIKE '%Catatan Sipil%'
                              OR nama_skpd LIKE '%Pencatatan Sipil%'
                              OR nama_skpd LIKE '%Kependudukan%' THEN 0 ELSE 1 END")
            ->orderByDesc('is_antrianonline')
            ->orderBy('nama_skpd');
    }

    private function commonData(): array
    {
        return [
            'settings' => SiteSetting::allKeyed(),
            'socials'  => SocialLink::where('is_active', true)->orderBy('order_index')->get(),
        ];
    }

    /**
     * Beranda /antrian-online — tampilkan 10 tenant (prioritas Capil).
     */
    public function index()
    {
        $tenants = $this->tenantQuery()->limit(10)->get();

        return view('antrian_online.index', array_merge($this->commonData(), [
            'tenants' => $tenants,
        ]));
    }

    /**
     * /list-tenant — seluruh tenant.
     */
    public function listTenant()
    {
        $tenants = $this->tenantQuery()->get();

        return view('antrian_online.list_tenant', array_merge($this->commonData(), [
            'tenants' => $tenants,
        ]));
    }

    /**
     * /antrian-online/tenant/{skpd} — daftar layanan (loket) milik tenant.
     */
    public function layanan($skpd)
    {
        $tenant = Skpd::where('isaktif', 1)->findOrFail($skpd);

        // Hanya tenant yang sudah diaktifkan untuk antrian online
        if (!$tenant->is_antrianonline) {
            return redirect()->route('antrian-online')
                ->with('error', 'Layanan antrean online untuk instansi ini belum tersedia.');
        }

        $lokets = Loket::where('skpd_id', $skpd)
            ->where('isaktif', 1)
            ->orderBy('nama_loket')
            ->get();

        return view('antrian_online.layanan', array_merge($this->commonData(), [
            'tenant' => $tenant,
            'lokets' => $lokets,
        ]));
    }
}
