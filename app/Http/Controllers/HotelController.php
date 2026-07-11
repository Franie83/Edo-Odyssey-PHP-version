<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HotelController extends Controller
{
    public function index(Request $request)
    {
        $query = Hotel::where('is_active', true);

        // 🔍 Search
        if ($s = $request->q) {
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhere('city', 'like', "%{$s}%");
            });
        }

        // 🏙️ City filter
        if ($city = $request->city) {
            $query->where('city', 'like', "%{$city}%");
        }

        // ⭐ Stars filter
        if ($stars = $request->stars) {
            $query->where('stars', $stars);
        }

        // 💰 Price range
        if ($minPrice = $request->min_price) {
            $query->where('price_per_night', '>=', $minPrice);
        }
        if ($maxPrice = $request->max_price) {
            $query->where('price_per_night', '<=', $maxPrice);
        }

        // ⭐ Rating filter (minimum average rating)
        if ($minRating = $request->min_rating) {
            $query->whereHas('reviews', function ($q) use ($minRating) {
                $q->select('target_id')
                  ->where('target_type', 'Hotel')
                  ->where('is_approved', true)
                  ->groupBy('target_id')
                  ->havingRaw('AVG(rating) >= ?', [$minRating]);
            });
        }

        // 📊 Sorting
        if ($sort = $request->sort) {
            match($sort) {
                'price_asc'  => $query->orderBy('price_per_night'),
                'price_desc' => $query->orderByDesc('price_per_night'),
                'rating'     => $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating'),
                default      => $query->orderByDesc('is_featured')->orderByDesc('created_at'),
            };
        } else {
            $query->orderByDesc('is_featured')->orderByDesc('created_at');
        }

        // 🏙️ Get distinct cities for dropdown
        $cities = Hotel::where('is_active', true)
                       ->whereNotNull('city')
                       ->distinct()
                       ->pluck('city');

        return view('hotels.index', [
            'hotels' => $query->paginate(12)->withQueryString(),
            'cities' => $cities,
        ]);
    }

    public function show(int $id)
    {
        $hotel  = Hotel::with('rooms', 'reviews.user')->findOrFail($id);
        $isFav  = Auth::check() && Favourite::where('user_id', Auth::id())
                                             ->where('target_type', 'Hotel')
                                             ->where('target_id', $id)->exists();
        return view('hotels.show', [
            'hotel'      => $hotel,
            'reviews'    => $hotel->reviews()->with('user')->orderByDesc('created_at')->limit(10)->get(),
            'avg_rating' => $hotel->avg_rating,
            'is_fav'     => $isFav,
        ]);
    }
}