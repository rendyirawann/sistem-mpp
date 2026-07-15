<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FormPersyaratan extends Model
{
    protected $table = 'form_persyaratan';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'kode', 'nama', 'deskripsi', 'skema', 'is_active',
    ];

    protected $casts = [
        'skema'     => 'array',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            if (empty($m->{$m->getKeyName()})) {
                $m->{$m->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /** Loket (layanan) yang memakai form ini. */
    public function lokets()
    {
        return $this->belongsToMany(
            Loket::class,
            'loket_form_persyaratan',
            'form_persyaratan_id',
            'loket_id'
        )->withPivot('urutan')->withTimestamps();
    }

    /** Semua field (flat) dari seluruh section pada skema. */
    public function fields(): array
    {
        $out = [];
        foreach (($this->skema['sections'] ?? []) as $section) {
            foreach (($section['fields'] ?? []) as $field) {
                $out[] = $field;
            }
        }
        return $out;
    }
}
