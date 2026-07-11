@extends('layouts.admin')
@section('title', 'Bookings – Admin')
@section('page_title', 'Bookings')
@section('page_subtitle', 'Manage all bookings')
@section('content')
<div class="d-flex gap-2 mb-3">
  <form method="GET" class="d-flex gap-2">
    <select name="status" class="form-select form-select-sm" style="width:auto"><option value="">All Statuses</option>@foreach(['Pending','AdminApproved','AdminRejected','VendorAccepted','VendorRejected','Completed','Cancelled'] as $s)<option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ $s }}</option>@endforeach</select>
    <select name="type" class="form-select form-select-sm" style="width:auto"><option value="">All Types</option>@foreach(['Guide','Hotel','Restaurant','Attraction','Event'] as $t)<option value="{{ $t }}" {{ request('type')==$t?'selected':'' }}>{{ $t }}</option>@endforeach</select>
    <button class="btn btn-blue btn-sm">Filter</button>
  </form>
</div>
<div class="card border-0 shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 admin-table">
        <thead><tr><th>Reference</th><th>User</th><th>Type</th><th>Target</th><th>Date</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($bookings as $b)
        <tr>
          <td><code class="text-blue small">{{ $b->reference_code }}</code></td>
          <td class="small">{{ $b->user?->full_name }}</td>
          <td><span class="badge bg-blue-100 text-blue">{{ $b->booking_type }}</span></td>
          <td class="small">{{ Str::limit($b->target_name, 20) }}</td>
          <td class="small">{{ $b->start_date?->format('d M Y') }}</td>
          <td class="text-gold fw-semibold small">₦{{ number_format($b->total_price) }}</td>
          <td>
            @php
              $statusColors = [
                'Pending'        => 'warning text-dark',
                'AdminApproved'  => 'info',
                'AdminRejected'  => 'danger',
                'VendorAccepted' => 'success',
                'VendorRejected' => 'danger',
                'Completed'      => 'secondary',
                'Cancelled'      => 'secondary',
              ];
              $color = $statusColors[$b->booking_status] ?? 'secondary';
            @endphp
            <span class="status-badge badge bg-{{ $color }}">{{ $b->booking_status }}</span>
          </td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('admin.booking_detail', $b->id) }}" class="btn btn-outline-info btn-sm py-0" title="View Details"><i class="bi bi-eye"></i></a>
              @if($b->booking_status === 'Pending')
                <form method="POST" action="{{ route('admin.booking_approve', $b->id) }}" class="d-inline">@csrf<button class="btn btn-success btn-sm py-0">Approve</button></form>
                <form method="POST" action="{{ route('admin.booking_reject', $b->id) }}" class="d-inline">@csrf<button class="btn btn-danger btn-sm py-0">Reject</button></form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center py-4 text-muted">No bookings found.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">{{ $bookings->withQueryString()->links() }}</div>
  </div>
</div>
@endsection