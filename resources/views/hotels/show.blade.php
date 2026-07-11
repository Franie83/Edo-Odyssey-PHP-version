@extends('layouts.app')
@section('title', $hotel->name . ' – Edo Odyssey')
@section('content')
<div class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('main.home') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ route('hotels.list_hotels') }}">Hotels</a></li><li class="breadcrumb-item active">{{ $hotel->name }}</li></ol></nav>
    <h1>{{ $hotel->name }}</h1>
    <div class="d-flex gap-3 flex-wrap mt-2">
      <div class="text-gold">@for($i=0;$i<($hotel->stars??3);$i++)<i class="bi bi-star-fill"></i>@endfor</div>
      <span class="text-white-50 small"><i class="bi bi-geo-alt me-1"></i>{{ $hotel->address }}</span>
    </div>
  </div>
</div>
<div class="container py-4">
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="detail-image mb-4"><img src="{{ \App\Helpers\Helpers::imageUrl($hotel->image_url) }}" alt="{{ $hotel->name }}"></div>
      <h3 class="text-blue fw-bold">About {{ $hotel->name }}</h3>
      <p class="text-muted">{{ $hotel->description }}</p>

      <!-- Rooms -->
      @if($hotel->rooms->count())
      <h4 class="fw-bold text-blue mt-4 mb-3">Available Rooms</h4>
      <div class="row g-3">
        @foreach($hotel->rooms as $room)
        <div class="col-md-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h6 class="fw-bold text-blue">{{ $room->room_type }}</h6>
              <p class="small text-muted mb-1">{{ $room->description }}</p>
              <div class="d-flex justify-content-between align-items-center">
                <span class="text-gold fw-bold">₦{{ number_format($room->price_per_night) }}/night</span>
                <span class="badge bg-blue-100 text-blue">{{ $room->capacity }} guests</span>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      @endif

      <!-- Reviews -->
      <h4 class="fw-bold text-blue mt-4 mb-3"><i class="bi bi-star me-2 text-gold"></i>Reviews ({{ count($reviews) }})</h4>
      @forelse($reviews as $r)
      <div class="review-card mb-3">
        <div class="d-flex gap-3">
          <div class="review-avatar flex-shrink-0">{{ $r->user?->first_name[0] ?? '?' }}</div>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between">
              <strong class="small">{{ $r->user?->full_name }}</strong>
              <small class="text-muted">{{ $r->created_at?->format('d M Y') }}</small>
            </div>
            <div class="star-rating small">@for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$r->rating?'-fill':'' }}"></i>@endfor</div>
            <p class="text-muted small mb-0 mt-1">{{ $r->comment }}</p>
          </div>
        </div>
      </div>
      @empty
      <p class="text-muted">No reviews yet. Be the first!</p>
      @endforelse
      @auth
      <form method="POST" action="{{ route('reviews.store') }}" class="mt-4 p-3 bg-blue-100 rounded">
        @csrf
        <input type="hidden" name="target_type" value="Hotel">
        <input type="hidden" name="target_id" value="{{ $hotel->id }}">
        <h6 class="fw-bold text-blue mb-3">Write a Review</h6>
        <div class="star-select d-flex gap-1 mb-2"><input type="hidden" name="rating" value="5">@for($i=1;$i<=5;$i++)<i class="bi bi-star-fill text-gold fs-5" data-star="{{ $i }}" style="cursor:pointer"></i>@endfor</div>
        <textarea name="comment" class="form-control border-0 mb-2" rows="3" placeholder="Share your experience…"></textarea>
        <button class="btn btn-gold btn-sm">Submit Review +10 pts</button>
      </form>
      @endauth
    </div>
    <div class="col-lg-4">
      <div class="info-card mb-3">
        <h5 class="fw-bold text-blue mb-3">Hotel Info</h5>
        @if($hotel->address)<p class="small mb-1"><i class="bi bi-geo-alt me-2 text-gold"></i>{{ $hotel->address }}</p>@endif
        @if($hotel->phone)<p class="small mb-1"><i class="bi bi-telephone me-2 text-gold"></i>{{ $hotel->phone }}</p>@endif
        @if($hotel->email)<p class="small mb-1"><i class="bi bi-envelope me-2 text-gold"></i>{{ $hotel->email }}</p>@endif
        @if($hotel->check_in_time)<p class="small mb-1"><i class="bi bi-clock me-2 text-gold"></i>Check-in: {{ $hotel->check_in_time }}</p>@endif
        @if($hotel->check_out_time)<p class="small mb-0"><i class="bi bi-clock-history me-2 text-gold"></i>Check-out: {{ $hotel->check_out_time }}</p>@endif
        <hr>
        <div class="d-flex align-items-center gap-1"><div class="star-rating">@for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$avg_rating?'-fill':'' }}"></i>@endfor</div><small class="text-muted">({{ $avg_rating }})</small></div>
      </div>
      <a href="{{ route('bookings.new_booking', ['type' => 'Hotel', 'id' => $hotel->id]) }}" class="btn btn-gold w-100 mb-3"><i class="bi bi-calendar-check me-2"></i>Book a Room</a>
      @auth
      <form method="POST" action="{{ route('dashboard.toggle_favourite', ['type' => 'Hotel', 'id' => $hotel->id]) }}" class="mb-3">@csrf<button class="btn {{ $is_fav ? 'btn-danger' : 'btn-outline-secondary' }} w-100"><i class="bi bi-heart{{ $is_fav ? '-fill' : '' }} me-1"></i>{{ $is_fav ? 'Remove from Favourites' : 'Add to Favourites' }}</button></form>
      @endauth
    </div>
  </div>
</div>
@endsection
