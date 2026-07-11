<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'location', 'address',
        'start_date', 'end_date', 'image_url', 'ticket_price',
        'capacity', 'organizer', 'category', 'website',
        'is_featured', 'is_active', 'status',
        'user_id', // ✅ added
    ];

    protected $casts = [
        'start_date'   => 'datetime',
        'end_date'     => 'datetime',
        'ticket_price' => 'decimal:2',
        'is_featured'  => 'boolean',
        'is_active'    => 'boolean',
    ];

    // backward-compat alias
    public function getDateAttribute() { return $this->start_date; }

    protected static function booted(): void
    {
        static::creating(function ($e) {
            if (!$e->slug) $e->slug = Str::slug($e->name) . '-' . Str::random(4);
        });
    }

    public function getImageUrlDisplayAttribute(): string
    {
        return Helpers::imageUrl($this->image_url);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'target_id')
                    ->where('target_type', 'Event')
                    ->where('is_approved', true);
    }

    // ✅ New relationship: the user who created/owns this event
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}