<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WilayahKabupaten extends Model
{
    use HasFactory;

    protected $table = 'wilayah_kabupaten';

    protected $fillable = [
        'id',
        'nama',
        'kode_pos',
        'wilayah_provinsi_id',
        'tipe',
    ];

    /**
     * Relasi ke Provinsi (many-to-one)
     * wilayah_kabupaten.wilayah_provinsi_id → wilayah_provinsi.id
     */
    public function wilayahprovinsi()
    {
        return $this->belongsTo(WilayahProvinsi::class, 'wilayah_provinsi_id', 'id');
    }


    public function wilayahkecamatan()
    {
        return $this->hasMany(WilayahKecamatan::class, 'wilayah_kabupaten_id', 'id');
    }
}
