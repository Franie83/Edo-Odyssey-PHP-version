@extends('layouts.app')
@section('title', 'FAQs – Edo Odyssey')
@section('content')
<div class="page-hero">
  <div class="container">
    <h1>Frequently Asked Questions</h1>
    <p class="text-white-50">Everything you need to know about Edo Odyssey</p>
  </div>
</div>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-9">
      @forelse($faqs as $category => $items)
      <h4 class="fw-bold text-blue mb-3 mt-4">{{ $category ?: 'General' }}</h4>
      <div class="accordion mb-3" id="faq-{{ Str::slug($category ?: 'general') }}">
        @foreach($items as $i => $faq)
        <div class="accordion-item border-0 shadow-sm mb-2" style="border-radius:10px">
          <h2 class="accordion-header">
            <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }} fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq-{{ $faq->id }}" style="border-radius:10px">
              {{ $faq->question }}
            </button>
          </h2>
          <div id="faq-{{ $faq->id }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}">
            <div class="accordion-body text-muted">{{ $faq->answer }}</div>
          </div>
        </div>
        @endforeach
      </div>
      @empty
      <div class="text-center py-5 text-muted">
        <i class="bi bi-question-circle fs-1 d-block mb-3"></i>
        <p>No FAQs available yet.</p>
      </div>
      @endforelse
      <div class="text-center mt-4 p-4 bg-blue-100 rounded">
        <h5 class="fw-bold text-blue">Still have questions?</h5>
        <p class="text-muted">Our team is happy to help.</p>
        <a href="{{ route('main.contact') }}" class="btn btn-gold px-4">Contact Us</a>
      </div>
    </div>
  </div>
</div>
@endsection
