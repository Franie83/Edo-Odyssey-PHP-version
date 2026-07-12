@extends('layouts.admin')
@section('title', 'Attractions – Admin')
@section('page_title', 'Attractions')
@section('page_subtitle', 'Manage tourism attractions')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <form class="d-flex gap-2" method="GET">
    <input type="search" name="q" class="form-control form-control-sm" placeholder="Search attractions…" value="{{ request('q') }}" style="width:220px">
    <button class="btn btn-blue btn-sm">Filter</button>
  </form>
  <div class="d-flex gap-2">
    {{-- Add Attraction --}}
    <a href="{{ route('admin.attraction_create') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus me-1"></i>Add Attraction</a>
    {{-- Regenerate All QR --}}
    <form method="POST" action="{{ route('admin.attractions.generate_all_qr') }}" class="d-inline">
      @csrf
      <button class="btn btn-outline-primary btn-sm" title="Regenerate QR codes for all attractions" onclick="return confirm('This will regenerate QR codes for ALL attractions. Proceed?')">
        <i class="bi bi-qr-code me-1"></i>Regenerate All QR
      </button>
    </form>
  </div>
</div>
<div class="card border-0 shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 admin-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Category</th>
            <th>User</th>
            <th>Price</th>
            <th>Views</th>
            <th>Featured</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        @forelse($attractions as $a)
        <tr>
          <td>
            <div class="d-flex align-items-center gap-2">
              <img src="{{ \App\Helpers\Helpers::imageUrl($a->image_url) }}" 
                   width="40" height="35" style="object-fit:cover;border-radius:6px" alt="{{ $a->name }}">
              <span class="fw-semibold small">{{ $a->name }}</span>
            </div>
          </td>
          <td class="small">{{ $a->category?->name ?? '—' }}</td>
          <td class="small">{{ $a->user?->full_name ?? '—' }}</td>
          <td class="small text-gold fw-semibold">{{ $a->ticket_price == 0 ? 'Free' : '₦'.number_format($a->ticket_price) }}</td>
          <td class="small">{{ number_format($a->views) }}</td>
          <td>@if($a->is_featured)<span class="badge bg-gold text-dark">Featured</span>@else<span class="text-muted">—</span>@endif</td>
          <td><span class="status-badge badge {{ $a->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $a->is_active ? 'Active' : 'Inactive' }}</span></td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('admin.attraction_edit', $a->id) }}" class="btn btn-outline-secondary btn-sm py-0"><i class="bi bi-pencil"></i></a>
              <form method="POST" action="{{ route('admin.attraction_regen_qr', $a->id) }}" class="d-inline">@csrf<button class="btn btn-outline-info btn-sm py-0" title="Regenerate QR"><i class="bi bi-qr-code"></i></button></form>
              <form method="POST" action="{{ route('admin.attraction_delete', $a->id) }}" class="d-inline" onsubmit="return confirm('Delete this attraction?')">@csrf<button class="btn btn-outline-danger btn-sm py-0"><i class="bi bi-trash"></i></button></form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center py-4 text-muted">No attractions found.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">{{ $attractions->withQueryString()->links() }}</div>
  </div>
</div>
@endsection