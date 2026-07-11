<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsSetting extends Model
{
    protected $fillable = ['key', 'value', 'label', 'type'];

    public static function get(string $key, $default = null)
    {
        return optional(static::where('key', $key)->first())->value ?? $default;
    }

    public static function set(string $key, $value, string $label = '', string $type = 'text'): void
    {
        static::updateOrCreate(['key' => $key], compact('value', 'label', 'type'));
    }
}
