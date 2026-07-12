<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AttractionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

// ─── TEST ROUTE ──────────────────────────────────────────────────────────────
Route::get('/test', function () {
    return response()->json([
        'status' => 'OK',
        'message' => 'Laravel is working!',
        'environment' => app()->environment(),
        'database' => config('database.default'),
        'timestamp' => now()->toDateTimeString()
    ]);
});

Route::get('/test-laravel', function () {
    return 'Laravel route is working!';
});

// ─── Public ─────────────────────────────────────────────────────────────────
Route::get('/',          [MainController::class, 'home'])->name('main.home');
Route::get('/about',     [MainController::class, 'about'])->name('main.about');
Route::get('/contact',   [MainController::class, 'contact'])->name('main.contact');
Route::post('/contact',  [MainController::class, 'contactSubmit'])->name('main.contact_submit');
Route::get('/faq',       [MainController::class, 'faq'])->name('main.faq');
Route::get('/privacy',   [MainController::class, 'privacy'])->name('main.privacy');
Route::get('/terms',     [MainController::class, 'terms'])->name('main.terms');

// ─── Auth ────────────────────────────────────────────────────────────────────
Route::get('/auth/login',              [AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/auth/login',             [AuthController::class, 'login'])->name('auth.login_post');
Route::get('/auth/register',           [AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/auth/register',          [AuthController::class, 'register'])->name('auth.register_post');
Route::get('/auth/logout',             [AuthController::class, 'logout'])->name('auth.logout');
Route::get('/auth/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('auth.quick_login');

// ─── Attractions ──────────────────────────────────────────────────────────────
Route::get('/attractions',       [AttractionController::class, 'index'])->name('attractions.list_attractions');
Route::get('/attractions/{id}',  [AttractionController::class, 'show'])->name('attractions.detail');

// ─── Guides ───────────────────────────────────────────────────────────────────
Route::get('/guides',       [GuideController::class, 'index'])->name('guides.list_guides');
Route::get('/guides/{id}',  [GuideController::class, 'show'])->name('guides.detail');

// ─── Hotels ───────────────────────────────────────────────────────────────────
Route::get('/hotels',       [HotelController::class, 'index'])->name('hotels.list_hotels');
Route::get('/hotels/{id}',  [HotelController::class, 'show'])->name('hotels.detail');

// ─── Restaurants ──────────────────────────────────────────────────────────────
Route::get('/restaurants',       [RestaurantController::class, 'index'])->name('restaurants.list_restaurants');
Route::get('/restaurants/{id}',  [RestaurantController::class, 'show'])->name('restaurants.detail');

// ─── Events ───────────────────────────────────────────────────────────────────
Route::get('/events',       [EventController::class, 'index'])->name('events.list_events');
Route::get('/events/{id}',  [EventController::class, 'show'])->name('events.detail');

// ─── News ─────────────────────────────────────────────────────────────────────
Route::get('/news',       [NewsController::class, 'index'])->name('news.list_news');
Route::get('/news/{id}',  [NewsController::class, 'show'])->name('news.detail');
Route::post('/news/{id}/comment', [NewsController::class, 'comment'])->middleware('auth')->name('news.comment');

// ─── QR & Search ─────────────────────────────────────────────────────────────
Route::get('/qr/{code}',  [QrController::class, 'scan'])->name('qr.scan');
Route::get('/search',     [SearchController::class, 'results'])->name('search.results');

// ─── Authenticated ───────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard',           [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/profile',   [DashboardController::class, 'profile'])->name('dashboard.profile');
    Route::post('/dashboard/profile',  [DashboardController::class, 'updateProfile'])->name('dashboard.update_profile');
    Route::post('/dashboard/notifications/{id}/read',  [DashboardController::class, 'markRead'])->name('dashboard.mark_read');
    Route::post('/dashboard/notifications/read-all',   [DashboardController::class, 'markAllRead'])->name('dashboard.mark_all_read');
    Route::get('/dashboard/bookings/{id}',             [DashboardController::class, 'bookingDetail'])->name('dashboard.booking_detail');
    Route::post('/dashboard/favourites/{type}/{id}',   [DashboardController::class, 'toggleFavourite'])->name('dashboard.toggle_favourite');

    // ─── Guide Management ──────────────────────────────────────────────────
    Route::get('/dashboard/guide/edit',        [DashboardController::class, 'editGuide'])->name('dashboard.guide.edit');
    Route::post('/dashboard/guide/update',     [DashboardController::class, 'updateGuide'])->name('dashboard.guide.update');

    // ─── Hotel Management ──────────────────────────────────────────────────
    Route::get('/dashboard/hotels',           [DashboardController::class, 'myHotels'])->name('dashboard.hotels.index');
    Route::get('/dashboard/hotels/create',    [DashboardController::class, 'createHotel'])->name('dashboard.hotels.create');
    Route::post('/dashboard/hotels',          [DashboardController::class, 'storeHotel'])->name('dashboard.hotels.store');
    Route::get('/dashboard/hotels/{id}/edit', [DashboardController::class, 'editHotel'])->name('dashboard.hotels.edit');
    Route::put('/dashboard/hotels/{id}',      [DashboardController::class, 'updateHotel'])->name('dashboard.hotels.update');
    // Fallback for browsers that don't support PUT
    Route::post('/dashboard/hotels/{id}/update', [DashboardController::class, 'updateHotel'])->name('dashboard.hotels.update.post');

    // ─── Restaurant Management ────────────────────────────────────────────
    Route::get('/dashboard/restaurants',           [DashboardController::class, 'myRestaurants'])->name('dashboard.restaurants.index');
    Route::get('/dashboard/restaurants/create',    [DashboardController::class, 'createRestaurant'])->name('dashboard.restaurants.create');
    Route::post('/dashboard/restaurants',          [DashboardController::class, 'storeRestaurant'])->name('dashboard.restaurants.store');
    Route::get('/dashboard/restaurants/{id}/edit', [DashboardController::class, 'editRestaurant'])->name('dashboard.restaurants.edit');
    Route::put('/dashboard/restaurants/{id}',      [DashboardController::class, 'updateRestaurant'])->name('dashboard.restaurants.update');
    Route::post('/dashboard/restaurants/{id}/update', [DashboardController::class, 'updateRestaurant'])->name('dashboard.restaurants.update.post');

    // ─── Event Management ──────────────────────────────────────────────────
    Route::get('/dashboard/events',           [DashboardController::class, 'myEvents'])->name('dashboard.events.index');
    Route::get('/dashboard/events/create',    [DashboardController::class, 'createEvent'])->name('dashboard.events.create');
    Route::post('/dashboard/events',          [DashboardController::class, 'storeEvent'])->name('dashboard.events.store');
    Route::get('/dashboard/events/{id}/edit', [DashboardController::class, 'editEvent'])->name('dashboard.events.edit');
    Route::put('/dashboard/events/{id}',      [DashboardController::class, 'updateEvent'])->name('dashboard.events.update');
    Route::post('/dashboard/events/{id}/update', [DashboardController::class, 'updateEvent'])->name('dashboard.events.update.post');

    // Bookings
    Route::get('/bookings/new/{type}/{id}',       [BookingController::class, 'create'])->name('bookings.new_booking');
    Route::post('/bookings/new/{type}/{id}',      [BookingController::class, 'store'])->name('bookings.store_booking');
    Route::post('/bookings/{id}/cancel',          [BookingController::class, 'cancel'])->name('bookings.cancel_booking');
    Route::post('/bookings/{id}/target-confirm',  [BookingController::class, 'targetConfirm'])->name('bookings.target_confirm_booking');
    Route::post('/bookings/{id}/target-reject',   [BookingController::class, 'targetReject'])->name('bookings.target_reject_booking');
    Route::post('/bookings/{id}/complete',        [BookingController::class, 'complete'])->name('bookings.complete_booking');

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

// ─── Admin ────────────────────────────────────────────────────────────────────
Route::prefix('/admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/',           [AdminController::class, 'dashboard'])->name('dashboard');

    // Users
    Route::get('/users',             [AdminController::class, 'users'])->name('users');
    Route::get('/users/new',         [AdminController::class, 'userCreate'])->name('user_create');
    Route::post('/users/new',        [AdminController::class, 'userStore'])->name('user_store');
    Route::get('/users/{id}/edit',   [AdminController::class, 'userEdit'])->name('user_edit');
    Route::post('/users/{id}/edit',  [AdminController::class, 'userUpdate'])->name('user_update');
    Route::post('/users/{id}/delete',[AdminController::class, 'userDelete'])->name('user_delete');

    // Attractions
    Route::get('/attractions',               [AdminController::class, 'attractions'])->name('attractions');
    Route::get('/attractions/new',           [AdminController::class, 'attractionCreate'])->name('attraction_create');
    Route::post('/attractions/new',          [AdminController::class, 'attractionStore'])->name('attraction_store');
    Route::get('/attractions/{id}/edit',     [AdminController::class, 'attractionEdit'])->name('attraction_edit');
    Route::post('/attractions/{id}/edit',    [AdminController::class, 'attractionUpdate'])->name('attraction_update');
    Route::post('/attractions/{id}/delete',  [AdminController::class, 'attractionDelete'])->name('attraction_delete');
    Route::post('/attractions/{id}/qr',      [AdminController::class, 'attractionRegenQr'])->name('attraction_regen_qr');
    Route::post('/attractions/generate-all-qr', [AdminController::class, 'generateAllQr'])->name('attractions.generate_all_qr');

    // Guides
    Route::get('/guides',               [AdminController::class, 'guides'])->name('guides');
    Route::get('/guides/new',           [AdminController::class, 'guideCreate'])->name('guide_create');
    Route::post('/guides/new',          [AdminController::class, 'guideStore'])->name('guide_store');
    Route::get('/guides/{id}/edit',     [AdminController::class, 'guideEdit'])->name('guide_edit');
    Route::post('/guides/{id}/edit',    [AdminController::class, 'guideUpdate'])->name('guide_update');
    Route::post('/guides/{id}/verify',  [AdminController::class, 'guideVerify'])->name('guide_verify');
    Route::post('/guides/{id}/delete',  [AdminController::class, 'guideDelete'])->name('guide_delete');

    // Hotels
    Route::get('/hotels',              [AdminController::class, 'hotels'])->name('hotels');
    Route::get('/hotels/new',          [AdminController::class, 'hotelCreate'])->name('hotel_create');
    Route::post('/hotels/new',         [AdminController::class, 'hotelStore'])->name('hotel_store');
    Route::get('/hotels/{id}/edit',    [AdminController::class, 'hotelEdit'])->name('hotel_edit');
    Route::post('/hotels/{id}/edit',   [AdminController::class, 'hotelUpdate'])->name('hotel_update');
    Route::post('/hotels/{id}/delete', [AdminController::class, 'hotelDelete'])->name('hotel_delete');
    Route::post('/hotels/{id}/approve', [AdminController::class, 'approveHotel'])->name('hotel_approve');

    // Restaurants
    Route::get('/restaurants',              [AdminController::class, 'restaurants'])->name('restaurants');
    Route::get('/restaurants/new',          [AdminController::class, 'restaurantCreate'])->name('restaurant_create');
    Route::post('/restaurants/new',         [AdminController::class, 'restaurantStore'])->name('restaurant_store');
    Route::get('/restaurants/{id}/edit',    [AdminController::class, 'restaurantEdit'])->name('restaurant_edit');
    Route::post('/restaurants/{id}/edit',   [AdminController::class, 'restaurantUpdate'])->name('restaurant_update');
    Route::post('/restaurants/{id}/delete', [AdminController::class, 'restaurantDelete'])->name('restaurant_delete');
    Route::post('/restaurants/{id}/approve', [AdminController::class, 'approveRestaurant'])->name('restaurant_approve');

    // Events
    Route::get('/events',              [AdminController::class, 'events'])->name('events');
    Route::get('/events/new',          [AdminController::class, 'eventCreate'])->name('event_create');
    Route::post('/events/new',         [AdminController::class, 'eventStore'])->name('event_store');
    Route::get('/events/{id}/edit',    [AdminController::class, 'eventEdit'])->name('event_edit');
    Route::post('/events/{id}/edit',   [AdminController::class, 'eventUpdate'])->name('event_update');
    Route::post('/events/{id}/delete', [AdminController::class, 'eventDelete'])->name('event_delete');
    Route::post('/events/{id}/approve', [AdminController::class, 'approveEvent'])->name('event_approve');

    // News
    Route::get('/news',              [AdminController::class, 'news'])->name('news');
    Route::get('/news/new',          [AdminController::class, 'newsCreate'])->name('news_create');
    Route::post('/news/new',         [AdminController::class, 'newsStore'])->name('news_store');
    Route::get('/news/{id}/edit',    [AdminController::class, 'newsEdit'])->name('news_edit');
    Route::post('/news/{id}/edit',   [AdminController::class, 'newsUpdate'])->name('news_update');
    Route::post('/news/{id}/delete', [AdminController::class, 'newsDelete'])->name('news_delete');

    // Categories
    Route::get('/categories',              [AdminController::class, 'categories'])->name('categories');
    Route::get('/categories/new',          [AdminController::class, 'categoryCreate'])->name('category_create');
    Route::post('/categories/new',         [AdminController::class, 'categoryStore'])->name('category_store');
    Route::post('/categories/{id}/edit',   [AdminController::class, 'categoryUpdate'])->name('category_update');
    Route::post('/categories/{id}/delete', [AdminController::class, 'categoryDelete'])->name('category_delete');

    // Bookings
    Route::get('/bookings',              [AdminController::class, 'bookings'])->name('bookings');
    Route::post('/bookings/{id}/approve',[AdminController::class, 'bookingApprove'])->name('booking_approve');
    Route::post('/bookings/{id}/reject', [AdminController::class, 'bookingReject'])->name('booking_reject');
    Route::get('/bookings/{id}',         [AdminController::class, 'bookingDetail'])->name('booking_detail');

    // Reviews
    Route::get('/reviews',              [AdminController::class, 'reviews'])->name('reviews');
    Route::post('/reviews/{id}/delete', [AdminController::class, 'reviewDelete'])->name('review_delete');
    Route::post('/reviews/{id}/approve', [AdminController::class, 'approveReview'])->name('review_approve');

    // Analytics / Audit / CMS
    Route::get('/analytics',  [AdminController::class, 'analytics'])->name('analytics');
    Route::get('/audit-logs', [AdminController::class, 'auditLogs'])->name('audit_logs');
    Route::get('/cms',        [AdminController::class, 'cms'])->name('cms');
    Route::post('/cms',       [AdminController::class, 'cmsUpdate'])->name('cms_update');

    // FAQs
    Route::get('/faqs',              [AdminController::class, 'faqs'])->name('faqs');
    Route::post('/faqs/new',         [AdminController::class, 'faqStore'])->name('faq_store');
    Route::post('/faqs/{id}/edit',   [AdminController::class, 'faqUpdate'])->name('faq_update');
    Route::post('/faqs/{id}/delete', [AdminController::class, 'faqDelete'])->name('faq_delete');

    // Partners
    Route::get('/partners',               [AdminController::class, 'partners'])->name('partners');
    Route::post('/partners/new',          [AdminController::class, 'partnerStore'])->name('partner_store');
    Route::post('/partners/{id}/delete',  [AdminController::class, 'partnerDelete'])->name('partner_delete');

    // Notify all
    Route::post('/notify-all', [AdminController::class, 'notifyAll'])->name('notify_all');

    // CSV/Excel export
    Route::get('/analytics/export', [AdminController::class, 'analyticsExport'])->name('analytics_export');

    // Download ZIP
    Route::get('/download-zip', [AdminController::class, 'downloadZip'])->name('download_zip');
});