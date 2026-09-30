@php
    $announcements = $section['data'] ?? ($pageData['announcements'] ?? []);
@endphp

@if(!empty($announcements))
<section class="announcements-section py-5 bg-light {{ $section['custom_class'] ?? '' }}">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-primary fw-bold text-uppercase">{{ $section['subtitle'] ?? 'Stay Updated' }}</span>
            <h2 class="fw-bold text-dark">{{ $section['title'] ?? 'School Announcements' }}</h2>
        </div>

        <div class="row g-4">
            @foreach($announcements as $ann)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 transition-all hover-shadow">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill">
                                {{ $ann['badge'] }}
                            </span>
                            <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $ann['date'] }}</small>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">{{ $ann['title'] }}</h5>
                        @if(!empty($ann['content']))
                            <p class="text-muted small flex-grow-1 mb-3">{{ Str::limit($ann['content'], 120) }}</p>
                        @endif
                        @if(!empty($ann['link_url']))
                            <div class="mt-auto pt-2 border-top">
                                <a href="{{ $ann['link_url'] }}" class="text-primary fw-semibold text-decoration-none small">
                                    {{ $ann['link_text'] ?? 'Read Announcement' }} <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
