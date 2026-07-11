@extends('layouts.app')
@section('title', 'My Restaurants – Dashboard')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-blue">My Restaurants</h2>
        <a href="{{ route('dashboard.restaurants.create') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus me-1"></i>Add Restaurant</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($restaurants->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Restaurant Name</th>
                                <th>Cuisine</th>
                                <th>City</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($restaurants as $restaurant)
                                <tr>
                                    <td class="fw-semibold">{{ $restaurant->name }}</td>
                                    <td>{{ $restaurant->cuisine_type ?? '—' }}</td>
                                    <td>{{ $restaurant->city ?? '—' }}</td>
                                    <td>
                                        @if($restaurant->is_active)
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('dashboard.restaurants.edit', $restaurant->id) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-cup-hot" style="font-size:2.5rem"></i>
                    <p class="mt-2">You haven't added any restaurants yet. <a href="{{ route('dashboard.restaurants.create') }}" class="text-blue fw-semibold">Add one now</a></p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection