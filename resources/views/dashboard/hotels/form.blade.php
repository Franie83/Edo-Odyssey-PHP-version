@extends('layouts.app')
@section('title', ($hotel->id ? 'Edit Hotel' : 'Add Hotel') . ' – Dashboard')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold text-blue">{{ $hotel->id ? 'Edit Hotel' : 'Add New Hotel' }}</div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger py-2 small"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                    @endif

                    <form method="POST" action="{{ $hotel->id ? route('dashboard.hotels.update', $hotel->id) : route('dashboard.hotels.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if($hotel->id)
                            @method('PUT')
                        @endif

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Hotel Name *</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $hotel->name) }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Description</label>
                                <textarea name="description" class="form-control" rows="4">{{ old('description', $hotel->description) }}</textarea>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold small">Address</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address', $hotel->address) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">City</label>
                                <input type="text" name="city" class="form-control" value="{{ old('city', $hotel->city) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Price per Night (₦) *</label>
                                <input type="number" name="price_per_night" class="form-control" step="500" value="{{ old('price_per_night', $hotel->price_per_night) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Stars</label>
                                <select name="stars" class="form-select">
                                    @for($s=1;$s<=5;$s++)
                                        <option value="{{ $s }}" {{ old('stars', $hotel->stars)==$s?'selected':'' }}>{{ $s }} Star{{ $s>1?'s':'' }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $hotel->phone) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Check‑in Time</label>
                                <input type="text" name="check_in_time" class="form-control" value="{{ old('check_in_time', $hotel->check_in_time) }}" placeholder="2:00 PM">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Check‑out Time</label>
                                <input type="text" name="check_out_time" class="form-control" value="{{ old('check_out_time', $hotel->check_out_time) }}" placeholder="12:00 PM">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Amenities</label>
                                <input type="text" name="amenities" class="form-control" value="{{ old('amenities', $hotel->amenities) }}" placeholder="Pool, Gym, WiFi, Restaurant">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Main Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                @if($hotel->image_url)
                                    <small class="text-muted">Current: <a href="{{ \App\Helpers\Helpers::imageUrl($hotel->image_url) }}" target="_blank">View</a></small>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button class="btn btn-gold">{{ $hotel->id ? 'Update' : 'Add' }} Hotel</button>
                            <a href="{{ route('dashboard.hotels.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                        <small class="text-muted d-block mt-3">Your hotel will be set to <strong>Pending</strong> and must be approved by an admin.</small>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection