<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Favourite;

class GuideController extends Controller
{
    public function index(Request $request)
    {
        $query = Guide::where('verification_status', 'Approved')->with('user');

        // 🔍 Search (name, bio, languages, specializations)
        if ($search = $request->q) {
            $query->where(function ($q) use ($search) {
                $q->where('bio', 'like', "%{$search}%")
                  ->orWhere('languages', 'like', "%{$search}%")
                  ->orWhere('specializations', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('first_name', 'like', "%{$search}%")
                                                     ->orWhere('last_name', 'like', "%{$search}%"));
            });
        }

        // 🗣️ Language filter
        if ($lang = $request->language) {
            $query->where('languages', 'like', "%{$lang}%");
        }

        // 💰 Price range (hourly rate)
        if ($minPrice = $request->min_price) {
            $query->where('hourly_rate', '>=', $minPrice);
        }
        if ($maxPrice = $request->max_price) {
            $query->where('hourly_rate', '<=', $maxPrice);
        }

        // ⭐ Rating filter (minimum average rating)
        if ($minRating = $request->min_rating) {
            $query->whereHas('reviews', function ($q) use ($minRating) {
                $q->select('target_id')
                  ->where('target_type', 'Guide')
                  ->where('is_approved', true)
                  ->groupBy('target_id')
                  ->havingRaw('AVG(rating) >= ?', [$minRating]);
            });
        }

        // 📊 Sorting
        if ($sort = $request->sort) {
            match($sort) {
                'rate_asc'  => $query->orderBy('hourly_rate'),
                'rate_desc' => $query->orderByDesc('hourly_rate'),
                'exp_desc'  => $query->orderByDesc('experience'),
                'rating'    => $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating'),
                default     => $query->orderByDesc('is_featured')->orderByDesc('created_at'),
            };
        } else {
            $query->orderByDesc('is_featured')->orderByDesc('created_at');
        }

        // 🏷️ Get distinct languages for dropdown (optional, but we keep text filter for flexibility)
        // Could add a dropdown if needed, but leave as text for now.

        return view('guides.index', [
            'guides' => $query->paginate(12)->withQueryString(),
        ]);
    }

    public function show(int $id)
    {
        $guide = Guide::with('user', 'reviews.user', 'availability')->findOrFail($id);
        $isFav = false;
        if (Auth::check()) {
            $isFav = Favourite::where('user_id', Auth::id())
                               ->where('target_type', 'Guide')
                               ->where('target_id', $id)->exists();
        }
        return view('guides.show', [
            'guide'      => $guide,
            'reviews'    => $guide->reviews()->with('user')->orderByDesc('created_at')->limit(10)->get(),
            'avg_rating' => $guide->avg_rating,
            'is_fav'     => $isFav,
        ]);
    }
}