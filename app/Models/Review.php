<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'target_type', 'target_id', 'rating', 'comment', 'title', 'is_approved'];
    protected $casts    = ['is_approved' => 'boolean', 'rating' => 'integer'];

    public function user() { return $this->belongsTo(User::class); }
}
