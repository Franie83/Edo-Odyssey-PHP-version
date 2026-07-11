@extends('layouts.admin')
@section('title', 'Booking ' . $booking->reference_code . ' – Admin')
@section('page_title', 'Booking Details')
@section('page_subtitle', 'Reference: ' . $booking->reference_code)
@section('content')

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-3 pb-2 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold text-blue">Booking Information</h5>
                @php
                    $statusColors = [
                        'Pending'        => 'warning',
                        'AdminApproved'  => 'info',
                        'AdminRejected'  => 'danger',
                        'VendorAccepted' => 'success',
                        'VendorRejected' => 'danger',
                        'Completed'      => 'secondary',
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
                            <div class="small text-muted fw-semibold mb-1">User</div>
                            <div class="fw-bold">{{ $booking->user?->full_name }}</div>
                            <div class="text-muted small">{{ $booking->user?->email }}</div>
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
                            <div class="fw-bold">{{ $booking->guests }}</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded">
                            <div class="small text-muted fw-semibold mb-1">Start Date</div>
                            <div class="fw-bold">{{ $booking->start_date?->format('d M Y') ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded">
                            <div class="small text-muted fw-semibold mb-1">End Date</div>
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
                </div>

                {{-- Comments from admin / vendor --}}
                <div class="row mt-3">
                    @if($booking->admin_comment)
                    <div class="col-md-6">
                        <div class="p-3 border border-info rounded">
                            <div class="small text-muted fw-semibold mb-1"><i class="bi bi-shield-check text-info me-1"></i>Admin Comment</div>
                            <div>{{ $booking->admin_comment }}</div>
                        </div>
                    </div>
                    @endif
                    @if($booking->vendor_comment)
                    <div class="col-md-6">
                        <div class="p-3 border border-success rounded">
                            <div class="small text-muted fw-semibold mb-1"><i class="bi bi-chat-dots text-success me-1"></i>Vendor Comment</div>
                            <div>{{ $booking->vendor_comment }}</div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Admin actions if pending --}}
                @if($booking->booking_status === 'Pending')
                <div class="mt-4 p-3 bg-light rounded border">
                    <h6 class="fw-bold text-blue">Admin Action</h6>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <form method="POST" action="{{ route('admin.booking_approve', $booking->id) }}">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label small">Comment (optional)</label>
                                    <input type="text" name="admin_comment" class="form-control form-control-sm" placeholder="Optional note">
                                </div>
                                <button class="btn btn-success btn-sm w-100"><i class="bi bi-check-lg me-1"></i>Approve</button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form method="POST" action="{{ route('admin.booking_reject', $booking->id) }}">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label small">Reason (optional)</label>
                                    <input type="text" name="admin_comment" class="form-control form-control-sm" placeholder="Reason for rejection">
                                </div>
                                <button class="btn btn-danger btn-sm w-100"><i class="bi bi-x-lg me-1"></i>Reject</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Sidebar: Status History --}}
    <div class="col-lg-4">
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

@endsection