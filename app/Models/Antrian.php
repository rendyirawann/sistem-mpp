<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Antrian extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'skpd_id',
        'loket_id',
        'customer_id',
        'no_urut',
        'no_antrian',
        'status',
        'tanggal',
        'waktu_ambil',
        'waktu_panggil',
        'waktu_selesai',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_ambil' => 'datetime',
        'waktu_panggil' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    public function scopeHariIni($q)
    {
        return $q->whereDate('tanggal', now());
    }

    public function skpd()
    {
        return $this->belongsTo(Skpd::class);
    }
}
