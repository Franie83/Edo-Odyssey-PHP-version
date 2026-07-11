@extends('layouts.admin')
@section('title', 'Hotels – Admin')
@section('page_title', 'Hotels')
@section('page_subtitle', 'Manage hotel listings')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <form class="d-flex gap-2" method="GET">
    <input type="search" name="q" class="form-control form-control-sm" placeholder="Search hotels…" value="{{ request('q') }}" style="width:220px">
    <select name="status" class="form-select form-select-sm" style="width:auto">
      <option value="">All Statuses</option>
      <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
      <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Pending</option>
    </select>
    <button class="btn btn-blue btn-sm">Filter</button>
  </form>
  <a href="{{ route('admin.hotel_create') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus me-1"></i>Add Hotel</a>
</div>
<div class="card border-0 shadow-sm"><div class="card-body p-0">
  <div class="table-responsive">
    <table class="table table-hover mb-0 admin-table">
      <thead>
        <tr>
          <th>Hotel</th>
          <th>Stars</th>
          <th>Price/Night</th>
          <th>City</th>
          <th>User</th>
          <th>Featured</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      @forelse($hotels as $h)
      <tr>
        <td><div class="d-flex align-items-center gap-2"><img src="{{ \App\Helpers\Helpers::imageUrl($h->image_url) }}" width="40" height="35" style="object-fit:cover;border-radius:6px" alt=""><span class="fw-semibold small">{{ $h->name }}</span></div></td>
        <td class="text-gold">@for($i=0;$i<($h->stars??3);$i++)<i class="bi bi-star-fill" style="font-size:.75rem"></i>@endfor</td>
        <td class="text-gold fw-semibold small">₦{{ number_format($h->price_per_night) }}</td>
        <td class="small">{{ $h->city }}</td>
        <td class="small">{{ $h->user?->full_name ?? '—' }}</td>
        <td>@if($h->is_featured)<span class="badge bg-gold text-dark">Featured</span>@else<span class="text-muted">—</span>@endif</td>
        <td><span class="status-badge badge {{ $h->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $h->is_active ? 'Active' : 'Inactive' }}</span></td>
        <td>
          <div class="d-flex gap-1">
            <a href="{{ route('admin.hotel_edit', $h->id) }}" class="btn btn-outline-secondary btn-sm py-0"><i class="bi bi-pencil"></i></a>
            @if(!$h->is_active)
              <form method="POST" action="{{ route('admin.hotel_approve', $h->id) }}" class="d-inline">
                @csrf
                <button class="btn btn-success btn-sm py-0" title="Approve"><i class="bi bi-check-lg"></i></button>
              </form>
            @endif
            <form method="POST" action="{{ route('admin.hotel_delete', $h->id) }}" class="d-inline" onsubmit="return confirm('Delete this hotel?')">
              @csrf
              <button class="btn btn-outline-danger btn-sm py-0"><i class="bi bi-trash"></i></button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="8" class="text-center py-4 text-muted">No hotels found.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3">{{ $hotels->withQueryString()->links() }}</div>
</div></div>
@endsection