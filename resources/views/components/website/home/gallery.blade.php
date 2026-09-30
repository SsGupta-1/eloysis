<section id="gallery" class="gallery-section py-5">

    <div class="container">

        <div class="text-center mb-5">

            <span class="about-subtitle">

                GALLERY

            </span>

            <h2 class="about-title">

                School Moments

            </h2>

        </div>

        <div class="row">

            @foreach($pageData['gallery'] as $image)

            <div class="col-lg-4 col-md-6 mb-4">

                <a href="{{ asset($image['image']) }}"
                    target="_blank"
                    class="gallery-item">

                    <img
                        src="{{ asset($image['image']) }}"
                        class="img-fluid rounded"
                        alt="Gallery">

                </a>

            </div>

            @endforeach

        </div>

    </div>

</section>