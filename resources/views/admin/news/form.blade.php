@extends('layouts.admin')
@section('title', ($article->id ? 'Edit Article' : 'New Article') . ' – Admin')
@section('page_title', $article->id ? 'Edit Article' : 'Publish Article')
@section('content')
<div class="row justify-content-center"><div class="col-md-10">
  <div class="card border-0 shadow-sm"><div class="card-body p-4">
    <form method="POST" action="{{ $article->id ? route('admin.news_update', $article->id) : route('admin.news_store') }}" enctype="multipart/form-data">
      @csrf
      <div class="row g-3">
        <div class="col-12"><label class="form-label fw-semibold small">Title *</label><input type="text" name="title" class="form-control" value="{{ old('title', $article->title) }}" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold small">Category</label><input type="text" name="category" class="form-control" value="{{ old('category', $article->category) }}" placeholder="Industry News, Heritage, Nature…"></div>
        <div class="col-md-6"><label class="form-label fw-semibold small">Author</label><input type="text" name="author" class="form-control" value="{{ old('author', $article->author ?? Auth::user()->full_name) }}"></div>
        <div class="col-12"><label class="form-label fw-semibold small">Content *</label><textarea name="content" class="form-control" rows="12" required>{{ old('content', $article->content) }}</textarea></div>
        <div class="col-12"><label class="form-label fw-semibold small">Tags (comma-separated)</label><input type="text" name="tags" class="form-control" value="{{ old('tags', $article->tags) }}" placeholder="tourism, culture, benin"></div>
        <div class="col-md-6"><label class="form-label fw-semibold small">Main Image</label><input type="file" name="image" class="form-control" accept="image/*">@if($article->image_url)<small class="text-muted">Current: <a href="{{ \App\Helpers\Helpers::imageUrl($article->image_url) }}" target="_blank">View</a></small>@endif</div>
        <div class="col-md-3"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="is_featured" {{ old('is_featured', $article->is_featured)?'checked':'' }}><label class="form-check-label">Featured</label></div></div>
        <div class="col-md-3"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="is_published" {{ old('is_published', $article->is_published ?? true)?'checked':'' }}><label class="form-check-label">Publish Now</label></div></div>
      </div>
      <div class="d-flex gap-2 mt-4"><button class="btn btn-gold">{{ $article->id ? 'Update Article' : 'Publish Article' }}</button><a href="{{ route('admin.news') }}" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
  </div></div>
</div></div>
@endsection
