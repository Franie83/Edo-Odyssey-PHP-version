@extends('layouts.app')
@section('title', 'Book ' . $target_name . ' – Edo Odyssey')
@section('content')
<div class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('main.home') }}">Home</a></li><li class="breadcrumb-item active">New Booking</li></ol></nav>
    <h1>Book: {{ $target_name }}</h1>
    <p class="text-white-50">{{ $booking_type }} booking</p>
  </div>
</div>
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-7">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          @if($errors->any())
          <div class="alert alert-danger py-2"><ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
          @endif

          <form method="POST" action="{{ route('bookings.store_booking', ['type' => $booking_type, 'id' => $target_id]) }}">
            @csrf
            <div class="mb-3">
              <label class="form-label fw-semibold">Start Date *</label>
              <input type="date" name="start_date" class="form-control" min="{{ now()->addDay()->format('Y-m-d') }}" value="{{ old('start_date') }}" required>
            </div>
            @if(in_array($booking_type, ['Hotel', 'Guide']))
            <div class="mb-3">
              <label class="form-label fw-semibold">End Date</label>
              <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
            </div>
            @endif
            <div class="mb-3">
              <label class="form-label fw-semibold">Number of Guests *</label>
              <input type="number" name="guests" class="form-control" min="1" max="50" value="{{ old('guests', 1) }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Special Requests</label>
              <textarea name="special_requests" class="form-control" rows="3" placeholder="Any special requirements?">{{ old('special_requests') }}</textarea>
            </div>

            <div class="info-card mb-4">
              <h6 class="fw-bold text-blue">Pricing Info</h6>
              <p class="small text-muted mb-1">Base price per person: <strong class="text-gold">₦{{ number_format($base_price) }}</strong></p>
              <p class="small text-muted mb-0">Total will be calculated based on guests and duration.</p>
            </div>

            <button type="submit" class="btn btn-gold w-100 py-2 fw-semibold">
              <i class="bi bi-calendar-check me-2"></i>Confirm Booking
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
