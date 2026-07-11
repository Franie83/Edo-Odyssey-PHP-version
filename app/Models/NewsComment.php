<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsComment extends Model
{
    use SoftDeletes;

    protected $fillable = ['news_id', 'user_id', 'content', 'is_approved'];
    protected $casts    = ['is_approved' => 'boolean'];

    public function news() { return $this->belongsTo(News::class); }
    public function user() { return $this->belongsTo(User::class); }
}
