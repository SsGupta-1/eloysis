@php
    $messages = $section['data'] ?? ($pageData['important_messages'] ?? []);
    $firstMsg = $messages[0] ?? null;
@endphp

@if(!empty($firstMsg))
<div class="important-message-ribbon bg-danger text-white py-2 px-3 shadow-sm {{ $section['custom_class'] ?? '' }}">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center flex-grow-1 overflow-hidden">
                <span class="badge bg-white text-danger fw-bold text-uppercase me-2 px-2 py-1">
                    <i class="bi bi-bell-fill me-1"></i> Urgent Notice
                </span>
                <div class="text-truncate fw-medium">
                    <strong>{{ $firstMsg['title'] }}:</strong> {{ $firstMsg['message'] }}
                </div>
            </div>
            @if(!empty($firstMsg['action_url']) && !empty($firstMsg['action_text']))
                <div>
                    <a href="{{ $firstMsg['action_url'] }}" class="btn btn-sm btn-light text-danger fw-bold rounded-pill px-3 shadow-sm">
                        {{ $firstMsg['action_text'] }} <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endif
