<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FormPersyaratanValue extends Model
{
    protected $table = 'form_persyaratan_values';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'antrian_id', 'form_persyaratan_id', 'kode', 'nama_form', 'nilai',
    ];

    protected $casts = [
        'nilai' => 'array',
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

    public function antrian()
    {
        return $this->belongsTo(Antrian::class, 'antrian_id', 'id');
    }

    public function form()
    {
        return $this->belongsTo(FormPersyaratan::class, 'form_persyaratan_id', 'id');
    }

    /**
     * Susun isian jadi struktur siap-tampil untuk scan/detail:
     * ['kode','nama','sections'=>[ ['title', 'items'=>[ ['label','value','fileUrl'] ] ] ]].
     */
    public function toDisplay(): array
    {
        $skema = optional($this->form)->skema ?? ['sections' => []];
        $nilai = is_array($this->nilai) ? $this->nilai : [];
        $sections = [];

        foreach (($skema['sections'] ?? []) as $sec) {
            $items = [];
            foreach (($sec['fields'] ?? []) as $f) {
                $key = $f['key'] ?? null;
                if (!$key || !array_key_exists($key, $nilai)) continue;
                $v = $nilai[$key];
                if ($v === null || $v === '' || (is_array($v) && count($v) === 0)) continue;

                $type = $f['type'] ?? 'text';
                $item = ['label' => $f['label'] ?? $key, 'value' => '', 'fileUrl' => null];

                if ($type === 'file' && is_array($v) && !empty($v['file'])) {
                    $item['value']   = $v['name'] ?? 'Dokumen';
                    $item['fileUrl'] = asset('storage/' . $v['file']);
                } elseif ($type === 'wilayah' && is_array($v)) {
                    $item['value'] = implode(', ', array_filter([
                        $v['desa'] ?? null, $v['kecamatan'] ?? null, $v['kabupaten'] ?? null, $v['provinsi'] ?? null,
                    ]));
                    if ($item['value'] === '') continue;
                } elseif ($type === 'repeater' && is_array($v)) {
                    $lines = [];
                    foreach ($v as $i => $row) {
                        if (!is_array($row)) continue;
                        $parts = [];
                        foreach (($f['columns'] ?? []) as $col) {
                            $ck = $col['key'] ?? null;
                            if ($ck && isset($row[$ck]) && $row[$ck] !== '') $parts[] = ($col['label'] ?? $ck) . ': ' . $row[$ck];
                        }
                        if ($parts) $lines[] = ($i + 1) . '. ' . implode(', ', $parts);
                    }
                    if (!$lines) continue;
                    $item['value'] = implode("\n", $lines);
                } elseif (is_array($v)) {
                    $item['value'] = implode(', ', $v);
                } else {
                    $item['value'] = (string) $v;
                }

                if ($item['value'] === '' && !$item['fileUrl']) continue;
                $items[] = $item;
            }
            if ($items) $sections[] = ['title' => $sec['title'] ?? '', 'items' => $items];
        }

        return ['kode' => $this->kode, 'nama' => $this->nama_form, 'sections' => $sections];
    }
}
