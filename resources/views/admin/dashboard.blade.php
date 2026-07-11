@extends('layouts.admin')
@section('title', 'Admin Dashboard – Edo Odyssey')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Overview of Edo Odyssey platform')
@section('content')

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
        <div class="stat-card"><div class="stat-number">{{ number_format($stats['total_users']) }}</div><div class="small text-muted">Total Users</div></div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card"><div class="stat-number">{{ number_format($stats['total_attractions']) }}</div><div class="small text-muted">Attractions</div></div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card"><div class="stat-number">{{ number_format($stats['total_bookings']) }}</div><div class="small text-muted">Bookings</div></div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card"><div class="stat-number">{{ number_format($stats['pending_bookings']) }}</div><div class="small text-muted">Pending Bookings</div></div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card"><div class="stat-number">{{ number_format($stats['total_guides']) }}</div><div class="small text-muted">Guides</div></div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card"><div class="stat-number">{{ number_format($stats['pending_guides']) }}</div><div class="small text-muted">Pending Guides</div></div>
    </div>
</div>

{{-- Pending Approvals Section --}}
@php
    $hasPending = ($stats['pending_hotels'] ?? 0) + ($stats['pending_restaurants'] ?? 0) + ($stats['pending_events'] ?? 0) + ($stats['pending_guides'] ?? 0);
@endphp

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3 pb-1">
                <h5 class="mb-0 fw-bold text-blue"><i class="bi bi-clock-history me-2 text-gold"></i>Pending Approvals</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- Guides --}}
                    <div class="col-md-3 col-6">
                        <div class="p-3 bg-light rounded-3 text-center">
                            <div class="display-6 fw-bold text-warning">{{ $stats['pending_guides'] ?? 0 }}</div>
                            <div class="text-muted small">Guides</div>
                            @if(($stats['pending_guides'] ?? 0) > 0)
                                <a href="{{ route('admin.guides', ['status' => 'Pending']) }}" class="btn btn-sm btn-outline-primary mt-1">Review</a>
                            @endif
                        </div>
                    </div>
                    {{-- Hotels --}}
                    <div class="col-md-3 col-6">
                        <div class="p-3 bg-light rounded-3 text-center">
                            <div class="display-6 fw-bold text-warning">{{ $stats['pending_hotels'] ?? 0 }}</div>
                            <div class="text-muted small">Hotels</div>
                            @if(($stats['pending_hotels'] ?? 0) > 0)
                                <a href="{{ route('admin.hotels', ['status' => 'inactive']) }}" class="btn btn-sm btn-outline-primary mt-1">Review</a>
                            @endif
                        </div>
                    </div>
                    {{-- Restaurants --}}
                    <div class="col-md-3 col-6">
                        <div class="p-3 bg-light rounded-3 text-center">
                            <div class="display-6 fw-bold text-warning">{{ $stats['pending_restaurants'] ?? 0 }}</div>
                            <div class="text-muted small">Restaurants</div>
                            @if(($stats['pending_restaurants'] ?? 0) > 0)
                                <a href="{{ route('admin.restaurants', ['status' => 'inactive']) }}" class="btn btn-sm btn-outline-primary mt-1">Review</a>
                            @endif
                        </div>
                    </div>
                    {{-- Events --}}
                    <div class="col-md-3 col-6">
                        <div class="p-3 bg-light rounded-3 text-center">
                            <div class="display-6 fw-bold text-warning">{{ $stats['pending_events'] ?? 0 }}</div>
                            <div class="text-muted small">Events</div>
                            @if(($stats['pending_events'] ?? 0) > 0)
                                <a href="{{ route('admin.events', ['status' => 'inactive']) }}" class="btn btn-sm btn-outline-primary mt-1">Review</a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Recent pending items list --}}
                @if($hasPending > 0)
                    <hr>
                    <h6 class="fw-bold">Recent pending requests</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>User</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($recent_pending))
                                    @foreach($recent_pending['guides'] as $guide)
                                        <tr>
                                            <td><span class="badge bg-info">Guide</span></td>
                                            <td>{{ $guide->user?->full_name }}</td>
                                            <td>{{ $guide->user?->email }}</td>
                                            <td>{{ $guide->created_at->format('d M Y') }}</td>
                                            <td><a href="{{ route('admin.guide_edit', $guide->id) }}" class="btn btn-sm btn-outline-primary">Review</a></td>
                                        </tr>
                                    @endforeach
                                    @foreach($recent_pending['hotels'] as $hotel)
                                        <tr>
                                            <td><span class="badge bg-primary">Hotel</span></td>
                                            <td>{{ $hotel->name }}</td>
                                            <td>{{ $hotel->user?->full_name }}</td>
                                            <td>{{ $hotel->created_at->format('d M Y') }}</td>
                                            <td><a href="{{ route('admin.hotel_edit', $hotel->id) }}" class="btn btn-sm btn-outline-primary">Review</a></td>
                                        </tr>
                                    @endforeach
                                    @foreach($recent_pending['restaurants'] as $restaurant)
                                        <tr>
                                            <td><span class="badge bg-success">Restaurant</span></td>
                                            <td>{{ $restaurant->name }}</td>
                                            <td>{{ $restaurant->user?->full_name }}</td>
                                            <td>{{ $restaurant->created_at->format('d M Y') }}</td>
                                            <td><a href="{{ route('admin.restaurant_edit', $restaurant->id) }}" class="btn btn-sm btn-outline-primary">Review</a></td>
                                        </tr>
                                    @endforeach
                                    @foreach($recent_pending['events'] as $event)
                                        <tr>
                                            <td><span class="badge bg-warning">Event</span></td>
                                            <td>{{ $event->name }}</td>
                                            <td>{{ $event->user?->full_name }}</td>
                                            <td>{{ $event->created_at->format('d M Y') }}</td>
                                            <td><a href="{{ route('admin.event_edit', $event->id) }}" class="btn btn-sm btn-outline-primary">Review</a></td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mt-3">🎉 No pending approvals at the moment.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Recent Bookings & Users --}}
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3 pb-1"><h5 class="mb-0 fw-bold"><i class="bi bi-calendar-check me-2 text-gold"></i>Recent Bookings</h5></div>
            <div class="card-body p-0">
                @if($recent_bookings->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0 admin-table">
                        <thead><tr><th>Ref</th><th>User</th><th>Target</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            @foreach($recent_bookings as $b)
                            <tr>
                                <td><code>{{ $b->reference_code }}</code></td>
                                <td>{{ $b->user?->full_name }}</td>
                                <td>{{ $b->target_name }}</td>
                                <td class="text-gold fw-semibold">₦{{ number_format($b->total_price) }}</td>
                                <td><span class="badge bg-{{ ['Approved'=>'info','Confirmed'=>'success','Completed'=>'secondary','Pending'=>'warning text-dark','Rejected'=>'danger','Cancelled'=>'danger'][$b->booking_status] ?? 'secondary' }}">{{ $b->booking_status }}</span></td>
                                <td><a href="{{ route('admin.bookings') }}" class="btn btn-sm btn-outline-secondary">View</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">No recent bookings.</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3 pb-1"><h5 class="mb-0 fw-bold"><i class="bi bi-people me-2 text-gold"></i>Recent Users</h5></div>
            <div class="card-body p-0">
                @if($recent_users->count())
                <ul class="list-group list-group-flush">
                    @foreach($recent_users as $u)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-semibold">{{ $u->full_name }}</span><br>
                            <small class="text-muted">{{ $u->email }}</small>
                        </div>
                        <span class="badge bg-{{ $u->role === 'Super Admin' ? 'danger' : ($u->role === 'Agency Admin' ? 'warning' : 'secondary') }}">{{ $u->role }}</span>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="text-center py-4 text-muted">No recent users.</div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex flex-wrap gap-2">
                <a href="{{ route('admin.attractions') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-building me-1"></i>Manage Attractions</a>
                <a href="{{ route('admin.guides', ['status' => 'Pending']) }}" class="btn btn-outline-warning btn-sm"><i class="bi bi-person-badge me-1"></i>Pending Guides</a>
                <a href="{{ route('admin.hotels', ['status' => 'inactive']) }}" class="btn btn-outline-warning btn-sm"><i class="bi bi-building-fill me-1"></i>Pending Hotels</a>
                <a href="{{ route('admin.restaurants', ['status' => 'inactive']) }}" class="btn btn-outline-warning btn-sm"><i class="bi bi-cup-hot me-1"></i>Pending Restaurants</a>
                <a href="{{ route('admin.events', ['status' => 'inactive']) }}" class="btn btn-outline-warning btn-sm"><i class="bi bi-calendar-event me-1"></i>Pending Events</a>
                <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-people me-1"></i>All Users</a>
                <a href="{{ route('admin.bookings', ['status' => 'Pending']) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-calendar-check me-1"></i>Pending Bookings</a>
            </div>
        </div>
    </div>
</div>
@endsection