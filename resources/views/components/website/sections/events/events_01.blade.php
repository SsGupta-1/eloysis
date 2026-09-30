@php
    $events = $section['data'] ?? ($pageData['events'] ?? []);
@endphp

@if(!empty($events))
<section id="events" class="events-section py-5 {{ $section['custom_class'] ?? '' }}">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-5">
            <div>
                <span class="text-primary fw-bold text-uppercase">{{ $section['subtitle'] ?? 'Mark Your Calendar' }}</span>
                <h2 class="fw-bold text-dark mb-0">{{ $section['title'] ?? 'Upcoming Events & Celebrations' }}</h2>
            </div>
            <a href="{{ route('events') }}" class="btn btn-outline-primary rounded-pill px-4 mt-3 mt-md-0">
                View All Events <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @foreach($events as $event)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 position-relative overflow-hidden hover-card">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-3 text-center p-2 me-3 flex-shrink-0 shadow-sm" style="min-width: 60px;">
                                <h4 class="fw-bold mb-0 lh-1">{{ $event['date'] }}</h4>
                                <small class="text-uppercase fw-bold" style="font-size: 11px;">{{ $event['month'] }}</small>
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
                        @if(!empty($event['description']))
                            <p class="text-muted small mb-3 flex-grow-1">{{ Str::limit($event['description'], 90) }}</p>
                        @endif
                        <div class="mt-auto pt-2 border-top">
                            <a href="{{ $event['url'] ?? route('events') }}" class="text-primary fw-semibold small text-decoration-none">
                                Event Details <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
