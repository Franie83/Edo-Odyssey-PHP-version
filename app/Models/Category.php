<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'icon', 'color', 'is_active', 'sort_order'];
    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::creating(function ($cat) {
            if (!$cat->slug) $cat->slug = Str::slug($cat->name);
        });
    }

    public function attractions()
    {
        return $this->hasMany(Attraction::class);
    }
}
