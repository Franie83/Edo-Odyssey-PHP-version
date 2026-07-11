@extends('layouts.admin')
@section('title', 'Audit Logs – Admin')
@section('page_title', 'Audit Logs')
@section('page_subtitle', 'Track all admin and user actions')
@section('content')
<div class="mb-3">
  <form method="GET" class="d-flex gap-2">
    <input type="search" name="q" class="form-control form-control-sm" placeholder="Search logs…" value="{{ request('q') }}" style="width:280px">
    <button class="btn btn-blue btn-sm">Search</button>
  </form>
</div>
<div class="card border-0 shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 admin-table">
        <thead><tr><th>When</th><th>User</th><th>Action</th><th>Entity</th><th>IP</th><th>Description</th></tr></thead>
        <tbody>
        @forelse($logs as $log)
        <tr>
          <td class="small text-muted">{{ $log->created_at?->format('d M Y H:i') }}</td>
          <td class="small">{{ $log->user?->full_name ?? 'System' }}</td>
          <td><span class="badge bg-blue-100 text-blue small">{{ $log->action }}</span></td>
          <td class="small text-muted">{{ $log->entity_type }} {{ $log->entity_id ? "#$log->entity_id" : '' }}</td>
          <td class="small text-muted">{{ $log->ip_address }}</td>
          <td class="small">{{ Str::limit($log->description, 60) }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center py-4 text-muted">No audit logs yet.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">{{ $logs->withQueryString()->links() }}</div>
  </div>
</div>
@endsection
