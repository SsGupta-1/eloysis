@php
    $announcements = $section['data'] ?? ($pageData['announcements'] ?? []);
@endphp

@if(!empty($announcements))
<div class="announcement-ticker-wrap bg-primary-subtle border-bottom border-primary-subtle py-2 {{ $section['custom_class'] ?? '' }}">
    <div class="container">
        <div class="d-flex align-items-center">
            <div class="d-flex align-items-center bg-primary text-white px-3 py-1 rounded-pill fw-bold small text-uppercase me-3 shadow-sm flex-shrink-0">
                <i class="bi bi-megaphone-fill me-1"></i> Updates
            </div>
            <div class="overflow-hidden flex-grow-1 position-relative" style="height: 28px;">
                <marquee behavior="scroll" direction="left" scrollamount="6" onmouseover="this.stop();" onmouseout="this.start();">
                    <div class="d-inline-flex align-items-center gap-4">
                        @foreach($announcements as $ann)
                            <div class="d-inline-flex align-items-center gap-2">
                                <span class="badge bg-primary text-white">{{ $ann['badge'] }}</span>
                                <a href="{{ $ann['link_url'] ?? '#' }}" class="text-decoration-none text-dark fw-medium">
                                    {{ $ann['title'] }}
                                </a>
                                @if(!empty($ann['link_text']) && !empty($ann['link_url']))
                                    <a href="{{ $ann['link_url'] }}" class="badge bg-dark text-white text-decoration-none ms-1">
                                        {{ $ann['link_text'] }}
                                    </a>
                                @endif
                                <span class="text-muted mx-2">•</span>
                            </div>
                        @endforeach
                    </div>
                </marquee>
            </div>
        </div>
    </div>
</div>
@endif
