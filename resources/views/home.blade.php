@extends('layouts.app')
@section('title', 'Edo Odyssey – Cradle of Black Civilization')
@section('content')

<!-- ── HERO ─────────────────────────────────────────────────── -->
<section class="hero-section">
  <div class="hero-bg" style="background-image:url('https://images.unsplash.com/photo-1580654712603-eb43273aff33?auto=format&fit=crop&q=80&w=1600')"></div>
  <div class="hero-overlay"></div>
  <div class="container hero-content py-5">
    <div class="row align-items-center min-vh-75">
      <div class="col-lg-7">
        <div class="hero-badge"><i class="bi bi-award-fill me-1"></i>Official Edo State Tourism Agency</div>
        <h1 class="hero-title mb-3">
          <span class="accent">Edo State</span> – Cradle of Black Civilization
        </h1>
        <p class="hero-sub mb-4">Welcome to the official digital gateway of the Edo State Tourism Agency (EDSTA). Explore ancient royal palaces, watch world-famous bronze casters at work, track wildlife in tropical rainforests, and book certified local tour guides—all in one place.</p>
        <div class="d-flex flex-wrap gap-3 mb-5">
          <a href="{{ route('attractions.list_attractions') }}" class="btn btn-gold btn-lg px-4"><i class="bi bi-compass me-2"></i>Explore Now</a>
          <a href="{{ route('auth.register') }}" class="btn btn-outline-light btn-lg px-4"><i class="bi bi-person-plus me-2"></i>Join EDSTA</a>
        </div>
        <div class="hero-stats">
          <div class="text-center">
            <div class="hero-stat-num">{{ $total_attractions }}+</div>
            <div class="hero-stat-label">Attractions</div>
          </div>
          <div class="text-center">
            <div class="hero-stat-num">{{ $total_guides }}+</div>
            <div class="hero-stat-label">Guides</div>
          </div>
          <div class="text-center">
            <div class="hero-stat-num">{{ $total_hotels }}+</div>
            <div class="hero-stat-label">Hotels</div>
          </div>
          <div class="text-center">
            <div class="hero-stat-num">600+</div>
            <div class="hero-stat-label">Years History</div>
          </div>
        </div>
      </div>
      <div class="col-lg-5 d-none d-lg-flex justify-content-end">
        <div class="quick-login-banner">
          <p class="text-gold fw-bold mb-2 small text-uppercase letter-spacing"><i class="bi bi-lightning-fill me-1"></i>Quick Demo Login</p>
          <div class="d-flex flex-column gap-2">
            <a href="{{ route('auth.quick_login', 'superadmin') }}" class="btn btn-sm btn-gold w-100 text-start"><i class="bi bi-shield-star me-2"></i>Super Admin — Full Access</a>
            <a href="{{ route('auth.quick_login', 'admin') }}" class="btn btn-sm btn-outline-gold w-100 text-start"><i class="bi bi-shield-check me-2"></i>Agency Admin</a>
            <a href="{{ route('auth.quick_login', 'tourist') }}" class="btn btn-sm btn-outline-light w-100 text-start"><i class="bi bi-person me-2"></i>Tourist — Akenzua Musa</a>
            <a href="{{ route('auth.quick_login', 'guide') }}" class="btn btn-sm btn-outline-light w-100 text-start"><i class="bi bi-person-badge me-2"></i>Guide — Osaro Edokpayi</a>
            <a href="{{ route('auth.quick_login', 'hotel') }}" class="btn btn-sm btn-outline-light w-100 text-start"><i class="bi bi-building me-2"></i>Hotel Manager</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── CATEGORIES ─────────────────────────────────────────────── -->
<section class="py-5 bg-blue-100">
  <div class="container">
    <div class="row justify-content-center mb-4">
      <div class="col-lg-6 text-center">
        <div class="section-badge"><i class="bi bi-grid me-1"></i>Explore by Category</div>
        <h2 class="section-title">Discover Edo's Wonders</h2>
        <div class="section-divider"></div>
      </div>
    </div>
    <div class="row g-3 justify-content-center">
      @foreach($categories as $cat)
      <div class="col-6 col-md-3 col-lg-2">
        <a href="{{ route('attractions.list_attractions') }}?category={{ $cat->id }}" class="text-decoration-none">
          <div class="text-center p-3 bg-white rounded-3 shadow-sm h-100">
            <div class="mb-2" style="font-size:2rem;color:{{ $cat->color ?? '#1a3a6b' }}"><i class="bi {{ $cat->icon ?? 'bi-building-fill' }}"></i></div>
            <div class="fw-semibold small text-dark">{{ $cat->name }}</div>
          </div>
        </a>
      </div>
      @endforeach
      <div class="col-6 col-md-3 col-lg-2">
        <a href="{{ route('guides.list_guides') }}" class="text-decoration-none">
          <div class="text-center p-3 bg-white rounded-3 shadow-sm h-100">
            <div class="mb-2" style="font-size:2rem;color:#c9a227"><i class="bi bi-person-badge"></i></div>
            <div class="fw-semibold small text-dark">Tour Guides</div>
          </div>
        </a>
      </div>
      <div class="col-6 col-md-3 col-lg-2">
        <a href="{{ route('hotels.list_hotels') }}" class="text-decoration-none">
          <div class="text-center p-3 bg-white rounded-3 shadow-sm h-100">
            <div class="mb-2" style="font-size:2rem;color:#1a3a6b"><i class="bi bi-building-fill"></i></div>
            <div class="fw-semibold small text-dark">Hotels</div>
          </div>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ── FEATURED ATTRACTIONS ───────────────────────────────── -->
<section class="py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <div class="section-badge"><i class="bi bi-star-fill me-1"></i>Featured</div>
        <h2 class="section-title mb-0">Top Attractions</h2>
        <div class="section-divider left"></div>
      </div>
      <a href="{{ route('attractions.list_attractions') }}" class="btn btn-outline-secondary btn-sm">View All <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
    <div class="row g-4">
      @forelse($featured_attractions as $attraction)
      <div class="col-md-6 col-lg-4">
        <div class="attraction-card card">
          <div class="card-img-wrap">
            <img src="{{ \App\Helpers\Helpers::imageUrl($attraction->image_url) }}" alt="{{ $attraction->name }}" loading="lazy">
            <div class="card-category-badge">{{ $attraction->category?->name ?? 'Attraction' }}</div>
            <div class="card-price-badge">{{ $attraction->ticket_price == 0 ? 'Free' : '₦' . number_format($attraction->ticket_price) }}</div>
          </div>
          <div class="card-body">
            <h5 class="card-title fw-bold text-blue mb-1" style="font-size:.95rem">{{ $attraction->name }}</h5>
            <div class="d-flex align-items-center gap-2 mb-2">
              <div class="star-rating">
                @for($i = 1; $i <= 5; $i++)
                  <i class="bi bi-star{{ $i <= $attraction->avg_rating ? '-fill' : '' }}"></i>
                @endfor
              </div>
              <small class="text-muted">({{ $attraction->avg_rating }})</small>
            </div>
            <p class="card-text text-muted text-truncate-2 small mb-3">{{ Str::limit($attraction->description, 100) }}</p>
            <div class="d-flex align-items-center justify-content-between">
              <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ Str::limit($attraction->address, 30) }}</small>
              <a href="{{ route('attractions.detail', $attraction->id) }}" class="btn btn-blue btn-sm">Explore</a>
            </div>
          </div>
        </div>
      </div>
      @empty
      <div class="col-12 text-center py-5 text-muted">
        <i class="bi bi-building fs-1 d-block mb-3"></i>
        <p class="mb-2">No featured attractions yet.</p>
        @auth @if(Auth::user()->is_admin)<a href="{{ route('admin.attractions') }}" class="btn btn-gold btn-sm">Go to Admin</a>@endif @endauth
      </div>
      @endforelse
    </div>
  </div>
</section>

<!-- ── FEATURED GUIDES ─────────────────────────────────────── -->
<section class="py-5 bg-blue-100">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <div class="section-badge"><i class="bi bi-person-badge me-1"></i>Certified</div>
        <h2 class="section-title mb-0">Expert Tour Guides</h2>
        <div class="section-divider left"></div>
      </div>
      <a href="{{ route('guides.list_guides') }}" class="btn btn-outline-secondary btn-sm">All Guides <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
    <div class="row g-4">
      @foreach($featured_guides as $guide)
      <div class="col-md-6 col-lg-3">
        <div class="guide-card card p-3 text-center h-100">
          <div class="mb-3">
            @if($guide->user?->avatar_url)
            <img src="{{ \App\Helpers\Helpers::imageUrl($guide->user->avatar_url) }}" class="guide-avatar mx-auto" alt="{{ $guide->user->full_name }}">
            @else
            <div class="guide-avatar-placeholder mx-auto"><i class="bi bi-person-fill"></i></div>
            @endif
          </div>
          <h6 class="fw-bold text-blue">{{ $guide->user?->full_name ?? 'Tour Guide' }}</h6>
          <div class="star-rating mb-1">
            @for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i <= $guide->avg_rating ? '-fill' : '' }}"></i>@endfor
          </div>
          <p class="text-muted small mb-2">{{ $guide->experience ?? 0 }} yrs experience</p>
          <div class="d-flex flex-wrap justify-content-center gap-1 mb-3">
            @if($guide->languages)
              @foreach(explode(',', $guide->languages) as $lang)
              <span class="lang-badge">{{ trim($lang) }}</span>
              @endforeach
            @endif
          </div>
          <div class="d-flex justify-content-between align-items-center mt-auto">
            <div class="text-gold fw-bold">₦{{ number_format($guide->hourly_rate ?? 0) }}/hr</div>
            <a href="{{ route('bookings.new_booking', ['type' => 'Guide', 'id' => $guide->id]) }}" class="btn btn-blue btn-sm">Book</a>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- ── UPCOMING EVENTS ─────────────────────────────────────── -->
<section class="py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <div class="section-badge"><i class="bi bi-calendar-event me-1"></i>Coming Up</div>
        <h2 class="section-title mb-0">Upcoming Events</h2>
        <div class="section-divider left"></div>
      </div>
      <a href="{{ route('events.list_events') }}" class="btn btn-outline-secondary btn-sm">All Events <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
    <div class="row g-4">
      @forelse($upcoming_events as $event)
      <div class="col-md-6 col-lg-4">
        <div class="event-card card">
          <div class="card-img-wrap" style="height:200px">
            <img src="{{ \App\Helpers\Helpers::imageUrl($event->image_url) }}" alt="{{ $event->name }}" loading="lazy">
            <div class="card-category-badge">{{ $event->category ?? 'Event' }}</div>
            <div class="card-price-badge">{{ $event->ticket_price == 0 ? 'Free' : '₦' . number_format($event->ticket_price) }}</div>
          </div>
          <div class="card-body">
            <h5 class="card-title fw-bold text-blue" style="font-size:.9rem">{{ $event->name }}</h5>
            <p class="text-muted small mb-2"><i class="bi bi-calendar me-1"></i>{{ $event->start_date?->format('d M Y') ?? 'TBD' }} • <i class="bi bi-geo-alt ms-1 me-1"></i>{{ $event->location ?? 'TBD' }}</p>
            <div class="d-flex gap-2 mb-2" data-countdown="{{ $event->start_date?->toIso8601String() }}">
              <div class="countdown-box text-center"><div class="countdown-num">--</div></div>
              <div class="countdown-sep text-gold fw-bold">:</div>
              <div class="countdown-box text-center"><div class="countdown-num">--</div></div>
              <div class="countdown-sep text-gold fw-bold">:</div>
              <div class="countdown-box text-center"><div class="countdown-num">--</div></div>
              <div class="countdown-sep text-gold fw-bold">:</div>
              <div class="countdown-box text-center"><div class="countdown-num">--</div></div>
            </div>
            <a href="{{ route('events.detail', $event->id) }}" class="btn btn-blue btn-sm w-100">View Event</a>
          </div>
        </div>
      </div>
      @empty
      <div class="col-12 text-center py-5 text-muted">
        <i class="bi bi-calendar-event fs-1 d-block mb-3"></i>
        <p>No upcoming events. Check back later.</p>
      </div>
      @endforelse
    </div>
  </div>
</section>

<!-- ── HOTELS ─────────────────────────────────────────────── -->
<section class="py-5" style="background:linear-gradient(135deg,var(--blue-900) 0%,var(--blue-800) 100%)">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <div class="section-badge" style="background:rgba(201,162,39,.15);color:var(--gold-400)"><i class="bi bi-building me-1"></i>Accommodations</div>
        <h2 class="section-title mb-0 text-white">Where to Stay</h2>
        <div class="section-divider left"></div>
      </div>
      <a href="{{ route('hotels.list_hotels') }}" class="btn btn-outline-gold btn-sm">All Hotels</a>
    </div>
    <div class="row g-4">
      @forelse($featured_hotels as $hotel)
      <div class="col-md-4">
        <div class="hotel-card card">
          <div class="card-img-wrap" style="height:200px">
            <img src="{{ \App\Helpers\Helpers::imageUrl($hotel->image_url) }}" alt="{{ $hotel->name }}" loading="lazy">
            <div class="card-price-badge">₦{{ number_format($hotel->price_per_night) }}/night</div>
          </div>
          <div class="card-body">
            <h5 class="card-title fw-bold text-blue" style="font-size:.9rem">{{ $hotel->name }}</h5>
            <div class="text-gold mb-1">@for($i=0;$i<($hotel->stars??3);$i++)<i class="bi bi-star-fill" style="font-size:.75rem"></i>@endfor</div>
            <p class="text-muted small mb-2 text-truncate-2">{{ Str::limit($hotel->description, 80) }}</p>
            <div class="d-flex justify-content-between">
              <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ Str::limit($hotel->address, 20) }}</small>
              <a href="{{ route('hotels.detail', $hotel->id) }}" class="btn btn-blue btn-sm">View</a>
            </div>
          </div>
        </div>
      </div>
      @empty
      <div class="col-12 text-center py-5 text-white-50">
        <i class="bi bi-building fs-1 d-block mb-3"></i><p>No featured hotels yet.</p>
      </div>
      @endforelse
    </div>
  </div>
</section>

<!-- ── NEWS ───────────────────────────────────────────────── -->
<section class="py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <div class="section-badge"><i class="bi bi-newspaper me-1"></i>Latest</div>
        <h2 class="section-title mb-0">Tourism News</h2>
        <div class="section-divider left"></div>
      </div>
      <a href="{{ route('news.list_news') }}" class="btn btn-outline-secondary btn-sm">All News</a>
    </div>
    <div class="row g-4">
      @if($recent_news->count())
      <div class="col-lg-6">
        @php $article = $recent_news->first(); @endphp
        <div class="news-card card h-100">
          <div class="card-img-wrap" style="height:280px">
            <img src="{{ \App\Helpers\Helpers::imageUrl($article->image_url) }}" alt="{{ $article->title }}" loading="lazy">
            @if($article->is_featured)<div class="card-category-badge"><i class="bi bi-star-fill me-1"></i>Featured</div>@endif
          </div>
          <div class="card-body">
            <span class="badge bg-blue-100 text-blue mb-2">{{ $article->category ?? 'News' }}</span>
            <h4 class="card-title fw-bold text-blue">{{ $article->title }}</h4>
            <p class="text-muted">{{ Str::limit(strip_tags($article->content), 150) }}</p>
            <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted">By {{ $article->author ?? 'EDSTA' }} · {{ $article->created_at?->format('d M Y') }}</small>
              <a href="{{ route('news.detail', $article->id) }}" class="btn btn-blue btn-sm">Read More</a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="d-flex flex-column gap-3 h-100">
          @foreach($recent_news->slice(1, 3) as $article)
          <div class="news-card card">
            <div class="row g-0">
              <div class="col-4">
                <img src="{{ \App\Helpers\Helpers::imageUrl($article->image_url) }}" class="w-100 h-100 object-fit-cover rounded-start" alt="{{ $article->title }}" style="min-height:100px">
              </div>
              <div class="col-8">
                <div class="card-body py-2">
                  <span class="badge bg-blue-100 text-blue" style="font-size:.65rem">{{ $article->category ?? 'News' }}</span>
                  <h6 class="card-title fw-bold text-blue mt-1 text-truncate-2" style="font-size:.85rem">{{ $article->title }}</h6>
                  <small class="text-muted">{{ $article->created_at?->format('d M Y') }}</small><br>
                  <a href="{{ route('news.detail', $article->id) }}" class="btn btn-blue btn-sm mt-1 py-0">Read</a>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
      @else
      <div class="col-12 text-center py-5 text-muted">
        <i class="bi bi-newspaper fs-1 d-block mb-3"></i><p>No news articles yet.</p>
      </div>
      @endif
    </div>
  </div>
</section>

<!-- ── CTA ─────────────────────────────────────────────────── -->
<section class="py-5" style="background:linear-gradient(135deg,var(--gold-600),var(--gold-500))">
  <div class="container text-center">
    <h2 class="fw-bold text-white mb-2" style="font-family:'Playfair Display',serif;font-size:2rem">Ready to Explore Edo State?</h2>
    <p class="text-white mb-4 opacity-90">Join thousands of travellers discovering the Cradle of Black Civilization.</p>
    <div class="d-flex gap-3 justify-content-center">
      <a href="{{ route('auth.register') }}" class="btn btn-dark btn-lg px-4 fw-semibold">Create Free Account</a>
      <a href="{{ route('attractions.list_attractions') }}" class="btn btn-outline-dark btn-lg px-4">Browse Attractions</a>
    </div>
  </div>
</section>
@endsection
