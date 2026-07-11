<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Admin – Edo Odyssey')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/static/css/main.css">
  @yield('head')
</head>
<body class="admin-body">

<!-- Sidebar -->
<div class="admin-sidebar" id="adminSidebar">
  <div class="admin-sidebar-header">
    <div class="d-flex align-items-center gap-2 mb-1">
      <div class="brand-icon" style="width:34px;height:34px;font-size:1rem"><i class="bi bi-compass-fill"></i></div>
      <div>
        <div class="brand-title" style="font-size:.85rem">EDO ODYSSEY</div>
        <div class="brand-subtitle" style="font-size:.58rem">Admin Panel</div>
      </div>
    </div>
    <div class="d-flex align-items-center gap-2 mt-2">
      <div class="avatar-initials-sm">{{ Auth::user()->first_name[0] }}{{ Auth::user()->last_name[0] }}</div>
      <div>
        <div class="text-white small fw-semibold">{{ Auth::user()->full_name }}</div>
        <div style="font-size:.65rem;color:var(--gold-400)">{{ Auth::user()->role }}</div>
      </div>
    </div>
  </div>

  <nav class="flex-grow-1 py-2 px-2">
    <div class="nav-section">Overview</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a>

    <div class="nav-section mt-2">Content</div>
    <a href="{{ route('admin.attractions') }}" class="nav-link {{ request()->routeIs('admin.attraction*') ? 'active' : '' }}"><i class="bi bi-building"></i> Attractions</a>
    <a href="{{ route('admin.guides') }}" class="nav-link {{ request()->routeIs('admin.guide*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i> Guides</a>
    <a href="{{ route('admin.hotels') }}" class="nav-link {{ request()->routeIs('admin.hotel*') ? 'active' : '' }}"><i class="bi bi-building-fill"></i> Hotels</a>
    <a href="{{ route('admin.restaurants') }}" class="nav-link {{ request()->routeIs('admin.restaurant*') ? 'active' : '' }}"><i class="bi bi-cup-hot"></i> Restaurants</a>
    <a href="{{ route('admin.events') }}" class="nav-link {{ request()->routeIs('admin.event*') ? 'active' : '' }}"><i class="bi bi-calendar-event"></i> Events</a>
    <a href="{{ route('admin.news') }}" class="nav-link {{ request()->routeIs('admin.news*') ? 'active' : '' }}"><i class="bi bi-newspaper"></i> News</a>
    <a href="{{ route('admin.categories') }}" class="nav-link {{ request()->routeIs('admin.categor*') ? 'active' : '' }}"><i class="bi bi-tags"></i> Categories</a>

    <div class="nav-section mt-2">Operations</div>
    <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.user*') ? 'active' : '' }}"><i class="bi bi-people"></i> Users</a>
    <a href="{{ route('admin.bookings') }}" class="nav-link {{ request()->routeIs('admin.booking*') ? 'active' : '' }}"><i class="bi bi-calendar-check"></i> Bookings</a>
    <a href="{{ route('admin.reviews') }}" class="nav-link {{ request()->routeIs('admin.review*') ? 'active' : '' }}"><i class="bi bi-star"></i> Reviews</a>

    <div class="nav-section mt-2">Intelligence</div>
    <a href="{{ route('admin.analytics') }}" class="nav-link {{ request()->routeIs('admin.analytics*') ? 'active' : '' }}"><i class="bi bi-bar-chart-line"></i> Analytics</a>
    <a href="{{ route('admin.audit_logs') }}" class="nav-link {{ request()->routeIs('admin.audit_logs') ? 'active' : '' }}"><i class="bi bi-journal-text"></i> Audit Logs</a>

    <div class="nav-section mt-2">Settings</div>
    <a href="{{ route('admin.cms') }}" class="nav-link {{ request()->routeIs('admin.cms') ? 'active' : '' }}"><i class="bi bi-gear"></i> CMS Settings</a>
    <a href="{{ route('admin.download_zip') }}" class="nav-link text-warning-emphasis"><i class="bi bi-file-earmark-zip"></i> Download ZIP</a>

    <div class="nav-section mt-2">Portal</div>
    <a href="{{ route('main.home') }}" class="nav-link"><i class="bi bi-house"></i> View Site</a>
    <a href="{{ route('auth.logout') }}" class="nav-link text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </nav>
</div>

<!-- Main content -->
<div class="admin-main">
  <div class="admin-topbar">
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <div>
        <h6 class="mb-0 fw-bold text-blue">@yield('page_title', 'Admin Panel')</h6>
        <small class="text-muted">@yield('page_subtitle', 'Edo Odyssey CMS')</small>
      </div>
    </div>
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('main.home') }}" class="btn btn-outline-secondary btn-sm" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>View Site</a>
      <div class="dropdown">
        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
          <div class="avatar-initials-sm d-inline-flex me-1">{{ Auth::user()->first_name[0] }}{{ Auth::user()->last_name[0] }}</div>
          {{ Auth::user()->first_name }}
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="{{ route('dashboard.profile') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="{{ route('auth.logout') }}"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="px-4 pt-2">
    @foreach(['success','danger','warning','info'] as $type)
      @if(session($type))
      <div class="alert alert-{{ $type }} alert-dismissible fade show mb-2" role="alert">
        <i class="bi bi-{{ $type=='success' ? 'check-circle' : ($type=='danger' ? 'x-circle' : 'info-circle') }} me-2"></i>{{ session($type) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif
    @endforeach
  </div>

  <div class="admin-content">
    @yield('content')
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script src="/static/js/main.js"></script>
@yield('scripts')
</body>
</html>
