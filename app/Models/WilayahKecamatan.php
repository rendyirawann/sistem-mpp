<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WilayahKecamatan extends Model
{
    use HasFactory;

    protected $table = 'wilayah_kecamatan';

    protected $fillable = [
        'id',
        'nama',
        'kode_pos',
        'wilayah_kabupaten_id',
    ];

    protected $casts = [
        'kode_pos' => 'array', // karena field text, bisa diisi JSON untuk banyak kode pos
    ];

    // Relasi ke Kabupaten (many-to-one)
    public function wilayahkabupaten(): BelongsTo
    {
        return $this->belongsTo(WilayahKabupaten::class, 'wilayah_kabupaten_id', 'id');
    }

    public function wilayahdesa()
    {
        return $this->hasMany(WilayahDesa::class, 'wilayah_kecamatan_id', 'id');
    }
}