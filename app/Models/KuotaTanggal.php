<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KuotaTanggal extends Model
{
    protected $table = 'kuota_tanggal';

    protected $fillable = ['skpd_id', 'tanggal', 'kuota_online', 'kuota_kiosk'];

    protected $casts = [
        'tanggal'      => 'date',
        'kuota_online' => 'integer',
        'kuota_kiosk'  => 'integer',
    ];
}
