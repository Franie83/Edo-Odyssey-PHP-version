@extends('layouts.admin')
@section('title', 'Restaurants – Admin')
@section('page_title', 'Restaurants')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <form class="d-flex gap-2" method="GET">
    <input type="search" name="q" class="form-control form-control-sm" placeholder="Search restaurants…" value="{{ request('q') }}" style="width:200px">
    <select name="status" class="form-select form-select-sm" style="width:auto">
      <option value="">All Statuses</option>
      <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
      <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Pending</option>
    </select>
    <button class="btn btn-blue btn-sm">Filter</button>
  </form>
  <a href="{{ route('admin.restaurant_create') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus me-1"></i>Add Restaurant</a>
</div>

<div class="card border-0 shadow-sm"><div class="card-body p-0">
  <div class="table-responsive"><table class="table table-hover mb-0 admin-table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Cuisine</th>
        <th>City</th>
        <th>User</th>
        <th>Featured</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    @forelse($restaurants as $r)
    <tr>
      <td class="fw-semibold small">{{ $r->name }}</td>
      <td class="small">{{ $r->cuisine_type }}</td>
      <td class="small">{{ $r->city }}</td>
      <td class="small">{{ $r->user?->full_name ?? '—' }}</td>
      <td>@if($r->is_featured)<span class="badge bg-gold text-dark">Featured</span>@else<span class="text-muted">—</span>@endif</td>
      <td><span class="status-badge badge {{ $r->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $r->is_active ? 'Active' : 'Inactive' }}</span></td>
      <td>
        <div class="d-flex gap-1">
          <a href="{{ route('admin.restaurant_edit', $r->id) }}" class="btn btn-outline-secondary btn-sm py-0"><i class="bi bi-pencil"></i></a>
          @if(!$r->is_active)
            <form method="POST" action="{{ route('admin.restaurant_approve', $r->id) }}" class="d-inline">
              @csrf
              <button class="btn btn-success btn-sm py-0" title="Approve"><i class="bi bi-check-lg"></i></button>
            </form>
          @endif
          <form method="POST" action="{{ route('admin.restaurant_delete', $r->id) }}" class="d-inline" onsubmit="return confirm('Delete this restaurant?')">
            @csrf
            <button class="btn btn-outline-danger btn-sm py-0"><i class="bi bi-trash"></i></button>
          </form>
        </div>
      </td>
    </tr>
    @empty<tr><td colspan="7" class="text-center py-4 text-muted">No restaurants.</td></tr>
    @endforelse
    </tbody>
  </table></div>
  <div class="p-3">{{ $restaurants->withQueryString()->links() }}</div>
</div></div>
@endsection