<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LayananSkm extends Model
{
    protected $table = 'layanan_skm';

    protected $fillable = [
        'id_opd',
        'opd',
        'id_layanan',
        'layanan'
    ];

    /**
     * Get the SKPD that owns the service.
     */
    public function skpd()
    {
        return $this->belongsTo(Skpd::class, 'id_opd', 'external_id_sukma');
    }
}
