<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $fillable = ['title', 'image_url', 'link', 'position', 'start_date', 'end_date', 'is_active', 'impressions', 'clicks'];
    protected $casts    = ['is_active' => 'boolean', 'start_date' => 'datetime', 'end_date' => 'datetime'];
}
