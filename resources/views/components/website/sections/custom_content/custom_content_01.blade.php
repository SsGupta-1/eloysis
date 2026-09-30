@php
    $data = $section['data'] ?? [];
    $title = $section['title'] ?? 'Custom Section';
    $subtitle = $section['subtitle'] ?? null;
    $content = $data['content'] ?? ($section['settings']['content'] ?? '');
    $image = $data['image'] ?? null;
    $btnText = $data['button_text'] ?? ($section['settings']['button_text'] ?? null);
    $btnUrl = $data['button_url'] ?? ($section['settings']['button_url'] ?? null);
    $bgColor = $data['bg_color'] ?? ($section['settings']['bg_color'] ?? '#f8fafc');
    $textColor = $data['text_color'] ?? ($section['settings']['text_color'] ?? '#1e293b');
@endphp

<section class="custom-section-split py-5 {{ $section['custom_class'] ?? '' }}" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
    <div class="container py-lg-4">
        <div class="row align-items-center g-5">
            <div class="{{ $image ? 'col-lg-6' : 'col-12' }}">
                @if(!empty($subtitle))
                    <span class="badge bg-primary px-3 py-2 rounded-pill mb-3">
                        {{ $subtitle }}
                    </span>
                @endif
                <h2 class="fw-bold mb-3 lh-tight" style="color: {{ $textColor }};">
                    {{ $title }}
                </h2>
                <div class="lead opacity-90 mb-4" style="white-space: pre-line;">
                    {!! nl2br(e($content)) !!}
                </div>
                @if(!empty($btnText) && !empty($btnUrl))
                    <a href="{{ $btnUrl }}" class="btn btn-primary btn-lg rounded-pill px-4 shadow">
                        {{ $btnText }} <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                @endif
            </div>

            @if(!empty($image))
                <div class="col-lg-6">
                    <div class="rounded-4 overflow-hidden shadow-lg">
                        <img src="{{ $image }}" class="img-fluid w-100" alt="{{ $title }}" style="max-height: 400px; object-fit: cover;">
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
