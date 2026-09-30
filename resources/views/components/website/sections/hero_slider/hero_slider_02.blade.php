@php
    $sliders = $section['data'] ?? ($pageData['sliders'] ?? []);
    $firstSlide = $sliders[0] ?? null;
@endphp

@if(!empty($sliders))
<section id="hero" class="hero-split-section py-5 bg-light {{ $section['custom_class'] ?? '' }}">
    <div class="container py-lg-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="badge bg-primary px-3 py-2 rounded-pill mb-3">
                    <i class="bi bi-star-fill me-1"></i> {{ $pageData['institute']['name'] ?? 'Top Ranked School' }}
                </span>
                <h1 class="display-4 fw-bold text-dark mb-3 lh-tight">
                    {{ $firstSlide['title'] ?? 'Empowering Students Through Quality Education' }}
                </h1>
                <p class="lead text-muted mb-4">
                    {{ $firstSlide['subtitle'] ?? 'Experience modern learning methodologies, dedicated mentorship, and future-ready education.' }}
                </p>
                <div class="d-flex flex-wrap gap-3 mb-4">
                    @if(!empty($firstSlide['button_text']))
                        <a href="{{ $firstSlide['button_url'] ?? '#' }}" class="btn btn-primary btn-lg rounded-pill px-4 shadow">
                            {{ $firstSlide['button_text'] }} <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    @endif
                    <a href="{{ route('admission') }}" class="btn btn-outline-dark btn-lg rounded-pill px-4">
                        Online Admission
                    </a>
                </div>

                {{-- Highlights pills --}}
                <div class="row g-3 pt-3 border-top">
                    <div class="col-sm-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-success fs-4 me-2"></i>
                            <div>
                                <small class="text-muted d-block">Success Rate</small>
                                <strong class="text-dark">98% Board Pass</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-laptop text-primary fs-4 me-2"></i>
                            <div>
                                <small class="text-muted d-block">Smart Labs</small>
                                <strong class="text-dark">Digital Campus</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-trophy-fill text-warning fs-4 me-2"></i>
                            <div>
                                <small class="text-muted d-block">Sports</small>
                                <strong class="text-dark">State Champions</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div id="splitHeroCarousel" class="carousel slide carousel-fade rounded-4 shadow-lg overflow-hidden" data-bs-ride="carousel">
                    <div class="carousel-inner" style="min-height: 420px;">
                        @foreach($sliders as $index => $slide)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="{{ $slide['image'] }}" class="d-block w-100" alt="{{ $slide['title'] }}" style="height: 420px; object-fit: cover;">
                                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded-3 p-3 mb-2 mx-3">
                                    <h5 class="fw-bold mb-1">{{ $slide['title'] }}</h5>
                                    @if(!empty($slide['subtitle']))
                                        <small class="text-light">{{ $slide['subtitle'] }}</small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#splitHeroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#splitHeroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
