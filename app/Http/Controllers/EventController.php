<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Favourite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::where('is_active', true);

        // 🔍 Search
        if ($s = $request->q) {
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('location', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhere('category', 'like', "%{$s}%");
            });
        }

        // 🏷️ Category filter
        if ($cat = $request->category) {
            $query->where('category', $cat);
        }

        // 🏙️ City filter (location)
        if ($city = $request->city) {
            $query->where('location', 'like', "%{$city}%");
        }

        // 💰 Price range
        if ($minPrice = $request->min_price) {
            $query->where('ticket_price', '>=', $minPrice);
        }
        if ($maxPrice = $request->max_price) {
            $query->where('ticket_price', '<=', $maxPrice);
        }

        // ⭐ Rating filter (minimum average rating)
        if ($minRating = $request->min_rating) {
            $query->whereHas('reviews', function ($q) use ($minRating) {
                $q->select('target_id')
                  ->where('target_type', 'Event')
                  ->where('is_approved', true)
                  ->groupBy('target_id')
                  ->havingRaw('AVG(rating) >= ?', [$minRating]);
            });
        }

        // 📅 Upcoming filter
        if ($request->upcoming) {
            $query->where('start_date', '>', now());
        }

        // 📊 Sorting
        if ($sort = $request->sort) {
            match($sort) {
                'date_asc'   => $query->orderBy('start_date'),
                'date_desc'  => $query->orderByDesc('start_date'),
                'price_asc'  => $query->orderBy('ticket_price'),
                'price_desc' => $query->orderByDesc('ticket_price'),
                'rating'     => $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating'),
                default      => $query->orderBy('start_date'),
            };
        } else {
            $query->orderBy('start_date');
        }

        // 🏙️ Get distinct locations for dropdown
        $cities = Event::where('is_active', true)
                       ->whereNotNull('location')
                       ->distinct()
                       ->pluck('location');

        // 📌 Get distinct categories for dropdown
        $categories = Event::where('is_active', true)
                           ->whereNotNull('category')
                           ->distinct()
                           ->pluck('category');

        return view('events.index', [
            'events'     => $query->paginate(12)->withQueryString(),
            'cities'     => $cities,
            'categories' => $categories,
        ]);
    }

    public function show(int $id)
    {
        $event = Event::with('reviews.user')->findOrFail($id);
        $isFav = Auth::check() && Favourite::where('user_id', Auth::id())
                                            ->where('target_type', 'Event')
                                            ->where('target_id', $id)->exists();
        return view('events.show', [
            'event'      => $event,
            'reviews'    => $event->reviews()->with('user')->orderByDesc('created_at')->limit(10)->get(),
            'avg_rating' => $event->avg_rating,
            'is_fav'     => $isFav,
        ]);
    }
}