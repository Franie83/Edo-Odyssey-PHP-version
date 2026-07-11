@extends('layouts.app')
@section('title', $restaurant->name . ' – Edo Odyssey')
@section('content')
<div class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('main.home') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ route('restaurants.list_restaurants') }}">Restaurants</a></li><li class="breadcrumb-item active">{{ $restaurant->name }}</li></ol></nav>
    <h1>{{ $restaurant->name }}</h1>
    <span class="text-white-50 small"><i class="bi bi-cup-hot me-1"></i>{{ $restaurant->cuisine_type }}</span>
  </div>
</div>
<div class="container py-4">
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="detail-image mb-4"><img src="{{ \App\Helpers\Helpers::imageUrl($restaurant->image_url) }}" alt="{{ $restaurant->name }}"></div>
      <p class="text-muted">{{ $restaurant->description }}</p>

      @if($restaurant->menu->count())
      <h4 class="fw-bold text-blue mt-4 mb-3"><i class="bi bi-menu-button me-2 text-gold"></i>Menu</h4>
      @php $menuByCategory = $restaurant->menu->groupBy('category'); @endphp
      @foreach($menuByCategory as $category => $items)
      <h5 class="text-blue fw-semibold mb-2">{{ $category ?: 'Main Menu' }}</h5>
      <div class="row g-2 mb-3">
        @foreach($items as $item)
        <div class="col-md-6">
          <div class="d-flex justify-content-between align-items-center p-2 border rounded">
            <div>
              <div class="fw-semibold small">{{ $item->name }}@if($item->is_vegetarian)<span class="badge bg-success ms-1" style="font-size:.6rem">V</span>@endif</div>
              <div class="text-muted" style="font-size:.75rem">{{ Str::limit($item->description, 50) }}</div>
            </div>
            <div class="text-gold fw-bold small">₦{{ number_format($item->price) }}</div>
          </div>
        </div>
        @endforeach
      </div>
      @endforeach
      @endif

      <!-- Reviews -->
      <h4 class="fw-bold text-blue mt-4 mb-3"><i class="bi bi-star me-2 text-gold"></i>Reviews</h4>
      @forelse($reviews as $r)
      <div class="review-card mb-3"><div class="d-flex gap-3"><div class="review-avatar flex-shrink-0">{{ $r->user?->first_name[0] ?? '?' }}</div><div><strong class="small">{{ $r->user?->full_name }}</strong><div class="star-rating small">@for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$r->rating?'-fill':'' }}"></i>@endfor</div><p class="text-muted small mb-0 mt-1">{{ $r->comment }}</p></div></div></div>
      @empty
      <p class="text-muted">No reviews yet.</p>
      @endforelse
      @auth
      <form method="POST" action="{{ route('reviews.store') }}" class="mt-4 p-3 bg-restaurant-100 rounded">@csrf<input type="hidden" name="target_type" value="Restaurant"><input type="hidden" name="target_id" value="{{ $restaurant->id }}"><h6 class="fw-bold mb-3" style="color:var(--restaurant)">Write a Review</h6><div class="star-select d-flex gap-1 mb-2"><input type="hidden" name="rating" value="5">@for($i=1;$i<=5;$i++)<i class="bi bi-star-fill text-gold fs-5" data-star="{{ $i }}" style="cursor:pointer"></i>@endfor</div><textarea name="comment" class="form-control border-0 mb-2" rows="3" placeholder="Share your dining experience…"></textarea><button class="btn btn-restaurant btn-sm">Submit Review +10 pts</button></form>
      @endauth
    </div>
    <div class="col-lg-4">
      <div class="info-card mb-3" style="border-left-color:var(--restaurant)">
        <h5 class="fw-bold mb-3" style="color:var(--restaurant)">Restaurant Info</h5>
        @if($restaurant->address)<p class="small mb-1"><i class="bi bi-geo-alt me-2" style="color:var(--restaurant)"></i>{{ $restaurant->address }}</p>@endif
        @if($restaurant->phone)<p class="small mb-1"><i class="bi bi-telephone me-2" style="color:var(--restaurant)"></i>{{ $restaurant->phone }}</p>@endif
        @if($restaurant->opening_hours)<p class="small mb-1"><i class="bi bi-clock me-2" style="color:var(--restaurant)"></i>{{ $restaurant->opening_hours }}</p>@endif
        @if($restaurant->avg_price)<p class="small mb-0"><i class="bi bi-cash me-2" style="color:var(--restaurant)"></i>Avg: ₦{{ number_format($restaurant->avg_price) }}</p>@endif
        <hr><div class="d-flex align-items-center gap-1"><div class="star-rating">@for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$avg_rating?'-fill':'' }}"></i>@endfor</div><small class="text-muted">({{ $avg_rating }})</small></div>
      </div>
      <a href="{{ route('bookings.new_booking', ['type' => 'Restaurant', 'id' => $restaurant->id]) }}" class="btn btn-restaurant w-100 mb-3"><i class="bi bi-calendar-check me-2"></i>Make Reservation</a>
      @auth
      <form method="POST" action="{{ route('dashboard.toggle_favourite', ['type' => 'Restaurant', 'id' => $restaurant->id]) }}" class="mb-3">@csrf<button class="btn {{ $is_fav ? 'btn-danger' : 'btn-outline-secondary' }} w-100"><i class="bi bi-heart{{ $is_fav ? '-fill' : '' }} me-1"></i>{{ $is_fav ? 'Remove' : 'Add to Favourites' }}</button></form>
      @endauth
    </div>
  </div>
</div>
@endsection
