@extends('layouts.app')
@section('title', 'Events – Edo Odyssey')
@section('content')
<div class="page-hero">
  <div class="container">
    <h1><i class="bi bi-calendar-event me-2 text-gold"></i>Events in Edo State</h1>
    <p class="text-white-50">Festivals, cultural celebrations, and community gatherings</p>
  </div>
</div>
<div class="container py-4">
  <form class="filter-bar row g-2" method="GET">
    {{-- Search --}}
    <div class="col-12 col-md-3"><input type="search" name="q" class="form-control" placeholder="Search events…" value="{{ request('q') }}"></div>

    {{-- Category --}}
    <div class="col-6 col-md-2">
      <select name="category" class="form-select">
        <option value="">All Categories</option>
        @foreach($categories as $cat)
        <option value="{{ $cat }}" {{ request('category')==$cat?'selected':'' }}>{{ $cat }}</option>
        @endforeach
      </select>
    </div>

    {{-- City --}}
    <div class="col-6 col-md-2">
      <select name="city" class="form-select">
        <option value="">All Cities</option>
        @foreach($cities as $city)
        <option value="{{ $city }}" {{ request('city')==$city?'selected':'' }}>{{ $city }}</option>
        @endforeach
      </select>
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

    {{-- Upcoming --}}
    <div class="col-6 col-md-2">
      <div class="form-check mt-2">
        <input class="form-check-input" type="checkbox" name="upcoming" id="upcoming" value="1" {{ request('upcoming')?'checked':'' }}>
        <label class="form-check-label" for="upcoming">Upcoming only</label>
      </div>
    </div>

    {{-- Sort --}}
    <div class="col-6 col-md-2">
      <select name="sort" class="form-select">
        <option value="">Sort by</option>
        <option value="date_asc" {{ request('sort')=='date_asc'?'selected':'' }}>Date: Earliest</option>
        <option value="date_desc" {{ request('sort')=='date_desc'?'selected':'' }}>Date: Latest</option>
        <option value="price_asc" {{ request('sort')=='price_asc'?'selected':'' }}>Price: Low–High</option>
        <option value="price_desc" {{ request('sort')=='price_desc'?'selected':'' }}>Price: High–Low</option>
        <option value="rating" {{ request('sort')=='rating'?'selected':'' }}>Highest Rated</option>
      </select>
    </div>

    <div class="col-6 col-md-2">
      <button class="btn btn-blue w-100" type="submit"><i class="bi bi-search me-1"></i>Filter</button>
    </div>
  </form>

  <div class="row g-4">
    @forelse($events as $event)
    <div class="col-md-6 col-lg-4">
      <div class="event-card card">
        <div class="card-img-wrap" style="height:200px"><img src="{{ \App\Helpers\Helpers::imageUrl($event->image_url) }}" alt="{{ $event->name }}" loading="lazy"><div class="card-category-badge">{{ $event->category ?? 'Event' }}</div><div class="card-price-badge">{{ $event->ticket_price == 0 ? 'Free' : '₦'.number_format($event->ticket_price) }}</div></div>
        <div class="card-body">
          <h5 class="card-title fw-bold text-blue" style="font-size:.9rem">{{ $event->name }}</h5>
          <div class="d-flex align-items-center gap-2 mb-1">
            <div class="star-rating small">@for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$event->avg_rating?'-fill':'' }}"></i>@endfor</div>
            <small class="text-muted">({{ number_format($event->avg_rating, 1) }})</small>
          </div>
          <p class="text-muted small mb-2"><i class="bi bi-calendar me-1"></i>{{ $event->start_date?->format('d M Y') ?? 'TBD' }} • <i class="bi bi-geo-alt ms-1 me-1"></i>{{ $event->location ?? 'TBD' }}</p>
          <div class="d-flex gap-2 mb-2" data-countdown="{{ $event->start_date?->toIso8601String() }}">
            <div class="countdown-box text-center"><div class="countdown-num">--</div></div><div class="countdown-sep text-gold fw-bold">:</div><div class="countdown-box text-center"><div class="countdown-num">--</div></div><div class="countdown-sep text-gold fw-bold">:</div><div class="countdown-box text-center"><div class="countdown-num">--</div></div><div class="countdown-sep text-gold fw-bold">:</div><div class="countdown-box text-center"><div class="countdown-num">--</div></div>
          </div>
          <a href="{{ route('events.detail', $event->id) }}" class="btn btn-blue btn-sm w-100">View Event</a>
        </div>
      </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted"><i class="bi bi-calendar-event fs-1 d-block mb-3"></i><p>No events found. <a href="{{ route('events.list_events') }}">Clear filters</a></p></div>
    @endforelse
  </div>
  <div class="d-flex justify-content-center mt-4">{{ $events->links() }}</div>
</div>
@endsection