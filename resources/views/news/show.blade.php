@extends('layouts.app')
@section('title', $article->title . ' – Edo Odyssey')
@section('content')
<div class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('main.home') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ route('news.list_news') }}">News</a></li><li class="breadcrumb-item active">Article</li></ol></nav>
    <span class="badge bg-gold text-dark mb-2">{{ $article->category ?? 'News' }}</span>
    <h1>{{ $article->title }}</h1>
    <p class="text-white-50 small">By {{ $article->author ?? 'EDSTA Communications' }} · {{ $article->created_at?->format('d M Y') }} · {{ $article->views }} views</p>
  </div>
</div>
<div class="container py-4">
  <div class="row g-4">
    <div class="col-lg-8">
      @if($article->image_url)
      <div class="detail-image mb-4"><img src="{{ \App\Helpers\Helpers::imageUrl($article->image_url) }}" alt="{{ $article->title }}"></div>
      @endif
      <div class="prose text-muted" style="line-height:1.8;font-size:1.05rem">{!! nl2br(e($article->content)) !!}</div>

      <!-- Comments -->
      <div class="mt-5">
        <h4 class="fw-bold text-blue mb-3"><i class="bi bi-chat me-2 text-gold"></i>Comments ({{ $comments->count() }})</h4>
        @forelse($comments as $c)
        <div class="d-flex gap-3 mb-3 p-3 border rounded">
          <div class="avatar-initials-sm flex-shrink-0">{{ $c->user?->first_name[0] ?? '?' }}</div>
          <div>
            <div class="fw-semibold small">{{ $c->user?->full_name }} <span class="text-muted fw-normal">· {{ $c->created_at?->format('d M Y H:i') }}</span></div>
            <p class="text-muted small mb-0 mt-1">{{ $c->content }}</p>
          </div>
        </div>
        @empty
        <p class="text-muted">No comments yet.</p>
        @endforelse

        @auth
        <form method="POST" action="{{ route('news.comment', $article->id) }}" class="mt-4 p-3 bg-blue-100 rounded">
          @csrf
          <h6 class="fw-bold text-blue mb-2">Add a Comment</h6>
          <textarea name="content" class="form-control border-0 mb-2" rows="3" placeholder="Share your thoughts…" required></textarea>
          <button class="btn btn-blue btn-sm">Post Comment +5 pts</button>
        </form>
        @else
        <div class="mt-3 p-3 bg-blue-100 rounded text-center">
          <a href="{{ route('auth.login') }}" class="btn btn-blue btn-sm">Login to comment</a>
        </div>
        @endauth
      </div>
    </div>
    <div class="col-lg-4">
      @if($related->count())
      <div class="sidebar-card">
        <h6>Related Articles</h6>
        @foreach($related as $r)
        <a href="{{ route('news.detail', $r->id) }}" class="d-flex gap-2 mb-3 text-decoration-none">
          <img src="{{ \App\Helpers\Helpers::imageUrl($r->image_url) }}" width="70" height="55" style="object-fit:cover;border-radius:6px;flex-shrink:0" alt="">
          <div><div class="small fw-semibold text-blue text-truncate-2">{{ $r->title }}</div><div class="text-muted" style="font-size:.7rem">{{ $r->created_at?->format('d M Y') }}</div></div>
        </a>
        @endforeach
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
