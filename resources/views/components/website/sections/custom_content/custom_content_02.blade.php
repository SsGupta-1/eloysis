@php
    $data = $section['data'] ?? [];
    $title = $section['title'] ?? 'Special Announcement';
    $subtitle = $section['subtitle'] ?? null;
    $content = $data['content'] ?? ($section['settings']['content'] ?? '');
    $bgImage = $data['bg_image'] ?? null;
    $btnText = $data['button_text'] ?? ($section['settings']['button_text'] ?? null);
    $btnUrl = $data['button_url'] ?? ($section['settings']['button_url'] ?? null);
    $bgColor = $data['bg_color'] ?? ($section['settings']['bg_color'] ?? '#0f172a');
    $textColor = $data['text_color'] ?? ($section['settings']['text_color'] ?? '#ffffff');

    $bgStyle = $bgImage
        ? "background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.85)), url('{$bgImage}') center/cover no-repeat;"
        : "background-color: {$bgColor};";
@endphp

<section class="custom-section-banner py-5 position-relative {{ $section['custom_class'] ?? '' }}" style="{{ $bgStyle }} color: {{ $textColor }}; min-height: 380px;">
    <div class="container py-lg-5 text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if(!empty($subtitle))
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">
                        {{ $subtitle }}
                    </span>
                @endif
                <h2 class="display-5 fw-bold mb-3" style="color: {{ $textColor }};">
                    {{ $title }}
                </h2>
                <div class="lead opacity-90 mb-4" style="white-space: pre-line;">
                    {!! nl2br(e($content)) !!}
                </div>
                @if(!empty($btnText) && !empty($btnUrl))
                    <a href="{{ $btnUrl }}" class="btn btn-warning btn-lg text-dark fw-bold rounded-pill px-5 shadow">
                        {{ $btnText }} <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
