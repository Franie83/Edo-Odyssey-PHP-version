@extends('layouts.app')
@section('title', 'About EDSTA – Edo Odyssey')
@section('content')
<div class="page-hero">
  <div class="container">
    <h1>About EDSTA</h1>
    <p class="text-white-50">Edo State Tourism Agency — Guardians of Culture & Heritage</p>
  </div>
</div>
<div class="container py-5">
  <div class="row g-5">
    <div class="col-lg-8">
      <h2 class="text-blue fw-bold mb-4">Our Mission</h2>
      <p class="lead text-muted mb-4">The Edo State Tourism Agency (EDSTA) is a statutory public corporation established to guide, preserve, license, and promote the natural landmarks, cultural festivals, and historical heritage monuments of Edo State.</p>
      <p class="text-muted">Edo State, home to the ancient Benin Kingdom — one of the oldest and most sophisticated civilizations in Africa — boasts a rich tapestry of history, art, and culture. From the world-famous Benin Bronzes that now grace the world's greatest museums, to the living traditions of the Oba's Palace, EDSTA exists to ensure these wonders are accessible, preserved, and celebrated by all.</p>
      <h4 class="text-blue fw-bold mt-4 mb-3">What We Do</h4>
      <div class="row g-3">
        @php $functions = [
          ['icon' => 'bi-shield-check', 'title' => 'License & Regulate', 'desc' => 'We certify tour guides, hotels, and tourism businesses to ensure quality standards.'],
          ['icon' => 'bi-gem', 'title' => 'Preserve Heritage', 'desc' => 'We work to conserve Edo\'s cultural monuments, arts, and traditions for future generations.'],
          ['icon' => 'bi-globe', 'title' => 'Promote Tourism', 'desc' => 'We market Edo State as a premier destination nationally and internationally.'],
          ['icon' => 'bi-people', 'title' => 'Community Development', 'desc' => 'We empower local communities through sustainable tourism initiatives.'],
        ]; @endphp
        @foreach($functions as $f)
        <div class="col-md-6">
          <div class="info-card">
            <i class="bi {{ $f['icon'] }} text-gold fs-4 mb-2 d-block"></i>
            <h6 class="fw-bold text-blue">{{ $f['title'] }}</h6>
            <p class="text-muted small mb-0">{{ $f['desc'] }}</p>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    <div class="col-lg-4">
      <div class="sidebar-card text-center" style="background:linear-gradient(135deg,var(--blue-900),var(--blue-800));color:#fff">
        <i class="bi bi-compass-fill" style="font-size:3rem;color:var(--gold-400)"></i>
        <h4 class="fw-bold mt-3">Edo Odyssey</h4>
        <p class="text-white-50 small">The official digital gateway to Edo State's tourism ecosystem</p>
        <hr style="border-color:rgba(255,255,255,.2)">
        <div class="row g-2 text-center">
          <div class="col-6"><div style="font-size:1.5rem;font-weight:700;color:var(--gold-400)">600+</div><div class="small text-white-50">Years of History</div></div>
          <div class="col-6"><div style="font-size:1.5rem;font-weight:700;color:var(--gold-400)">50+</div><div class="small text-white-50">Attractions</div></div>
          <div class="col-6"><div style="font-size:1.5rem;font-weight:700;color:var(--gold-400)">UNESCO</div><div class="small text-white-50">Heritage Sites</div></div>
          <div class="col-6"><div style="font-size:1.5rem;font-weight:700;color:var(--gold-400)">4M+</div><div class="small text-white-50">Annual Visitors</div></div>
        </div>
      </div>
      <div class="sidebar-card mt-3">
        <h6>Contact EDSTA</h6>
        <p class="small text-muted mb-1"><i class="bi bi-geo-alt me-2 text-gold"></i>{{ $settings['contact_address'] ?? 'EDSTA HQ, Government House Road, GRA, Benin City' }}</p>
        <p class="small text-muted mb-1"><i class="bi bi-telephone me-2 text-gold"></i>{{ $settings['contact_phone'] ?? '+234 (0) 52 255 000' }}</p>
        <p class="small text-muted mb-1"><i class="bi bi-envelope me-2 text-gold"></i>{{ $settings['contact_email'] ?? 'info@edsta.edo.gov.ng' }}</p>
        <a href="{{ route('main.contact') }}" class="btn btn-gold btn-sm w-100 mt-2">Send a Message</a>
      </div>
    </div>
  </div>
</div>
@endsection
