<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelRoom extends Model
{
    protected $fillable = [
        'hotel_id', 'room_type', 'description', 'price_per_night',
        'capacity', 'available_count', 'image_url', 'amenities', 'is_available',
    ];
    protected $casts = ['is_available' => 'boolean', 'price_per_night' => 'decimal:2'];

    public function hotel() { return $this->belongsTo(Hotel::class); }
}
