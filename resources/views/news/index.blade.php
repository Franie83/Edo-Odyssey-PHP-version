@extends('layouts.app')
@section('title', 'News – Edo Odyssey')
@section('content')
<div class="page-hero">
  <div class="container">
    <h1><i class="bi bi-newspaper me-2 text-gold"></i>Tourism News</h1>
    <p class="text-white-50">Latest updates from the Edo State Tourism Agency</p>
  </div>
</div>
<div class="container py-4">
  <form class="filter-bar row g-2" method="GET">
    <div class="col-md-5"><input type="search" name="q" class="form-control" placeholder="Search news…" value="{{ request('q') }}"></div>
    <div class="col-md-2"><button class="btn btn-blue w-100" type="submit"><i class="bi bi-search me-1"></i>Search</button></div>
  </form>
  <div class="row g-4">
    @forelse($articles as $article)
    <div class="col-md-6 col-lg-4">
      <div class="news-card card h-100">
        <div class="card-img-wrap" style="height:200px"><img src="{{ \App\Helpers\Helpers::imageUrl($article->image_url) }}" alt="{{ $article->title }}" loading="lazy">@if($article->is_featured)<div class="card-category-badge"><i class="bi bi-star-fill me-1"></i>Featured</div>@endif</div>
        <div class="card-body">
          <span class="badge bg-blue-100 text-blue mb-2">{{ $article->category ?? 'News' }}</span>
          <h5 class="card-title fw-bold text-blue">{{ $article->title }}</h5>
          <p class="text-muted small text-truncate-3">{{ Str::limit(strip_tags($article->content), 120) }}</p>
          <div class="d-flex justify-content-between align-items-center mt-auto">
            <small class="text-muted">{{ $article->author ?? 'EDSTA' }} · {{ $article->created_at?->format('d M Y') }}</small>
            <a href="{{ route('news.detail', $article->id) }}" class="btn btn-blue btn-sm">Read</a>
          </div>
        </div>
      </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted"><i class="bi bi-newspaper fs-1 d-block mb-3"></i><p>No articles found.</p></div>
    @endforelse
  </div>
  <div class="d-flex justify-content-center mt-4">{{ $articles->links() }}</div>
</div>
@endsection
