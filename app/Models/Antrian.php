<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    use HasFactory;

    protected $table = 'antrians';

    protected $fillable = [
        'loket_id',
        'nomor_urut',
        'nomor_antrian',
        'tanggal',
        'status',
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

    /**
     * Relasi:
     * Antrian milik satu Loket
     */
    public function loket()
    {
        return $this->belongsTo(Loket::class);
    }

    /**
     * Scope:
     * Antrian hari ini
     */
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal', now()->toDateString());
    }

    /**
     * Scope:
     * Berdasarkan Loket
     */
    public function scopeByLoket($query, $loketId)
    {
        return $query->where('loket_id', $loketId);
    }
}
