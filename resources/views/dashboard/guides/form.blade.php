@extends('layouts.app')
@section('title', 'Edit Guide Profile – Dashboard')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold text-blue">Edit Your Guide Profile</div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger py-2 small"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                    @endif

                    <form method="POST" action="{{ route('dashboard.guide.update') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
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
                                <input type="text" name="certification" class="form-control" value="{{ old('certification', $guide->certification) }}" placeholder="e.g., Licensed by Edo State Tourism Board">
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button class="btn btn-gold">Update & Submit for Review</button>
                            <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                        <small class="text-muted d-block mt-3">Your profile will be set to <strong>Pending</strong> after update and must be approved by an admin.</small>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection