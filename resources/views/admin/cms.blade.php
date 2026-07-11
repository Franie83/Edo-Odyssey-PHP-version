@extends('layouts.admin')
@section('title', 'CMS Settings – Admin')
@section('page_title', 'CMS Settings')
@section('page_subtitle', 'Manage site content and configuration')
@section('content')
<div class="row justify-content-center">
  <div class="col-lg-9">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.cms_update') }}">
          @csrf
          @foreach($settings as $setting)
          <div class="mb-3">
            <label class="form-label fw-semibold small">{{ $setting->label ?: ucwords(str_replace('_',' ',$setting->key)) }}</label>
            @if($setting->type === 'textarea')
            <textarea name="settings[{{ $setting->key }}]" class="form-control" rows="3">{{ old("settings.{$setting->key}", $setting->value) }}</textarea>
            @else
            <input type="{{ $setting->type ?? 'text' }}" name="settings[{{ $setting->key }}]" class="form-control" value="{{ old("settings.{$setting->key}", $setting->value) }}">
            @endif
          </div>
          @endforeach
          <div class="d-flex gap-2 mt-4">
            <button class="btn btn-gold px-4">Save Settings</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Notify All Users -->
    <div class="card border-0 shadow-sm mt-4">
      <div class="card-body p-4">
        <h5 class="fw-bold text-blue mb-3"><i class="bi bi-bell me-2"></i>Send Notification to All Users</h5>
        <form method="POST" action="{{ route('admin.notify_all') }}">
          @csrf
          <div class="mb-3"><label class="form-label fw-semibold small">Title *</label><input type="text" name="title" class="form-control" required></div>
          <div class="mb-3"><label class="form-label fw-semibold small">Message *</label><textarea name="message" class="form-control" rows="3" required></textarea></div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Type</label>
            <select name="type" class="form-select"><option value="info">Info</option><option value="success">Success</option><option value="warning">Warning</option></select>
          </div>
          <button class="btn btn-blue"><i class="bi bi-send me-2"></i>Send to All Users</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
