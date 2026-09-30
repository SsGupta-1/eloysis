@php
    $gallery = $section['data'] ?? ($pageData['gallery'] ?? []);
    $categories = array_unique(array_filter(array_column($gallery, 'category')));
@endphp

@if(!empty($gallery))
<section id="gallery" class="gallery-tabbed-section py-5 {{ $section['custom_class'] ?? '' }}">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-primary fw-bold text-uppercase">{{ $section['subtitle'] ?? 'Moments at School' }}</span>
            <h2 class="fw-bold text-dark">{{ $section['title'] ?? 'Campus Visual Showcase' }}</h2>

            {{-- Category Filter Buttons --}}
            <div class="d-flex flex-wrap justify-content-center gap-2 mt-4" id="galleryFilterButtons">
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 filter-btn active" data-filter="all">
                    All Photos
                </button>
                @foreach($categories as $cat)
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 filter-btn" data-filter="{{ Str::slug($cat) }}">
                        {{ $cat }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="row g-4" id="galleryItemsContainer">
            @foreach($gallery as $item)
                <div class="col-sm-6 col-lg-4 gallery-item-wrap" data-category="{{ Str::slug($item['category']) }}">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative">
                        <img src="{{ $item['image'] }}" class="w-100" alt="{{ $item['title'] ?? 'Gallery' }}" style="height: 260px; object-fit: cover;">
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-primary text-white">{{ $item['category'] }}</span>
                        </div>
                        <div class="card-body p-3">
                            <h6 class="fw-bold text-dark mb-0 text-truncate">{{ $item['title'] ?? 'Campus Moment' }}</h6>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@push('scripts')
<script>
$(function() {
    $('#galleryFilterButtons .filter-btn').on('click', function() {
        $('#galleryFilterButtons .filter-btn').removeClass('active btn-primary').addClass('btn-outline-primary');
        $(this).addClass('active btn-primary').removeClass('btn-outline-primary');

        const filter = $(this).data('filter');
        if (filter === 'all') {
            $('.gallery-item-wrap').fadeIn();
        } else {
            $('.gallery-item-wrap').hide();
            $('.gallery-item-wrap[data-category="' + filter + '"]').fadeIn();
        }
    });
});
</script>
@endpush
@endif
