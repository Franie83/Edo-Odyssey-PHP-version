@extends('layouts.app')
@section('title', 'Restaurants – Edo Odyssey')
@section('content')
<div class="page-hero">
  <div class="container">
    <h1><i class="bi bi-cup-hot me-2 text-gold"></i>Restaurants in Edo State</h1>
    <p class="text-white-50">Discover authentic Edo cuisine and dining experiences</p>
  </div>
</div>
<div class="container py-4">
  <form class="filter-bar row g-2" method="GET">
    {{-- Search --}}
    <div class="col-12 col-md-3"><input type="search" name="q" class="form-control" placeholder="Search restaurants…" value="{{ request('q') }}"></div>

    {{-- City --}}
    <div class="col-6 col-md-2">
      <select name="city" class="form-select">
        <option value="">All Cities</option>
        @foreach($cities as $city)
        <option value="{{ $city }}" {{ request('city')==$city?'selected':'' }}>{{ $city }}</option>
        @endforeach
      </select>
    </div>

    {{-- Cuisine --}}
    <div class="col-6 col-md-2">
      <input type="text" name="cuisine" class="form-control" placeholder="Cuisine type" value="{{ request('cuisine') }}">
    </div>

    {{-- Price Range (avg_price) --}}
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
    @forelse($restaurants as $restaurant)
    <div class="col-md-6 col-lg-4">
      <div class="card border-0 shadow-sm h-100" style="border-radius:var(--radius)">
        <div class="card-img-wrap"><img src="{{ \App\Helpers\Helpers::imageUrl($restaurant->image_url) }}" alt="{{ $restaurant->name }}" loading="lazy"><div class="card-category-badge" style="background:var(--restaurant)">{{ $restaurant->cuisine_type ?? 'Cuisine' }}</div></div>
        <div class="card-body">
          <h5 class="card-title fw-bold text-blue mb-1">{{ $restaurant->name }}</h5>
          <div class="d-flex align-items-center gap-2 mb-2"><div class="star-rating small">@for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$restaurant->avg_rating?'-fill':'' }}"></i>@endfor</div><small class="text-muted">({{ number_format($restaurant->avg_rating, 1) }})</small></div>
          <p class="text-muted small mb-2 text-truncate-2">{{ Str::limit($restaurant->description, 80) }}</p>
          <div class="d-flex justify-content-between">
            <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $restaurant->opening_hours ?? 'Hours vary' }}</small>
            <a href="{{ route('restaurants.detail', $restaurant->id) }}" class="btn btn-sm btn-restaurant">View Menu</a>
          </div>
        </div>
      </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted"><i class="bi bi-cup-hot fs-1 d-block mb-3"></i><p>No restaurants found. <a href="{{ route('restaurants.list_restaurants') }}">Clear filters</a></p></div>
    @endforelse
  </div>
  <div class="d-flex justify-content-center mt-4">{{ $restaurants->links() }}</div>
</div>
@endsection