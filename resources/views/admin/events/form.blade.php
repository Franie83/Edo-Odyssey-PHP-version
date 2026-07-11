@extends('layouts.admin')
@section('title', ($event->id ? 'Edit Event' : 'New Event') . ' – Admin')
@section('page_title', $event->id ? 'Edit Event' : 'Add Event')
@section('content')
<div class="row justify-content-center"><div class="col-md-9">
  <div class="card border-0 shadow-sm"><div class="card-body p-4">
    <form method="POST" action="{{ $event->id ? route('admin.event_update', $event->id) : route('admin.event_store') }}" enctype="multipart/form-data">
      @csrf
      <div class="row g-3">
        <div class="col-md-8"><label class="form-label fw-semibold small">Name *</label><input type="text" name="name" class="form-control" value="{{ old('name', $event->name) }}" required></div>
        <div class="col-md-4"><label class="form-label fw-semibold small">Category</label><input type="text" name="category" class="form-control" value="{{ old('category', $event->category) }}" placeholder="Cultural Festival, Music…"></div>

        {{-- ✅ Assigned User dropdown --}}
        <div class="col-12">
          <label class="form-label fw-semibold small">Assigned User *</label>
          <select name="user_id" class="form-select" required>
            <option value="">Select user</option>
            @foreach($users as $user)
              <option value="{{ $user->id }}" {{ old('user_id', $event->user_id) == $user->id ? 'selected' : '' }}>
                {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-12"><label class="form-label fw-semibold small">Description</label><textarea name="description" class="form-control" rows="4">{{ old('description', $event->description) }}</textarea></div>
        <div class="col-md-6"><label class="form-label fw-semibold small">Start Date</label><input type="date" name="start_date" class="form-control" value="{{ old('start_date', $event->start_date?->format('Y-m-d')) }}"></div>
        <div class="col-md-6"><label class="form-label fw-semibold small">End Date</label><input type="date" name="end_date" class="form-control" value="{{ old('end_date', $event->end_date?->format('Y-m-d')) }}"></div>
        <div class="col-md-8"><label class="form-label fw-semibold small">Location</label><input type="text" name="location" class="form-control" value="{{ old('location', $event->location) }}"></div>
        <div class="col-md-4"><label class="form-label fw-semibold small">Ticket Price (₦)</label><input type="number" name="ticket_price" class="form-control" step="100" value="{{ old('ticket_price', $event->ticket_price ?? 0) }}"></div>
        <div class="col-md-6"><label class="form-label fw-semibold small">Organizer</label><input type="text" name="organizer" class="form-control" value="{{ old('organizer', $event->organizer) }}"></div>
        <div class="col-md-6"><label class="form-label fw-semibold small">Capacity</label><input type="number" name="capacity" class="form-control" value="{{ old('capacity', $event->capacity) }}"></div>
        <div class="col-md-6"><label class="form-label fw-semibold small">Main Image</label><input type="file" name="image" class="form-control" accept="image/*">@if($event->image_url)<small class="text-muted">Current: <a href="{{ \App\Helpers\Helpers::imageUrl($event->image_url) }}" target="_blank">View</a></small>@endif</div>
        <div class="col-md-3"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="is_featured" {{ old('is_featured', $event->is_featured)?'checked':'' }}><label class="form-check-label">Featured</label></div></div>
        <div class="col-md-3"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="is_active" {{ old('is_active', $event->is_active ?? true)?'checked':'' }}><label class="form-check-label">Active</label></div></div>
      </div>
      <div class="d-flex gap-2 mt-4"><button class="btn btn-gold">{{ $event->id ? 'Update Event' : 'Create Event' }}</button><a href="{{ route('admin.events') }}" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
  </div></div>
</div></div>
@endsection