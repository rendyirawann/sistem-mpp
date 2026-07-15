<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $table = 'site_settings';

    protected $fillable = ['key', 'value'];

    /** Ambil 1 nilai setting berdasarkan key. */
    public static function get(string $key, $default = null)
    {
        $row = static::where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    /** Simpan / perbarui 1 setting. */
    public static function set(string $key, $value)
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /** Seluruh setting sebagai array [key => value]. */
    public static function allKeyed(): array
    {
        return static::pluck('value', 'key')->toArray();
    }
}
