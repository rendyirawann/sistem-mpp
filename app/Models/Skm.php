<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Skm extends Model
{
    protected $table = 'skm';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    // 🔥 DAFTARKAN SEMUA FIELD AGAR BISA DISIMPAN 🔥
    protected $fillable = [
        'antrian_id',
        'nilai',
        'umur',
        'jk',
        'pendidikan',
        'pekerjaan',
        'disabilitas',
        'jenis_layanan_id',
        'u1',
        'u2',
        'u3',
        'u4',
        'u5',
        'u6',
        'u7',
        'u8',
        'u9',
        'is_pungli',
        'pungli_kontak',
        'pungli_keterangan',
        'kritik_saran'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function antrian()
    {
        return $this->belongsTo(Antrian::class);
    }
}
