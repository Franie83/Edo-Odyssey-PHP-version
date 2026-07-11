<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['booking_id', 'user_id', 'amount', 'currency', 'status', 'provider', 'reference', 'metadata'];
    protected $casts    = ['metadata' => 'array', 'amount' => 'decimal:2'];

    public function booking() { return $this->belongsTo(Booking::class); }
    public function user()    { return $this->belongsTo(User::class); }
}
