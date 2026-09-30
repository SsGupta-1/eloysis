@php
    $messages = $section['data'] ?? ($pageData['important_messages'] ?? []);
    $firstMsg = $messages[0] ?? null;
    $type = $firstMsg['type'] ?? 'info';
    $bgClass = match($type) {
        'danger' => 'border-danger bg-danger-subtle text-danger-emphasis',
        'warning' => 'border-warning bg-warning-subtle text-warning-emphasis',
        'success' => 'border-success bg-success-subtle text-success-emphasis',
        default => 'border-primary bg-primary-subtle text-primary-emphasis',
    };
@endphp

@if(!empty($firstMsg))
<div class="container py-3 {{ $section['custom_class'] ?? '' }}">
    <div class="card border-2 shadow-sm rounded-4 {{ $bgClass }}">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-start gap-3">
                <div class="fs-1 text-primary">
                    <i class="bi bi-info-circle-fill"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">{{ $firstMsg['title'] }}</h5>
                    <p class="mb-0">{{ $firstMsg['message'] }}</p>
                </div>
            </div>
            @if(!empty($firstMsg['action_url']) && !empty($firstMsg['action_text']))
                <div>
                    <a href="{{ $firstMsg['action_url'] }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        {{ $firstMsg['action_text'] }} <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endif
