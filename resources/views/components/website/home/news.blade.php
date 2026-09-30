<section id="news" class="news-section py-5">

    <div class="container">

        <div class="text-center mb-5">

            <span class="about-subtitle">
                LATEST NEWS
            </span>

            <h2 class="about-title">
                Recent News & Updates
            </h2>

        </div>

        <div class="row">

            @foreach($pageData['news'] as $news)

            <div class="col-lg-4 mb-4">

                <div class="news-card">

                    <img src="{{ $news['image'] }}"
                        alt="{{ $news['title'] }}">

                    <div class="news-body">

                        <small class="news-date">

                            <i class="bi bi-calendar-event"></i>

                            {{ $news['date'] }}

                        </small>

                        <h5>{{ $news['title'] }}</h5>

                        <p>{{ $news['description'] }}</p>

                        <a href="{{ $news['url'] }}"
                            class="btn btn-sm btn-primary">

                            Read More

                        </a>

                    </div>

                </div>

            </div>

            @endforeach

        </div>

    </div>

</section>