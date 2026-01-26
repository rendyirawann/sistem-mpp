<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    use HasFactory;

    protected $table = 'antrians';

    /**
     * Karena pakai UUID
     */
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Field yang boleh diisi mass-assignment
     */
    protected $fillable = [
        'id',
        'customer_id',
        'skpd_id',
        'loket_id',
        'nomor_urut',
        'no_antrian',
        'status',
        'tanggal',
        'waktu_ambil',
    ];

    /**
     * Cast tipe data agar sesuai database
     */
    protected $casts = [
        'nomor_urut'  => 'integer',
        'status'      => 'integer',
        'tanggal'     => 'date',
        'waktu_ambil' => 'datetime',
    ];

    /**
     * ================= RELATION =================
     */
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
}
