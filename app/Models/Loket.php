<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loket extends Model
{
    use HasFactory;

    protected $table = 'lokets';

    protected $fillable = [
        'kode_loket',
        'nama_loket',
        'prefix_antrian',
        'status',
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
}
