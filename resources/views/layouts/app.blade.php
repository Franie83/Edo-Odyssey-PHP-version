<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Edo Odyssey – Official Edo State Tourism Platform')</title>
  <meta name="description" content="{{ $settings['meta_description'] ?? 'Explore Edo State cultural heritage and tourism' }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/static/css/main.css">
  @yield('head')
  <style>
    /* Enhanced styles for better UX */
    :root {
      --gold-400: #e0b83a;
      --gold-500: #c9a227;
      --gold-600: #b08a1f;
      --blue-800: #0d2447;
      --blue-900: #0a1a33;
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Navbar improvements */
    .main-navbar .nav-link {
      position: relative;
      transition: var(--transition);
      padding: 0.5rem 1rem;
      border-radius: 8px;
    }
    .main-navbar .nav-link::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      width: 0;
      height: 2px;
      background: var(--gold-500);
      transition: var(--transition);
      transform: translateX(-50%);
    }
    .main-navbar .nav-link:hover::after,
    .main-navbar .nav-link.active::after {
      width: 60%;
    }
    .main-navbar .nav-link:hover {
      background: rgba(201, 162, 39, 0.1);
    }
    .main-navbar .nav-link.active {
      color: var(--gold-400) !important;
    }

    /* Dropdown improvements */
    .mega-dropdown {
      border: none;
      border-radius: 12px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.15);
      padding: 0.5rem;
      min-width: 220px;
      animation: slideDown 0.2s ease-out;
    }
    .mega-dropdown .dropdown-item {
      border-radius: 8px;
      padding: 0.6rem 1rem;
      transition: var(--transition);
    }
    .mega-dropdown .dropdown-item:hover {
      background: rgba(26, 58, 107, 0.05);
      transform: translateX(4px);
    }
    .mega-dropdown .dropdown-item i {
      transition: var(--transition);
    }
    .mega-dropdown .dropdown-item:hover i {
      color: var(--gold-500) !important;
    }

    @keyframes slideDown {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Flash messages as toasts */
    #flash-container {
      position: fixed;
      top: 80px;
      right: 20px;
      z-index: 9999;
      max-width: 400px;
      width: 100%;
    }
    .flash-toast {
      border: none;
      border-radius: 12px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.15);
      padding: 1rem 1.25rem;
      animation: slideInRight 0.4s ease-out;
      backdrop-filter: blur(10px);
      background: rgba(255,255,255,0.95);
    }
    .flash-toast .btn-close {
      font-size: 0.7rem;
    }
    .flash-toast i {
      font-size: 1.2rem;
    }
    @keyframes slideInRight {
      from { opacity: 0; transform: translateX(40px); }
      to { opacity: 1; transform: translateX(0); }
    }

    /* Footer improvements */
    .footer-links a {
      transition: var(--transition);
      display: inline-block;
    }
    .footer-links a:hover {
      color: var(--gold-400) !important;
      transform: translateX(4px);
    }
    .site-footer .text-gold {
      transition: var(--transition);
    }
    .site-footer .text-gold:hover {
      transform: translateY(-2px);
    }

    /* Search form improvements */
    .search-form .form-control {
      border-radius: 20px 0 0 20px;
      border: 1px solid rgba(255,255,255,0.2);
      background: rgba(255,255,255,0.1);
      color: #fff;
      transition: var(--transition);
    }
    .search-form .form-control:focus {
      background: rgba(255,255,255,0.2);
      border-color: var(--gold-500);
      box-shadow: none;
    }
    .search-form .form-control::placeholder {
      color: rgba(255,255,255,0.6);
    }
    .search-form .btn {
      border-radius: 0 20px 20px 0;
    }

    /* User avatar dropdown */
    .avatar-initials-sm {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      font-size: 0.75rem;
      background: var(--gold-500);
      color: #fff;
    }
    .dropdown-menu .dropdown-item {
      transition: var(--transition);
      border-radius: 8px;
      padding: 0.5rem 1rem;
    }
    .dropdown-menu .dropdown-item:hover {
      background: rgba(201, 162, 39, 0.1);
      transform: translateX(4px);
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
      #flash-container {
        top: 70px;
        right: 10px;
        left: 10px;
        max-width: 100%;
      }
      .main-navbar .nav-link::after {
        display: none;
      }
      .mega-dropdown {
        border-radius: 8px;
        margin-top: 0.5rem;
      }
    }

    /* Scrollbar styling */
    ::-webkit-scrollbar {
      width: 8px;
    }
    ::-webkit-scrollbar-track {
      background: #f1f1f1;
    }
    ::-webkit-scrollbar-thumb {
      background: var(--gold-500);
      border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: var(--gold-600);
    }

    /* Loading skeleton animation */
    .skeleton {
      animation: shimmer 1.5s infinite;
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200% 100%;
    }
    @keyframes shimmer {
      0% { background-position: -200% 0; }
      100% { background-position: 200% 0; }
    }
  </style>
</head>
<body>

<!-- TOP BANNER -->
<div class="top-banner py-1 d-none d-md-block">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="d-flex gap-3 small">
      <span><i class="bi bi-telephone-fill me-1"></i>{{ $settings['contact_phone'] ?? '+234 (0) 52 255 000' }}</span>
      <span><i class="bi bi-envelope-fill me-1"></i>{{ $settings['contact_email'] ?? 'info@edsta.edo.gov.ng' }}</span>
    </div>
    <div class="d-flex gap-2 small">
      <a href="#" class="text-gold transition-hover"><i class="bi bi-facebook"></i></a>
      <a href="#" class="text-gold transition-hover"><i class="bi bi-twitter-x"></i></a>
      <a href="#" class="text-gold transition-hover"><i class="bi bi-instagram"></i></a>
      <a href="#" class="text-gold transition-hover"><i class="bi bi-youtube"></i></a>
    </div>
  </div>
</div>

<!-- MAIN NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark main-navbar sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('main.home') }}">
      <div class="brand-icon"><i class="bi bi-compass-fill"></i></div>
      <div>
        <div class="brand-title">EDO ODYSSEY</div>
        <div class="brand-subtitle">Official Tourism Platform</div>
      </div>
    </a>

    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mx-auto gap-1">
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('main.home') ? 'active' : '' }}" href="{{ route('main.home') }}">Home</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Explore</a>
          <ul class="dropdown-menu mega-dropdown">
            <li><a class="dropdown-item" href="{{ route('attractions.list_attractions') }}"><i class="bi bi-building me-2 text-primary"></i>Attractions</a></li>
            <li><a class="dropdown-item" href="{{ route('guides.list_guides') }}"><i class="bi bi-person-badge me-2 text-primary"></i>Tour Guides</a></li>
            <li><a class="dropdown-item" href="{{ route('hotels.list_hotels') }}"><i class="bi bi-building-fill me-2 text-primary"></i>Hotels</a></li>
            <li><a class="dropdown-item" href="{{ route('restaurants.list_restaurants') }}"><i class="bi bi-cup-hot me-2 text-primary"></i>Restaurants</a></li>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}" href="{{ route('events.list_events') }}">Events</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}" href="{{ route('news.list_news') }}">News</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('main.about') }}">About</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('main.contact') }}">Contact</a></li>
      </ul>

      <form class="d-flex me-3 search-form" action="{{ route('search.results') }}" method="get">
        <div class="input-group input-group-sm">
          <input class="form-control nav-search" type="search" name="q" placeholder="Search Edo…" value="{{ request('q') }}">
          <button class="btn btn-gold btn-sm" type="submit"><i class="bi bi-search"></i></button>
        </div>
      </form>

      @auth
      <div class="dropdown">
        <button class="btn btn-outline-gold dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
          @if(Auth::user()->avatar_url)
          <img src="{{ \App\Helpers\Helpers::imageUrl(Auth::user()->avatar_url) }}" class="rounded-circle" width="28" height="28" alt="">
          @else
          <div class="avatar-initials-sm">{{ Auth::user()->first_name[0] }}{{ Auth::user()->last_name[0] }}</div>
          @endif
          <span class="d-none d-lg-inline">{{ Auth::user()->first_name }}</span>
          @if(isset($unread_notifs) && $unread_notifs > 0)
          <span class="badge bg-danger rounded-pill">{{ $unread_notifs }}</span>
          @endif
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li class="dropdown-header">{{ Auth::user()->full_name }}<br><small class="text-muted">{{ Auth::user()->role }}</small></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="{{ route('dashboard.index') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
          <li><a class="dropdown-item" href="{{ route('dashboard.profile') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
          @if(Auth::user()->is_admin)
          <li><a class="dropdown-item text-primary fw-semibold" href="{{ route('admin.dashboard') }}"><i class="bi bi-shield-check me-2"></i>Admin Panel</a></li>
          @endif
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="{{ route('auth.logout') }}"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
      </div>
      @else
      <div class="d-flex gap-2">
        <a href="{{ route('auth.login') }}" class="btn btn-outline-gold btn-sm">Login</a>
        <a href="{{ route('auth.register') }}" class="btn btn-gold btn-sm">Register</a>
      </div>
      @endauth
    </div>
  </div>
</nav>

<!-- FLASH MESSAGES (Toast-style) -->
<div id="flash-container">
  @foreach(['success','danger','warning','info'] as $type)
    @if(session($type))
    <div class="flash-toast alert alert-{{ $type }} alert-dismissible fade show mb-2" role="alert">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-{{ $type=='success' ? 'check-circle-fill text-success' : ($type=='danger' ? 'x-circle-fill text-danger' : ($type=='warning' ? 'exclamation-triangle-fill text-warning' : 'info-circle-fill text-info')) }}"></i>
        <div class="flex-grow-1 small">{{ session($type) }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
    @endif
  @endforeach
</div>

<!-- PAGE CONTENT -->
@yield('content')

<!-- FOOTER -->
<footer class="site-footer mt-5">
  <div class="footer-top py-5">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4">
          <div class="d-flex align-items-center gap-2 mb-3">
            <div class="brand-icon"><i class="bi bi-compass-fill"></i></div>
            <div>
              <div class="brand-title text-white">EDO ODYSSEY</div>
              <div class="brand-subtitle" style="font-size:0.65rem">Official Tourism Platform</div>
            </div>
          </div>
          <p class="text-white-50 small">{{ isset($settings['about_agency']) ? Str::limit($settings['about_agency'], 200) : 'Discover the rich cultural heritage of Edo State.' }}</p>
          <div class="d-flex gap-3 mt-3">
            <a href="#" class="text-gold fs-5 transition-hover"><i class="bi bi-facebook"></i></a>
            <a href="#" class="text-gold fs-5 transition-hover"><i class="bi bi-twitter-x"></i></a>
            <a href="#" class="text-gold fs-5 transition-hover"><i class="bi bi-instagram"></i></a>
            <a href="#" class="text-gold fs-5 transition-hover"><i class="bi bi-youtube"></i></a>
          </div>
        </div>
        <div class="col-lg-2 col-6">
          <h6 class="text-gold fw-bold mb-3 text-uppercase letter-spacing">Explore</h6>
          <ul class="list-unstyled footer-links">
            <li><a href="{{ route('attractions.list_attractions') }}">Attractions</a></li>
            <li><a href="{{ route('guides.list_guides') }}">Tour Guides</a></li>
            <li><a href="{{ route('hotels.list_hotels') }}">Hotels</a></li>
            <li><a href="{{ route('restaurants.list_restaurants') }}">Restaurants</a></li>
            <li><a href="{{ route('events.list_events') }}">Events</a></li>
          </ul>
        </div>
        <div class="col-lg-2 col-6">
          <h6 class="text-gold fw-bold mb-3 text-uppercase letter-spacing">EDSTA</h6>
          <ul class="list-unstyled footer-links">
            <li><a href="{{ route('main.about') }}">About EDSTA</a></li>
            <li><a href="{{ route('news.list_news') }}">Tourism News</a></li>
            <li><a href="{{ route('main.faq') }}">FAQs</a></li>
            <li><a href="{{ route('main.contact') }}">Contact</a></li>
            <li><a href="{{ route('main.privacy') }}">Privacy Policy</a></li>
          </ul>
        </div>
        <div class="col-lg-4">
          <h6 class="text-gold fw-bold mb-3 text-uppercase letter-spacing">Quick Login (Demo)</h6>
          <div class="d-flex flex-wrap gap-1 mb-3">
            <a href="{{ route('auth.quick_login', 'superadmin') }}" class="btn btn-outline-gold btn-sm"><i class="bi bi-shield-star me-1"></i>Super Admin</a>
            <a href="{{ route('auth.quick_login', 'tourist') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-person me-1"></i>Tourist</a>
            <a href="{{ route('auth.quick_login', 'guide') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-person-badge me-1"></i>Guide</a>
            <a href="{{ route('auth.quick_login', 'hotel') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-building me-1"></i>Hotel</a>
            <a href="{{ route('auth.quick_login', 'restaurant') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-cup-hot me-1"></i>Restaurant</a>
          </div>
          <h6 class="text-gold fw-bold mb-2 text-uppercase letter-spacing" style="font-size:0.7rem">Contact</h6>
          <p class="text-white-50 small mb-1"><i class="bi bi-geo-alt me-1"></i>{{ $settings['contact_address'] ?? 'EDSTA HQ, Government House Road, GRA, Benin City' }}</p>
          <p class="text-white-50 small mb-1"><i class="bi bi-telephone me-1"></i>{{ $settings['contact_phone'] ?? '+234 (0) 52 255 000' }}</p>
          <p class="text-white-50 small"><i class="bi bi-envelope me-1"></i>{{ $settings['contact_email'] ?? 'info@edsta.edo.gov.ng' }}</p>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom py-2 text-center">
    <small class="text-white-50">{{ $settings['footer_text'] ?? '© 2026 Edo State Tourism Agency. All rights reserved.' }}</small>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/static/js/main.js"></script>

{{-- Auto-dismiss flash messages after 5 seconds --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const flashContainer = document.getElementById('flash-container');
    if (flashContainer) {
      const alerts = flashContainer.querySelectorAll('.flash-toast');
      alerts.forEach(function(alert, index) {
        setTimeout(function() {
          const bsAlert = new bootstrap.Alert(alert);
          bsAlert.close();
        }, 5000 + (index * 500));
      });
    }
  });
</script>

@yield('scripts')
</body>
</html>