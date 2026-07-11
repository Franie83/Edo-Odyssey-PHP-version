@extends('layouts.app')
@section('title', '403 Forbidden – Edo Odyssey')
@section('content')
<div class="min-vh-100 d-flex align-items-center" style="background:linear-gradient(135deg,var(--blue-900),var(--blue-700))">
  <div class="container text-center text-white py-5">
    <h1 style="font-size:6rem;font-family:'Playfair Display',serif;font-weight:700;color:var(--gold-400)">403</h1>
    <h2 class="fw-bold mb-3">Access Denied</h2>
    <p class="text-white-50 mb-4">{{ $exception->getMessage() ?? 'You do not have permission to access this page.' }}</p>
    <a href="{{ route('main.home') }}" class="btn btn-gold px-4">Go Home</a>
  </div>
</div>
@endsection
