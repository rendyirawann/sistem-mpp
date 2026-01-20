<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    use HasFactory;

    protected $table = 'antrians';

    protected $fillable = [
        'skpd_id',
        'loket_id',
        'customer_id',
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

    /* =======================
     | RELATIONS
     ======================= */

    public function skpd()
    {
        return $this->belongsTo(Skpd::class);
    }

    public function loket()
    {
        return $this->belongsTo(Loket::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /* =======================
     | QUERY SCOPES
     ======================= */

    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal', now()->toDateString());
    }

    public function scopeByLoket($query, $loketId)
    {
        return $query->where('loket_id', $loketId);
    }
}
