@extends('layouts.admin')
@section('title', 'News – Admin')
@section('page_title', 'News Articles')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <form class="d-flex gap-2" method="GET"><input type="search" name="q" class="form-control form-control-sm" placeholder="Search…" value="{{ request('q') }}" style="width:200px"><button class="btn btn-blue btn-sm">Filter</button></form>
  <a href="{{ route('admin.news_create') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus me-1"></i>Publish Article</a>
</div>
<div class="card border-0 shadow-sm"><div class="card-body p-0">
  <div class="table-responsive"><table class="table table-hover mb-0 admin-table">
    <thead><tr><th>Title</th><th>Category</th><th>Author</th><th>Published</th><th>Featured</th><th>Actions</th></tr></thead>
    <tbody>
    @forelse($articles as $a)
    <tr>
      <td class="fw-semibold small">{{ Str::limit($a->title, 40) }}</td>
      <td class="small">{{ $a->category }}</td>
      <td class="small">{{ $a->author }}</td>
      <td class="small text-muted">{{ $a->created_at?->format('d M Y') }}</td>
      <td>@if($a->is_featured)<span class="badge bg-gold text-dark">Featured</span>@else<span class="text-muted">—</span>@endif</td>
      <td><div class="d-flex gap-1"><a href="{{ route('admin.news_edit', $a->id) }}" class="btn btn-outline-secondary btn-sm py-0"><i class="bi bi-pencil"></i></a><form method="POST" action="{{ route('admin.news_delete', $a->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">@csrf<button class="btn btn-outline-danger btn-sm py-0"><i class="bi bi-trash"></i></button></form></div></td>
    </tr>
    @empty<tr><td colspan="6" class="text-center py-4 text-muted">No articles.</td></tr>
    @endforelse
    </tbody>
  </table></div>
  <div class="p-3">{{ $articles->withQueryString()->links() }}</div>
</div></div>
@endsection
