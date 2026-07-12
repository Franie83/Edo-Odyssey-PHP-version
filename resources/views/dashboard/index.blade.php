@extends('layouts.app')
@section('title', 'Dashboard – Edo Odyssey')
@section('content')
<div class="page-hero py-4">
  <div class="container">
    <div class="d-flex align-items-center gap-3">
      @if(Auth::user()->avatar_url)
      <img src="{{ \App\Helpers\Helpers::imageUrl(Auth::user()->avatar_url) }}" class="rounded-circle border border-gold border-3" width="60" height="60" alt="">
      @else
      <div class="avatar-lg">{{ Auth::user()->first_name[0] }}{{ Auth::user()->last_name[0] }}</div>
      @endif
      <div>
        <h1 class="mb-0" style="font-size:1.6rem">Welcome, {{ Auth::user()->first_name }}!</h1>
        <p class="text-white-50 mb-0 small">{{ Auth::user()->role }} Account · <i class="bi bi-trophy-fill text-gold me-1"></i>{{ Auth::user()->heritage_points ?? 0 }} Heritage Points</p>
      </div>
      <div class="ms-auto d-flex gap-2">
        <a href="{{ route('dashboard.profile') }}" class="btn btn-outline-gold btn-sm"><i class="bi bi-person me-1"></i>Profile</a>
        @if(Auth::user()->is_admin)<a href="{{ route('admin.dashboard') }}" class="btn btn-gold btn-sm"><i class="bi bi-shield-check me-1"></i>Admin Panel</a>@endif
      </div>
    </div>
  </div>
</div>

<div class="container py-4">
  <!-- Stats -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="stat-icon bg-blue-100"><i class="bi bi-calendar-check text-blue"></i></div>
          <span class="badge bg-blue-100 text-blue">{{ $stats['pending_bookings'] ?? 0 }} pending</span>
        </div>
        <div class="stat-number">{{ $stats['total_bookings'] ?? 0 }}</div>
        <div class="small text-muted">Total Bookings</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card" style="border-bottom-color:var(--gold-500)">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="stat-icon bg-gold-100"><i class="bi bi-check-circle text-gold"></i></div>
        </div>
        <div class="stat-number">{{ $stats['completed_bookings'] ?? 0 }}</div>
        <div class="small text-muted">Completed Trips</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card" style="border-bottom-color:#2d8a4e">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="stat-icon" style="background:#e8f5e9"><i class="bi bi-star text-success"></i></div>
        </div>
        <div class="stat-number">{{ $stats['total_reviews'] ?? 0 }}</div>
        <div class="small text-muted">Reviews Given</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card" style="border-bottom-color:#c9a227">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="stat-icon bg-gold-100"><i class="bi bi-trophy text-gold"></i></div>
        </div>
        <div class="stat-number">{{ $stats['heritage_points'] ?? 0 }}</div>
        <div class="small text-muted">Heritage Points</div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Bookings Table -->
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-3 pb-1 d-flex justify-content-between align-items-center">
          <h5 class="mb-0 fw-bold text-blue"><i class="bi bi-calendar-check me-2 text-gold"></i>My Bookings</h5>
          <a href="{{ route('attractions.list_attractions') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus me-1"></i>New Booking</a>
        </div>
        <div class="card-body p-0">
          @if(isset($bookings) && $bookings->count())
          <div class="table-responsive">
            <table class="table table-hover mb-0 admin-table">
              <thead><tr><th>Ref</th><th>Type</th><th>Target</th><th>Date</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
              <tbody>
              @foreach($bookings as $b)
              <tr>
                <td><code class="text-blue">{{ $b->reference_code ?? 'N/A' }}</code></td>
                <td><span class="badge bg-blue-100 text-blue">{{ $b->booking_type ?? 'N/A' }}</span></td>
                <td class="small">{{ $b->target_name ?? '—' }}</td>
                <td class="small">{{ $b->start_date?->format('d M Y') ?? '—' }}</td>
                <td class="fw-semibold text-gold">₦{{ number_format($b->total_price ?? 0) }}</td>
                <td>
                  @php
                    $statusColors = [
                      'Pending'        => 'warning text-dark',
                      'AdminApproved'  => 'info',
                      'AdminRejected'  => 'danger',
                      'VendorAccepted' => 'success',
                      'VendorRejected' => 'danger',
                      'Completed'      => 'secondary',
                      'Cancelled'      => 'secondary',
                    ];
                    $color = $statusColors[$b->booking_status ?? ''] ?? 'secondary';
                  @endphp
                  <span class="badge bg-{{ $color }}">{{ $b->booking_status ?? 'N/A' }}</span>
                </td>
                <td>
                  @if(isset($b->booking_status) && $b->booking_status === 'Pending' && $b->user_id == Auth::id())
                    <form method="POST" action="{{ route('bookings.cancel_booking', $b->id) }}" class="d-inline">
                      @csrf
                      <button class="btn btn-outline-danger btn-sm py-0" onclick="return confirm('Cancel this booking?')">Cancel</button>
                    </form>
                  @elseif(isset($b->booking_status) && $b->booking_status === 'AdminApproved' && method_exists($b, 'isVendor') && $b->isVendor())
                    <div class="d-flex flex-column gap-1">
                      <form method="POST" action="{{ route('bookings.target_confirm_booking', $b->id) }}" class="d-inline">
                        @csrf
                        <div class="d-flex gap-1">
                          <input type="text" name="vendor_comment" class="form-control form-control-sm" placeholder="Comment (optional)" style="width:100px">
                          <button class="btn btn-success btn-sm py-0">Accept</button>
                        </div>
                      </form>
                      <form method="POST" action="{{ route('bookings.target_reject_booking', $b->id) }}" class="d-inline">
                        @csrf
                        <div class="d-flex gap-1">
                          <input type="text" name="vendor_comment" class="form-control form-control-sm" placeholder="Reason (optional)" style="width:100px">
                          <button class="btn btn-danger btn-sm py-0">Reject</button>
                        </div>
                      </form>
                    </div>
                  @else
                    <span class="text-muted" style="font-size:.7rem">—</span>
                  @endif
                </td>
              </tr>
              @endforeach
              </tbody>
            </table>
          </div>
          @else
          <div class="text-center py-5 text-muted">
            <i class="bi bi-calendar-x" style="font-size:2.5rem"></i>
            <p class="mt-2">No bookings yet. <a href="{{ route('attractions.list_attractions') }}" class="text-blue">Start exploring!</a></p>
          </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
      <!-- Heritage Points -->
      @php $pts = Auth::user()->heritage_points ?? 0; @endphp
      <div class="sidebar-card text-center" style="border-left:4px solid var(--gold-500)">
        <div class="d-flex align-items-center justify-content-between">
          <h6 class="mb-0"><i class="bi bi-trophy-fill text-gold me-1"></i>Heritage Points</h6>
          <span class="badge bg-gold text-dark fs-6 px-3 py-2">{{ $pts }}</span>
        </div>
        <div class="mt-2">
          @if($pts >= 500) <span class="badge bg-gold text-dark">🏆 Platinum Explorer</span>
          @elseif($pts >= 200) <span class="badge bg-success">🥇 Gold Explorer</span>
          @elseif($pts >= 100) <span class="badge bg-info text-dark">🥈 Silver Explorer</span>
          @elseif($pts >= 50) <span class="badge bg-secondary">🥉 Bronze Explorer</span>
          @else <span class="badge bg-light text-dark">⭐ Explorer</span>
          @endif
        </div>
        @php
          $progress = $pts >= 500 ? 100 : ($pts >= 200 ? ($pts-200)/300*100 : ($pts >= 100 ? ($pts-100)/100*100 : ($pts >= 50 ? ($pts-50)/50*100 : $pts/50*100)));
        @endphp
        <div class="progress mt-3" style="height:10px;border-radius:10px">
          <div class="progress-bar bg-gold" style="width:{{ $progress }}%;border-radius:10px"></div>
        </div>
        <small class="text-muted mt-2 d-block">
          @if($pts < 50) {{ 50 - $pts }} points to Bronze 🥉
          @elseif($pts < 100) {{ 100 - $pts }} points to Silver 🥈
          @elseif($pts < 200) {{ 200 - $pts }} points to Gold 🥇
          @elseif($pts < 500) {{ 500 - $pts }} points to Platinum 🏆
          @else 🌟 Max Level Achieved!
          @endif
        </small>
      </div>

      <!-- Notifications -->
      <div class="sidebar-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6>Notifications <span class="badge bg-danger ms-1">{{ isset($notifications) ? $notifications->where('is_read', false)->count() : 0 }}</span></h6>
          @if(isset($notifications) && $notifications->where('is_read', false)->count() > 0)
          <form method="POST" action="{{ route('dashboard.mark_all_read') }}">
            @csrf
            <button class="btn btn-link btn-sm p-0 text-muted small">Mark all read</button>
          </form>
          @endif
        </div>
        @if(isset($notifications) && $notifications->count() > 0)
          @forelse($notifications->take(6) as $n)
          <div class="d-flex gap-2 mb-2 p-2 rounded {{ !$n->is_read ? 'bg-blue-100' : '' }}">
            <i class="bi bi-bell{{ !$n->is_read ? '-fill text-gold' : ' text-muted' }} flex-shrink-0 mt-1"></i>
            <div>
              <div class="small fw-semibold">{{ $n->title }}</div>
              <div class="text-muted" style="font-size:.75rem">{{ Str::limit($n->message, 80) }}</div>
              <div class="text-muted" style="font-size:.65rem">{{ $n->created_at?->format('d M Y H:i') }}</div>
            </div>
          </div>
          @empty
          <p class="text-muted small mb-0">No notifications.</p>
          @endforelse
        @else
          <p class="text-muted small mb-0">No notifications.</p>
        @endif
      </div>

      <!-- My Offerings -->
      @php
        $role = Auth::user()->role;
        $offerings = [
          'Guide' => ['route' => 'dashboard.guide.edit', 'label' => 'Manage Guide Profile', 'icon' => 'bi-person-badge'],
          'Hotel' => ['route' => 'dashboard.hotels.index', 'label' => 'My Hotels', 'icon' => 'bi-building-fill'],
          'Restaurant' => ['route' => 'dashboard.restaurants.index', 'label' => 'My Restaurants', 'icon' => 'bi-cup-hot'],
          'Event Organizer' => ['route' => 'dashboard.events.index', 'label' => 'My Events', 'icon' => 'bi-calendar-event'],
        ];
      @endphp
      @if(array_key_exists($role, $offerings))
      <div class="sidebar-card">
        <h6>Your Offerings</h6>
        <a href="{{ route($offerings[$role]['route']) }}" class="btn btn-outline-primary btn-sm w-100 text-start">
          <i class="bi {{ $offerings[$role]['icon'] }} me-2"></i>{{ $offerings[$role]['label'] }}
        </a>
        <small class="text-muted d-block mt-2">View and manage all your listings. New submissions require admin approval.</small>
      </div>
      @endif

      <!-- Quick Actions -->
      <div class="sidebar-card">
        <h6>Quick Actions</h6>
        <div class="d-flex flex-column gap-2">
          <a href="{{ route('attractions.list_attractions') }}" class="btn btn-blue btn-sm text-start"><i class="bi bi-building me-2"></i>Browse Attractions</a>
          <a href="{{ route('guides.list_guides') }}" class="btn btn-blue btn-sm text-start"><i class="bi bi-person-badge me-2"></i>Book a Guide</a>
          <a href="{{ route('hotels.list_hotels') }}" class="btn btn-outline-secondary btn-sm text-start"><i class="bi bi-building-fill me-2"></i>Find Hotels</a>
          <a href="{{ route('events.list_events') }}" class="btn btn-outline-secondary btn-sm text-start"><i class="bi bi-calendar-event me-2"></i>Upcoming Events</a>
          <a href="{{ route('dashboard.profile') }}" class="btn btn-outline-secondary btn-sm text-start"><i class="bi bi-person me-2"></i>Edit Profile</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection