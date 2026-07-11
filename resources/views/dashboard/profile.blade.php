@extends('layouts.app')
@section('title', 'Profile – Edo Odyssey')
@section('content')
<div class="page-hero">
  <div class="container">
    <h1>My Profile</h1>
    <p class="text-white-50">Manage your account details</p>
  </div>
</div>
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          @if($errors->any())<div class="alert alert-danger py-2 small"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
          <form method="POST" action="{{ route('dashboard.update_profile') }}" enctype="multipart/form-data">
            @csrf
            <div class="text-center mb-4">
              @if($user->avatar_url)
              <img src="{{ \App\Helpers\Helpers::imageUrl($user->avatar_url) }}" class="rounded-circle border border-gold border-3" width="80" height="80" style="object-fit:cover" alt="">
              @else
              <div class="avatar-lg mx-auto">{{ $user->first_name[0] }}{{ $user->last_name[0] }}</div>
              @endif
              <div class="mt-2"><label class="btn btn-outline-secondary btn-sm"><i class="bi bi-camera me-1"></i>Change Photo<input type="file" name="avatar" class="d-none" accept="image/*"></label></div>
            </div>
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label fw-semibold small">First Name *</label><input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required></div>
              <div class="col-md-6"><label class="form-label fw-semibold small">Last Name *</label><input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required></div>
              <div class="col-12"><label class="form-label fw-semibold small">Email</label><input type="email" class="form-control bg-gray-50" value="{{ $user->email }}" disabled></div>
              <div class="col-12"><label class="form-label fw-semibold small">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}"></div>
            </div>

            @if($user->role === 'Guide' && $user->guide)
            <hr class="my-4">
            <h6 class="fw-bold text-blue mb-3"><i class="bi bi-person-badge me-2 text-gold"></i>Guide Profile</h6>
            <div class="row g-3">
              <div class="col-12"><label class="form-label fw-semibold small">Bio</label><textarea name="bio" class="form-control" rows="3">{{ old('bio', $user->guide->bio) }}</textarea></div>
              <div class="col-md-6"><label class="form-label fw-semibold small">Languages (comma-separated)</label><input type="text" name="languages" class="form-control" value="{{ old('languages', $user->guide->languages) }}" placeholder="English, Yoruba, Bini"></div>
              <div class="col-md-6"><label class="form-label fw-semibold small">Specializations</label><input type="text" name="specializations" class="form-control" value="{{ old('specializations', $user->guide->specializations) }}"></div>
              <div class="col-md-4"><label class="form-label fw-semibold small">Experience (years)</label><input type="number" name="experience" class="form-control" value="{{ old('experience', $user->guide->experience) }}" min="0"></div>
              <div class="col-md-4"><label class="form-label fw-semibold small">Hourly Rate (₦)</label><input type="number" name="hourly_rate" class="form-control" value="{{ old('hourly_rate', $user->guide->hourly_rate) }}" step="500"></div>
              <div class="col-md-4"><label class="form-label fw-semibold small">Daily Rate (₦)</label><input type="number" name="daily_rate" class="form-control" value="{{ old('daily_rate', $user->guide->daily_rate) }}" step="1000"></div>
            </div>
            @endif

            <hr class="my-4">
            <h6 class="fw-bold text-blue mb-3"><i class="bi bi-lock me-2 text-gold"></i>Change Password</h6>
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label fw-semibold small">New Password</label><input type="password" name="password" class="form-control" placeholder="Leave blank to keep current" minlength="6"></div>
              <div class="col-md-6"><label class="form-label fw-semibold small">Confirm Password</label><input type="password" name="password_confirmation" class="form-control"></div>
            </div>

            <div class="d-flex gap-2 mt-4">
              <button class="btn btn-gold px-4">Save Changes</button>
              <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
