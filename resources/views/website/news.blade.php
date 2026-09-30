@extends('layouts.website.master')

@section('title', 'News & Announcements')

@section('content')

{{-- Breadcrumb / Hero --}}
<div class="bg-primary text-white py-5 text-center mb-5">
    <div class="container">
        <h1 class="fw-bold mb-2">News & Announcements</h1>
        <p class="lead mb-0">Stay updated with the latest happenings and notices</p>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">
        @forelse($pageData['news'] as $item)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                    @if(!empty($item['image_url']))
                        <img src="{{ $item['image_url'] }}" class="card-img-top" alt="{{ $item['title'] }}" style="height: 200px; object-fit: cover;">
                    @endif
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="text-muted small mb-2">
                            <i class="bi bi-calendar3 me-1"></i> {{ $item['date'] }}
                        </div>
                        <h5 class="card-title fw-bold mb-2 text-dark">{{ $item['title'] }}</h5>
                        <p class="card-text text-muted flex-grow-1">{{ $item['description'] }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">No news updates published yet.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection