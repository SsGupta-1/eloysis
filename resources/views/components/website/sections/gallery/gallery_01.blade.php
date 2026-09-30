@php
    $gallery = $section['data'] ?? ($pageData['gallery'] ?? []);
@endphp

@if(!empty($gallery))
<section id="gallery" class="gallery-section py-5 bg-light {{ $section['custom_class'] ?? '' }}">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-primary fw-bold text-uppercase">{{ $section['subtitle'] ?? 'Our Memories' }}</span>
            <h2 class="fw-bold text-dark">{{ $section['title'] ?? 'Campus Photo Gallery' }}</h2>
        </div>

        <div class="row g-4">
            @foreach($gallery as $item)
                <div class="col-sm-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative group hover-lift">
                        <img src="{{ $item['image'] }}" class="w-100" alt="{{ $item['title'] ?? 'Gallery' }}" style="height: 250px; object-fit: cover; transition: transform 0.4s ease;">
                        <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-dark bg-opacity-75 text-white">
                            <span class="badge bg-primary mb-1">{{ $item['category'] }}</span>
                            <h6 class="fw-bold mb-0 text-truncate">{{ $item['title'] ?? 'Campus Life' }}</h6>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
