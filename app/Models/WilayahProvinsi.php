<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WilayahProvinsi extends Model
{
    use HasFactory;

    protected $table = 'wilayah_provinsi';

    protected $fillable = [
        'id',
        'nama',
        'kode_pos',
    ];


    public function wilayahkabupaten()
{
    return $this->hasMany(WilayahKabupaten::class, 'wilayah_provinsi_id', 'id');
}

}
