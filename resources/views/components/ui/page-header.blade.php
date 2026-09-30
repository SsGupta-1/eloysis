@props([
    'title',
    'subtitle' => null,
    'icon' => 'bi-grid-1x2',
])

<div class="card border-0 shadow-sm mb-3 mb-md-4 p-3 p-md-4 bg-white page-header-card">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="fs-4 text-secondary opacity-75 d-none d-sm-flex align-items-center justify-content-center">
                <i class="bi {{ $icon }}"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0 text-dark fs-5 fs-md-4">
                    {{ $title }}
                </h4>
                @if($subtitle)
                    <p class="text-muted mb-0 fs-7 mt-1">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
        </div>

        @isset($actions)
            <div class="page-header-actions d-flex align-items-center flex-wrap gap-2 w-100 w-md-auto justify-content-start justify-content-md-end">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>