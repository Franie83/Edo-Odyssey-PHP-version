<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference_code', 'user_id', 'booking_type', 'target_id', 'target_name',
        'start_date', 'end_date', 'guests', 'total_price',
        'booking_status', 'special_requests', 'admin_comment', 'vendor_comment', // changed target_comment → vendor_comment
        'points_awarded',
    ];

    protected $casts = [
        'start_date'     => 'datetime',
        'end_date'       => 'datetime',
        'total_price'    => 'decimal:2',
        'points_awarded' => 'boolean',
    ];

    // ─── Relationships ──────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function histories()
    {
        return $this->hasMany(BookingStatusHistory::class)->orderBy('created_at', 'asc');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────
    public function logStatus(string $status, ?string $comment = null, ?int $userId = null)
    {
        return $this->histories()->create([
            'status'   => $status,
            'comment'  => $comment,
            'user_id'  => $userId ?? auth()->id(),
        ]);
    }

    public function isVendor()
    {
        $user = auth()->user();
        switch ($this->booking_type) {
            case 'Guide':
                return $user->guide && $user->guide->id == $this->target_id;
            case 'Hotel':
                return $user->hotels()->where('id', $this->target_id)->exists();
            case 'Restaurant':
                return $user->restaurants()->where('id', $this->target_id)->exists();
            case 'Event':
                return $user->events()->where('id', $this->target_id)->exists();
            default:
                return false;
        }
    }
}