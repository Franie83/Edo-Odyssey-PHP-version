@extends('layouts.app')
@section('title', 'Attractions – Edo Odyssey')
@section('content')
<div class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('main.home') }}">Home</a></li><li class="breadcrumb-item active">Attractions</li></ol></nav>
    <h1><i class="bi bi-building me-2 text-gold"></i>Edo State Attractions</h1>
    <p class="text-white-50">Discover historic sites, cultural landmarks, and natural wonders</p>
  </div>
</div>
<div class="container py-4">
  <form class="filter-bar row g-2" method="GET">
    {{-- Search --}}
    <div class="col-12 col-md-4"><input type="search" name="q" class="form-control" placeholder="Search attractions…" value="{{ request('q') }}"></div>

    {{-- Category --}}
    <div class="col-6 col-md-2">
      <select name="category" class="form-select">
        <option value="">All Categories</option>
        @foreach($categories as $cat)
        <option value="{{ $cat->id }}" {{ request('category')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
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
    <div class="col-4 col-md-2">
      <select name="min_rating" class="form-select">
        <option value="">⭐ Any Rating</option>
        @for($i=1;$i<=5;$i++)
        <option value="{{ $i }}" {{ request('min_rating')==$i?'selected':'' }}>{{ $i }}★ &amp; up</option>
        @endfor
      </select>
    </div>

    {{-- Sort & Submit --}}
    <div class="col-6 col-md-2">
      <select name="sort" class="form-select">
        <option value="">Sort by</option>
        <option value="popular" {{ request('sort')=='popular'?'selected':'' }}>Most Popular</option>
        <option value="rating" {{ request('sort')=='rating'?'selected':'' }}>Highest Rated</option>
        <option value="price_asc" {{ request('sort')=='price_asc'?'selected':'' }}>Price: Low–High</option>
        <option value="price_desc" {{ request('sort')=='price_desc'?'selected':'' }}>Price: High–Low</option>
      </select>
    </div>
    <div class="col-6 col-md-2">
      <button class="btn btn-blue w-100" type="submit"><i class="bi bi-search me-1"></i>Filter</button>
    </div>
  </form>

  <div class="row g-4">
    @forelse($attractions as $attraction)
    <div class="col-md-6 col-lg-4">
      <div class="attraction-card card">
        <div class="card-img-wrap">
          <img src="{{ \App\Helpers\Helpers::imageUrl($attraction->image_url) }}" alt="{{ $attraction->name }}" loading="lazy">
          <div class="card-category-badge">{{ $attraction->category?->name ?? 'Attraction' }}</div>
          <div class="card-price-badge">{{ $attraction->ticket_price == 0 ? 'Free' : '₦'.number_format($attraction->ticket_price) }}</div>
        </div>
        <div class="card-body">
          <h5 class="card-title fw-bold text-blue mb-1">{{ $attraction->name }}</h5>
          <div class="d-flex align-items-center gap-2 mb-2">
            <div class="star-rating">@for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$attraction->avg_rating?'-fill':'' }}"></i>@endfor</div>
            <small class="text-muted">({{ number_format($attraction->avg_rating, 1) }})</small>
          </div>
          <p class="card-text text-muted text-truncate-2 small mb-3">{{ Str::limit($attraction->description, 100) }}</p>
          <div class="d-flex align-items-center justify-content-between">
            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ Str::limit($attraction->address ?? $attraction->city, 30) }}</small>
            <a href="{{ route('attractions.detail', $attraction->id) }}" class="btn btn-blue btn-sm">Explore</a>
          </div>
        </div>
      </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted">
      <i class="bi bi-search fs-1 d-block mb-3"></i>
      <p>No attractions found. <a href="{{ route('attractions.list_attractions') }}">Clear filters</a></p>
    </div>
    @endforelse
  </div>
  <div class="d-flex justify-content-center mt-4">{{ $attractions->links() }}</div>
</div>
@endsection