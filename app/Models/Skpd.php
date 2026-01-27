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
}
