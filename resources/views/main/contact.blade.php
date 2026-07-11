@extends('layouts.app')
@section('title', 'Contact – Edo Odyssey')
@section('content')
<div class="page-hero">
  <div class="container">
    <h1>Contact Us</h1>
    <p class="text-white-50">Get in touch with the Edo State Tourism Agency</p>
  </div>
</div>
<div class="container py-5">
  <div class="row g-4">
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <h4 class="fw-bold text-blue mb-4">Send us a Message</h4>
          @if($errors->any())<div class="alert alert-danger py-2 small"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
          <form method="POST" action="{{ route('main.contact_submit') }}">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold small">Your Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold small">Email *</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold small">Subject *</label>
                <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold small">Message *</label>
                <textarea name="message" class="form-control" rows="5" required>{{ old('message') }}</textarea>
              </div>
            </div>
            <button type="submit" class="btn btn-gold mt-3 px-4 fw-semibold"><i class="bi bi-send me-2"></i>Send Message</button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="sidebar-card">
        <h5 class="fw-bold text-blue mb-3">EDSTA Headquarters</h5>
        <p class="small mb-2"><i class="bi bi-geo-alt text-gold me-2"></i>{{ $settings['contact_address'] ?? 'Government House Road, GRA, Benin City, Edo State, Nigeria' }}</p>
        <p class="small mb-2"><i class="bi bi-telephone text-gold me-2"></i>{{ $settings['contact_phone'] ?? '+234 (0) 52 255 000' }}</p>
        <p class="small mb-3"><i class="bi bi-envelope text-gold me-2"></i>{{ $settings['contact_email'] ?? 'info@edsta.edo.gov.ng' }}</p>
        <hr>
        <h6 class="fw-bold text-blue mb-2">Office Hours</h6>
        <p class="small text-muted mb-1">Monday – Friday: 8:00am – 5:00pm</p>
        <p class="small text-muted">Saturday: 9:00am – 1:00pm</p>
      </div>
    </div>
  </div>
</div>
@endsection
