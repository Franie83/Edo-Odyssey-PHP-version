<?php

namespace App\Http\Controllers;

use App\Models\Attraction;
use App\Models\Event;
use App\Models\Guide;
use App\Models\Hotel;
use App\Models\News;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function health(): JsonResponse
    {
        return response()->json([
            'status'    => 'ok',
            'service'   => 'Edo Odyssey API (Laravel)',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function attractions(Request $request): JsonResponse
    {
        $query = Attraction::where('is_active', true)->with('category');
        if ($featured = $request->boolean('featured')) $query->where('is_featured', true);
        if ($limit = $request->integer('limit', 20)) $query->limit($limit);
        return response()->json(['data' => $query->get()]);
    }

    public function guides(Request $request): JsonResponse
    {
        $query = Guide::where('verification_status', 'Approved')->with('user');
        if ($limit = $request->integer('limit', 20)) $query->limit($limit);
        return response()->json(['data' => $query->get()]);
    }

    public function hotels(Request $request): JsonResponse
    {
        $query = Hotel::where('is_active', true);
        if ($limit = $request->integer('limit', 20)) $query->limit($limit);
        return response()->json(['data' => $query->get()]);
    }

    public function events(Request $request): JsonResponse
    {
        $query = Event::where('is_active', true)->orderBy('start_date');
        if ($upcoming = $request->boolean('upcoming')) $query->where('start_date', '>', now());
        if ($limit = $request->integer('limit', 20)) $query->limit($limit);
        return response()->json(['data' => $query->get()]);
    }

    public function search(Request $request): JsonResponse
    {
        $q = $request->q;
        if (!$q) return response()->json(['error' => 'q parameter required'], 400);

        $like = "%{$q}%";
        return response()->json([
            'attractions' => Attraction::where('is_active', true)->where(fn($x) => $x->where('name', 'like', $like)->orWhere('city', 'like', $like))->limit(5)->get(['id','name','city','image_url']),
            'hotels'      => Hotel::where('is_active', true)->where('name', 'like', $like)->limit(5)->get(['id','name','city','price_per_night']),
            'events'      => Event::where('is_active', true)->where('name', 'like', $like)->limit(5)->get(['id','name','location','start_date']),
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'attractions' => Attraction::where('is_active', true)->count(),
            'guides'      => Guide::where('verification_status', 'Approved')->count(),
            'hotels'      => Hotel::where('is_active', true)->count(),
            'restaurants' => Restaurant::where('is_active', true)->count(),
            'events'      => Event::where('is_active', true)->count(),
            'news'        => News::where('is_published', true)->count(),
        ]);
    }
}
