@extends('layouts.app')
@section('title', ($restaurant->id ? 'Edit Restaurant' : 'Add Restaurant') . ' – Dashboard')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold text-blue">{{ $restaurant->id ? 'Edit Restaurant' : 'Add New Restaurant' }}</div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger py-2 small"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                    @endif

                    <form method="POST" action="{{ $restaurant->id ? route('dashboard.restaurants.update', $restaurant->id) : route('dashboard.restaurants.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if($restaurant->id)
                            @method('PUT')
                        @endif

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Restaurant Name *</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $restaurant->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Cuisine Type</label>
                                <input type="text" name="cuisine_type" class="form-control" value="{{ old('cuisine_type', $restaurant->cuisine_type) }}" placeholder="Nigerian, Continental…">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Average Price (₦)</label>
                                <input type="number" name="avg_price" class="form-control" value="{{ old('avg_price', $restaurant->avg_price) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $restaurant->description) }}</textarea>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold small">Address</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address', $restaurant->address) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">City</label>
                                <input type="text" name="city" class="form-control" value="{{ old('city', $restaurant->city) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $restaurant->phone) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Opening Hours</label>
                                <input type="text" name="opening_hours" class="form-control" value="{{ old('opening_hours', $restaurant->opening_hours) }}" placeholder="11am–10pm">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Main Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                @if($restaurant->image_url)
                                    <small class="text-muted">Current: <a href="{{ \App\Helpers\Helpers::imageUrl($restaurant->image_url) }}" target="_blank">View</a></small>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button class="btn btn-gold">{{ $restaurant->id ? 'Update' : 'Add' }} Restaurant</button>
                            <a href="{{ route('dashboard.restaurants.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                        <small class="text-muted d-block mt-3">Your restaurant will be set to <strong>Pending</strong> and must be approved by an admin.</small>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection