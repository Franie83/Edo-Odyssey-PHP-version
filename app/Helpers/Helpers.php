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
        if (!$path) {
            return $placeholder;
        }
        
        // If it's already a full URL (http or https), return as is
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        
        // If it's a storage path, check if it exists
        $storagePath = $path;
        
        // Remove leading slashes if present
        if (Str::startsWith($storagePath, '/')) {
            $storagePath = substr($storagePath, 1);
        }
        
        // Check if it exists in public disk
        if (Storage::disk('public')->exists($storagePath)) {
            return Storage::disk('public')->url($storagePath);
        }
        
        // If path is like "storage/filename.jpg", convert properly
        if (Str::startsWith($path, 'storage/')) {
            $pathWithoutStorage = substr($path, 8);
            if (Storage::disk('public')->exists($pathWithoutStorage)) {
                return Storage::disk('public')->url($pathWithoutStorage);
            }
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