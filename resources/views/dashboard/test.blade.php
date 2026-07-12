@extends('layouts.app')
@section('title', 'Test Dashboard')
@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h4>Dashboard Test</h4>
        </div>
        <div class="card-body">
            <p><strong>User:</strong> {{ Auth::user()->first_name }} ({{ Auth::user()->role }})</p>
            <p><strong>Bookings:</strong> {{ isset($bookings) ? $bookings->count() : 0 }}</p>
            <p><strong>Notifications:</strong> {{ isset($notifications) ? $notifications->count() : 0 }}</p>
            <p><strong>Reviews:</strong> {{ isset($reviews) ? $reviews->count() : 0 }}</p>
            
            @if(isset($stats))
                <hr>
                <h5>Stats:</h5>
                <ul>
                    <li>Total Bookings: {{ $stats['total_bookings'] }}</li>
                    <li>Pending: {{ $stats['pending_bookings'] }}</li>
                    <li>Completed: {{ $stats['completed_bookings'] }}</li>
                    <li>Reviews: {{ $stats['total_reviews'] }}</li>
                    <li>Heritage Points: {{ $stats['heritage_points'] }}</li>
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection