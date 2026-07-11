@extends('layouts.app')
@section('title', '404 Not Found – Edo Odyssey')
@section('content')
<div class="min-vh-100 d-flex align-items-center" style="background:linear-gradient(135deg,var(--blue-900),var(--blue-700))">
  <div class="container text-center text-white py-5">
    <div class="brand-icon mx-auto mb-4" style="width:80px;height:80px;font-size:2.5rem"><i class="bi bi-compass-fill"></i></div>
    <h1 style="font-size:6rem;font-family:'Playfair Display',serif;font-weight:700;color:var(--gold-400)">404</h1>
    <h2 class="fw-bold mb-3">Page Not Found</h2>
    <p class="text-white-50 mb-4">{{ $message ?? 'The page you are looking for does not exist or has been moved.' }}</p>
    <div class="d-flex gap-3 justify-content-center">
      <a href="{{ route('main.home') }}" class="btn btn-gold px-4">Go Home</a>
      <a href="javascript:history.back()" class="btn btn-outline-light px-4">Go Back</a>
    </div>
  </div>
</div>
@endsection
