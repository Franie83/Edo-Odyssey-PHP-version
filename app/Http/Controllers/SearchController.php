<?php

namespace App\Http\Controllers;

use App\Models\Attraction;
use App\Models\Event;
use App\Models\Guide;
use App\Models\Hotel;
use App\Models\News;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function results(Request $request)
    {
        $q = $request->q;
        if (!$q) return redirect()->route('main.home');

        $like = "%{$q}%";

        return view('search.results', [
            'query'       => $q,
            'attractions' => Attraction::where('is_active', true)->where(fn($x) => $x->where('name', 'like', $like)->orWhere('description', 'like', $like)->orWhere('city', 'like', $like))->limit(6)->get(),
            'hotels'      => Hotel::where('is_active', true)->where(fn($x) => $x->where('name', 'like', $like)->orWhere('city', 'like', $like))->limit(6)->get(),
            'restaurants' => Restaurant::where('is_active', true)->where(fn($x) => $x->where('name', 'like', $like)->orWhere('cuisine_type', 'like', $like))->limit(6)->get(),
            'events'      => Event::where('is_active', true)->where(fn($x) => $x->where('name', 'like', $like)->orWhere('location', 'like', $like))->limit(6)->get(),
            'news'        => News::where('is_published', true)->where(fn($x) => $x->where('title', 'like', $like)->orWhere('content', 'like', $like))->limit(6)->get(),
            'guides'      => Guide::where('verification_status', 'Approved')->whereHas('user', fn($u) => $u->where('first_name', 'like', $like)->orWhere('last_name', 'like', $like))->with('user')->limit(6)->get(),
        ]);
    }
}
