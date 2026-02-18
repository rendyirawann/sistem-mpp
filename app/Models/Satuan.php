<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Models\User;

class Satuan extends Model
{
    use HasFactory;

    protected $table = 'satuan';

    // Jika menggunakan UUID sebagai primary key:
    public $incrementing = false;
    protected $keyType = 'string';

    // Jika kamu ingin timestamps (created_at, updated_at)
    public $timestamps = true;

    protected $fillable = [
        'id',
        'nama',
        'user_id',
    ];

    /**
     * Generate UUID otomatis jika belum diisi.
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Relasi ke user (pemilik / pembuat).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
