<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Hotel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'slug', 'description', 'address', 'city',
        'latitude', 'longitude', 'image_url', 'gallery', 'website', 'phone', 'email',
        'stars', 'price_per_night', 'is_featured', 'is_active', 'status',
        'amenities', 'check_in_time', 'check_out_time',
    ];

    protected $casts = [
        'gallery'        => 'array',
        'is_featured'    => 'boolean',
        'is_active'      => 'boolean',
        'price_per_night'=> 'decimal:2',
        'stars'          => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($h) {
            if (!$h->slug) $h->slug = Str::slug($h->name) . '-' . Str::random(4);
        });
    }

    public function getAvgRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getImageUrlDisplayAttribute(): string
    {
        return Helpers::imageUrl($this->image_url);
    }

    public function user()       { return $this->belongsTo(User::class); }
    public function rooms()      { return $this->hasMany(HotelRoom::class); }
    public function reviews()
    {
        return $this->hasMany(Review::class, 'target_id')
                    ->where('target_type', 'Hotel')
                    ->where('is_approved', true);
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'target_id')->where('booking_type', 'Hotel');
    }
}
