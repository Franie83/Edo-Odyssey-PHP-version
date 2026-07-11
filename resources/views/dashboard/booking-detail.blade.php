@extends('layouts.app')
@section('title', 'Booking ' . $booking->reference_code . ' – Edo Odyssey')
@section('content')
<div class="page-hero py-4">
  <div class="container">
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('dashboard.index') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
      <div>
        <h1 class="mb-0" style="font-size:1.4rem">Booking {{ $booking->reference_code }}</h1>
        <p class="text-white-50 mb-0 small">{{ $booking->booking_type }} · Created {{ $booking->created_at->format('d M Y') }}</p>
      </div>
    </div>
  </div>
</div>

<div class="container py-4">
  <div class="row g-4">
    {{-- Main booking details --}}
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-3 pb-2 d-flex align-items-center justify-content-between">
          <h5 class="mb-0 fw-bold text-blue">Booking Details</h5>
          @php
            $statusColors = [
              'Pending'        => 'warning',
              'AdminApproved'  => 'info',
              'AdminRejected'  => 'danger',
              'VendorAccepted' => 'primary',
              'VendorRejected' => 'danger',
              'Completed'      => 'success',
              'Cancelled'      => 'secondary',
            ];
            $color = $statusColors[$booking->booking_status] ?? 'secondary';
          @endphp
          <span class="badge bg-{{ $color }} px-3 py-2">{{ $booking->booking_status }}</span>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-6">
              <div class="p-3 bg-light rounded">
                <div class="small text-muted fw-semibold mb-1">Reference</div>
                <div class="fw-bold">{{ $booking->reference_code }}</div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="p-3 bg-light rounded">
                <div class="small text-muted fw-semibold mb-1">Booking Type</div>
                <div class="fw-bold">{{ $booking->booking_type }}</div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="p-3 bg-light rounded">
                <div class="small text-muted fw-semibold mb-1">Service / Destination</div>
                <div class="fw-bold">{{ $booking->target_name }}</div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="p-3 bg-light rounded">
                <div class="small text-muted fw-semibold mb-1">Guests</div>
                <div class="fw-bold">{{ $booking->guests }} {{ Str::plural('guest', $booking->guests) }}</div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="p-3 bg-light rounded">
                <div class="small text-muted fw-semibold mb-1">Check-in / Start Date</div>
                <div class="fw-bold">{{ $booking->start_date?->format('d M Y') ?? '—' }}</div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="p-3 bg-light rounded">
                <div class="small text-muted fw-semibold mb-1">Check-out / End Date</div>
                <div class="fw-bold">{{ $booking->end_date?->format('d M Y') ?? '—' }}</div>
              </div>
            </div>
            <div class="col-12">
              <div class="p-3 bg-light rounded">
                <div class="small text-muted fw-semibold mb-1">Total Amount</div>
                <div class="fw-bold text-gold" style="font-size:1.3rem">₦{{ number_format($booking->total_price, 2) }}</div>
              </div>
            </div>
            @if($booking->special_requests)
            <div class="col-12">
              <div class="p-3 bg-light rounded">
                <div class="small text-muted fw-semibold mb-1">Special Requests</div>
                <div>{{ $booking->special_requests }}</div>
              </div>
            </div>
            @endif
            @if($booking->admin_comment)
            <div class="col-12">
              <div class="p-3 border border-info rounded">
                <div class="small text-muted fw-semibold mb-1"><i class="bi bi-shield-check text-info me-1"></i>Admin Comment</div>
                <div>{{ $booking->admin_comment }}</div>
              </div>
            </div>
            @endif
            @if($booking->vendor_comment)
            <div class="col-12">
              <div class="p-3 border border-success rounded">
                <div class="small text-muted fw-semibold mb-1"><i class="bi bi-chat-dots text-success me-1"></i>Vendor Comment</div>
                <div>{{ $booking->vendor_comment }}</div>
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>

      {{-- Heritage points notice --}}
      @if($booking->booking_status === 'Completed' && $booking->points_awarded)
      <div class="alert alert-success d-flex align-items-center gap-2">
        <i class="bi bi-trophy-fill text-gold fs-4"></i>
        <div>
          <strong>Heritage Points Awarded!</strong><br>
          <span class="small">You earned <strong>30 Heritage Points</strong> for completing this tour.</span>
        </div>
      </div>
      @endif
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
      {{-- Vendor Actions --}}
      @if($booking->booking_status === 'AdminApproved' && $booking->isVendor())
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-3 pb-2">
          <h6 class="mb-0 fw-bold text-blue">Vendor Response</h6>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('bookings.target_confirm_booking', $booking->id) }}" class="mb-2">
            @csrf
            <div class="mb-2">
              <label class="form-label small">Comment (optional)</label>
              <input type="text" name="vendor_comment" class="form-control form-control-sm" placeholder="Add a note">
            </div>
            <button class="btn btn-success btn-sm w-100"><i class="bi bi-check-lg me-1"></i>Accept Booking</button>
          </form>
          <form method="POST" action="{{ route('bookings.target_reject_booking', $booking->id) }}">
            @csrf
            <div class="mb-2">
              <label class="form-label small">Reason (optional)</label>
              <input type="text" name="vendor_comment" class="form-control form-control-sm" placeholder="Why reject?">
            </div>
            <button class="btn btn-danger btn-sm w-100"><i class="bi bi-x-lg me-1"></i>Reject Booking</button>
          </form>
        </div>
      </div>
      @endif

      {{-- Cancel Action for client --}}
      @if(in_array($booking->booking_status, ['Pending', 'AdminApproved']) && $booking->user_id == Auth::id())
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-3 pb-2">
          <h6 class="mb-0 fw-bold text-blue">Actions</h6>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('bookings.cancel_booking', $booking->id) }}" onsubmit="return confirm('Cancel this booking?')">
            @csrf
            <button class="btn btn-outline-danger w-100"><i class="bi bi-x-circle me-1"></i>Cancel Booking</button>
          </form>
        </div>
      </div>
      @endif

      {{-- Status History --}}
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-3 pb-2">
          <h6 class="mb-0 fw-bold text-blue">Approval Flow</h6>
        </div>
        <div class="card-body p-3">
          @if($booking->histories->count())
            <div class="timeline">
              @foreach($booking->histories as $history)
                <div class="d-flex gap-2 mb-3">
                  <div class="flex-shrink-0">
                    <div class="rounded-circle d-flex align-items-center justify-content-center 
                         bg-{{ $history->statusColor() }} text-white" 
                         style="width:32px;height:32px;font-size:.7rem">
                      <i class="bi bi-{{ $history->statusIcon() }}"></i>
                    </div>
                  </div>
                  <div>
                    <div class="fw-semibold small">{{ $history->status }}</div>
                    @if($history->comment)
                      <div class="text-muted small">{{ $history->comment }}</div>
                    @endif
                    <div class="text-muted" style="font-size:.65rem">
                      {{ $history->created_at->format('d M Y H:i') }} 
                      @if($history->user)
                        by {{ $history->user->full_name }}
                      @endif
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <p class="text-muted small">No status history yet.</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection