@extends('layouts.app')
@section('title', 'Privacy Policy – Edo Odyssey')
@section('content')
<div class="page-hero"><div class="container"><h1>Privacy Policy</h1><p class="text-white-50">Last updated: January 2026</p></div></div>
<div class="container py-5">
  <div class="row justify-content-center"><div class="col-lg-9">
    @foreach([
      ['title' => '1. Information We Collect', 'body' => 'We collect information you provide directly to us, such as your name, email address, phone number, and any other information you choose to provide when creating an account or making a booking.'],
      ['title' => '2. How We Use Your Information', 'body' => 'We use the information we collect to provide, maintain, and improve our services, process bookings and transactions, send you technical notices and support messages, and respond to your comments and questions.'],
      ['title' => '3. Information Sharing', 'body' => 'We do not sell, trade, or otherwise transfer your personally identifiable information to outside parties. This does not include trusted third parties who assist us in operating our website or servicing you.'],
      ['title' => '4. Data Security', 'body' => 'We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.'],
      ['title' => '5. Cookies', 'body' => 'Our website uses cookies to enhance your experience. You can choose to disable cookies through your browser settings, though this may affect some functionality of our services.'],
      ['title' => '6. Contact Us', 'body' => 'If you have any questions about this Privacy Policy, please contact us at info@edsta.edo.gov.ng.'],
    ] as $section)
    <h4 class="fw-bold text-blue mt-4 mb-2">{{ $section['title'] }}</h4>
    <p class="text-muted">{{ $section['body'] }}</p>
    @endforeach
  </div></div>
</div>
@endsection
