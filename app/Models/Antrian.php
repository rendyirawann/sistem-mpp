<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // 🔥 WAJIB: Tambahkan ini untuk UUID

class Antrian extends Model
{
    use HasFactory;

    protected $table = 'antrians';

    // Konfigurasi UUID
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * 🔥 FUNGSI PENTING: Otomatis isi ID dengan UUID saat create
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'id',
        'customer_id',
        'skpd_id',
        'loket_id',
        'no_urut', // 🔥 PERBAIKAN: Di database kolomnya 'no_urut', bukan 'nomor_urut'
        'no_antrian',
        'status',
        'sumber',      // 'kiosk' | 'online'
        'foto_wajah',
        'tanggal',
        'waktu_ambil',
        'waktu_panggil',
        'waktu_selesai',
    ];

    protected $casts = [
        'no_urut'     => 'integer', // Sesuaikan dengan nama kolom DB
        'status'      => 'integer',
        'tanggal'     => 'date',
        'waktu_ambil' => 'datetime',
        'waktu_panggil' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    /* =======================
     | RELATIONS
     ======================= */

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function skpd()
    {
        return $this->belongsTo(Skpd::class);
    }

    public function loket()
    {
        return $this->belongsTo(Loket::class);
    }

    /** Isian form persyaratan yang dilampirkan warga untuk antrean ini. */
    public function formValues()
    {
        return $this->hasMany(FormPersyaratanValue::class, 'antrian_id', 'id');
    }

    // Scope tambahan (opsional, ada di controller sebelumnya)
    public function scopeHariIni($q)
    {
        return $q->whereDate('tanggal', now());
    }
}
