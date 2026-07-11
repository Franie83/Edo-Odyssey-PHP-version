@extends('layouts.admin')
@section('title', ($guide->id ? 'Edit Guide' : 'New Guide') . ' – Admin')
@section('page_title', $guide->id ? 'Edit Guide Profile' : 'Add New Guide')
@section('page_subtitle', $guide->id ? $guide->user?->full_name : '')
@section('content')
<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        @if($errors->any())
        <div class="alert alert-danger py-2 small"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <form method="POST" action="{{ $guide->id ? route('admin.guide_update', $guide->id) : route('admin.guide_store') }}" enctype="multipart/form-data">
          @csrf
          @if($guide->id) @method('PUT') @endif

          <div class="row g-3">
            {{-- User dropdown --}}
            <div class="col-12">
              <label class="form-label fw-semibold small">Assigned User *</label>
              <select name="user_id" class="form-select" required>
                <option value="">Select user</option>
                @foreach($users as $user)
                  <option value="{{ $user->id }}" {{ old('user_id', $guide->user_id) == $user->id ? 'selected' : '' }}>
                    {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold small">Bio</label>
              <textarea name="bio" class="form-control" rows="4">{{ old('bio', $guide->bio) }}</textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Languages <span class="text-muted">(comma-separated)</span></label>
              <input type="text" name="languages" class="form-control" value="{{ old('languages', $guide->languages) }}" placeholder="English, Yoruba, Igbo">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Specializations</label>
              <input type="text" name="specializations" class="form-control" value="{{ old('specializations', $guide->specializations) }}" placeholder="History, Culture, Adventure">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Hourly Rate (₦)</label>
              <input type="number" name="hourly_rate" class="form-control" value="{{ old('hourly_rate', $guide->hourly_rate) }}" min="0" step="100">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Daily Rate (₦)</label>
              <input type="number" name="daily_rate" class="form-control" value="{{ old('daily_rate', $guide->daily_rate) }}" min="0" step="100">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Experience (years)</label>
              <input type="number" name="experience" class="form-control" value="{{ old('experience', $guide->experience) }}" min="0" max="50">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold small">Certification</label>
              <input type="text" name="certification" class="form-control" value="{{ old('certification', $guide->certification) }}" placeholder="Licensed by Edo State Tourism Board">
            </div>

            {{-- Verification status (editable by admin) --}}
            @if($guide->id)
            <div class="col-12">
              <label class="form-label fw-semibold small">Verification Status</label>
              <select name="verification_status" class="form-select">
                <option value="Pending" {{ old('verification_status', $guide->verification_status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Approved" {{ old('verification_status', $guide->verification_status) == 'Approved' ? 'selected' : '' }}>Approved</option>
                <option value="Rejected" {{ old('verification_status', $guide->verification_status) == 'Rejected' ? 'selected' : '' }}>Rejected</option>
              </select>
            </div>
            @endif

            <div class="col-md-6">
              <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $guide->is_featured) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold small" for="is_featured">Featured Guide</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" name="is_available" id="is_available" value="1" {{ old('is_available', $guide->is_available ?? true) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold small" for="is_available">Available for Booking</label>
              </div>
            </div>
          </div>
          <div class="d-flex gap-2 mt-4">
            <button class="btn btn-gold">{{ $guide->id ? 'Save Changes' : 'Create Guide' }}</button>
            <a href="{{ route('admin.guides') }}" class="btn btn-outline-secondary">Cancel</a>
            @if($guide->id)
            <form method="POST" action="{{ route('admin.guide_delete', $guide->id) }}" class="ms-auto d-inline" onsubmit="return confirm('Delete this guide profile?')">
              @csrf<button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Delete</button>
            </form>
            @endif
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection