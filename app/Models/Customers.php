<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    // Nama tabel (jika bukan default plural)
    protected $table = 'customers';

    // Primary key
    protected $primaryKey = 'id';

    // Mass assignment
    protected $fillable = [
        'nama',
        'nik',
        'jk',
        'no_hp',
    ];

    // Timestamp otomatis (created_at, updated_at)
    public $timestamps = true;

    /*
    |--------------------------------------------------------------------------
    | CASTING (Optional)
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR (Optional)
    |--------------------------------------------------------------------------
    */

    // Format nomor HP (contoh)
    public function getNoHpFormattedAttribute()
    {
        return preg_replace('/^0/', '+62', $this->no_hp);
    }
}
