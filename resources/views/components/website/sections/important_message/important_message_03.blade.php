@php
    $messages = $section['data'] ?? ($pageData['important_messages'] ?? []);
@endphp

@if(!empty($messages))
<div class="container py-4 {{ $section['custom_class'] ?? '' }}">
    <div class="bg-dark text-white p-4 p-md-5 rounded-4 shadow-lg position-relative overflow-hidden">
        <div class="position-absolute end-0 top-0 opacity-10 pe-3 pt-2 text-white" style="font-size: 150px; line-height: 1;">
            <i class="bi bi-shield-exclamation"></i>
        </div>
        <div class="position-relative" style="z-index: 2;">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-warning text-dark fw-bold mb-3 small">
                <i class="bi bi-broadcast"></i> OFFICIAL NOTICE
            </div>
            @foreach($messages as $msg)
                <h3 class="fw-bold mb-2">{{ $msg['title'] }}</h3>
                <p class="lead opacity-90 mb-4">{{ $msg['message'] }}</p>
                @if(!empty($msg['action_url']))
                    <a href="{{ $msg['action_url'] }}" class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow">
                        {{ $msg['action_text'] ?? 'Read Full Notice' }} <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endif
