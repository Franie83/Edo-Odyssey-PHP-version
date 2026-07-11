@extends('layouts.admin')
@section('title', 'FAQs – Admin')
@section('page_title', 'Frequently Asked Questions')
@section('page_subtitle', 'Manage the FAQ section shown to visitors')
@section('content')
<div class="row g-4">
  {{-- FAQ list --}}
  <div class="col-lg-7">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0 admin-table">
            <thead>
              <tr><th style="width:40%">Question</th><th>Category</th><th>Order</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
            @forelse($faqs as $faq)
            <tr>
              <td>
                <div class="fw-semibold small">{{ Str::limit($faq->question, 60) }}</div>
                <div class="text-muted" style="font-size:.7rem">{{ Str::limit($faq->answer, 80) }}</div>
              </td>
              <td class="small text-muted">{{ $faq->category ?: '—' }}</td>
              <td class="small">{{ $faq->sort_order }}</td>
              <td>
                <span class="badge {{ $faq->is_active ? 'bg-success' : 'bg-secondary' }}">
                  {{ $faq->is_active ? 'Active' : 'Hidden' }}
                </span>
              </td>
              <td>
                <div class="d-flex gap-1">
                  <button class="btn btn-outline-blue btn-sm py-0" data-bs-toggle="collapse" data-bs-target="#faq-edit-{{ $faq->id }}">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <form method="POST" action="{{ route('admin.faq_delete', $faq->id) }}" class="d-inline" onsubmit="return confirm('Delete this FAQ?')">
                    @csrf<button class="btn btn-outline-danger btn-sm py-0"><i class="bi bi-trash"></i></button>
                  </form>
                </div>
                {{-- Inline edit row --}}
                <div class="collapse mt-2" id="faq-edit-{{ $faq->id }}">
                  <div class="card card-body p-2 border-0 bg-light">
                    <form method="POST" action="{{ route('admin.faq_update', $faq->id) }}">@csrf
                      <div class="mb-2"><label class="form-label fw-semibold" style="font-size:.7rem">Question</label>
                        <input type="text" name="question" class="form-control form-control-sm" value="{{ old('question', $faq->question) }}" required>
                      </div>
                      <div class="mb-2"><label class="form-label fw-semibold" style="font-size:.7rem">Answer</label>
                        <textarea name="answer" class="form-control form-control-sm" rows="3" required>{{ old('answer', $faq->answer) }}</textarea>
                      </div>
                      <div class="row g-2 mb-2">
                        <div class="col-6"><label class="form-label fw-semibold" style="font-size:.7rem">Category</label>
                          <input type="text" name="category" class="form-control form-control-sm" value="{{ old('category', $faq->category) }}" placeholder="General">
                        </div>
                        <div class="col-6"><label class="form-label fw-semibold" style="font-size:.7rem">Sort Order</label>
                          <input type="number" name="sort_order" class="form-control form-control-sm" value="{{ old('sort_order', $faq->sort_order) }}">
                        </div>
                      </div>
                      <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $faq->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" style="font-size:.75rem">Active</label>
                      </div>
                      <button class="btn btn-gold btn-sm w-100">Update FAQ</button>
                    </form>
                  </div>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-4 text-muted">No FAQs yet. Add one →</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>
        <div class="p-3">{{ $faqs->links() }}</div>
      </div>
    </div>
  </div>

  {{-- Add FAQ form --}}
  <div class="col-lg-5">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white fw-bold text-blue border-0 pt-3 pb-1">Add FAQ</div>
      <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger py-2 small"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <form method="POST" action="{{ route('admin.faq_store') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label fw-semibold small">Question *</label>
            <input type="text" name="question" class="form-control" value="{{ old('question') }}" required placeholder="What is included in a guided tour?">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Answer *</label>
            <textarea name="answer" class="form-control" rows="4" required placeholder="Provide a clear, helpful answer…">{{ old('answer') }}</textarea>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-7">
              <label class="form-label fw-semibold small">Category</label>
              <input type="text" name="category" class="form-control" value="{{ old('category') }}" placeholder="General, Booking, Tours…">
            </div>
            <div class="col-5">
              <label class="form-label fw-semibold small">Sort Order</label>
              <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
            </div>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
            <label class="form-check-label small">Active (visible to visitors)</label>
          </div>
          <button class="btn btn-gold w-100">Add FAQ</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
