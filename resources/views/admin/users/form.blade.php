@extends('layouts.admin')
@section('title', ($user->id ? 'Edit User' : 'New User') . ' – Admin')
@section('page_title', $user->id ? 'Edit User' : 'Add User')
@section('content')
<div class="row justify-content-center">
  <div class="col-md-7">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        @if($errors->any())<div class="alert alert-danger py-2 small"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        <form method="POST" action="{{ $user->id ? route('admin.user_update', $user->id) : route('admin.user_store') }}">
          @csrf
          <div class="row g-3">
            <div class="col-6"><label class="form-label fw-semibold small">First Name *</label><input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required></div>
            <div class="col-6"><label class="form-label fw-semibold small">Last Name *</label><input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required></div>
            <div class="col-12"><label class="form-label fw-semibold small">Email *</label><input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required></div>
            <div class="col-md-6"><label class="form-label fw-semibold small">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}"></div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Role *</label>
              <select name="role" class="form-select" required>
                @foreach(['Tourist','Guide','Hotel','Restaurant','Agency Admin','Super Admin'] as $r)
                <option value="{{ $r }}" {{ old('role', $user->role)==$r?'selected':'' }}>{{ $r }}</option>
                @endforeach
              </select>
            </div>
            @if($user->id)
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Status</label>
              <select name="status" class="form-select">
                <option value="active" {{ $user->status=='active'?'selected':'' }}>Active</option>
                <option value="suspended" {{ $user->status=='suspended'?'selected':'' }}>Suspended</option>
              </select>
            </div>
            @endif
            <div class="col-12"><label class="form-label fw-semibold small">Password {{ $user->id ? '(leave blank to keep)' : '*' }}</label><input type="password" name="password" class="form-control" {{ !$user->id ? 'required' : '' }} minlength="6"></div>
          </div>
          <div class="d-flex gap-2 mt-4">
            <button class="btn btn-gold">{{ $user->id ? 'Update User' : 'Create User' }}</button>
            <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
