<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Favourite;
use App\Models\Guide;
use App\Models\Hotel;
use App\Models\Notification;
use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('guide');

        $bookings = Booking::where('user_id', $user->id)
                            ->orWhere(function ($q) use ($user) {
                                if ($user->role === 'Guide' && $user->guide) {
                                    $q->where('booking_type', 'Guide')->where('target_id', $user->guide->id);
                                }
                            })
                            ->orderByDesc('created_at')
                            ->get();

        $stats = [
            'total_bookings'    => $bookings->count(),
            'pending_bookings'  => $bookings->where('booking_status', 'Pending')->count(),
            'completed_bookings'=> $bookings->where('booking_status', 'Completed')->count(),
            'total_reviews'     => Review::where('user_id', $user->id)->count(),
            'heritage_points'   => $user->heritage_points,
        ];

        $notifications = Notification::where('user_id', $user->id)->orderByDesc('created_at')->limit(10)->get();
        $reviews       = Review::where('user_id', $user->id)->orderByDesc('created_at')->limit(10)->get();

        return view('dashboard.index', compact('bookings', 'stats', 'notifications', 'reviews') + ['guide_profile' => $user->guide]);
    }

    public function profile()
    {
        return view('dashboard.profile', ['user' => Auth::user()->load('guide')]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'first_name' => 'required|string|max:80',
            'last_name'  => 'required|string|max:80',
            'phone'      => 'nullable|string|max:30',
        ]);

        $data = $request->only('first_name', 'last_name', 'phone');

        if ($request->hasFile('avatar')) {
            $data['avatar_url'] = Helpers::saveUpload($request->file('avatar'), 'avatars');
        }

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Update guide profile if applicable
        if ($user->role === 'Guide' && $user->guide) {
            $user->guide->update($request->only('bio', 'languages', 'specializations', 'hourly_rate', 'daily_rate', 'experience'));
        }

        Helpers::logAction('update_profile', 'User', $user->id);
        return back()->with('success', 'Profile updated.');
    }

    // ─── Guide Management ────────────────────────────────────────────────────
    public function editGuide()
    {
        $user = Auth::user();
        $guide = $user->guide;
        if (!$guide) abort(404, 'Guide profile not found.');
        return view('dashboard.guides.form', compact('guide'));
    }

    public function updateGuide(Request $request)
    {
        $user = Auth::user();
        $guide = $user->guide;
        if (!$guide) abort(404);

        $data = $request->validate([
            'bio'             => 'nullable|string',
            'languages'       => 'nullable|string|max:255',
            'specializations' => 'nullable|string|max:255',
            'experience'      => 'nullable|integer|min:0',
            'hourly_rate'     => 'nullable|numeric|min:0',
            'daily_rate'      => 'nullable|numeric|min:0',
            'certification'   => 'nullable|string|max:255',
        ]);

        $guide->update($data);
        if ($guide->verification_status === 'Approved') {
            $guide->verification_status = 'Pending';
            $guide->save();
        }

        Helpers::logAction('update_guide', 'Guide', $guide->id);
        return redirect()->route('dashboard.index')->with('success', 'Guide profile updated and submitted for review.');
    }

    // ─── Hotels ──────────────────────────────────────────────────────────────
    public function myHotels()
    {
        $hotels = Hotel::where('user_id', Auth::id())->orderByDesc('created_at')->get();
        return view('dashboard.hotels.index', compact('hotels'));
    }

    public function createHotel()
    {
        return view('dashboard.hotels.form', ['hotel' => new Hotel()]);
    }

    public function storeHotel(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'address'          => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:100',
            'latitude'         => 'nullable|numeric',
            'longitude'        => 'nullable|numeric',
            'stars'            => 'nullable|integer|min:1|max:5',
            'price_per_night'  => 'required|numeric|min:0',
            'amenities'        => 'nullable|string',
            'check_in_time'    => 'nullable|string|max:50',
            'check_out_time'   => 'nullable|string|max:50',
            'website'          => 'nullable|url',
            'phone'            => 'nullable|string|max:50',
            'email'            => 'nullable|email',
        ]);

        if ($request->hasFile('image')) {
            $data['image_url'] = Helpers::saveUpload($request->file('image'), 'hotels');
        }

        $data['user_id'] = Auth::id();
        $data['is_featured'] = false;
        $data['is_active'] = false; // pending admin approval
        Hotel::create($data);

        Helpers::logAction('create_hotel', 'Hotel', null);
        return redirect()->route('dashboard.hotels.index')->with('success', 'Hotel added and submitted for review.');
    }

    public function editHotel($id)
    {
        $hotel = Hotel::where('user_id', Auth::id())->findOrFail($id);
        return view('dashboard.hotels.form', compact('hotel'));
    }

    public function updateHotel(Request $request, $id)
    {
        $hotel = Hotel::where('user_id', Auth::id())->findOrFail($id);

        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'address'          => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:100',
            'latitude'         => 'nullable|numeric',
            'longitude'        => 'nullable|numeric',
            'stars'            => 'nullable|integer|min:1|max:5',
            'price_per_night'  => 'required|numeric|min:0',
            'amenities'        => 'nullable|string',
            'check_in_time'    => 'nullable|string|max:50',
            'check_out_time'   => 'nullable|string|max:50',
            'website'          => 'nullable|url',
            'phone'            => 'nullable|string|max:50',
            'email'            => 'nullable|email',
        ]);

        if ($request->hasFile('image')) {
            $data['image_url'] = Helpers::saveUpload($request->file('image'), 'hotels');
        }

        // If the hotel was active, set it back to pending for re‑approval
        if ($hotel->is_active) {
            $data['is_active'] = false;
        }

        $hotel->update($data);
        Helpers::logAction('update_hotel', 'Hotel', $id);
        return redirect()->route('dashboard.hotels.index')->with('success', 'Hotel updated and submitted for review.');
    }

    // ─── Restaurants ──────────────────────────────────────────────────────────
    public function myRestaurants()
    {
        $restaurants = Restaurant::where('user_id', Auth::id())->orderByDesc('created_at')->get();
        return view('dashboard.restaurants.index', compact('restaurants'));
    }

    public function createRestaurant()
    {
        return view('dashboard.restaurants.form', ['restaurant' => new Restaurant()]);
    }

    public function storeRestaurant(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'address'       => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:100',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
            'cuisine_type'  => 'nullable|string|max:100',
            'opening_hours' => 'nullable|string|max:255',
            'avg_price'     => 'nullable|numeric|min:0',
            'website'       => 'nullable|url',
            'phone'         => 'nullable|string|max:50',
            'email'         => 'nullable|email',
        ]);

        if ($request->hasFile('image')) {
            $data['image_url'] = Helpers::saveUpload($request->file('image'), 'restaurants');
        }

        $data['user_id'] = Auth::id();
        $data['is_featured'] = false;
        $data['is_active'] = false; // pending admin approval
        Restaurant::create($data);

        Helpers::logAction('create_restaurant', 'Restaurant', null);
        return redirect()->route('dashboard.restaurants.index')->with('success', 'Restaurant added and submitted for review.');
    }

    public function editRestaurant($id)
    {
        $restaurant = Restaurant::where('user_id', Auth::id())->findOrFail($id);
        return view('dashboard.restaurants.form', compact('restaurant'));
    }

    public function updateRestaurant(Request $request, $id)
    {
        $restaurant = Restaurant::where('user_id', Auth::id())->findOrFail($id);

        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'address'       => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:100',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
            'cuisine_type'  => 'nullable|string|max:100',
            'opening_hours' => 'nullable|string|max:255',
            'avg_price'     => 'nullable|numeric|min:0',
            'website'       => 'nullable|url',
            'phone'         => 'nullable|string|max:50',
            'email'         => 'nullable|email',
        ]);

        if ($request->hasFile('image')) {
            $data['image_url'] = Helpers::saveUpload($request->file('image'), 'restaurants');
        }

        if ($restaurant->is_active) {
            $data['is_active'] = false;
        }

        $restaurant->update($data);
        Helpers::logAction('update_restaurant', 'Restaurant', $id);
        return redirect()->route('dashboard.restaurants.index')->with('success', 'Restaurant updated and submitted for review.');
    }

    // ─── Events ──────────────────────────────────────────────────────────────
    public function myEvents()
    {
        $events = Event::where('user_id', Auth::id())->orderByDesc('created_at')->get();
        return view('dashboard.events.index', compact('events'));
    }

    public function createEvent()
    {
        return view('dashboard.events.form', ['event' => new Event()]);
    }

    public function storeEvent(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'location'      => 'nullable|string|max:255',
            'address'       => 'nullable|string|max:255',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
            'ticket_price'  => 'nullable|numeric|min:0',
            'capacity'      => 'nullable|integer|min:0',
            'organizer'     => 'nullable|string|max:255',
            'category'      => 'nullable|string|max:100',
            'website'       => 'nullable|url',
        ]);

        if ($request->hasFile('image')) {
            $data['image_url'] = Helpers::saveUpload($request->file('image'), 'events');
        }

        $data['user_id'] = Auth::id();
        $data['is_featured'] = false;
        $data['is_active'] = false; // pending admin approval
        Event::create($data);

        Helpers::logAction('create_event', 'Event', null);
        return redirect()->route('dashboard.events.index')->with('success', 'Event added and submitted for review.');
    }

    public function editEvent($id)
    {
        $event = Event::where('user_id', Auth::id())->findOrFail($id);
        return view('dashboard.events.form', compact('event'));
    }

    public function updateEvent(Request $request, $id)
    {
        $event = Event::where('user_id', Auth::id())->findOrFail($id);

        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'location'      => 'nullable|string|max:255',
            'address'       => 'nullable|string|max:255',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
            'ticket_price'  => 'nullable|numeric|min:0',
            'capacity'      => 'nullable|integer|min:0',
            'organizer'     => 'nullable|string|max:255',
            'category'      => 'nullable|string|max:100',
            'website'       => 'nullable|url',
        ]);

        if ($request->hasFile('image')) {
            $data['image_url'] = Helpers::saveUpload($request->file('image'), 'events');
        }

        if ($event->is_active) {
            $data['is_active'] = false;
        }

        $event->update($data);
        Helpers::logAction('update_event', 'Event', $id);
        return redirect()->route('dashboard.events.index')->with('success', 'Event updated and submitted for review.');
    }

    // ─── Favourites & Notifications ─────────────────────────────────────────
    public function markRead(Request $request, int $id)
    {
        Notification::where('user_id', Auth::id())->where('id', $id)->update(['is_read' => true]);
        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead(Request $request)
    {
        Notification::where('user_id', Auth::id())->update(['is_read' => true]);
        return back()->with('success', 'All notifications marked as read.');
    }

    public function bookingDetail(int $id)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);
        return view('dashboard.booking-detail', compact('booking'));
    }

    public function toggleFavourite(Request $request, string $type, int $id)
    {
        $fav = Favourite::where('user_id', Auth::id())
                         ->where('target_type', $type)
                         ->where('target_id', $id)
                         ->first();
        if ($fav) {
            $fav->delete();
            $msg = 'Removed from favourites.';
        } else {
            Favourite::create(['user_id' => Auth::id(), 'target_type' => $type, 'target_id' => $id]);
            Helpers::awardPoints(Auth::user(), 5, 'Adding a favourite');
            $msg = 'Added to favourites! +5 Heritage Points.';
        }
        return back()->with('success', $msg);
    }
}