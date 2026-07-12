<?php

namespace App\Helpers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Helpers
{
    /**
     * Return a displayable image URL (absolute URL, storage URL, or placeholder).
     */
    public static function imageUrl(?string $path, string $placeholder = 'https://via.placeholder.com/600x400?text=No+Image'): string
    {
        // If no path, return placeholder
        if (empty($path)) {
            return $placeholder;
        }
        
        // If it's already a full URL (http or https), return as is
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        
        // Clean the path
        $cleanPath = $path;
        
        // Remove leading slashes
        if (Str::startsWith($cleanPath, '/')) {
            $cleanPath = substr($cleanPath, 1);
        }
        
        // Remove 'storage/' prefix if present
        if (Str::startsWith($cleanPath, 'storage/')) {
            $cleanPath = substr($cleanPath, 8);
        }
        
        // Check if file exists in public disk
        if (Storage::disk('public')->exists($cleanPath)) {
            return Storage::disk('public')->url($cleanPath);
        }
        
        // Check if file exists in 'attractions/' folder
        if (Storage::disk('public')->exists('attractions/' . $cleanPath)) {
            return Storage::disk('public')->url('attractions/' . $cleanPath);
        }
        
        // Check if file exists in 'uploads/' folder
        if (Storage::disk('public')->exists('uploads/' . $cleanPath)) {
            return Storage::disk('public')->url('uploads/' . $cleanPath);
        }
        
        // Check if file exists in 'hotels/' folder
        if (Storage::disk('public')->exists('hotels/' . $cleanPath)) {
            return Storage::disk('public')->url('hotels/' . $cleanPath);
        }
        
        // Check if file exists in 'restaurants/' folder
        if (Storage::disk('public')->exists('restaurants/' . $cleanPath)) {
            return Storage::disk('public')->url('restaurants/' . $cleanPath);
        }
        
        // Check if file exists in 'events/' folder
        if (Storage::disk('public')->exists('events/' . $cleanPath)) {
            return Storage::disk('public')->url('events/' . $cleanPath);
        }
        
        // Check if file exists in 'partners/' folder
        if (Storage::disk('public')->exists('partners/' . $cleanPath)) {
            return Storage::disk('public')->url('partners/' . $cleanPath);
        }
        
        // Check if file exists in 'avatars/' folder
        if (Storage::disk('public')->exists('avatars/' . $cleanPath)) {
            return Storage::disk('public')->url('avatars/' . $cleanPath);
        }
        
        // Return placeholder if file doesn't exist
        return $placeholder;
    }

    /**
     * Write an audit log entry.
     */
    public static function logAction(string $action, string $entityType = '', ?int $entityId = null, ?string $description = null): void
    {
        try {
            AuditLog::create([
                'user_id'     => Auth::id(),
                'action'      => $action,
                'entity_type' => $entityType,
                'entity_id'   => $entityId,
                'description' => $description ?? $action,
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silently fail – audit log should never break the main flow
        }
    }

    /**
     * Award heritage points to a user.
     */
    public static function awardPoints(\App\Models\User $user, int $points, string $reason = ''): void
    {
        $user->increment('heritage_points', $points);
        if ($reason) {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title'   => "Heritage Points Earned",
                'message' => "You earned {$points} heritage points: {$reason}",
                'type'    => 'points',
            ]);
        }
    }

    /**
     * Handle file upload and return relative path.
     */
    public static function saveUpload($file, string $folder = 'uploads'): ?string
    {
        if (!$file || !$file->isValid()) {
            return null;
        }
        // Store in the specified folder within 'public' disk
        $path = $file->store($folder, 'public');
        return $path;
    }

    /**
     * Generate an 8-char unique reference code.
     */
    public static function generateReference(): string
    {
        return strtoupper(Str::random(3) . now()->format('ymd') . Str::random(2));
    }
}