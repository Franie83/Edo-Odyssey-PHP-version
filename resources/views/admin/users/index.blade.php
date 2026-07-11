@extends('layouts.admin')
@section('title', 'Users – Admin')
@section('page_title', 'Users')
@section('page_subtitle', 'Manage platform users')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <form class="d-flex gap-2" method="GET">
    <input type="search" name="q" class="form-control form-control-sm" placeholder="Search users…" value="{{ request('q') }}">
    <select name="role" class="form-select form-select-sm" style="width:auto">
      <option value="">All Roles</option>
      @foreach(['Tourist','Guide','Hotel','Restaurant','Agency Admin','Super Admin'] as $r)
      <option value="{{ $r }}" {{ request('role')==$r?'selected':'' }}>{{ $r }}</option>
      @endforeach
    </select>
    <button class="btn btn-blue btn-sm">Filter</button>
  </form>
  <a href="{{ route('admin.user_create') }}" class="btn btn-gold btn-sm"><i class="bi bi-person-plus me-1"></i>Add User</a>
</div>
<div class="card border-0 shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 admin-table">
        <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Heritage Pts</th><th>Joined</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($users as $u)
        <tr>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="avatar-initials-sm flex-shrink-0">{{ $u->first_name[0] }}{{ $u->last_name[0] }}</div>
              <span class="fw-semibold small">{{ $u->full_name }}</span>
            </div>
          </td>
          <td class="small">{{ $u->email }}</td>
          <td><span class="badge bg-blue-100 text-blue">{{ $u->role }}</span></td>
          <td class="text-gold fw-semibold">{{ $u->heritage_points }}</td>
          <td class="small text-muted">{{ $u->created_at?->format('d M Y') }}</td>
          <td>
            <a href="{{ route('admin.user_edit', $u->id) }}" class="btn btn-outline-secondary btn-sm py-0"><i class="bi bi-pencil"></i></a>
            @if($u->id !== Auth::id())
            <form method="POST" action="{{ route('admin.user_delete', $u->id) }}" class="d-inline" onsubmit="return confirm('Delete this user?')">
              @csrf
              <button class="btn btn-outline-danger btn-sm py-0"><i class="bi bi-trash"></i></button>
            </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center py-4 text-muted">No users found.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">{{ $users->withQueryString()->links() }}</div>
  </div>
</div>
@endsection
