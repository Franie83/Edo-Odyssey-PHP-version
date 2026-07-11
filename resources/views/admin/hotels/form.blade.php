@extends('layouts.admin')
@section('title', ($hotel->id ? 'Edit Hotel' : 'New Hotel') . ' – Admin')
@section('page_title', $hotel->id ? 'Edit Hotel' : 'Add Hotel')
@section('content')
<div class="row justify-content-center"><div class="col-md-9">
  <div class="card border-0 shadow-sm"><div class="card-body p-4">
    <form method="POST" action="{{ $hotel->id ? route('admin.hotel_update', $hotel->id) : route('admin.hotel_store') }}" enctype="multipart/form-data">
      @csrf
      <div class="row g-3">
        <div class="col-md-8"><label class="form-label fw-semibold small">Name *</label><input type="text" name="name" class="form-control" value="{{ old('name', $hotel->name) }}" required></div>
        <div class="col-md-4"><label class="form-label fw-semibold small">Stars</label><select name="stars" class="form-select">@for($s=1;$s<=5;$s++)<option value="{{ $s }}" {{ old('stars', $hotel->stars)==$s?'selected':'' }}>{{ $s }} Star{{ $s>1?'s':'' }}</option>@endfor</select></div>

        {{-- ✅ Assigned User dropdown --}}
        <div class="col-12">
          <label class="form-label fw-semibold small">Assigned User *</label>
          <select name="user_id" class="form-select" required>
            <option value="">Select user</option>
            @foreach($users as $user)
              <option value="{{ $user->id }}" {{ old('user_id', $hotel->user_id) == $user->id ? 'selected' : '' }}>
                {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-12"><label class="form-label fw-semibold small">Description</label><textarea name="description" class="form-control" rows="4">{{ old('description', $hotel->description) }}</textarea></div>
        <div class="col-md-8"><label class="form-label fw-semibold small">Address</label><input type="text" name="address" class="form-control" value="{{ old('address', $hotel->address) }}"></div>
        <div class="col-md-4"><label class="form-label fw-semibold small">City</label><input type="text" name="city" class="form-control" value="{{ old('city', $hotel->city) }}"></div>
        <div class="col-md-4"><label class="form-label fw-semibold small">Price/Night (₦) *</label><input type="number" name="price_per_night" class="form-control" step="500" value="{{ old('price_per_night', $hotel->price_per_night) }}" required></div>
        <div class="col-md-4"><label class="form-label fw-semibold small">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $hotel->phone) }}"></div>
        <div class="col-md-4"><label class="form-label fw-semibold small">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $hotel->email) }}"></div>
        <div class="col-md-4"><label class="form-label fw-semibold small">Check-in Time</label><input type="text" name="check_in_time" class="form-control" value="{{ old('check_in_time', $hotel->check_in_time) }}" placeholder="2:00 PM"></div>
        <div class="col-md-4"><label class="form-label fw-semibold small">Check-out Time</label><input type="text" name="check_out_time" class="form-control" value="{{ old('check_out_time', $hotel->check_out_time) }}" placeholder="12:00 PM"></div>
        <div class="col-md-4"><label class="form-label fw-semibold small">Website</label><input type="url" name="website" class="form-control" value="{{ old('website', $hotel->website) }}"></div>
        <div class="col-12"><label class="form-label fw-semibold small">Amenities</label><input type="text" name="amenities" class="form-control" value="{{ old('amenities', $hotel->amenities) }}" placeholder="Pool, Gym, WiFi, Restaurant"></div>
        <div class="col-md-6"><label class="form-label fw-semibold small">Main Image</label><input type="file" name="image" class="form-control" accept="image/*">@if($hotel->image_url)<small class="text-muted">Current: <a href="{{ \App\Helpers\Helpers::imageUrl($hotel->image_url) }}" target="_blank">View</a></small>@endif</div>
        <div class="col-md-3"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="is_featured" {{ old('is_featured', $hotel->is_featured)?'checked':'' }}><label class="form-check-label">Featured</label></div></div>
        <div class="col-md-3"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="is_active" {{ old('is_active', $hotel->is_active ?? true)?'checked':'' }}><label class="form-check-label">Active</label></div></div>
      </div>
      <div class="d-flex gap-2 mt-4">
        <button class="btn btn-gold">{{ $hotel->id ? 'Update Hotel' : 'Create Hotel' }}</button>
        <a href="{{ route('admin.hotels') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div></div>
</div></div>
@endsection