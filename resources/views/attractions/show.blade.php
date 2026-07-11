@extends('layouts.app')
@section('title', $attraction->name . ' – Edo Odyssey')
@section('content')
<div class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('main.home') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ route('attractions.list_attractions') }}">Attractions</a></li><li class="breadcrumb-item active">{{ $attraction->name }}</li></ol></nav>
    <h1>{{ $attraction->name }}</h1>
    <div class="d-flex gap-3 flex-wrap mt-2">
      @if($attraction->category)<span class="badge bg-gold text-dark">{{ $attraction->category->name }}</span>@endif
      <span class="text-white-50 small"><i class="bi bi-geo-alt me-1"></i>{{ $attraction->address }}</span>
      <span class="text-white-50 small"><i class="bi bi-eye me-1"></i>{{ number_format($attraction->views) }} views</span>
    </div>
  </div>
</div>

<div class="container py-4">
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="detail-image mb-4">
        <img src="{{ \App\Helpers\Helpers::imageUrl($attraction->image_url) }}" alt="{{ $attraction->name }}">
      </div>

      @if($attraction->gallery)
      <div class="row g-2 mb-4">
        @foreach(array_slice($attraction->gallery, 0, 4) as $img)
        <div class="col-3"><img src="{{ \App\Helpers\Helpers::imageUrl($img) }}" class="w-100 rounded" style="height:100px;object-fit:cover" alt="Gallery"></div>
        @endforeach
      </div>
      @endif

      <div class="mb-4">
        <h3 class="text-blue fw-bold">About this Attraction</h3>
        <p class="text-muted">{{ $attraction->description }}</p>
        @if($attraction->history)
        <h5 class="text-blue fw-bold mt-3">Historical Background</h5>
        <p class="text-muted">{{ $attraction->history }}</p>
        @endif
      </div>

      <!-- Reviews -->
      <div class="mb-4">
        <h4 class="fw-bold text-blue mb-3"><i class="bi bi-star me-2 text-gold"></i>Reviews ({{ count($reviews) }})</h4>
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
              @if($r->title)<p class="fw-semibold mb-1 small">{{ $r->title }}</p>@endif
              <p class="text-muted small mb-0">{{ $r->comment }}</p>
            </div>
          </div>
        </div>
        @empty
        <p class="text-muted">No reviews yet. Be the first!</p>
        @endforelse

        @auth
        <form method="POST" action="{{ route('reviews.store') }}" class="mt-4 p-3 bg-blue-100 rounded">
          @csrf
          <input type="hidden" name="target_type" value="Attraction">
          <input type="hidden" name="target_id" value="{{ $attraction->id }}">
          <h6 class="fw-bold text-blue mb-3">Write a Review</h6>
          <div class="mb-2">
            <div class="star-select d-flex gap-1 mb-2">
              <input type="hidden" name="rating" value="5">
              @for($i=1;$i<=5;$i++)<i class="bi bi-star-fill text-gold fs-5" data-star="{{ $i }}" style="cursor:pointer"></i>@endfor
            </div>
          </div>
          <input type="text" name="title" class="form-control mb-2 border-0" placeholder="Review title (optional)">
          <textarea name="comment" class="form-control border-0 mb-2" rows="3" placeholder="Share your experience…"></textarea>
          <button class="btn btn-gold btn-sm">Submit Review +10 pts</button>
        </form>
        @endauth
      </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
      <div class="info-card mb-3">
        <h5 class="fw-bold text-blue mb-3">Info</h5>
        @if($attraction->address)<p class="small mb-1"><i class="bi bi-geo-alt me-2 text-gold"></i>{{ $attraction->address }}</p>@endif
        @if($attraction->opening_hours)<p class="small mb-1"><i class="bi bi-clock me-2 text-gold"></i>{{ $attraction->opening_hours }}</p>@endif
        @if($attraction->phone)<p class="small mb-1"><i class="bi bi-telephone me-2 text-gold"></i>{{ $attraction->phone }}</p>@endif
        @if($attraction->email)<p class="small mb-1"><i class="bi bi-envelope me-2 text-gold"></i>{{ $attraction->email }}</p>@endif
        @if($attraction->website)<p class="small mb-1"><i class="bi bi-globe me-2 text-gold"></i><a href="{{ $attraction->website }}" target="_blank">Website</a></p>@endif
        <hr>
        <div class="d-flex justify-content-between align-items-center">
          <span class="fw-semibold">Entry Fee</span>
          <span class="badge bg-gold text-dark fs-6">{{ $attraction->ticket_price == 0 ? 'Free' : '₦'.number_format($attraction->ticket_price) }}</span>
        </div>
        <div class="d-flex align-items-center gap-1 mt-2">
          <div class="star-rating">@for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$avg_rating?'-fill':'' }}"></i>@endfor</div>
          <small class="text-muted">({{ $avg_rating }} / 5.0)</small>
        </div>
      </div>

      <a href="{{ route('bookings.new_booking', ['type' => 'Attraction', 'id' => $attraction->id]) }}" class="btn btn-gold w-100 mb-3">
        <i class="bi bi-calendar-check me-2"></i>Book a Visit
      </a>

      @auth
      <form method="POST" action="{{ route('dashboard.toggle_favourite', ['type' => 'Attraction', 'id' => $attraction->id]) }}" class="mb-3">
        @csrf
        <button class="btn {{ $is_fav ? 'btn-danger' : 'btn-outline-secondary' }} w-100">
          <i class="bi bi-heart{{ $is_fav ? '-fill' : '' }} me-1"></i>{{ $is_fav ? 'Remove from Favourites' : 'Add to Favourites' }}
        </button>
      </form>
      @endauth

      {{-- QR Code --}}
      @if($attraction->qrCode)
      <div class="sidebar-card text-center">
        <h6>QR Code</h6>
        @php
          // Use image_path if available, otherwise fallback to code
          $qrPath = $attraction->qrCode->image_path ?? 'qr_codes/' . $attraction->qrCode->code . '.svg';
        @endphp
        <img src="{{ asset('storage/' . $qrPath) }}" width="120" alt="QR Code" class="qr-frame">
        <p class="small text-muted mt-2">Scan to visit this attraction</p>
      </div>
      @endif

      @if($related->count())
      <div class="sidebar-card">
        <h6>Related Attractions</h6>
        @foreach($related as $r)
        <a href="{{ route('attractions.detail', $r->id) }}" class="d-flex gap-2 mb-2 text-decoration-none">
          <img src="{{ \App\Helpers\Helpers::imageUrl($r->image_url) }}" width="60" height="50" style="object-fit:cover;border-radius:6px" alt="">
          <div><div class="small fw-semibold text-blue">{{ $r->name }}</div><div class="text-muted" style="font-size:.7rem">{{ Str::limit($r->address, 30) }}</div></div>
        </a>
        @endforeach
      </div>
      @endif
    </div>
  </div>
</div>
@endsection