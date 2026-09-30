@php
    $announcements = $section['data'] ?? ($pageData['announcements'] ?? []);
@endphp

@if(!empty($announcements))
<section class="announcements-list-section py-5 {{ $section['custom_class'] ?? '' }}">
    <div class="container">
        <div class="card border-0 shadow rounded-4 overflow-hidden">
            <div class="card-header bg-primary text-white p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-0"><i class="bi bi-pin-angle-fill me-2"></i> {{ $section['title'] ?? 'Notice Board & Bulletins' }}</h4>
                    <small class="text-light opacity-75">{{ $section['subtitle'] ?? 'Latest updates published by administration' }}</small>
                </div>
                <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold">{{ count($announcements) }} Active</span>
            </div>
            <div class="list-group list-group-flush">
                @foreach($announcements as $ann)
                    <div class="list-group-item p-3 p-md-4 d-flex flex-wrap align-items-center justify-content-between gap-3 hover-bg-light">
                        <div class="d-flex align-items-start gap-3 flex-grow-1">
                            <div class="bg-primary-subtle text-primary p-2 rounded-3 text-center flex-shrink-0" style="min-width: 50px;">
                                <i class="bi bi-bell-fill fs-5"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-secondary-subtle text-secondary small">{{ $ann['badge'] }}</span>
                                    <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>{{ $ann['date'] }}</small>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">{{ $ann['title'] }}</h6>
                                @if(!empty($ann['content']))
                                    <p class="text-muted small mb-0">{{ Str::limit($ann['content'], 140) }}</p>
                                @endif
                            </div>
                        </div>
                        @if(!empty($ann['link_url']))
                            <div>
                                <a href="{{ $ann['link_url'] }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    {{ $ann['link_text'] ?? 'View Notice' }} <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
