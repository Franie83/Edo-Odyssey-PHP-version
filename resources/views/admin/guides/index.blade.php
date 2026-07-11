@extends('layouts.admin')
@section('title', 'Guides – Admin')
@section('page_title', 'Tour Guides')
@section('page_subtitle', 'Review and verify guide applications')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <form method="GET" class="d-flex gap-2">
    <input type="search" name="q" class="form-control form-control-sm" placeholder="Search guides…" value="{{ request('q') }}" style="width:220px">
    <select name="status" class="form-select form-select-sm" style="width:auto">
      <option value="">All Statuses</option>
      @foreach(['Pending','Approved','Rejected'] as $s)
        <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ $s }}</option>
      @endforeach
    </select>
    <button class="btn btn-blue btn-sm">Filter</button>
  </form>
  <a href="{{ route('admin.guide_create') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus me-1"></i>Add Guide</a>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 admin-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Rate</th>
            <th>Exp</th>
            <th>Languages</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        @forelse($guides as $g)
        <tr>
          <td>
            <div class="fw-semibold small">{{ $g->user?->full_name }}</div>
            <div class="text-muted" style="font-size:.7rem">{{ $g->user?->email }}</div>
          </td>
          <td class="small text-gold fw-semibold">₦{{ number_format($g->hourly_rate) }}/hr</td>
          <td class="small">{{ $g->experience }} yrs</td>
          <td class="small">{{ Str::limit($g->languages, 30) }}</td>
          <td>
            @if($g->verification_status==='Approved')
              <span class="badge-verified">Approved</span>
            @elseif($g->verification_status==='Pending')
              <span class="badge-pending">Pending</span>
            @else
              <span class="badge-rejected">Rejected</span>
            @endif
          </td>
          <td>
            <div class="d-flex gap-1">
              @if($g->verification_status !== 'Approved')
              <form method="POST" action="{{ route('admin.guide_verify', $g->id) }}" class="d-inline">
                @csrf
                <input type="hidden" name="status" value="Approved">
                <button class="btn btn-success btn-sm py-0">Approve</button>
              </form>
              @endif
              @if($g->verification_status !== 'Rejected')
              <form method="POST" action="{{ route('admin.guide_verify', $g->id) }}" class="d-inline">
                @csrf
                <input type="hidden" name="status" value="Rejected">
                <button class="btn btn-danger btn-sm py-0">Reject</button>
              </form>
              @endif
              <a href="{{ route('admin.guide_edit', $g->id) }}" class="btn btn-outline-secondary btn-sm py-0"><i class="bi bi-pencil"></i></a>
              <form method="POST" action="{{ route('admin.guide_delete', $g->id) }}" class="d-inline" onsubmit="return confirm('Delete this guide?')">
                @csrf
                <button class="btn btn-outline-danger btn-sm py-0"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center py-4 text-muted">No guides found.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">{{ $guides->withQueryString()->links() }}</div>
  </div>
</div>
@endsection