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
use Illuminate\Support\Facades\Log;

class MainController extends Controller
{
    public function home()
    {
        // 🔧 ISOLATION TEST: Return JSON to verify Laravel is working
        return response()->json([
            'status' => 'OK',
            'message' => 'Laravel is working on Render!',
            'environment' => app()->environment(),
            'database' => config('database.default'),
            'timestamp' => now()->toDateTimeString()
        ]);

        /* // Restore this when testing is complete
        // Wrap each query in try/catch to isolate issues
        try {
            $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        } catch (\Exception $e) {
            Log::error('Categories query failed: ' . $e->getMessage());
            $categories = collect();
        }

        try {
            $featured_attractions = Attraction::where('is_featured', true)
                ->where('is_active', true)
                ->with('category')
                ->limit(6)
                ->get();
        } catch (\Exception $e) {
            Log::error('Featured attractions query failed: ' . $e->getMessage());
            $featured_attractions = collect();
        }

        try {
            $featured_guides = Guide::where('is_featured', true)
                ->where('verification_status', 'Approved')
                ->with('user')
                ->limit(4)
                ->get();
        } catch (\Exception $e) {
            Log::error('Featured guides query failed: ' . $e->getMessage());
            $featured_guides = collect();
        }

        try {
            $featured_hotels = Hotel::where('is_featured', true)
                ->where('is_active', true)
                ->limit(3)
                ->get();
        } catch (\Exception $e) {
            Log::error('Featured hotels query failed: ' . $e->getMessage());
            $featured_hotels = collect();
        }

        try {
            $upcoming_events = Event::where('is_active', true)
                ->where('start_date', '>', now())
                ->orderBy('start_date')
                ->limit(3)
                ->get();
        } catch (\Exception $e) {
            Log::error('Upcoming events query failed: ' . $e->getMessage());
            $upcoming_events = collect();
        }

        try {
            $recent_news = News::where('is_published', true)
                ->orderByDesc('created_at')
                ->limit(4)
                ->get();
        } catch (\Exception $e) {
            Log::error('Recent news query failed: ' . $e->getMessage());
            $recent_news = collect();
        }

        try {
            $partners = Partner::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        } catch (\Exception $e) {
            Log::error('Partners query failed: ' . $e->getMessage());
            $partners = collect();
        }

        // Count queries
        try {
            $total_attractions = Attraction::where('is_active', true)->count();
        } catch (\Exception $e) {
            Log::error('Total attractions count failed: ' . $e->getMessage());
            $total_attractions = 0;
        }

        try {
            $total_guides = Guide::where('verification_status', 'Approved')->count();
        } catch (\Exception $e) {
            Log::error('Total guides count failed: ' . $e->getMessage());
            $total_guides = 0;
        }

        try {
            $total_hotels = Hotel::where('is_active', true)->count();
        } catch (\Exception $e) {
            Log::error('Total hotels count failed: ' . $e->getMessage());
            $total_hotels = 0;
        }

        return view('home', [
            'categories'          => $categories,
            'featured_attractions'=> $featured_attractions,
            'featured_guides'     => $featured_guides,
            'featured_hotels'     => $featured_hotels,
            'upcoming_events'     => $upcoming_events,
            'recent_news'         => $recent_news,
            'partners'            => $partners,
            'total_attractions'   => $total_attractions,
            'total_guides'        => $total_guides,
            'total_hotels'        => $total_hotels,
        ]);
        */
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
        return back()->with('success', 'Thank you! Your message has been received and will be reviewed shortly.');
    }

    public function faq()
    {
        try {
            $faqs = Faq::where('is_active', true)->orderBy('sort_order')->get()->groupBy('category');
        } catch (\Exception $e) {
            Log::error('FAQs query failed: ' . $e->getMessage());
            $faqs = collect();
        }
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