@extends('layouts.app')
@section('title', 'Search: ' . $query . ' – Edo Odyssey')
@section('content')
<div class="page-hero">
  <div class="container">
    <h1>Search Results for "{{ $query }}"</h1>
    <p class="text-white-50">Showing results across all categories</p>
  </div>
</div>
<div class="container py-4">
  <form class="filter-bar row g-2 mb-4" method="GET">
    <div class="col-md-8"><input type="search" name="q" class="form-control" placeholder="Search Edo Odyssey…" value="{{ $query }}"></div>
    <div class="col-md-2"><button class="btn btn-blue w-100"><i class="bi bi-search me-1"></i>Search</button></div>
  </form>

  @php $totalFound = $attractions->count() + $hotels->count() + $restaurants->count() + $events->count() + $news->count() + $guides->count(); @endphp
  <p class="text-muted mb-4">Found {{ $totalFound }} result{{ $totalFound != 1 ? 's' : '' }} for "{{ $query }}"</p>

  @if($attractions->count())
  <h4 class="fw-bold text-blue mb-3"><i class="bi bi-building me-2 text-gold"></i>Attractions</h4>
  <div class="row g-3 mb-4">
    @foreach($attractions as $a)
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="row g-0">
          <div class="col-4"><img src="{{ \App\Helpers\Helpers::imageUrl($a->image_url) }}" class="w-100 h-100 rounded-start" style="object-fit:cover;min-height:80px" alt=""></div>
          <div class="col-8 card-body py-2">
            <h6 class="fw-bold text-blue mb-1 small">{{ $a->name }}</h6>
            <small class="text-muted d-block">{{ Str::limit($a->address, 30) }}</small>
            <a href="{{ route('attractions.detail', $a->id) }}" class="btn btn-blue btn-sm mt-1 py-0">View</a>
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @endif

  @if($hotels->count())
  <h4 class="fw-bold text-blue mb-3"><i class="bi bi-building-fill me-2 text-gold"></i>Hotels</h4>
  <div class="row g-3 mb-4">
    @foreach($hotels as $h)
    <div class="col-md-4">
      <div class="card border-0 shadow-sm"><div class="card-body py-2">
        <h6 class="fw-bold text-blue mb-1 small">{{ $h->name }}</h6>
        <small class="text-muted">₦{{ number_format($h->price_per_night) }}/night · {{ $h->city }}</small><br>
        <a href="{{ route('hotels.detail', $h->id) }}" class="btn btn-blue btn-sm mt-1 py-0">View</a>
      </div></div>
    </div>
    @endforeach
  </div>
  @endif

  @if($events->count())
  <h4 class="fw-bold text-blue mb-3"><i class="bi bi-calendar-event me-2 text-gold"></i>Events</h4>
  <div class="row g-3 mb-4">
    @foreach($events as $e)
    <div class="col-md-4">
      <div class="card border-0 shadow-sm"><div class="card-body py-2">
        <h6 class="fw-bold text-blue mb-1 small">{{ $e->name }}</h6>
        <small class="text-muted">{{ $e->start_date?->format('d M Y') }} · {{ $e->location }}</small><br>
        <a href="{{ route('events.detail', $e->id) }}" class="btn btn-blue btn-sm mt-1 py-0">View</a>
      </div></div>
    </div>
    @endforeach
  </div>
  @endif

  @if($news->count())
  <h4 class="fw-bold text-blue mb-3"><i class="bi bi-newspaper me-2 text-gold"></i>News</h4>
  <div class="row g-3 mb-4">
    @foreach($news as $n)
    <div class="col-md-4">
      <div class="card border-0 shadow-sm"><div class="card-body py-2">
        <h6 class="fw-bold text-blue mb-1 small">{{ $n->title }}</h6>
        <small class="text-muted">{{ $n->created_at?->format('d M Y') }}</small><br>
        <a href="{{ route('news.detail', $n->id) }}" class="btn btn-blue btn-sm mt-1 py-0">Read</a>
      </div></div>
    </div>
    @endforeach
  </div>
  @endif

  @if($totalFound === 0)
  <div class="text-center py-5 text-muted">
    <i class="bi bi-search fs-1 d-block mb-3"></i>
    <h4>No results found for "{{ $query }}"</h4>
    <p>Try different keywords or browse our categories.</p>
    <a href="{{ route('main.home') }}" class="btn btn-blue mt-2">Back to Home</a>
  </div>
  @endif
</div>
@endsection
