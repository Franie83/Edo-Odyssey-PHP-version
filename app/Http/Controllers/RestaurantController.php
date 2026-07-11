<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $query = Restaurant::where('is_active', true);

        // 🔍 Search
        if ($s = $request->q) {
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('cuisine_type', 'like', "%{$s}%")
                  ->orWhere('city', 'like', "%{$s}%");
            });
        }

        // 🏙️ City filter
        if ($city = $request->city) {
            $query->where('city', 'like', "%{$city}%");
        }

        // 🍽️ Cuisine filter (text input, not dropdown for flexibility)
        if ($cuisine = $request->cuisine) {
            $query->where('cuisine_type', 'like', "%{$cuisine}%");
        }

        // 💰 Price range (avg_price)
        if ($minPrice = $request->min_price) {
            $query->where('avg_price', '>=', $minPrice);
        }
        if ($maxPrice = $request->max_price) {
            $query->where('avg_price', '<=', $maxPrice);
        }

        // ⭐ Rating filter (minimum average rating)
        if ($minRating = $request->min_rating) {
            $query->whereHas('reviews', function ($q) use ($minRating) {
                $q->select('target_id')
                  ->where('target_type', 'Restaurant')
                  ->where('is_approved', true)
                  ->groupBy('target_id')
                  ->havingRaw('AVG(rating) >= ?', [$minRating]);
            });
        }

        // 📊 Sorting
        if ($sort = $request->sort) {
            match($sort) {
                'price_asc'  => $query->orderBy('avg_price'),
                'price_desc' => $query->orderByDesc('avg_price'),
                'rating'     => $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating'),
                default      => $query->orderByDesc('is_featured')->orderByDesc('created_at'),
            };
        } else {
            $query->orderByDesc('is_featured')->orderByDesc('created_at');
        }

        // 🏙️ Get distinct cities for dropdown
        $cities = Restaurant::where('is_active', true)
                            ->whereNotNull('city')
                            ->distinct()
                            ->pluck('city');

        return view('restaurants.index', [
            'restaurants' => $query->paginate(12)->withQueryString(),
            'cities'      => $cities,
        ]);
    }

    public function show(int $id)
    {
        $restaurant = Restaurant::with('menu', 'reviews.user')->findOrFail($id);
        $isFav = Auth::check() && Favourite::where('user_id', Auth::id())
                                            ->where('target_type', 'Restaurant')
                                            ->where('target_id', $id)->exists();
        return view('restaurants.show', [
            'restaurant' => $restaurant,
            'reviews'    => $restaurant->reviews()->with('user')->orderByDesc('created_at')->limit(10)->get(),
            'avg_rating' => $restaurant->avg_rating,
            'is_fav'     => $isFav,
        ]);
    }
}