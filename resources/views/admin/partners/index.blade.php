@extends('layouts.admin')
@section('title', 'Partners – Admin')
@section('page_title', 'Partner Organisations')
@section('page_subtitle', 'Manage logos and links shown in the footer / home page')
@section('content')
<div class="row g-4">
  {{-- Partners list --}}
  <div class="col-lg-7">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0 admin-table">
            <thead>
              <tr><th>Logo</th><th>Name</th><th>Website</th><th>Order</th><th>Actions</th></tr>
            </thead>
            <tbody>
            @forelse($partners as $partner)
            <tr>
              <td style="width:60px">
                @if($partner->logo_url)
                <img src="{{ \App\Helpers\Helpers::imageUrl($partner->logo_url) }}" alt="{{ $partner->name }}" style="height:36px;object-fit:contain;max-width:80px">
                @else
                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:48px;height:36px">
                  <i class="bi bi-building text-muted"></i>
                </div>
                @endif
              </td>
              <td class="fw-semibold small">{{ $partner->name }}</td>
              <td class="small">
                @if($partner->website)
                <a href="{{ $partner->website }}" target="_blank" class="text-blue text-decoration-none">
                  {{ Str::limit($partner->website, 35) }} <i class="bi bi-box-arrow-up-right" style="font-size:.65rem"></i>
                </a>
                @else
                <span class="text-muted">—</span>
                @endif
              </td>
              <td class="small">{{ $partner->sort_order }}</td>
              <td>
                <form method="POST" action="{{ route('admin.partner_delete', $partner->id) }}" class="d-inline" onsubmit="return confirm('Remove partner {{ addslashes($partner->name) }}?')">
                  @csrf<button class="btn btn-outline-danger btn-sm py-0"><i class="bi bi-trash"></i></button>
                </form>
              </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-4 text-muted">No partners yet. Add one →</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>
        <div class="p-3">{{ $partners->links() }}</div>
      </div>
    </div>
  </div>

  {{-- Add partner form --}}
  <div class="col-lg-5">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white fw-bold text-blue border-0 pt-3 pb-1">Add Partner</div>
      <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger py-2 small"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <form method="POST" action="{{ route('admin.partner_store') }}" enctype="multipart/form-data">
          @csrf
          <div class="mb-3">
            <label class="form-label fw-semibold small">Organisation Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Edo State Tourism Board">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Website URL</label>
            <input type="url" name="website" class="form-control" value="{{ old('website') }}" placeholder="https://example.com">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Logo Image</label>
            <input type="file" name="logo" class="form-control" accept="image/*">
            <div class="form-text text-muted" style="font-size:.72rem">PNG or SVG recommended. Max 2 MB.</div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
          </div>
          <button class="btn btn-gold w-100">Add Partner</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
