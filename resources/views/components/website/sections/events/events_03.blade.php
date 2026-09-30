@php
    $events = $section['data'] ?? ($pageData['events'] ?? []);
    $featured = $events[0] ?? null;
    $remaining = array_slice($events, 1);
@endphp

@if(!empty($featured))
<section id="events" class="events-featured-section py-5 {{ $section['custom_class'] ?? '' }}">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-primary fw-bold text-uppercase">{{ $section['subtitle'] ?? 'Major Highlights' }}</span>
            <h2 class="fw-bold text-dark">{{ $section['title'] ?? 'Upcoming Featured Events' }}</h2>
        </div>

        <div class="row g-4">
            {{-- Big featured event --}}
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow rounded-4 overflow-hidden bg-primary text-white p-4 p-md-5 d-flex flex-column justify-content-between">
                    <div>
                        <span class="badge bg-white text-primary fw-bold px-3 py-2 rounded-pill mb-3">
                            <i class="bi bi-star-fill text-warning me-1"></i> Featured Highlight
                        </span>
                        <h3 class="display-6 fw-bold mb-3">{{ $featured['title'] }}</h3>
                        <p class="lead opacity-90 mb-4">{{ $featured['description'] ?? 'Join our premier school festival celebrating student talent and leadership.' }}</p>
                    </div>
                    <div>
                        <div class="d-flex flex-wrap gap-4 mb-4">
                            <div>
                                <small class="text-white-50 d-block">DATE</small>
                                <strong class="fs-5">{{ $featured['full_date'] ?? ($featured['date'].' '.$featured['month']) }}</strong>
                            </div>
                            <div>
                                <small class="text-white-50 d-block">TIME</small>
                                <strong class="fs-5">{{ $featured['time'] }}</strong>
                            </div>
                            <div>
                                <small class="text-white-50 d-block">VENUE</small>
                                <strong class="fs-5">{{ $featured['location'] }}</strong>
                            </div>
                        </div>
                        <a href="{{ $featured['url'] ?? route('events') }}" class="btn btn-light btn-lg text-primary fw-bold rounded-pill px-4 shadow">
                            Participate / Register <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Remaining events list --}}
            <div class="col-lg-6">
                <div class="d-flex flex-column gap-3">
                    @foreach($remaining as $ev)
                        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary text-white rounded-3 text-center p-2 flex-shrink-0" style="min-width: 55px;">
                                    <h5 class="fw-bold mb-0 lh-1">{{ $ev['date'] }}</h5>
                                    <small class="text-uppercase fw-bold" style="font-size: 10px;">{{ $ev['month'] }}</small>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark mb-1">{{ $ev['title'] }}</h6>
                                    <small class="text-muted d-block"><i class="bi bi-clock me-1"></i>{{ $ev['time'] }} | <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $ev['location'] }}</small>
                                </div>
                                <a href="{{ $ev['url'] ?? route('events') }}" class="btn btn-sm btn-outline-primary rounded-circle p-2">
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif
