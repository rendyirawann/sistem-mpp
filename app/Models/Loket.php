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

    // public function skpd()
    // {
    //     return $this->belongsTo(Skpd::class);
    // }

    public function antrians()
    {
        return $this->hasMany(Antrian::class);
    }

    public function skpd()
    {
        return $this->belongsTo(Skpd::class, 'skpd_id', 'id');
    }

    /** Form persyaratan yang ditautkan ke layanan ini (0..N). */
    public function formPersyaratan()
    {
        return $this->belongsToMany(
            FormPersyaratan::class,
            'loket_form_persyaratan',
            'loket_id',
            'form_persyaratan_id'
        )->withPivot('urutan')->withTimestamps()->orderBy('urutan');
    }

    /** True jika layanan sudah punya data antrian -> tidak boleh dihapus / ganti nama. */
    public function hasAntrianData(): bool
    {
        return Antrian::where('loket_id', $this->id)->exists();
    }
}
