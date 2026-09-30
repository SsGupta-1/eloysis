@php
    $sliders = $section['data'] ?? ($pageData['sliders'] ?? []);
@endphp

@if(!empty($sliders))
<section id="hero" class="hero-glass-section position-relative overflow-hidden {{ $section['custom_class'] ?? '' }}">
    <div id="glassHeroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
        {{-- Carousel Indicators --}}
        @if(count($sliders) > 1)
            <div class="carousel-indicators mb-3" style="z-index: 5;">
                @foreach($sliders as $index => $slide)
                    <button type="button" data-bs-target="#glassHeroCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
                @endforeach
            </div>
        @endif

        <div class="carousel-inner">
            @foreach($sliders as $index => $slide)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    {{-- Slide Background with Light Gradient to Keep Image Clearly Visible --}}
                    <div class="position-relative d-flex align-items-center justify-content-center py-5" style="min-height: 560px; background: linear-gradient(to bottom, rgba(15, 23, 42, 0.25) 0%, rgba(15, 23, 42, 0.45) 100%), url('{{ $slide['image'] }}') center/cover no-repeat;">
                        <div class="container py-4">
                            <div class="row justify-content-center">
                                <div class="col-lg-8 col-xl-7 col-md-10 text-center">
                                    {{-- Sleek, Translucent Glassmorphism Card --}}
                                    <div class="p-3 p-sm-4 p-md-4 rounded-4 shadow text-white" style="background: rgba(15, 23, 42, 0.38); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); border: 1px solid rgba(255, 255, 255, 0.25); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);">
                                        <span class="badge bg-white text-primary px-3 py-1 rounded-pill fw-bold text-uppercase mb-2 shadow-sm small">
                                            <i class="bi bi-shield-check text-primary me-1"></i> Admissions Open 2026-27
                                        </span>
                                        <h2 class="display-5 fw-bold text-white mb-2 lh-tight" style="text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);">
                                            {{ $slide['title'] }}
                                        </h2>
                                        @if(!empty($slide['subtitle']))
                                            <p class="text-white mb-3 px-md-3 small fs-6" style="text-shadow: 0 1px 6px rgba(0, 0, 0, 0.7); opacity: 0.95;">
                                                {{ $slide['subtitle'] }}
                                            </p>
                                        @endif
                                        <div class="d-flex flex-wrap justify-content-center gap-2 pt-1">
                                            @if(!empty($slide['button_text']))
                                                <a href="{{ $slide['button_url'] ?? '#' }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                                    {{ $slide['button_text'] }} <i class="bi bi-arrow-right ms-1"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('admission') }}" class="btn btn-light rounded-pill px-4 shadow-sm">
                                                <i class="bi bi-pencil-square me-1"></i> Apply for Admission
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Controls --}}
        @if(count($sliders) > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#glassHeroCarousel" data-bs-slide="prev" style="z-index: 5;">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#glassHeroCarousel" data-bs-slide="next" style="z-index: 5;">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        @endif
    </div>
</section>
@endif
