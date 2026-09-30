@php
    $events = $section['data'] ?? ($pageData['events'] ?? []);
@endphp

@if(!empty($events))
<section id="events" class="events-timeline-section py-5 bg-light {{ $section['custom_class'] ?? '' }}">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-primary fw-bold text-uppercase">{{ $section['subtitle'] ?? 'Event Schedule' }}</span>
            <h2 class="fw-bold text-dark">{{ $section['title'] ?? 'School Activity Timeline' }}</h2>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="timeline position-relative">
                    @foreach($events as $index => $event)
                        <div class="card border-0 shadow-sm rounded-4 mb-4 p-4 hover-shadow">
                            <div class="row align-items-center g-3">
                                <div class="col-md-3 text-center border-end-md">
                                    <div class="bg-primary-subtle text-primary p-3 rounded-4">
                                        <h3 class="fw-bold mb-0 text-primary">{{ $event['date'] }}</h3>
                                        <div class="text-uppercase fw-bold small">{{ $event['month'] }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="fw-bold text-dark mb-1">{{ $event['title'] }}</h5>
                                    <div class="d-flex flex-wrap gap-3 text-muted small mb-2">
                                        <span><i class="bi bi-clock me-1 text-primary"></i>{{ $event['time'] }}</span>
                                        <span><i class="bi bi-geo-alt-fill me-1 text-danger"></i>{{ $event['location'] }}</span>
                                    </div>
                                    @if(!empty($event['description']))
                                        <p class="text-muted small mb-0">{{ Str::limit($event['description'], 110) }}</p>
                                    @endif
                                </div>
                                <div class="col-md-3 text-md-end">
                                    <a href="{{ $event['url'] ?? route('events') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif
