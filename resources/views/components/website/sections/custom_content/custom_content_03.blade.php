@php
    $data = $section['data'] ?? [];
    $title = $section['title'] ?? 'Feature Highlights';
    $subtitle = $section['subtitle'] ?? null;
    $content = $data['content'] ?? ($section['settings']['content'] ?? '');
    $bgColor = $data['bg_color'] ?? ($section['settings']['bg_color'] ?? '#ffffff');
    $textColor = $data['text_color'] ?? ($section['settings']['text_color'] ?? '#0f172a');
@endphp

<section class="custom-section-box py-5 {{ $section['custom_class'] ?? '' }}" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
    <div class="container">
        <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5" style="border-top: 5px solid #0d6efd !important;">
            <div class="text-center mb-4">
                @if(!empty($subtitle))
                    <span class="text-primary fw-bold text-uppercase">{{ $subtitle }}</span>
                @endif
                <h2 class="fw-bold text-dark mb-0">{{ $title }}</h2>
            </div>
            <div class="lead text-muted text-center mb-0" style="white-space: pre-line;">
                {!! nl2br(e($content)) !!}
            </div>
        </div>
    </div>
</section>
