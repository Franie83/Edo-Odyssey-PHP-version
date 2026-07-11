@extends('layouts.admin')
@section('title', 'Reviews – Admin')
@section('page_title', 'Reviews')
@section('page_subtitle', 'Moderate user reviews')
@section('content')
<div class="mb-3">
  <form method="GET" class="d-flex gap-2">
    <select name="type" class="form-select form-select-sm" style="width:auto">
      <option value="">All Types</option>
      @foreach(['Attraction','Guide','Hotel','Restaurant','Event'] as $t)
        <option value="{{ $t }}" {{ request('type')==$t?'selected':'' }}>{{ $t }}</option>
      @endforeach
    </select>
    <select name="status" class="form-select form-select-sm" style="width:auto">
      <option value="">All Statuses</option>
      <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
      <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
    </select>
    <button class="btn btn-blue btn-sm">Filter</button>
  </form>
</div>
<div class="card border-0 shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 admin-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Type</th>
            <th>Rating</th>
            <th>Review</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        @forelse($reviews as $r)
        <tr>
          <td class="small">{{ $r->user?->full_name }}</td>
          <td><span class="badge bg-blue-100 text-blue">{{ $r->target_type }}</span></td>
          <td>
            <div class="star-rating small">
              @for($i=1;$i<=5;$i++)
                <i class="bi bi-star{{ $i<=$r->rating?'-fill':'' }}"></i>
              @endfor
            </div>
          </td>
          <td class="small">{{ Str::limit($r->comment, 60) }}</td>
          <td>
            @if($r->is_approved)
              <span class="badge bg-success">Approved</span>
            @else
              <span class="badge bg-warning text-dark">Pending</span>
            @endif
          </td>
          <td class="small text-muted">{{ $r->created_at?->format('d M Y') }}</td>
          <td>
            <div class="d-flex gap-1">
              @if(!$r->is_approved)
                <form method="POST" action="{{ route('admin.review_approve', $r->id) }}" class="d-inline">
                  @csrf
                  <button class="btn btn-success btn-sm py-0" title="Approve"><i class="bi bi-check-lg"></i></button>
                </form>
              @endif
              <form method="POST" action="{{ route('admin.review_delete', $r->id) }}" class="d-inline" onsubmit="return confirm('Delete this review?')">
                @csrf
                <button class="btn btn-outline-danger btn-sm py-0"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center py-4 text-muted">No reviews found.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">{{ $reviews->withQueryString()->links() }}</div>
  </div>
</div>
@endsection