@php
    $gallery = $section['data'] ?? ($pageData['gallery'] ?? []);
@endphp

@if(!empty($gallery))
<section id="gallery" class="gallery-masonry-section py-5 bg-dark text-white {{ $section['custom_class'] ?? '' }}">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-warning fw-bold text-uppercase">{{ $section['subtitle'] ?? 'Memories in Focus' }}</span>
            <h2 class="fw-bold text-white">{{ $section['title'] ?? 'Campus Life In Pictures' }}</h2>
        </div>

        <div class="row g-3">
            @foreach($gallery as $index => $item)
                @php
                    $isLarge = ($index % 5 === 0);
                    $colClass = $isLarge ? 'col-md-6 col-lg-8' : 'col-md-6 col-lg-4';
                    $height = $isLarge ? '320px' : '200px';
                @endphp
                <div class="{{ $colClass }}">
                    <div class="card border-0 rounded-4 overflow-hidden position-relative shadow h-100">
                        <img src="{{ $item['image'] }}" class="w-100 h-100" alt="{{ $item['title'] ?? 'Photo' }}" style="min-height: {{ $height }}; object-fit: cover; filter: brightness(0.85);">
                        <div class="position-absolute bottom-0 start-0 p-3 bg-gradient-dark w-100">
                            <span class="badge bg-warning text-dark mb-1">{{ $item['category'] }}</span>
                            <h6 class="fw-bold text-white mb-0">{{ $item['title'] ?? 'Campus Event' }}</h6>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
