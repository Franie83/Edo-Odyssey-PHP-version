@extends('layouts.admin')
@section('title', ($attraction->id ? 'Edit Attraction' : 'New Attraction') . ' – Admin')
@section('page_title', $attraction->id ? 'Edit Attraction' : 'Add Attraction')
@section('content')
<div class="row justify-content-center">
  <div class="col-md-9">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <form method="POST" action="{{ $attraction->id ? route('admin.attraction_update', $attraction->id) : route('admin.attraction_store') }}" enctype="multipart/form-data">
          @csrf
          <div class="row g-3">
            <div class="col-md-8"><label class="form-label fw-semibold small">Name *</label><input type="text" name="name" class="form-control" value="{{ old('name', $attraction->name) }}" required></div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Category</label>
              <select name="category_id" class="form-select">
                <option value="">Select category</option>
                @foreach($categories as $cat)<option value="{{ $cat->id }}" {{ old('category_id', $attraction->category_id)==$cat->id?'selected':'' }}>{{ $cat->name }}</option>@endforeach
              </select>
            </div>

            {{-- Assigned User --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Assigned User *</label>
              <select name="user_id" class="form-select" required>
                <option value="">Select user</option>
                @foreach($users as $user)
                  <option value="{{ $user->id }}" {{ old('user_id', $attraction->user_id) == $user->id ? 'selected' : '' }}>
                    {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-12"><label class="form-label fw-semibold small">Description</label><textarea name="description" class="form-control" rows="4">{{ old('description', $attraction->description) }}</textarea></div>
            <div class="col-12"><label class="form-label fw-semibold small">Historical Background</label><textarea name="history" class="form-control" rows="3">{{ old('history', $attraction->history) }}</textarea></div>
            <div class="col-md-8"><label class="form-label fw-semibold small">Address</label><input type="text" name="address" class="form-control" value="{{ old('address', $attraction->address) }}"></div>
            <div class="col-md-4"><label class="form-label fw-semibold small">City</label><input type="text" name="city" class="form-control" value="{{ old('city', $attraction->city) }}"></div>
            <div class="col-md-4"><label class="form-label fw-semibold small">Ticket Price (₦)</label><input type="number" name="ticket_price" class="form-control" step="0.01" value="{{ old('ticket_price', $attraction->ticket_price) }}"></div>
            <div class="col-md-4"><label class="form-label fw-semibold small">Opening Hours</label><input type="text" name="opening_hours" class="form-control" value="{{ old('opening_hours', $attraction->opening_hours) }}" placeholder="9am–5pm daily"></div>
            <div class="col-md-4"><label class="form-label fw-semibold small">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $attraction->phone) }}"></div>
            <div class="col-md-6"><label class="form-label fw-semibold small">Website</label><input type="url" name="website" class="form-control" value="{{ old('website', $attraction->website) }}"></div>
            <div class="col-md-3"><label class="form-label fw-semibold small">Latitude</label><input type="number" name="latitude" class="form-control" step="any" value="{{ old('latitude', $attraction->latitude) }}"></div>
            <div class="col-md-3"><label class="form-label fw-semibold small">Longitude</label><input type="number" name="longitude" class="form-control" step="any" value="{{ old('longitude', $attraction->longitude) }}"></div>
            
            {{-- Image Upload Field --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Upload Image</label>
              <input type="file" name="image" class="form-control" accept="image/*">
              <small class="text-muted">Upload a new image file</small>
              @if($attraction->image_url)
                <div class="mt-1">
                  <small class="text-muted">Current: <a href="{{ \App\Helpers\Helpers::imageUrl($attraction->image_url) }}" target="_blank">View Image</a></small>
                </div>
              @endif
            </div>
            
            {{-- Image URL Field --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Image URL</label>
              <input type="url" name="image_url" class="form-control" placeholder="https://example.com/image.jpg" value="{{ old('image_url', $attraction->image_url ?? '') }}">
              <small class="text-muted">Or paste an image URL (e.g., from Unsplash)</small>
            </div>
            
            <div class="col-md-3"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" {{ old('is_featured', $attraction->is_featured)?'checked':'' }}><label class="form-check-label" for="is_featured">Featured</label></div></div>
            <div class="col-md-3"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ old('is_active', $attraction->is_active ?? true)?'checked':'' }}><label class="form-check-label" for="is_active">Active</label></div></div>
          </div>
          <div class="d-flex gap-2 mt-4">
            <button class="btn btn-gold">{{ $attraction->id ? 'Update Attraction' : 'Create Attraction' }}</button>
            <a href="{{ route('admin.attractions') }}" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection