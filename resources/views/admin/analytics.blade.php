@extends('layouts.admin')
@section('title', 'Analytics – Admin')
@section('page_title', 'Analytics')
@section('page_subtitle', 'Platform performance metrics')
@section('content')

{{-- Date Range Filter --}}
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
    <label class="form-label mb-0 small fw-semibold">From:</label>
    <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}" style="width:150px">
    <label class="form-label mb-0 small fw-semibold">To:</label>
    <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}" style="width:150px">
    <button class="btn btn-blue btn-sm">Filter</button>
    @if(request('from') || request('to'))
      <a href="{{ route('admin.analytics') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
    @endif
  </form>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.analytics_export', ['type' => 'bookings', 'from' => request('from'), 'to' => request('to')]) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-download me-1"></i>Export Bookings CSV</a>
    <a href="{{ route('admin.analytics_export', ['type' => 'users', 'from' => request('from'), 'to' => request('to')]) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-download me-1"></i>Export Users CSV</a>
  </div>
</div>

{{-- Key Metrics --}}
<div class="row g-3 mb-4">
  @php
    $metrics = [
      ['label'=>'Total Revenue','val'=>'₦'.number_format($stats['revenue']),'icon'=>'bi-currency-dollar','color'=>'var(--gold-500)'],
      ['label'=>'Total Bookings','val'=>number_format($stats['bookings']),'icon'=>'bi-calendar-check','color'=>'var(--blue-600)'],
      ['label'=>'Total Reviews','val'=>number_format($stats['reviews']),'icon'=>'bi-star','color'=>'var(--gold-500)'],
      ['label'=>'Total Users','val'=>number_format($stats['users']),'icon'=>'bi-people','color'=>'var(--blue-600)'],
    ];
  @endphp
  @foreach($metrics as $m)
  <div class="col-6 col-md-3">
    <div class="admin-stat" style="border-left-color:{{ $m['color'] }}">
      <div class="admin-stat-icon bg-blue-100"><i class="bi {{ $m['icon'] }}" style="color:{{ $m['color'] }}"></i></div>
      <div><div class="admin-stat-value" style="font-size:1.3rem">{{ $m['val'] }}</div><div class="admin-stat-label">{{ $m['label'] }}</div></div>
    </div>
  </div>
  @endforeach
</div>

<div class="row g-4">
  {{-- Revenue Over Time --}}
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <h6 class="fw-bold text-blue mb-3">Revenue Over Time (Completed Bookings)</h6>
        <canvas id="revenueChart" height="100"></canvas>
      </div>
    </div>
  </div>

  {{-- Bookings by Status --}}
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <h6 class="fw-bold text-blue mb-3">Bookings by Status</h6>
        <canvas id="statusChart" height="220"></canvas>
      </div>
    </div>
  </div>

  {{-- Bookings by Type --}}
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <h6 class="fw-bold text-blue mb-3">Bookings by Type</h6>
        <canvas id="typeChart" height="220"></canvas>
      </div>
    </div>
  </div>

  {{-- Users by Role --}}
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <h6 class="fw-bold text-blue mb-3">Users by Role</h6>
        <canvas id="roleChart" height="220"></canvas>
      </div>
    </div>
  </div>

  {{-- Content Summary --}}
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <h6 class="fw-bold text-blue mb-3">Content Summary</h6>
        <div class="row g-2">
          @foreach([
            ['Attractions', $stats['attractions'], 'bi-building'],
            ['Hotels', $stats['hotels'], 'bi-building-fill'],
            ['Restaurants', $stats['restaurants'], 'bi-cup-hot'],
            ['Guides', $stats['guides'], 'bi-person-badge'],
            ['Events', $stats['events'], 'bi-calendar-event'],
            ['News Articles', $stats['news'], 'bi-newspaper'],
          ] as [$label, $val, $icon])
          <div class="col-md-2 col-4 text-center">
            <div class="p-2 bg-blue-100 rounded">
              <i class="bi {{ $icon }} text-blue fs-4 d-block mb-1"></i>
              <div class="fw-bold text-blue">{{ number_format($val) }}</div>
              <div class="text-muted small">{{ $label }}</div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const palette = ['#1a3a6b','#c9a227','#2255a4','#e0b83a','#0d2447','#e05c27'];

function makeChart(id, labels, data) {
  new Chart(document.getElementById(id), {
    type: 'doughnut',
    data: { labels, datasets: [{ data, backgroundColor: palette }] },
    options: { plugins: { legend: { position: 'bottom' } } }
  });
}

// Revenue over time (line chart)
const revenueLabels = {!! json_encode($revenue_labels) !!};
const revenueData = {!! json_encode($revenue_data) !!};
if (revenueLabels.length > 0) {
  new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: { labels: revenueLabels, datasets: [{ label: 'Revenue (₦)', data: revenueData, borderColor: '#c9a227', fill: false, tension: 0.1 }] },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
  });
}

makeChart('statusChart', {!! json_encode(array_keys($bookings_by_status->toArray())) !!}, {!! json_encode(array_values($bookings_by_status->toArray())) !!});
makeChart('typeChart',   {!! json_encode(array_keys($bookings_by_type->toArray())) !!},   {!! json_encode(array_values($bookings_by_type->toArray())) !!});
makeChart('roleChart',   {!! json_encode(array_keys($users_by_role->toArray())) !!},   {!! json_encode(array_values($users_by_role->toArray())) !!});
</script>
@endsection