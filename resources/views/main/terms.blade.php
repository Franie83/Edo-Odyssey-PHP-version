@extends('layouts.app')
@section('title', 'Terms of Service – Edo Odyssey')
@section('content')
<div class="page-hero"><div class="container"><h1>Terms of Service</h1><p class="text-white-50">Last updated: January 2026</p></div></div>
<div class="container py-5">
  <div class="row justify-content-center"><div class="col-lg-9">
    @foreach([
      ['title' => '1. Acceptance of Terms', 'body' => 'By using Edo Odyssey, you agree to be bound by these Terms of Service. If you do not agree, please do not use our services.'],
      ['title' => '2. User Accounts', 'body' => 'You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.'],
      ['title' => '3. Bookings and Payments', 'body' => 'All bookings are subject to availability and confirmation. Prices are displayed in Nigerian Naira (₦). We reserve the right to modify prices at any time.'],
      ['title' => '4. Cancellation Policy', 'body' => 'Bookings may be cancelled before they are approved by admin. Once confirmed, cancellation policies depend on the service provider.'],
      ['title' => '5. Prohibited Activities', 'body' => 'You may not use our services for any unlawful purpose, misrepresent your identity, or interfere with the proper functioning of the platform.'],
      ['title' => '6. Governing Law', 'body' => 'These terms are governed by the laws of the Federal Republic of Nigeria. Any disputes shall be resolved in the courts of Edo State.'],
    ] as $section)
    <h4 class="fw-bold text-blue mt-4 mb-2">{{ $section['title'] }}</h4>
    <p class="text-muted">{{ $section['body'] }}</p>
    @endforeach
  </div></div>
</div>
@endsection
