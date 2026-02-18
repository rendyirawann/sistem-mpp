<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    use HasFactory;

    protected $table = 'counters';

    protected $fillable = [
        'loket_id',
        'nama_counter',
        'status',
    ];

    public function loket()
    {
        return $this->belongsTo(Loket::class);
    }
}
