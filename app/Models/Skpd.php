<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Skpd extends Model
{
    protected $table = 'skpd';

    protected $fillable = [
        'nama_skpd',
        'kepala_skpd',
        'lokasi',
        'nip_kepala',
        'isaktif',
        'logo_skpd',
        'no_antrian',
        'external_id_sukma', // <--- TAMBAHKAN INI
        'buka_senin_kamis',
        'tutup_senin_kamis',
        'buka_jumat',
        'tutup_jumat',
        // --- TAMBAHAN SABTU ---
        'is_sabtu_buka',
        'buka_sabtu',
        'tutup_sabtu',
        // ----------------------
        'kuota_harian',
        'kuota_online',
        'kuota_kiosk',
        'is_force_close',
        'is_antrianonline',
    ];

    protected $casts = [
        'isaktif'          => 'boolean',
        'is_sabtu_buka'    => 'boolean',
        'is_force_close'   => 'boolean',
        'is_antrianonline' => 'boolean',
        'kuota_online'     => 'integer',
        'kuota_kiosk'      => 'integer',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /* =======================
     | RELATIONS
     ======================= */

    // SKPD memiliki banyak loket
    public function lokets()
    {
        return $this->hasMany(Loket::class);
    }

    /** True jika instansi sudah punya data antrian -> tidak boleh dihapus / ganti nama. */
    public function hasAntrianData(): bool
    {
        return Antrian::where('skpd_id', $this->id)->exists();
    }
}
