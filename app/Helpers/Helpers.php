<?php

namespace App\Helpers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Cloudinary\Cloudinary;

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
        
        // Check if file exists in public disk - use asset() for URL
        if (Storage::disk('public')->exists($cleanPath)) {
            return asset('storage/' . $cleanPath);
        }
        
        // Check if file exists in 'attractions/' folder
        if (Storage::disk('public')->exists('attractions/' . $cleanPath)) {
            return asset('storage/attractions/' . $cleanPath);
        }
        
        // Check if file exists in 'uploads/' folder
        if (Storage::disk('public')->exists('uploads/' . $cleanPath)) {
            return asset('storage/uploads/' . $cleanPath);
        }
        
        // Check if file exists in 'hotels/' folder
        if (Storage::disk('public')->exists('hotels/' . $cleanPath)) {
            return asset('storage/hotels/' . $cleanPath);
        }
        
        // Check if file exists in 'restaurants/' folder
        if (Storage::disk('public')->exists('restaurants/' . $cleanPath)) {
            return asset('storage/restaurants/' . $cleanPath);
        }
        
        // Check if file exists in 'events/' folder
        if (Storage::disk('public')->exists('events/' . $cleanPath)) {
            return asset('storage/events/' . $cleanPath);
        }
        
        // Check if file exists in 'partners/' folder
        if (Storage::disk('public')->exists('partners/' . $cleanPath)) {
            return asset('storage/partners/' . $cleanPath);
        }
        
        // Check if file exists in 'avatars/' folder
        if (Storage::disk('public')->exists('avatars/' . $cleanPath)) {
            return asset('storage/avatars/' . $cleanPath);
        }
        
        // If the path starts with 'attractions/' but doesn't exist, try to show it anyway
        if (Str::startsWith($path, 'attractions/')) {
            return asset('storage/' . $path);
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
     * Handle file upload and return path.
     * Uploads to Cloudinary if configured, otherwise falls back to local storage.
     */
    public static function saveUpload($file, string $folder = 'uploads'): ?string
    {
        if (!$file || !$file->isValid()) {
            return null;
        }
        
        // Check if Cloudinary is configured
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');
        
        if ($cloudName && $apiKey && $apiSecret) {
            try {
                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => $cloudName,
                        'api_key' => $apiKey,
                        'api_secret' => $apiSecret,
                    ],
                ]);
                
                $upload = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                    'folder' => 'edoodyssey/' . $folder,
                    'public_id' => time() . '_' . Str::random(10),
                ]);
                
                return $upload['secure_url'];
            } catch (\Exception $e) {
                // Fallback to local storage if Cloudinary fails
                return $file->store($folder, 'public');
            }
        }
        
        // Fallback to local storage
        return $file->store($folder, 'public');
    }

    /**
     * Generate an 8-char unique reference code.
     */
    public static function generateReference(): string
    {
        return strtoupper(Str::random(3) . now()->format('ymd') . Str::random(2));
    }
}