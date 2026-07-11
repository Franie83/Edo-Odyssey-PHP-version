<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class News extends Model
{
    use SoftDeletes;

    protected $table = 'news';

    protected $fillable = [
        'title', 'slug', 'content', 'excerpt', 'image_url',
        'author', 'author_user_id', 'category', 'tags',
        'is_featured', 'is_published', 'views', 'published_at',
    ];

    protected $casts = [
        'is_featured'  => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Jinja2 template used article.created_at so we alias it
    public function getCreatedAtAttribute($value) { return $value ? \Carbon\Carbon::parse($value) : null; }

    protected static function booted(): void
    {
        static::creating(function ($n) {
            if (!$n->slug) $n->slug = Str::slug($n->title) . '-' . Str::random(4);
        });
    }

    public function getImageUrlDisplayAttribute(): string
    {
        return Helpers::imageUrl($this->image_url);
    }

    public function authorUser()
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }

    public function comments()
    {
        return $this->hasMany(NewsComment::class)->where('is_approved', true);
    }
}
