@extends('layouts.app')
@section('title', 'Tour Guides – Edo Odyssey')
@section('content')
<div class="page-hero">
  <div class="container">
    <h1><i class="bi bi-person-badge me-2 text-gold"></i>Certified Tour Guides</h1>
    <p class="text-white-50">Expert local guides to enrich your Edo State experience</p>
  </div>
</div>
<div class="container py-4">
  <form class="filter-bar row g-2" method="GET">
    {{-- Search --}}
    <div class="col-12 col-md-3"><input type="search" name="q" class="form-control" placeholder="Search guides by name, language…" value="{{ request('q') }}"></div>

    {{-- Language --}}
    <div class="col-4 col-md-2">
      <input type="text" name="language" class="form-control" placeholder="Language" value="{{ request('language') }}">
    </div>

    {{-- Price Range --}}
    <div class="col-4 col-md-2">
      <input type="number" name="min_price" class="form-control" placeholder="Min ₦" value="{{ request('min_price') }}" min="0">
    </div>
    <div class="col-4 col-md-2">
      <input type="number" name="max_price" class="form-control" placeholder="Max ₦" value="{{ request('max_price') }}" min="0">
    </div>

    {{-- Rating --}}
    <div class="col-4 col-md-1">
      <select name="min_rating" class="form-select">
        <option value="">⭐ Any</option>
        @for($i=1;$i<=5;$i++)
        <option value="{{ $i }}" {{ request('min_rating')==$i?'selected':'' }}>{{ $i }}★</option>
        @endfor
      </select>
    </div>

    {{-- Sort --}}
    <div class="col-4 col-md-2">
      <select name="sort" class="form-select">
        <option value="">Sort by</option>
        <option value="rate_asc" {{ request('sort')=='rate_asc'?'selected':'' }}>Rate: Low–High</option>
        <option value="rate_desc" {{ request('sort')=='rate_desc'?'selected':'' }}>Rate: High–Low</option>
        <option value="exp_desc" {{ request('sort')=='exp_desc'?'selected':'' }}>Most Experienced</option>
        <option value="rating" {{ request('sort')=='rating'?'selected':'' }}>Highest Rated</option>
      </select>
    </div>

    <div class="col-4 col-md-2">
      <button class="btn btn-blue w-100" type="submit"><i class="bi bi-search me-1"></i>Filter</button>
    </div>
  </form>

  <div class="row g-4">
    @forelse($guides as $guide)
    <div class="col-md-6 col-lg-3">
      <div class="guide-card card p-3 text-center h-100">
        <div class="mb-3">
          @if($guide->user?->avatar_url)
          <img src="{{ \App\Helpers\Helpers::imageUrl($guide->user->avatar_url) }}" class="guide-avatar mx-auto" alt="">
          @else
          <div class="guide-avatar-placeholder mx-auto"><i class="bi bi-person-fill"></i></div>
          @endif
        </div>
        <h6 class="fw-bold text-blue">{{ $guide->user?->full_name ?? 'Tour Guide' }}</h6>
        <div class="star-rating mb-1">@for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$guide->avg_rating?'-fill':'' }}"></i>@endfor</div>
        <p class="text-muted small mb-2">{{ $guide->experience ?? 0 }} yrs experience</p>
        @if($guide->languages)
        <div class="d-flex flex-wrap justify-content-center gap-1 mb-2">
          @foreach(array_slice(explode(',', $guide->languages), 0, 3) as $l)
          <span class="lang-badge">{{ trim($l) }}</span>
          @endforeach
        </div>
        @endif
        <p class="small text-muted text-truncate-2 mb-3">{{ Str::limit($guide->specializations, 60) }}</p>
        <div class="d-flex justify-content-between align-items-center mt-auto">
          <div class="text-gold fw-bold">₦{{ number_format($guide->hourly_rate ?? 0) }}/hr</div>
          <a href="{{ route('bookings.new_booking', ['type' => 'Guide', 'id' => $guide->id]) }}" class="btn btn-blue btn-sm">Book</a>
        </div>
      </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted"><i class="bi bi-person-badge fs-1 d-block mb-3"></i><p>No guides found.</p></div>
    @endforelse
  </div>
  <div class="d-flex justify-content-center mt-4">{{ $guides->links() }}</div>
</div>
@endsection