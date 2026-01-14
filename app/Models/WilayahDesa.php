<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WilayahDesa extends Model
{
    use HasFactory;

    protected $table = 'wilayah_desa';

    protected $fillable = [
        'id',
        'nama',
        'kode_pos',
        'wilayah_kecamatan_id',
    ];

    protected $casts = [
        'kode_pos' => 'array', // karena field text, bisa diisi JSON untuk banyak kode pos
    ];

    // Relasi ke Kecamatan (many-to-one)
    public function wilayahkecamatan(): BelongsTo
    {
        return $this->belongsTo(WilayahKecamatan::class, 'wilayah_kecamatan_id', 'id');
    }
}