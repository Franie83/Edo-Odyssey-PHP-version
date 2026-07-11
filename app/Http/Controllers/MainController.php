<?php

namespace App\Http\Controllers;

use App\Models\Attraction;
use App\Models\Category;
use App\Models\Event;
use App\Models\Faq;
use App\Models\Guide;
use App\Models\Hotel;
use App\Models\News;
use App\Models\Partner;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function home()
    {
        return view('home', [
            'categories'          => Category::where('is_active', true)->orderBy('sort_order')->get(),
            'featured_attractions'=> Attraction::where('is_featured', true)->where('is_active', true)->with('category')->limit(6)->get(),
            'featured_guides'     => Guide::where('is_featured', true)->where('verification_status', 'Approved')->with('user')->limit(4)->get(),
            'featured_hotels'     => Hotel::where('is_featured', true)->where('is_active', true)->limit(3)->get(),
            'upcoming_events'     => Event::where('is_active', true)->where('start_date', '>', now())->orderBy('start_date')->limit(3)->get(),
            'recent_news'         => News::where('is_published', true)->orderByDesc('created_at')->limit(4)->get(),
            'partners'            => Partner::where('is_active', true)->orderBy('sort_order')->get(),
            'total_attractions'   => Attraction::where('is_active', true)->count(),
            'total_guides'        => Guide::where('verification_status', 'Approved')->count(),
            'total_hotels'        => Hotel::where('is_active', true)->count(),
        ]);
    }

    public function about()
    {
        return view('main.about');
    }

    public function contact()
    {
        return view('main.contact');
    }

    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:120',
            'email'   => 'required|email',
            'subject' => 'required|string|max:200',
            'message' => 'required|string',
        ]);
        // In production, send email. For now just flash success.
        return back()->with('success', 'Thank you! Your message has been received and will be reviewed shortly.');
    }

    public function faq()
    {
        $faqs = Faq::where('is_active', true)->orderBy('sort_order')->get()->groupBy('category');
        return view('main.faq', compact('faqs'));
    }

    public function privacy()
    {
        return view('main.privacy');
    }

    public function terms()
    {
        return view('main.terms');
    }
}
