<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;
use App\Models\SocialLink;

class LandingContentSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Pengaturan situs (hero + footer) =====
        $settings = [
            'brand_name'        => 'Portal Antrian MPP',

            // Hero
            'hero_title'        => 'Layanan Antrean Online Mandiri',
            'hero_subtitle'     => 'Solusi digital pemerintah untuk kemudahan akses layanan publik. Ambil antrean Anda kapan saja dan di mana saja tanpa perlu menunggu fisik di lokasi.',
            'hero_button_label' => 'Panduan',
            'hero_button_link'  => '#',

            // Antrian online — hari operasional (ISO: 1=Senin .. 7=Minggu)
            'online_hari'       => '1,2,3,4,5',
            // Batas jam ambil antrian utk HARI INI (lewat jam ini -> hanya hari berikutnya)
            'online_cutoff'     => '14:00',

            // Footer
            'footer_brand'       => 'PEMERINTAH KABUPATEN DELI SERDANG',
            'footer_description' => 'Portal resmi Pemerintah Kabupaten Deli Serdang, menyediakan informasi dan layanan publik untuk masyarakat.',
            'footer_email'       => 'example@gmail.com',
            'footer_phone'       => '(061) 7950300',
            'footer_address'     => "Jl. Besar Lubuk Pakam No. 1\nLubuk Pakam, Deli Serdang\nSumatera Utara 20512",
            'footer_copyright'   => '© ' . date('Y') . ' Pemerintah Kabupaten Deli Serdang. Hak Cipta Dilindungi.',
        ];

        foreach ($settings as $key => $value) {
            // updateOrCreate agar idempoten, tapi jangan timpa jika sudah pernah diubah admin
            SiteSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        // ===== Tautan media sosial (icon dideteksi otomatis dari URL) =====
        $socials = [
            ['label' => 'Facebook',  'url' => 'https://facebook.com',            'order_index' => 1],
            ['label' => 'TikTok',    'url' => 'https://www.tiktok.com/@deliserdang', 'order_index' => 2],
            ['label' => 'Instagram', 'url' => 'https://instagram.com/pemkabdeliserdang', 'order_index' => 3],
        ];

        foreach ($socials as $s) {
            SocialLink::firstOrCreate(
                ['url' => $s['url']],
                ['label' => $s['label'], 'order_index' => $s['order_index'], 'is_active' => true]
            );
        }

        // Default: aktifkan tenant Catatan Sipil untuk antrian online.
        // Hanya dijalankan sekali (saat belum ada tenant online) agar tidak
        // menimpa pilihan admin di kemudian hari.
        if (\App\Models\Skpd::where('is_antrianonline', true)->count() === 0) {
            \App\Models\Skpd::where(function ($q) {
                $q->where('nama_skpd', 'like', '%Catatan Sipil%')
                  ->orWhere('nama_skpd', 'like', '%Pencatatan Sipil%')
                  ->orWhere('nama_skpd', 'like', '%Kependudukan%');
            })->update(['is_antrianonline' => true]);
        }
    }
}
