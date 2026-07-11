<?php

namespace App\Http\Controllers;

use App\Models\Attraction;
use App\Models\Category;
use App\Models\Favourite;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttractionController extends Controller
{
    // -------------------- FRONTEND --------------------
    public function index(Request $request)
    {
        $query = Attraction::where('is_active', true)->with('category');

        // 🔍 Search
        if ($search = $request->q) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // 🏷️ Category filter
        if ($cat = $request->category) {
            $query->where('category_id', $cat);
        }

        // 🌆 City filter
        if ($city = $request->city) {
            $query->where('city', 'like', "%{$city}%");
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
                  ->where('target_type', 'Attraction')
                  ->where('is_approved', true)
                  ->groupBy('target_id')
                  ->havingRaw('AVG(rating) >= ?', [$minRating]);
            });
        }

        // 📊 Sorting
        if ($sort = $request->sort) {
            match($sort) {
                'price_asc'  => $query->orderBy('ticket_price'),
                'price_desc' => $query->orderByDesc('ticket_price'),
                'popular'    => $query->orderByDesc('views'),
                'rating'     => $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating'),
                default      => $query->orderByDesc('is_featured')->orderByDesc('created_at'),
            };
        } else {
            $query->orderByDesc('is_featured')->orderByDesc('created_at');
        }

        // 🏙️ Get distinct cities for filter dropdown
        $cities = Attraction::where('is_active', true)
                            ->whereNotNull('city')
                            ->distinct()
                            ->pluck('city');

        return view('attractions.index', [
            'attractions' => $query->paginate(12)->withQueryString(),
            'categories'  => Category::where('is_active', true)->get(),
            'cities'      => $cities,
        ]);
    }

    public function show(int $id)
    {
        $attraction = Attraction::with('category', 'reviews.user')->findOrFail($id);
        $attraction->increment('views');

        $isFav = false;
        if (Auth::check()) {
            $isFav = Favourite::where('user_id', Auth::id())
                               ->where('target_type', 'Attraction')
                               ->where('target_id', $id)->exists();
        }

        return view('attractions.show', [
            'attraction' => $attraction,
            'reviews'    => $attraction->reviews()->with('user')->orderByDesc('created_at')->limit(10)->get(),
            'avg_rating' => $attraction->avg_rating,
            'is_fav'     => $isFav,
            'related'    => Attraction::where('category_id', $attraction->category_id)
                                      ->where('id', '!=', $id)
                                      ->limit(3)->get(),
        ]);
    }

    // -------------------- ADMIN CRUD --------------------
    public function adminIndex()
    {
        $attractions = Attraction::with('category')->orderByDesc('created_at')->get();
        return view('admin.attractions.index', compact('attractions'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.attractions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'category_id'   => 'nullable|exists:categories,id',
            'description'   => 'nullable|string',
            'history'       => 'nullable|string',
            'address'       => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:100',
            'ticket_price'  => 'nullable|numeric',
            'opening_hours' => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:50',
            'website'       => 'nullable|url|max:255',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_featured'   => 'nullable|boolean',
            'is_active'     => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('attractions', 'public');
            $validated['image_url'] = $path;
        }

        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active']   = $request->has('is_active');

        Attraction::create($validated);

        return redirect()->route('admin.attractions.index')
                         ->with('success', 'Attraction created successfully.');
    }

    public function edit($id)
    {
        $attraction = Attraction::findOrFail($id);
        $categories = Category::where('is_active', true)->get();
        return view('admin.attractions.edit', compact('attraction', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $attraction = Attraction::findOrFail($id);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'category_id'   => 'nullable|exists:categories,id',
            'description'   => 'nullable|string',
            'history'       => 'nullable|string',
            'address'       => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:100',
            'ticket_price'  => 'nullable|numeric',
            'opening_hours' => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:50',
            'website'       => 'nullable|url|max:255',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_featured'   => 'nullable|boolean',
            'is_active'     => 'nullable|boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($attraction->image_url) {
                Storage::disk('public')->delete($attraction->image_url);
            }
            $path = $request->file('image')->store('attractions', 'public');
            $validated['image_url'] = $path;
        } else {
            $validated['image_url'] = $attraction->image_url;
        }

        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active']   = $request->has('is_active');

        $attraction->update($validated);

        return redirect()->route('admin.attractions.index')
                         ->with('success', 'Attraction updated successfully.');
    }

    public function destroy($id)
    {
        $attraction = Attraction::findOrFail($id);
        if ($attraction->image_url) {
            Storage::disk('public')->delete($attraction->image_url);
        }
        $attraction->delete();

        return redirect()->route('admin.attractions.index')
                         ->with('success', 'Attraction deleted.');
    }
}