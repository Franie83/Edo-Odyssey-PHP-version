<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Advertisement;
use App\Models\AuditLog;
use App\Models\Attraction;
use App\Models\Booking;
use App\Models\Category;
use App\Models\CmsSetting;
use App\Models\Event;
use App\Models\Faq;
use App\Models\Guide;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\News;
use App\Models\Notification;
use App\Models\Partner;
use App\Models\QrCode;
use App\Models\Restaurant;
use App\Models\RestaurantMenu;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminController extends Controller
{
    // ─── Dashboard ────────────────────────────────────────────────────────────
    public function dashboard()
    {
        $stats = [
            'total_users'       => User::count(),
            'total_attractions' => Attraction::count(),
            'total_bookings'    => Booking::count(),
            'total_guides'      => Guide::count(),
            'total_hotels'      => Hotel::count(),
            'total_restaurants' => Restaurant::count(),
            'total_events'      => Event::count(),
            'total_news'        => News::count(),
            'pending_bookings'  => Booking::where('booking_status', 'Pending')->count(),
            'pending_guides'    => Guide::where('verification_status', 'Pending')->count(),
            'pending_hotels'    => Hotel::where('is_active', false)->count(),
            'pending_restaurants' => Restaurant::where('is_active', false)->count(),
            'pending_events'    => Event::where('is_active', false)->count(),
        ];

        $recent_pending = [
            'guides'       => Guide::where('verification_status', 'Pending')->with('user')->orderByDesc('created_at')->limit(5)->get(),
            'hotels'       => Hotel::where('is_active', false)->with('user')->orderByDesc('created_at')->limit(5)->get(),
            'restaurants'  => Restaurant::where('is_active', false)->with('user')->orderByDesc('created_at')->limit(5)->get(),
            'events'       => Event::where('is_active', false)->with('user')->orderByDesc('created_at')->limit(5)->get(),
        ];

        $recent_bookings = Booking::with('user')->orderByDesc('created_at')->limit(8)->get();
        $recent_users    = User::orderByDesc('created_at')->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_pending', 'recent_bookings', 'recent_users'));
    }

    // ─── Users ────────────────────────────────────────────────────────────────
    public function users(Request $request)
    {
        $query = User::query();
        if ($s = $request->q)    $query->where(fn($q) => $q->where('first_name', 'like', "%{$s}%")->orWhere('last_name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        if ($r = $request->role) $query->where('role', $r);
        return view('admin.users.index', ['users' => $query->orderByDesc('created_at')->paginate(20)->withQueryString()]);
    }

    public function userCreate()  { return view('admin.users.form', ['user' => new User()]); }

    public function userStore(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required', 'last_name' => 'required',
            'email' => 'required|email|unique:users', 'password' => 'required|min:6',
            'role' => 'required', 'phone' => 'nullable',
        ]);
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        Helpers::logAction('admin_create_user', 'User', $user->id);
        return redirect()->route('admin.users')->with('success', 'User created.');
    }

    public function userEdit(int $id) { return view('admin.users.form', ['user' => User::findOrFail($id)]); }

    public function userUpdate(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'first_name' => 'required', 'last_name' => 'required',
            'email' => "required|email|unique:users,email,{$id}",
            'role' => 'required', 'status' => 'required', 'phone' => 'nullable',
        ]);
        if ($request->filled('password')) $data['password'] = Hash::make($request->password);
        $user->update($data);
        Helpers::logAction('admin_update_user', 'User', $id);
        return redirect()->route('admin.users')->with('success', 'User updated.');
    }

    public function userDelete(int $id)
    {
        User::findOrFail($id)->delete();
        Helpers::logAction('admin_delete_user', 'User', $id);
        return back()->with('success', 'User deleted.');
    }

    // ─── Attractions ──────────────────────────────────────────────────────────
    public function attractions(Request $request)
    {
        $query = Attraction::with('category', 'user');
        if ($s = $request->q) $query->where('name', 'like', "%{$s}%");
        return view('admin.attractions.index', [
            'attractions' => $query->orderByDesc('created_at')->paginate(20)->withQueryString(),
        ]);
    }

    public function attractionCreate()
    {
        return view('admin.attractions.form', [
            'attraction' => new Attraction(),
            'categories' => Category::all(),
            'users'      => User::orderBy('first_name')->get(),
        ]);
    }

    public function attractionStore(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'history'       => 'nullable|string',
            'address'       => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:100',
            'category_id'   => 'nullable|exists:categories,id',
            'ticket_price'  => 'nullable|numeric',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
            'opening_hours' => 'nullable|string',
            'website'       => 'nullable|url',
            'phone'         => 'nullable|string|max:50',
            'user_id'       => 'required|exists:users,id',
            'image_url'     => 'nullable|url',
        ]);
        
        $data['is_featured'] = $request->has('is_featured');
        $data['is_active']   = $request->has('is_active');
        
        // Handle image - prioritize file upload over URL
        try {
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $uploadedPath = Helpers::saveUpload($request->file('image'), 'attractions');
                if ($uploadedPath) {
                    $data['image_url'] = $uploadedPath;
                    Log::info('Image uploaded successfully: ' . $uploadedPath);
                }
            } elseif ($request->filled('image_url')) {
                $data['image_url'] = $request->input('image_url');
                Log::info('Image URL set: ' . $data['image_url']);
            }
        } catch (\Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage());
        }
        
        $attr = Attraction::create($data);
        QrCode::generateForEntity('Attraction', $attr->id);
        Helpers::logAction('admin_create_attraction', 'Attraction', $attr->id);
        return redirect()->route('admin.attractions')->with('success', 'Attraction created successfully.');
    }

    public function attractionEdit(int $id)
    {
        return view('admin.attractions.form', [
            'attraction' => Attraction::findOrFail($id),
            'categories' => Category::all(),
            'users'      => User::orderBy('first_name')->get(),
        ]);
    }

    public function attractionUpdate(Request $request, int $id)
    {
        $attr = Attraction::findOrFail($id);
        
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'history'       => 'nullable|string',
            'address'       => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:100',
            'category_id'   => 'nullable|exists:categories,id',
            'ticket_price'  => 'nullable|numeric',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
            'opening_hours' => 'nullable|string',
            'website'       => 'nullable|url',
            'phone'         => 'nullable|string|max:50',
            'user_id'       => 'required|exists:users,id',
            'image_url'     => 'nullable|url',
        ]);
        
        $data['is_featured'] = $request->has('is_featured');
        $data['is_active']   = $request->has('is_active');
        
        // Handle image - prioritize file upload over URL
        try {
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $uploadedPath = Helpers::saveUpload($request->file('image'), 'attractions');
                if ($uploadedPath) {
                    $data['image_url'] = $uploadedPath;
                    Log::info('Image uploaded successfully: ' . $uploadedPath);
                }
            } elseif ($request->filled('image_url')) {
                $data['image_url'] = $request->input('image_url');
                Log::info('Image URL set: ' . $data['image_url']);
            }
            // If neither, keep existing image_url (not overwritten)
        } catch (\Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage());
        }
        
        $attr->update($data);
        Helpers::logAction('admin_update_attraction', 'Attraction', $id);
        return redirect()->route('admin.attractions')->with('success', 'Attraction updated successfully.');
    }

    public function attractionDelete(int $id)
    {
        Attraction::findOrFail($id)->delete();
        Helpers::logAction('admin_delete_attraction', 'Attraction', $id);
        return back()->with('success', 'Attraction deleted.');
    }

    public function attractionRegenQr(int $id)
    {
        $attr = Attraction::findOrFail($id);
        QrCode::generateForEntity('Attraction', $id);
        Helpers::logAction('admin_regen_qr', 'Attraction', $id);
        return back()->with('success', 'QR code regenerated.');
    }

    public function generateAllQr()
    {
        Artisan::call('generate:qr');
        return back()->with('success', 'All QR codes regenerated successfully.');
    }

    // ─── Guides ───────────────────────────────────────────────────────────────
    public function guides(Request $request)
    {
        $query = Guide::with('user');
        if ($s = $request->q) $query->whereHas('user', fn($u) => $u->where('first_name', 'like', "%{$s}%")->orWhere('last_name', 'like', "%{$s}%"));
        if ($v = $request->status) $query->where('verification_status', $v);
        return view('admin.guides.index', ['guides' => $query->orderByDesc('created_at')->paginate(20)->withQueryString()]);
    }

    public function guideCreate()
    {
        return view('admin.guides.form', [
            'guide' => new Guide(),
            'users' => User::orderBy('first_name')->get(),
        ]);
    }

    public function guideStore(Request $request)
    {
        $data = $request->validate([
            'bio'             => 'nullable|string',
            'languages'       => 'nullable|string',
            'specializations' => 'nullable|string',
            'experience'      => 'nullable|integer',
            'hourly_rate'     => 'nullable|numeric',
            'daily_rate'      => 'nullable|numeric',
            'certification'   => 'nullable|string',
            'user_id'         => 'required|exists:users,id',
        ]);
        $data['is_featured']   = $request->has('is_featured');
        $data['is_available']  = $request->has('is_available');
        $guide = Guide::create($data);
        Helpers::logAction('admin_create_guide', 'Guide', $guide->id);
        return redirect()->route('admin.guides')->with('success', 'Guide created.');
    }

    public function guideEdit(int $id)
    {
        return view('admin.guides.form', [
            'guide' => Guide::with('user')->findOrFail($id),
            'users' => User::orderBy('first_name')->get(),
        ]);
    }

    public function guideUpdate(Request $request, int $id)
    {
        $guide = Guide::findOrFail($id);
        $data = $request->validate([
            'bio'             => 'nullable|string',
            'languages'       => 'nullable|string',
            'specializations' => 'nullable|string',
            'experience'      => 'nullable|integer',
            'hourly_rate'     => 'nullable|numeric',
            'daily_rate'      => 'nullable|numeric',
            'certification'   => 'nullable|string',
            'user_id'         => 'required|exists:users,id',
        ]);
        $data['is_featured']  = $request->has('is_featured');
        $data['is_available'] = $request->has('is_available');
        $guide->update($data);
        Helpers::logAction('admin_update_guide', 'Guide', $id);
        return redirect()->route('admin.guides')->with('success', 'Guide updated.');
    }

    public function guideVerify(Request $request, int $id)
    {
        $guide  = Guide::with('user')->findOrFail($id);
        $status = $request->input('status', 'Approved');
        $guide->update(['verification_status' => $status]);
        if ($guide->user) {
            Notification::create([
                'user_id' => $guide->user_id,
                'title'   => "Guide Verification: {$status}",
                'message' => "Your guide profile has been {$status}.",
                'type'    => 'info',
            ]);
        }
        Helpers::logAction('admin_verify_guide', 'Guide', $id, "Status: {$status}");
        return back()->with('success', "Guide {$status}.");
    }

    public function guideDelete(int $id)
    {
        Guide::findOrFail($id)->delete();
        return back()->with('success', 'Guide deleted.');
    }

    // ─── Hotels ───────────────────────────────────────────────────────────────
    public function hotels(Request $request)
    {
        $query = Hotel::with('user');
        if ($s = $request->q) $query->where('name', 'like', "%{$s}%");
        if ($status = $request->status) {
            $query->where('is_active', $status === 'active' ? true : false);
        }
        return view('admin.hotels.index', ['hotels' => $query->orderByDesc('created_at')->paginate(20)->withQueryString()]);
    }

    public function hotelCreate()
    {
        return view('admin.hotels.form', [
            'hotel' => new Hotel(),
            'users' => User::orderBy('first_name')->get(),
        ]);
    }

    public function hotelStore(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'address'          => 'nullable|string',
            'city'             => 'nullable|string|max:100',
            'latitude'         => 'nullable|numeric',
            'longitude'        => 'nullable|numeric',
            'stars'            => 'nullable|integer|min:1|max:5',
            'price_per_night'  => 'nullable|numeric',
            'amenities'        => 'nullable|array',
            'check_in_time'    => 'nullable|string',
            'check_out_time'   => 'nullable|string',
            'website'          => 'nullable|url',
            'phone'            => 'nullable|string|max:50',
            'email'            => 'nullable|email',
            'user_id'          => 'required|exists:users,id',
        ]);
        $data['is_featured'] = $request->has('is_featured');
        $data['is_active']   = $request->has('is_active');
        if ($request->hasFile('image')) $data['image_url'] = Helpers::saveUpload($request->file('image'), 'hotels');
        $hotel = Hotel::create($data);
        Helpers::logAction('admin_create_hotel', 'Hotel', $hotel->id);
        return redirect()->route('admin.hotels')->with('success', 'Hotel created.');
    }

    public function hotelEdit(int $id)
    {
        return view('admin.hotels.form', [
            'hotel' => Hotel::findOrFail($id),
            'users' => User::orderBy('first_name')->get(),
        ]);
    }

    public function hotelUpdate(Request $request, int $id)
    {
        $hotel = Hotel::findOrFail($id);
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'address'          => 'nullable|string',
            'city'             => 'nullable|string|max:100',
            'latitude'         => 'nullable|numeric',
            'longitude'        => 'nullable|numeric',
            'stars'            => 'nullable|integer|min:1|max:5',
            'price_per_night'  => 'nullable|numeric',
            'amenities'        => 'nullable|array',
            'check_in_time'    => 'nullable|string',
            'check_out_time'   => 'nullable|string',
            'website'          => 'nullable|url',
            'phone'            => 'nullable|string|max:50',
            'email'            => 'nullable|email',
            'user_id'          => 'required|exists:users,id',
        ]);
        $data['is_featured'] = $request->has('is_featured');
        $data['is_active']   = $request->has('is_active');
        if ($request->hasFile('image')) $data['image_url'] = Helpers::saveUpload($request->file('image'), 'hotels');
        $hotel->update($data);
        Helpers::logAction('admin_update_hotel', 'Hotel', $id);
        return redirect()->route('admin.hotels')->with('success', 'Hotel updated.');
    }

    public function hotelDelete(int $id)
    {
        Hotel::findOrFail($id)->delete();
        return back()->with('success', 'Hotel deleted.');
    }

    public function approveHotel(int $id)
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->is_active = true;
        $hotel->save();

        Notification::create([
            'user_id' => $hotel->user_id,
            'title'   => 'Hotel Approved',
            'message' => 'Your hotel "'.$hotel->name.'" has been approved and is now live on the platform.',
            'type'    => 'success',
        ]);

        Helpers::logAction('approve_hotel', 'Hotel', $id);
        return back()->with('success', 'Hotel approved and is now active.');
    }

    // ─── Restaurants ──────────────────────────────────────────────────────────
    public function restaurants(Request $request)
    {
        $query = Restaurant::with('user');
        if ($s = $request->q) $query->where('name', 'like', "%{$s}%");
        if ($status = $request->status) {
            $query->where('is_active', $status === 'active' ? true : false);
        }
        return view('admin.restaurants.index', ['restaurants' => $query->orderByDesc('created_at')->paginate(20)->withQueryString()]);
    }

    public function restaurantCreate()
    {
        return view('admin.restaurants.form', [
            'restaurant' => new Restaurant(),
            'users'      => User::orderBy('first_name')->get(),
        ]);
    }

    public function restaurantStore(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'address'      => 'nullable|string',
            'city'         => 'nullable|string|max:100',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            'cuisine_type' => 'nullable|string|max:100',
            'opening_hours'=> 'nullable|string',
            'avg_price'    => 'nullable|numeric',
            'website'      => 'nullable|url',
            'phone'        => 'nullable|string|max:50',
            'email'        => 'nullable|email',
            'user_id'      => 'required|exists:users,id',
        ]);
        $data['is_featured'] = $request->has('is_featured');
        $data['is_active']   = $request->has('is_active');
        if ($request->hasFile('image')) $data['image_url'] = Helpers::saveUpload($request->file('image'), 'restaurants');
        $rest = Restaurant::create($data);
        Helpers::logAction('admin_create_restaurant', 'Restaurant', $rest->id);
        return redirect()->route('admin.restaurants')->with('success', 'Restaurant created.');
    }

    public function restaurantEdit(int $id)
    {
        return view('admin.restaurants.form', [
            'restaurant' => Restaurant::findOrFail($id),
            'users'      => User::orderBy('first_name')->get(),
        ]);
    }

    public function restaurantUpdate(Request $request, int $id)
    {
        $rest = Restaurant::findOrFail($id);
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'address'      => 'nullable|string',
            'city'         => 'nullable|string|max:100',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            'cuisine_type' => 'nullable|string|max:100',
            'opening_hours'=> 'nullable|string',
            'avg_price'    => 'nullable|numeric',
            'website'      => 'nullable|url',
            'phone'        => 'nullable|string|max:50',
            'email'        => 'nullable|email',
            'user_id'      => 'required|exists:users,id',
        ]);
        $data['is_featured'] = $request->has('is_featured');
        $data['is_active']   = $request->has('is_active');
        if ($request->hasFile('image')) $data['image_url'] = Helpers::saveUpload($request->file('image'), 'restaurants');
        $rest->update($data);
        Helpers::logAction('admin_update_restaurant', 'Restaurant', $id);
        return redirect()->route('admin.restaurants')->with('success', 'Restaurant updated.');
    }

    public function restaurantDelete(int $id)
    {
        Restaurant::findOrFail($id)->delete();
        return back()->with('success', 'Restaurant deleted.');
    }

    public function approveRestaurant(int $id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $restaurant->is_active = true;
        $restaurant->save();

        Notification::create([
            'user_id' => $restaurant->user_id,
            'title'   => 'Restaurant Approved',
            'message' => 'Your restaurant "'.$restaurant->name.'" has been approved and is now live on the platform.',
            'type'    => 'success',
        ]);

        Helpers::logAction('approve_restaurant', 'Restaurant', $id);
        return back()->with('success', 'Restaurant approved and is now active.');
    }

    // ─── Events ───────────────────────────────────────────────────────────────
    public function events(Request $request)
    {
        $query = Event::with('user');
        if ($s = $request->q) $query->where('name', 'like', "%{$s}%");
        if ($status = $request->status) {
            $query->where('is_active', $status === 'active' ? true : false);
        }
        return view('admin.events.index', ['events' => $query->orderByDesc('created_at')->paginate(20)->withQueryString()]);
    }

    public function eventCreate()
    {
        return view('admin.events.form', [
            'event' => new Event(),
            'users' => User::orderBy('first_name')->get(),
        ]);
    }

    public function eventStore(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'location'     => 'nullable|string|max:255',
            'address'      => 'nullable|string|max:255',
            'start_date'   => 'nullable|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'ticket_price' => 'nullable|numeric',
            'capacity'     => 'nullable|integer',
            'organizer'    => 'nullable|string|max:255',
            'category'     => 'nullable|string|max:100',
            'website'      => 'nullable|url',
            'user_id'      => 'required|exists:users,id',
        ]);
        $data['is_featured'] = $request->has('is_featured');
        $data['is_active']   = $request->has('is_active');
        if ($request->hasFile('image')) $data['image_url'] = Helpers::saveUpload($request->file('image'), 'events');
        $event = Event::create($data);
        Helpers::logAction('admin_create_event', 'Event', $event->id);
        return redirect()->route('admin.events')->with('success', 'Event created.');
    }

    public function eventEdit(int $id)
    {
        return view('admin.events.form', [
            'event' => Event::findOrFail($id),
            'users' => User::orderBy('first_name')->get(),
        ]);
    }

    public function eventUpdate(Request $request, int $id)
    {
        $event = Event::findOrFail($id);
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'location'     => 'nullable|string|max:255',
            'address'      => 'nullable|string|max:255',
            'start_date'   => 'nullable|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'ticket_price' => 'nullable|numeric',
            'capacity'     => 'nullable|integer',
            'organizer'    => 'nullable|string|max:255',
            'category'     => 'nullable|string|max:100',
            'website'      => 'nullable|url',
            'user_id'      => 'required|exists:users,id',
        ]);
        $data['is_featured'] = $request->has('is_featured');
        $data['is_active']   = $request->has('is_active');
        if ($request->hasFile('image')) $data['image_url'] = Helpers::saveUpload($request->file('image'), 'events');
        $event->update($data);
        return redirect()->route('admin.events')->with('success', 'Event updated.');
    }

    public function eventDelete(int $id) { Event::findOrFail($id)->delete(); return back()->with('success', 'Event deleted.'); }

    public function approveEvent(int $id)
    {
        $event = Event::findOrFail($id);
        $event->is_active = true;
        $event->save();

        Notification::create([
            'user_id' => $event->user_id,
            'title'   => 'Event Approved',
            'message' => 'Your event "'.$event->name.'" has been approved and is now live on the platform.',
            'type'    => 'success',
        ]);

        Helpers::logAction('approve_event', 'Event', $id);
        return back()->with('success', 'Event approved and is now active.');
    }

    // ─── Bookings ─────────────────────────────────────────────────────────────
    public function bookings(Request $request)
    {
        $query = Booking::with('user');
        if ($s = $request->status) $query->where('booking_status', $s);
        if ($t = $request->type)   $query->where('booking_type', $t);
        return view('admin.bookings.index', ['bookings' => $query->orderByDesc('created_at')->paginate(25)->withQueryString()]);
    }

    public function bookingApprove(Request $request, int $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update([
            'booking_status' => 'AdminApproved',
            'admin_comment'  => $request->admin_comment,
        ]);
        $booking->logStatus('AdminApproved', $request->admin_comment);

        Notification::create([
            'user_id' => $booking->user_id,
            'title'   => 'Booking Approved by Admin',
            'message' => "Your booking {$booking->reference_code} has been approved by admin. Waiting for vendor confirmation.",
            'type'    => 'booking',
        ]);

        Helpers::logAction('admin_approve_booking', 'Booking', $id);
        return back()->with('success', 'Booking approved, waiting for vendor confirmation.');
    }

    public function bookingReject(Request $request, int $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update([
            'booking_status' => 'AdminRejected',
            'admin_comment'  => $request->admin_comment,
        ]);
        $booking->logStatus('AdminRejected', $request->admin_comment);

        Notification::create([
            'user_id' => $booking->user_id,
            'title'   => 'Booking Rejected by Admin',
            'message' => "Your booking {$booking->reference_code} has been rejected by admin. Reason: " . $request->admin_comment,
            'type'    => 'booking',
        ]);

        Helpers::logAction('admin_reject_booking', 'Booking', $id);
        return back()->with('success', 'Booking rejected.');
    }

    public function bookingDetail(int $id)
    {
        $booking = Booking::with('histories.user', 'user')->findOrFail($id);
        return view('admin.bookings.detail', compact('booking'));
    }

    // ─── Reviews ──────────────────────────────────────────────────────────────
    public function reviews(Request $request)
    {
        $query = Review::with('user');
        if ($t = $request->type) $query->where('target_type', $t);
        if ($status = $request->status) {
            $query->where('is_approved', $status === 'approved' ? true : false);
        }
        return view('admin.reviews.index', ['reviews' => $query->orderByDesc('created_at')->paginate(25)->withQueryString()]);
    }

    public function reviewDelete(int $id) { Review::findOrFail($id)->delete(); return back()->with('success', 'Review deleted.'); }

    public function approveReview(int $id)
    {
        $review = Review::findOrFail($id);
        $review->is_approved = true;
        $review->save();

        Helpers::logAction('approve_review', 'Review', $id);
        return back()->with('success', 'Review approved and is now visible.');
    }

    // ─── Analytics ────────────────────────────────────────────────────────────
    public function analytics(Request $request)
    {
        $from = $request->input('from') ? Carbon::parse($request->from)->startOfDay() : null;
        $to   = $request->input('to')   ? Carbon::parse($request->to)->endOfDay() : null;

        $bookingQuery = Booking::query();
        if ($from) $bookingQuery->where('created_at', '>=', $from);
        if ($to)   $bookingQuery->where('created_at', '<=', $to);

        $completedQuery = clone $bookingQuery;
        $completedQuery->where('booking_status', 'Completed');

        $userQuery = User::query();
        if ($from) $userQuery->where('created_at', '>=', $from);
        if ($to)   $userQuery->where('created_at', '<=', $to);

        // Revenue over time (daily aggregated)
        $revenueQuery = Booking::where('booking_status', 'Completed');
        if ($from) $revenueQuery->where('created_at', '>=', $from);
        if ($to)   $revenueQuery->where('created_at', '<=', $to);
        if (!$from && !$to) {
            $revenueQuery->where('created_at', '>=', Carbon::now()->subDays(30));
        }

        $revenueData = $revenueQuery->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
                                    ->groupBy('date')
                                    ->orderBy('date')
                                    ->pluck('total', 'date');
        $revenue_labels = $revenueData->keys()->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray();
        $revenue_values = $revenueData->values()->toArray();

        return view('admin.analytics', [
            'stats' => [
                'users'       => $userQuery->count(),
                'attractions' => Attraction::count(),
                'hotels'      => Hotel::count(),
                'restaurants' => Restaurant::count(),
                'events'      => Event::count(),
                'news'        => News::count(),
                'guides'      => Guide::count(),
                'bookings'    => $bookingQuery->count(),
                'reviews'     => Review::count(),
                'revenue'     => $completedQuery->sum('total_price') ?? 0,
            ],
            'bookings_by_status' => (clone $bookingQuery)->selectRaw('booking_status, count(*) as total')->groupBy('booking_status')->pluck('total', 'booking_status'),
            'bookings_by_type'   => (clone $bookingQuery)->selectRaw('booking_type, count(*) as total')->groupBy('booking_type')->pluck('total', 'booking_type'),
            'users_by_role'      => $userQuery->selectRaw('role, count(*) as total')->groupBy('role')->pluck('total', 'role'),
            'revenue_labels'     => $revenue_labels,
            'revenue_data'       => $revenue_values,
        ]);
    }

    public function analyticsExport(Request $request)
    {
        $type = $request->input('type', 'bookings');
        $from = $request->input('from') ? Carbon::parse($request->from)->startOfDay() : null;
        $to   = $request->input('to')   ? Carbon::parse($request->to)->endOfDay() : null;

        $query = match($type) {
            'bookings' => Booking::with('user')->when($from, fn($q) => $q->where('created_at', '>=', $from))
                                                ->when($to, fn($q) => $q->where('created_at', '<=', $to)),
            'users'    => User::when($from, fn($q) => $q->where('created_at', '>=', $from))
                              ->when($to, fn($q) => $q->where('created_at', '<=', $to)),
            default    => collect(),
        };

        $rows = match($type) {
            'bookings' => $query->get()->map(fn($b) => [
                $b->reference_code,
                $b->booking_type,
                $b->target_name,
                $b->user?->full_name,
                $b->total_price,
                $b->booking_status,
                $b->created_at?->format('Y-m-d'),
            ]),
            'users' => $query->get()->map(fn($u) => [
                $u->full_name,
                $u->email,
                $u->role,
                $u->heritage_points,
                $u->created_at?->format('Y-m-d'),
            ]),
            default => collect(),
        };

        $headers = match($type) {
            'bookings' => ['Reference', 'Type', 'Target', 'User', 'Amount', 'Status', 'Date'],
            'users'    => ['Name', 'Email', 'Role', 'Points', 'Registered'],
            default    => [],
        };

        $csv = implode(',', $headers) . "\n";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$type}-export-" . now()->format('Ymd') . ".csv\"",
        ]);
    }

    // ─── Audit Logs ───────────────────────────────────────────────────────────
    public function auditLogs(Request $request)
    {
        $query = AuditLog::with('user');
        if ($s = $request->q) $query->where('action', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%");
        return view('admin.audit-logs', ['logs' => $query->orderByDesc('created_at')->paginate(30)->withQueryString()]);
    }

    // ─── CMS ──────────────────────────────────────────────────────────────────
    public function cms()
    {
        $defaults = [
            ['key' => 'site_name',       'label' => 'Site Name',       'type' => 'text'],
            ['key' => 'contact_phone',   'label' => 'Contact Phone',   'type' => 'text'],
            ['key' => 'contact_email',   'label' => 'Contact Email',   'type' => 'email'],
            ['key' => 'contact_address', 'label' => 'Address',         'type' => 'text'],
            ['key' => 'about_agency',    'label' => 'About EDSTA',     'type' => 'textarea'],
            ['key' => 'footer_text',     'label' => 'Footer Text',     'type' => 'text'],
            ['key' => 'meta_description','label' => 'Meta Description','type' => 'textarea'],
        ];
        foreach ($defaults as $d) CmsSetting::firstOrCreate(['key' => $d['key']], $d + ['value' => '']);
        return view('admin.cms', ['settings' => CmsSetting::orderBy('key')->get()]);
    }

    public function cmsUpdate(Request $request)
    {
        foreach ($request->input('settings', []) as $key => $value) {
            CmsSetting::where('key', $key)->update(['value' => $value]);
        }
        Helpers::logAction('admin_update_cms', 'CmsSetting');
        return back()->with('success', 'CMS settings saved.');
    }

    // ─── FAQs ─────────────────────────────────────────────────────────────────
    public function faqs()          { return view('admin.faqs.index', ['faqs' => Faq::orderBy('sort_order')->paginate(30)]); }
    public function faqStore(Request $request) { Faq::create($request->validate(['question' => 'required', 'answer' => 'required', 'category' => 'nullable', 'sort_order' => 'integer'])); return back()->with('success', 'FAQ added.'); }
    public function faqUpdate(Request $request, int $id) { Faq::findOrFail($id)->update($request->only('question', 'answer', 'category', 'sort_order', 'is_active')); return back()->with('success', 'FAQ updated.'); }
    public function faqDelete(int $id) { Faq::findOrFail($id)->delete(); return back()->with('success', 'FAQ deleted.'); }

    // ─── Partners ─────────────────────────────────────────────────────────────
    public function partners() { return view('admin.partners.index', ['partners' => Partner::orderBy('sort_order')->paginate(30)]); }
    public function partnerStore(Request $request)
    {
        $data = $request->validate(['name' => 'required', 'website' => 'nullable', 'sort_order' => 'integer']);
        if ($request->hasFile('logo')) $data['logo_url'] = Helpers::saveUpload($request->file('logo'), 'partners');
        Partner::create($data);
        return back()->with('success', 'Partner added.');
    }
    public function partnerDelete(int $id) { Partner::findOrFail($id)->delete(); return back()->with('success', 'Partner removed.'); }

    // ─── Notify All ───────────────────────────────────────────────────────────
    public function notifyAll(Request $request)
    {
        $request->validate(['title' => 'required', 'message' => 'required']);
        $users = User::pluck('id');
        foreach ($users as $uid) {
            Notification::create(['user_id' => $uid, 'title' => $request->title, 'message' => $request->message, 'type' => $request->type ?? 'info']);
        }
        Helpers::logAction('admin_notify_all', 'Notification', null, "Sent to {$users->count()} users");
        return back()->with('success', "Notification sent to {$users->count()} users.");
    }

    // ─── Download ZIP ─────────────────────────────────────────────────────────
    public function downloadZip()
    {
        $projectRoot = base_path();
        $zipName     = 'edo-odyssey-laravel-' . now()->format('Ymd-His') . '.zip';
        $tmpPath     = sys_get_temp_dir() . '/' . $zipName;

        $zip = new \ZipArchive();
        if ($zip->open($tmpPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->with('danger', 'Could not create ZIP file.');
        }

        $exclude = ['vendor', 'node_modules', '.git', 'storage/logs', 'storage/framework/cache', 'storage/framework/sessions', 'storage/framework/views'];

        $files = new \RecursiveIteratorIterator(
            new \RecursiveCallbackFilterIterator(
                new \RecursiveDirectoryIterator($projectRoot, \RecursiveDirectoryIterator::SKIP_DOTS),
                function ($file, $key, $iterator) use ($exclude, $projectRoot) {
                    $rel = ltrim(str_replace($projectRoot, '', $file->getPathname()), DIRECTORY_SEPARATOR);
                    foreach ($exclude as $ex) {
                        if (str_starts_with($rel, $ex)) return false;
                    }
                    return true;
                }
            )
        );

        foreach ($files as $file) {
            if ($file->isFile()) {
                $rel = ltrim(str_replace($projectRoot, '', $file->getRealPath()), DIRECTORY_SEPARATOR);
                $zip->addFile($file->getRealPath(), $rel);
            }
        }
        $zip->close();

        Helpers::logAction('admin_download_zip', 'System', null, 'Project ZIP downloaded');

        return response()->download($tmpPath, $zipName, ['Content-Type' => 'application/zip'])
                         ->deleteFileAfterSend(true);
    }
}