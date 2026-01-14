<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @OA\Schema(
 *     schema="Skpd",
 *     title="SKPD",
 *     description="Schema data SKPD",
 *
 *     @OA\Property(
 *         property="id",
 *         type="string",
 *         example="6d05c655-3531-4e61-83cc-36fb0e8ce8de",
 *         description="Primary key UUID char(36)"
 *     ),
 *
 *     @OA\Property(
 *         property="kode_skpd",
 *         type="string",
 *         maxLength=150,
 *         example="1.02.03",
 *         description="Kode unik SKPD"
 *     ),
 *
 *     @OA\Property(
 *         property="nama_skpd",
 *         type="string",
 *         maxLength=255,
 *         example="Dinas Pendidikan",
 *         description="Nama lengkap SKPD"
 *     ),
 *
 *     @OA\Property(
 *         property="kepala_skpd",
 *         type="string",
 *         maxLength=255,
 *         nullable=true,
 *         example="Budi Santoso",
 *         description="Nama kepala SKPD"
 *     ),
 *
 *     @OA\Property(
 *         property="nip_kepala",
 *         type="string",
 *         maxLength=255,
 *         nullable=true,
 *         example="198001012005011001",
 *         description="NIP kepala SKPD"
 *     ),
 *     @OA\Property(
 *         property="latitude",
 *         type="number",
 *         format="float",
 *         example="-3.56781234",
 *         description="Koordinat lintang lokasi SKPD"
 *     ),
 *
 *     @OA\Property(
 *         property="longitude",
 *         type="number",
 *         format="float",
 *         example="98.67891234",
 *         description="Koordinat bujur lokasi SKPD"
 *     ),
 *
 *     @OA\Property(
 *         property="isAktif",
 *         type="boolean",
 *         example=true,
 *         description="Status aktif SKPD"
 *     ),
 *
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2025-11-20T13:45:00+07:00",
 *         description="Tanggal dibuat"
 *     ),
 *
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         example="2025-11-20T13:45:00+07:00",
 *         description="Tanggal diperbarui"
 *     ),
 * )
 */
class Skpd extends Model
{
    protected $table = 'skpd';

    protected $fillable = [
        'kode_skpd',
        'nama_skpd',
        'kepala_skpd',
        'nip_kepala',
        'isAktif',
        'latitude',
        'longitude',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
