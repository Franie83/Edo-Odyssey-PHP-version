<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantMenu extends Model
{
    protected $fillable = [
        'restaurant_id', 'name', 'description', 'category', 'price',
        'image_url', 'is_available', 'is_vegetarian', 'is_featured',
    ];
    protected $casts = [
        'is_available'  => 'boolean',
        'is_vegetarian' => 'boolean',
        'is_featured'   => 'boolean',
        'price'         => 'decimal:2',
    ];

    public function restaurant() { return $this->belongsTo(Restaurant::class); }
}
