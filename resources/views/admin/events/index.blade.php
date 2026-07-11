@extends('layouts.admin')
@section('title', 'Events – Admin')
@section('page_title', 'Events')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <form class="d-flex gap-2" method="GET">
    <input type="search" name="q" class="form-control form-control-sm" placeholder="Search events…" value="{{ request('q') }}" style="width:200px">
    <select name="status" class="form-select form-select-sm" style="width:auto">
      <option value="">All Statuses</option>
      <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
      <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Pending</option>
    </select>
    <button class="btn btn-blue btn-sm">Filter</button>
  </form>
  <a href="{{ route('admin.event_create') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus me-1"></i>Add Event</a>
</div>

<div class="card border-0 shadow-sm"><div class="card-body p-0">
  <div class="table-responsive"><table class="table table-hover mb-0 admin-table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Category</th>
        <th>Date</th>
        <th>Location</th>
        <th>User</th>
        <th>Price</th>
        <th>Featured</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    @forelse($events as $e)
    <tr>
      <td class="fw-semibold small">{{ $e->name }}</td>
      <td class="small">{{ $e->category }}</td>
      <td class="small">{{ $e->start_date?->format('d M Y') ?? 'TBD' }}</td>
      <td class="small">{{ Str::limit($e->location, 25) }}</td>
      <td class="small">{{ $e->user?->full_name ?? '—' }}</td>
      <td class="small text-gold fw-semibold">{{ $e->ticket_price == 0 ? 'Free' : '₦'.number_format($e->ticket_price) }}</td>
      <td>@if($e->is_featured)<span class="badge bg-gold text-dark">Featured</span>@else<span class="text-muted">—</span>@endif</td>
      <td>
        <div class="d-flex gap-1">
          <a href="{{ route('admin.event_edit', $e->id) }}" class="btn btn-outline-secondary btn-sm py-0"><i class="bi bi-pencil"></i></a>
          @if(!$e->is_active)
            <form method="POST" action="{{ route('admin.event_approve', $e->id) }}" class="d-inline">
              @csrf
              <button class="btn btn-success btn-sm py-0" title="Approve"><i class="bi bi-check-lg"></i></button>
            </form>
          @endif
          <form method="POST" action="{{ route('admin.event_delete', $e->id) }}" class="d-inline" onsubmit="return confirm('Delete this event?')">
            @csrf
            <button class="btn btn-outline-danger btn-sm py-0"><i class="bi bi-trash"></i></button>
          </form>
        </div>
      </td>
    </tr>
    @empty<tr><td colspan="8" class="text-center py-4 text-muted">No events.</td></tr>
    @endforelse
    </tbody>
  </table></div>
  <div class="p-3">{{ $events->withQueryString()->links() }}</div>
</div></div>
@endsection