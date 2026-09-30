<section id="principal" class="principal-section py-5">

    <div class="container">

        <div class="row align-items-center">

            <div class="col-lg-4 text-center mb-4">

                <img
                    src="{{ $pageData['principal']['image'] }}"
                    class="img-fluid rounded shadow principal-image"
                    alt="Principal">

            </div>

            <div class="col-lg-8">

                <span class="about-subtitle">

                    PRINCIPAL'S MESSAGE

                </span>

                <h2 class="about-title">

                    Message From Our Principal

                </h2>

                <p class="mt-4">

                    {{ $pageData['principal']['message'] }}

                </p>

                <img
                    src="{{ $pageData['principal']['signature'] }}"
                    class="signature"
                    alt="Signature">

                <h5 class="mt-3 mb-0">

                    {{ $pageData['principal']['name'] }}

                </h5>

                <small>

                    {{ $pageData['principal']['designation'] }}

                </small>

            </div>

        </div>

    </div>

</section>