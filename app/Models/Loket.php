<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loket extends Model
{
    use HasFactory;

    protected $table = 'lokets';

    protected $fillable = [
        'skpd_id',
        'kode_tenant',
        'prefix_tenant',
        'nama_loket',
        'isaktif',
    ];

    public function skpd()
    {
        return $this->belongsTo(Skpd::class);
    }

    public function antrians()
    {
        return $this->hasMany(Antrian::class);
    }
}
