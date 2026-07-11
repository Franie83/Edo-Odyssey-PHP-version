<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Attraction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'history',
        'address', 'city', 'latitude', 'longitude',
        'image_url', 'gallery', 'website', 'phone', 'email',
        'opening_hours', 'ticket_price', 'is_featured', 'is_active',
        'status', 'views', 'qr_code_url',
        'user_id', // ✅ added
    ];

    protected $casts = [
        'gallery'     => 'array',
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
        'ticket_price'=> 'decimal:2',
        'latitude'    => 'decimal:7',
        'longitude'   => 'decimal:7',
    ];

    protected static function booted(): void
    {
        static::creating(function ($a) {
            if (!$a->slug) $a->slug = Str::slug($a->name) . '-' . Str::random(4);
        });
    }

    // ── Computed ─────────────────────────────────────────────────────────────
    public function getAvgRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getImageUrlDisplayAttribute(): string
    {
        return Helpers::imageUrl($this->image_url);
    }

    // ── Relationships ─────────────────────────────────────────────────────────
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'target_id')
                    ->where('target_type', 'Attraction')
                    ->where('is_approved', true);
    }

    public function qrCode()
    {
        return $this->hasOne(QrCode::class, 'entity_id')
                    ->where('entity_type', 'Attraction');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'target_id')
                    ->where('booking_type', 'Attraction');
    }

    // ✅ New relationship: the user who created/owns this attraction
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}