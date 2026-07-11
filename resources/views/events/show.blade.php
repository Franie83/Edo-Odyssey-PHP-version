@extends('layouts.app')
@section('title', $event->name . ' – Edo Odyssey')
@section('content')
<div class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('main.home') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ route('events.list_events') }}">Events</a></li><li class="breadcrumb-item active">{{ $event->name }}</li></ol></nav>
    <h1>{{ $event->name }}</h1>
    <div class="d-flex gap-3 flex-wrap mt-2">
      <span class="text-white-50 small"><i class="bi bi-calendar me-1"></i>{{ $event->start_date?->format('d M Y') ?? 'TBD' }}</span>
      <span class="text-white-50 small"><i class="bi bi-geo-alt me-1"></i>{{ $event->location ?? 'TBD' }}</span>
      @if($event->category)<span class="badge bg-gold text-dark">{{ $event->category }}</span>@endif
    </div>
  </div>
</div>
<div class="container py-4">
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="detail-image mb-4"><img src="{{ \App\Helpers\Helpers::imageUrl($event->image_url) }}" alt="{{ $event->name }}"></div>

      @if($event->start_date && $event->start_date->isFuture())
      <div class="bg-blue-900 rounded p-3 mb-4 text-center">
        <p class="text-gold small fw-semibold mb-2">Event starts in:</p>
        <div class="d-flex justify-content-center gap-2" data-countdown="{{ $event->start_date->toIso8601String() }}">
          <div class="countdown-box text-center"><div class="countdown-num">--</div><div class="countdown-label">Days</div></div>
          <div class="countdown-sep text-gold fw-bold">:</div>
          <div class="countdown-box text-center"><div class="countdown-num">--</div><div class="countdown-label">Hrs</div></div>
          <div class="countdown-sep text-gold fw-bold">:</div>
          <div class="countdown-box text-center"><div class="countdown-num">--</div><div class="countdown-label">Min</div></div>
          <div class="countdown-sep text-gold fw-bold">:</div>
          <div class="countdown-box text-center"><div class="countdown-num">--</div><div class="countdown-label">Sec</div></div>
        </div>
      </div>
      @endif

      <h3 class="text-blue fw-bold">About this Event</h3>
      <p class="text-muted">{{ $event->description }}</p>
    </div>
    <div class="col-lg-4">
      <div class="info-card mb-3">
        <h5 class="fw-bold text-blue mb-3">Event Details</h5>
        @if($event->start_date)<p class="small mb-1"><i class="bi bi-calendar me-2 text-gold"></i>{{ $event->start_date->format('d M Y') }}</p>@endif
        @if($event->end_date)<p class="small mb-1"><i class="bi bi-calendar-check me-2 text-gold"></i>Ends: {{ $event->end_date->format('d M Y') }}</p>@endif
        @if($event->location)<p class="small mb-1"><i class="bi bi-geo-alt me-2 text-gold"></i>{{ $event->location }}</p>@endif
        @if($event->organizer)<p class="small mb-1"><i class="bi bi-person me-2 text-gold"></i>{{ $event->organizer }}</p>@endif
        @if($event->capacity)<p class="small mb-1"><i class="bi bi-people me-2 text-gold"></i>Capacity: {{ number_format($event->capacity) }}</p>@endif
        <hr>
        <div class="d-flex justify-content-between align-items-center">
          <span class="fw-semibold">Entry Fee</span>
          <span class="badge bg-gold text-dark fs-6">{{ $event->ticket_price == 0 ? 'Free' : '₦'.number_format($event->ticket_price) }}</span>
        </div>
      </div>
      <a href="{{ route('bookings.new_booking', ['type' => 'Event', 'id' => $event->id]) }}" class="btn btn-gold w-100 mb-3"><i class="bi bi-ticket me-2"></i>Book Ticket</a>
    </div>
  </div>
</div>
@endsection
