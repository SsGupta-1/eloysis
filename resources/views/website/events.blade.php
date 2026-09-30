@extends('layouts.website.master')

@section('title', 'Upcoming Events')

@section('content')

{{-- Breadcrumb / Hero --}}
<div class="bg-primary text-white py-5 text-center mb-5">
    <div class="container">
        <h1 class="fw-bold mb-2">Upcoming Events</h1>
        <p class="lead mb-0">Join us in celebrating school moments, academic meets, and sports competitions</p>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">
        @forelse($pageData['events'] as $event)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-3 p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary-subtle text-primary rounded-3 text-center p-3 me-3" style="min-width: 65px;">
                            <h4 class="fw-bold mb-0">{{ $event['date'] }}</h4>
                            <small class="text-uppercase fw-bold">{{ $event['month'] }}</small>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">{{ $event['title'] }}</h5>
                            <div class="text-muted small">
                                <i class="bi bi-clock me-1"></i> {{ $event['time'] }}
                            </div>
                        </div>
                    </div>
                    <div class="text-muted small mb-3">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $event['location'] }}
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">No upcoming events scheduled at this moment.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection
