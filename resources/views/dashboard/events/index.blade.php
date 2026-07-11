@extends('layouts.app')
@section('title', 'My Events – Dashboard')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-blue">My Events</h2>
        <a href="{{ route('dashboard.events.create') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus me-1"></i>Add Event</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($events->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Event Name</th>
                                <th>Category</th>
                                <th>Date</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                                <tr>
                                    <td class="fw-semibold">{{ $event->name }}</td>
                                    <td>{{ $event->category ?? '—' }}</td>
                                    <td>{{ $event->start_date?->format('d M Y') ?? 'TBD' }}</td>
                                    <td>{{ $event->location ?? '—' }}</td>
                                    <td>
                                        @if($event->is_active)
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('dashboard.events.edit', $event->id) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-event" style="font-size:2.5rem"></i>
                    <p class="mt-2">You haven't added any events yet. <a href="{{ route('dashboard.events.create') }}" class="text-blue fw-semibold">Add one now</a></p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection