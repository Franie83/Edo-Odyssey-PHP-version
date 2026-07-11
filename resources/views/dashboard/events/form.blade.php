@extends('layouts.app')
@section('title', ($event->id ? 'Edit Event' : 'Add Event') . ' – Dashboard')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold text-blue">{{ $event->id ? 'Edit Event' : 'Add New Event' }}</div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger py-2 small"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                    @endif

                    <form method="POST" action="{{ $event->id ? route('dashboard.events.update', $event->id) : route('dashboard.events.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if($event->id)
                            @method('PUT')
                        @endif

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Event Name *</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $event->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Category</label>
                                <input type="text" name="category" class="form-control" value="{{ old('category', $event->category) }}" placeholder="Cultural Festival, Music…">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Ticket Price (₦)</label>
                                <input type="number" name="ticket_price" class="form-control" step="100" value="{{ old('ticket_price', $event->ticket_price ?? 0) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Description</label>
                                <textarea name="description" class="form-control" rows="4">{{ old('description', $event->description) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $event->start_date?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $event->end_date?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold small">Location</label>
                                <input type="text" name="location" class="form-control" value="{{ old('location', $event->location) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Capacity</label>
                                <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $event->capacity) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Organizer</label>
                                <input type="text" name="organizer" class="form-control" value="{{ old('organizer', $event->organizer) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Main Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                @if($event->image_url)
                                    <small class="text-muted">Current: <a href="{{ \App\Helpers\Helpers::imageUrl($event->image_url) }}" target="_blank">View</a></small>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button class="btn btn-gold">{{ $event->id ? 'Update' : 'Add' }} Event</button>
                            <a href="{{ route('dashboard.events.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                        <small class="text-muted d-block mt-3">Your event will be set to <strong>Pending</strong> and must be approved by an admin.</small>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection