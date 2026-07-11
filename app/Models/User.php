<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'email', 'password', 'first_name', 'last_name', 'phone',
        'role', 'status', 'avatar_url', 'heritage_points',
        'email_verified', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified'  => 'boolean',
        'heritage_points' => 'integer',
        'last_login_at'   => 'datetime',
    ];

    // ── Computed ─────────────────────────────────────────────────────────────
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getIsAdminAttribute(): bool
    {
        return in_array($this->role, ['Agency Admin', 'Super Admin']);
    }

    // ── Relationships ─────────────────────────────────────────────────────────
    public function guide()
    {
        return $this->hasOne(Guide::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function favourites()
    {
        return $this->hasMany(Favourite::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
