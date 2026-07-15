<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    protected $table = 'social_links';

    protected $fillable = ['label', 'url', 'platform', 'order_index', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'order_index' => 'integer',
    ];

    /**
     * Deteksi platform (untuk pemilihan ikon) otomatis dari URL.
     */
    public static function detectPlatform(?string $url): string
    {
        $u = strtolower(trim((string) $url));
        $map = [
            'instagram' => ['instagram.com', 'instagr.am'],
            'tiktok'    => ['tiktok.com'],
            'facebook'  => ['facebook.com', 'fb.com', 'fb.me'],
            'youtube'   => ['youtube.com', 'youtu.be'],
            'twitter'   => ['twitter.com', 'x.com'],
            'linkedin'  => ['linkedin.com', 'lnkd.in'],
            'whatsapp'  => ['wa.me', 'whatsapp.com', 'api.whatsapp'],
            'telegram'  => ['t.me', 'telegram.me'],
            'email'     => ['mailto:'],
        ];

        foreach ($map as $platform => $needles) {
            foreach ($needles as $needle) {
                if (str_contains($u, $needle)) {
                    return $platform;
                }
            }
        }

        return 'website';
    }

    protected static function boot()
    {
        parent::boot();

        // Selalu sinkronkan platform dari URL saat simpan
        static::saving(function ($model) {
            $model->platform = static::detectPlatform($model->url);
        });
    }
}
