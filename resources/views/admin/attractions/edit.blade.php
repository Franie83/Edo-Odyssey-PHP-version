@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-9">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.attractions.update', $attraction->id) }}" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label fw-semibold small">Name *</label>
              <input type="text" name="name" class="form-control" value="{{ old('name', $attraction->name) }}" required>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold small">Category</label>
              <select name="category_id" class="form-select">
                <option value="">Select category</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}" {{ $attraction->category_id == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold small">Description</label>
              <textarea name="description" class="form-control" rows="4">{{ old('description', $attraction->description) }}</textarea>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold small">Historical Background</label>
              <textarea name="history" class="form-control" rows="3">{{ old('history', $attraction->history) }}</textarea>
            </div>

            <div class="col-md-8">
              <label class="form-label fw-semibold small">Address</label>
              <input type="text" name="address" class="form-control" value="{{ old('address', $attraction->address) }}">
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold small">City</label>
              <input type="text" name="city" class="form-control" value="{{ old('city', $attraction->city) }}">
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold small">Ticket Price (₦)</label>
              <input type="number" name="ticket_price" class="form-control" step="0.01" value="{{ old('ticket_price', $attraction->ticket_price) }}">
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold small">Opening Hours</label>
              <input type="text" name="opening_hours" class="form-control" value="{{ old('opening_hours', $attraction->opening_hours) }}" placeholder="9am–5pm daily">
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold small">Phone</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone', $attraction->phone) }}">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold small">Website</label>
              <input type="url" name="website" class="form-control" value="{{ old('website', $attraction->website) }}">
            </div>

            <div class="col-md-3">
              <label class="form-label fw-semibold small">Latitude</label>
              <input type="number" name="latitude" class="form-control" step="any" value="{{ old('latitude', $attraction->latitude) }}">
            </div>

            <div class="col-md-3">
              <label class="form-label fw-semibold small">Longitude</label>
              <input type="number" name="longitude" class="form-control" step="any" value="{{ old('longitude', $attraction->longitude) }}">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold small">Main Image</label>
              <input type="file" name="image" class="form-control" accept="image/*">
              @if($attraction->image_url)
                <small class="text-muted">Current: 
                  <a href="{{ asset('storage/' . $attraction->image_url) }}" target="_blank">View</a>
                </small>
              @endif
            </div>

            <div class="col-md-3">
              <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" {{ $attraction->is_featured ? 'checked' : '' }}>
                <label class="form-check-label" for="is_featured">Featured</label>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ $attraction->is_active ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active</label>
              </div>
            </div>
          </div>

          <div class="d-flex gap-2 mt-4">
            <button class="btn btn-gold">Update Attraction</button>
            <a href="{{ route('admin.attractions.index') }}" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection