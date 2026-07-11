<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingStatusHistory extends Model
{
    protected $fillable = ['booking_id', 'status', 'comment', 'user_id'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statusColor()
    {
        return [
            'Pending'        => 'warning',
            'AdminApproved'  => 'info',
            'AdminRejected'  => 'danger',
            'VendorAccepted' => 'success',
            'VendorRejected' => 'danger',
            'Completed'      => 'secondary',
            'Cancelled'      => 'secondary',
        ][$this->status] ?? 'secondary';
    }

    public function statusIcon()
    {
        return [
            'Pending'        => 'clock',
            'AdminApproved'  => 'check-circle',
            'AdminRejected'  => 'x-circle',
            'VendorAccepted' => 'handshake',
            'VendorRejected' => 'x-circle',
            'Completed'      => 'trophy',
            'Cancelled'      => 'x-circle',
        ][$this->status] ?? 'circle';
    }
}