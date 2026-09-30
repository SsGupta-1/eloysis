@php
    $sliders = $section['data'] ?? ($pageData['sliders'] ?? []);
@endphp

@if(!empty($sliders))
<section id="hero" class="hero-section {{ $section['custom_class'] ?? '' }}">
    <div id="homeHeroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            @foreach($sliders as $index => $slide)
                <button type="button" data-bs-target="#homeHeroCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
            @endforeach
        </div>

        <div class="carousel-inner">
            @foreach($sliders as $index => $slide)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <div class="hero-slide-item" style="background: linear-gradient(rgba(15, 23, 42, 0.65), rgba(15, 23, 42, 0.75)), url('{{ $slide['image'] }}') center/cover no-repeat; min-height: 540px;">
                        <div class="container h-100">
                            <div class="row h-100 align-items-center py-5">
                                <div class="col-lg-8 text-white">
                                    <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-2 rounded-pill mb-3">
                                        <i class="bi bi-mortarboard-fill me-1"></i> Excellence in Education
                                    </span>
                                    <h1 class="display-4 fw-bold mb-3 lh-tight animate__animated animate__fadeInDown">
                                        {{ $slide['title'] }}
                                    </h1>
                                    @if(!empty($slide['subtitle']))
                                        <p class="lead mb-4 text-light opacity-90">
                                            {{ $slide['subtitle'] }}
                                        </p>
                                    @endif
                                    @if(!empty($slide['button_text']))
                                        <div class="d-flex flex-wrap gap-3">
                                            <a href="{{ $slide['button_url'] ?? '#' }}" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm">
                                                {{ $slide['button_text'] }} <i class="bi bi-arrow-right ms-1"></i>
                                            </a>
                                            <a href="{{ route('about') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">
                                                Learn More
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#homeHeroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#homeHeroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>
@endif
