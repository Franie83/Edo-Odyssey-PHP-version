<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guide extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'bio', 'languages', 'specializations', 'experience',
        'hourly_rate', 'daily_rate', 'certification',
        'verification_status', 'is_featured', 'is_available', 'status',
    ];

    protected $casts = [
        'experience'   => 'integer',
        'hourly_rate'  => 'decimal:2',
        'daily_rate'   => 'decimal:2',
        'is_featured'  => 'boolean',
        'is_available' => 'boolean',
    ];

    public function getAvgRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'target_id')
                    ->where('target_type', 'Guide')
                    ->where('is_approved', true);
    }

    public function availability()
    {
        return $this->hasMany(GuideAvailability::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'target_id')
                    ->where('booking_type', 'Guide');
    }
}
