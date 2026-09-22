@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'footer' => null,
    'class' => '',
])

<div {{ $attributes->merge(['class' => "card shadow-sm border-0 {$class}"]) }}>

    @if($title || $icon)

        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">

            <div>

                @if($title)
                    <h5 class="card-title mb-0">{{ $title }}</h5>
                @endif

                @if($subtitle)
                    <small class="text-muted">{{ $subtitle }}</small>
                @endif

            </div>

            @if($icon)
                <i class="bi {{ $icon }} fs-4 text-primary"></i>
            @endif

        </div>

    @endif

    <div class="card-body">

        {{ $slot }}

    </div>

    @if($footer)

        <div class="card-footer bg-white">

            {{ $footer }}

        </div>

    @endif

</div>