@extends('layouts.app')
@section('title', 'Hotels – Edo Odyssey')
@section('content')
<div class="page-hero">
  <div class="container">
    <h1><i class="bi bi-building-fill me-2 text-gold"></i>Hotels in Edo State</h1>
    <p class="text-white-50">Find the perfect place to stay during your visit</p>
  </div>
</div>
<div class="container py-4">
  <form class="filter-bar row g-2" method="GET">
    {{-- Search --}}
    <div class="col-12 col-md-3"><input type="search" name="q" class="form-control" placeholder="Search hotels…" value="{{ request('q') }}"></div>

    {{-- City --}}
    <div class="col-6 col-md-2">
      <select name="city" class="form-select">
        <option value="">All Cities</option>
        @foreach($cities as $city)
        <option value="{{ $city }}" {{ request('city')==$city?'selected':'' }}>{{ $city }}</option>
        @endforeach
      </select>
    </div>

    {{-- Stars --}}
    <div class="col-6 col-md-1">
      <select name="stars" class="form-select">
        <option value="">Stars</option>
        @for($s=5;$s>=1;$s--)
        <option value="{{ $s }}" {{ request('stars')==$s?'selected':'' }}>{{ $s }}★</option>
        @endfor
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

    {{-- Sort --}}
    <div class="col-6 col-md-2">
      <select name="sort" class="form-select">
        <option value="">Sort by</option>
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
    @forelse($hotels as $hotel)
    <div class="col-md-6 col-lg-4">
      <div class="hotel-card card">
        <div class="card-img-wrap"><img src="{{ \App\Helpers\Helpers::imageUrl($hotel->image_url) }}" alt="{{ $hotel->name }}" loading="lazy"><div class="card-price-badge">₦{{ number_format($hotel->price_per_night) }}/night</div></div>
        <div class="card-body">
          <h5 class="card-title fw-bold text-blue mb-1">{{ $hotel->name }}</h5>
          <div class="text-gold mb-1">@for($i=0;$i<($hotel->stars??3);$i++)<i class="bi bi-star-fill" style="font-size:.75rem"></i>@endfor</div>
          <div class="d-flex align-items-center gap-2 mb-2">
            <div class="star-rating small">@for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$hotel->avg_rating?'-fill':'' }}"></i>@endfor</div>
            <small class="text-muted">({{ number_format($hotel->avg_rating, 1) }})</small>
          </div>
          <p class="text-muted small mb-2 text-truncate-2">{{ Str::limit($hotel->description, 80) }}</p>
          <div class="d-flex justify-content-between">
            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ Str::limit($hotel->address ?? $hotel->city, 25) }}</small>
            <a href="{{ route('hotels.detail', $hotel->id) }}" class="btn btn-blue btn-sm">View</a>
          </div>
        </div>
      </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted"><i class="bi bi-building-fill fs-1 d-block mb-3"></i><p>No hotels found. <a href="{{ route('hotels.list_hotels') }}">Clear filters</a></p></div>
    @endforelse
  </div>
  <div class="d-flex justify-content-center mt-4">{{ $hotels->links() }}</div>
</div>
@endsection