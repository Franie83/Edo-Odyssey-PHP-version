@extends('layouts.app')
@section('title', ($guide->user?->full_name ?? 'Tour Guide') . ' – Edo Odyssey')
@section('content')
<div class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('main.home') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ route('guides.list_guides') }}">Guides</a></li><li class="breadcrumb-item active">{{ $guide->user?->full_name }}</li></ol></nav>
    <h1>{{ $guide->user?->full_name ?? 'Tour Guide' }}</h1>
    <p class="text-white-50">Certified Tour Guide · {{ $guide->experience ?? 0 }} years experience</p>
  </div>
</div>
<div class="container py-4">
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="d-flex gap-4 mb-4 p-4 bg-white rounded shadow-sm">
        @if($guide->user?->avatar_url)
        <img src="{{ \App\Helpers\Helpers::imageUrl($guide->user->avatar_url) }}" class="guide-avatar flex-shrink-0" alt="">
        @else
        <div class="guide-avatar-placeholder flex-shrink-0"><i class="bi bi-person-fill"></i></div>
        @endif
        <div>
          <h4 class="fw-bold text-blue mb-1">{{ $guide->user?->full_name }}</h4>
          <div class="d-flex align-items-center gap-2 mb-2">
            <div class="star-rating">@for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$avg_rating?'-fill':'' }}"></i>@endfor</div>
            <small class="text-muted">({{ $avg_rating }} avg)</small>
          </div>
          @if($guide->languages)
          <div class="d-flex flex-wrap gap-1 mb-2">@foreach(explode(',', $guide->languages) as $l)<span class="lang-badge">{{ trim($l) }}</span>@endforeach</div>
          @endif
          @if($guide->specializations)<p class="text-muted small mb-0"><i class="bi bi-compass me-1 text-gold"></i>{{ $guide->specializations }}</p>@endif
        </div>
      </div>

      @if($guide->bio)
      <h4 class="fw-bold text-blue mb-2">About</h4>
      <p class="text-muted">{{ $guide->bio }}</p>
      @endif

      @if($guide->availability->count())
      <h4 class="fw-bold text-blue mt-4 mb-3">Availability</h4>
      <div class="d-flex flex-wrap gap-2">
        @foreach($guide->availability->where('is_available', true) as $a)
        <span class="badge bg-blue-100 text-blue py-2 px-3">{{ $a->day_of_week }}: {{ $a->start_time }} – {{ $a->end_time }}</span>
        @endforeach
      </div>
      @endif

      <!-- Reviews -->
      <h4 class="fw-bold text-blue mt-4 mb-3"><i class="bi bi-star me-2 text-gold"></i>Reviews</h4>
      @forelse($reviews as $r)
      <div class="review-card mb-3"><div class="d-flex gap-3"><div class="review-avatar flex-shrink-0">{{ $r->user?->first_name[0] ?? '?' }}</div><div><strong class="small">{{ $r->user?->full_name }}</strong><div class="star-rating small">@for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$r->rating?'-fill':'' }}"></i>@endfor</div><p class="text-muted small mb-0">{{ $r->comment }}</p></div></div></div>
      @empty
      <p class="text-muted">No reviews yet.</p>
      @endforelse
      @auth
      <form method="POST" action="{{ route('reviews.store') }}" class="mt-3 p-3 bg-blue-100 rounded">@csrf<input type="hidden" name="target_type" value="Guide"><input type="hidden" name="target_id" value="{{ $guide->id }}"><h6 class="fw-bold text-blue mb-2">Write a Review</h6><div class="star-select d-flex gap-1 mb-2"><input type="hidden" name="rating" value="5">@for($i=1;$i<=5;$i++)<i class="bi bi-star-fill text-gold fs-5" data-star="{{ $i }}" style="cursor:pointer"></i>@endfor</div><textarea name="comment" class="form-control border-0 mb-2" rows="2" placeholder="Share your experience…"></textarea><button class="btn btn-gold btn-sm">Submit +10 pts</button></form>
      @endauth
    </div>
    <div class="col-lg-4">
      <div class="info-card mb-3">
        <h5 class="fw-bold text-blue mb-3">Pricing</h5>
        <div class="d-flex justify-content-between mb-2"><span class="small">Hourly Rate</span><span class="text-gold fw-bold">₦{{ number_format($guide->hourly_rate ?? 0) }}/hr</span></div>
        @if($guide->daily_rate)<div class="d-flex justify-content-between"><span class="small">Daily Rate</span><span class="text-gold fw-bold">₦{{ number_format($guide->daily_rate) }}/day</span></div>@endif
        @if($guide->certification)<hr><p class="small mb-0"><i class="bi bi-award me-2 text-gold"></i>{{ $guide->certification }}</p>@endif
      </div>
      <a href="{{ route('bookings.new_booking', ['type' => 'Guide', 'id' => $guide->id]) }}" class="btn btn-gold w-100 mb-3"><i class="bi bi-calendar-check me-2"></i>Book This Guide</a>
      @auth
      <form method="POST" action="{{ route('dashboard.toggle_favourite', ['type' => 'Guide', 'id' => $guide->id]) }}" class="mb-3">@csrf<button class="btn {{ $is_fav ? 'btn-danger' : 'btn-outline-secondary' }} w-100"><i class="bi bi-heart{{ $is_fav ? '-fill' : '' }} me-1"></i>{{ $is_fav ? 'Remove from Favourites' : 'Add to Favourites' }}</button></form>
      @endauth
    </div>
  </div>
</div>
@endsection
