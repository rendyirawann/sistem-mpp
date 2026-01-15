<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loket extends Model
{
    protected $table = 'lokets';

    protected $primaryKey = 'id';

    public $incrementing = false;   // ⬅️ WAJIB
    protected $keyType = 'string';  // ⬅️ WAJIB

    protected $fillable = [
        'id',
        'skpd_id',
        'nama_loket',
        'kode_tenant',
        'prefix_tenant',
        'isaktif'
    ];

    /**
     * Relasi:
     * 1 Loket memiliki banyak Antrian
     */
    public function antrians()
    {
        return $this->hasMany(Antrian::class);
    }

    /**
     * Relasi:
     * 1 Loket memiliki banyak Counter (opsional)
     */
    public function counters()
    {
        return $this->hasMany(Counter::class);
    }

    public function skpd()
    {
        return $this->belongsTo(Skpd::class, 'skpd_id', 'id');
    }
}
