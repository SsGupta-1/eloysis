<section id="contact" class="contact-section py-5">

    <div class="container">

        <div class="text-center mb-5">

            <span class="about-subtitle">

                {{ $pageData['contact']['subtitle'] }}

            </span>

            <h2 class="about-title">

                {{ $pageData['contact']['title'] }}

            </h2>

        </div>

        <div class="row">

            <div class="col-lg-5 mb-4">

                <div class="contact-info">

                    <div class="contact-item">

                        <i class="bi bi-geo-alt-fill"></i>

                        <div>

                            <h5>Address</h5>

                            <p>{{ $pageData['contact']['address'] }}</p>

                        </div>

                    </div>

                    <div class="contact-item">

                        <i class="bi bi-telephone-fill"></i>

                        <div>

                            <h5>Phone</h5>

                            <p>{{ $pageData['contact']['phone'] }}</p>

                        </div>

                    </div>

                    <div class="contact-item">

                        <i class="bi bi-envelope-fill"></i>

                        <div>

                            <h5>Email</h5>

                            <p>{{ $pageData['contact']['email'] }}</p>

                        </div>

                    </div>

                    <div class="contact-item">

                        <i class="bi bi-clock-fill"></i>

                        <div>

                            <h5>Office Hours</h5>

                            <p>{{ $pageData['contact']['working_hours'] }}</p>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-lg-7">

               <form
                    id="contactForm"
                    action="{{ route('contact.store') }}"
                    method="POST"
                >

                    @csrf

                    <div id="contactFormContent">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    placeholder="Your Name"
                                >

                                <div class="invalid-feedback" data-error="name"></div>

                            </div>

                            <div class="col-md-6 mb-3">

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    placeholder="Your Email"
                                >

                                <div class="invalid-feedback" data-error="email"></div>

                            </div>

                        </div>

                        <div class="mb-3">

                            <input
                                type="text"
                                name="phone"
                                class="form-control"
                                placeholder="Phone Number"
                            >

                            <div class="invalid-feedback" data-error="phone"></div>

                        </div>

                        <div class="mb-3">

                            <input
                                type="text"
                                name="subject"
                                class="form-control"
                                placeholder="Subject"
                            >

                            <div class="invalid-feedback" data-error="subject"></div>

                        </div>

                        <div class="mb-3">

                            <textarea
                                rows="5"
                                name="message"
                                class="form-control"
                                placeholder="Message"
                            ></textarea>

                            <div class="invalid-feedback" data-error="message"></div>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary"
                            id="contactSubmitBtn"
                        >
                            <span class="btn-text">
                                Send Message
                            </span>

                            <span
                                class="spinner-border spinner-border-sm d-none"
                                id="contactSubmitSpinner"
                            ></span>
                        </button>

                    </div>


                    {{-- Thank You Message --}}

                    <div
                        id="contactThankYou"
                        class="text-center py-5 d-none"
                    >

                        <div class="mb-3">

                            <i
                                class="bi bi-check-circle-fill text-success"
                                style="font-size: 60px;"
                            ></i>

                        </div>

                        <h3 class="mb-2">
                            Thank You!
                        </h3>

                        <p class="text-muted mb-0">
                            Your message has been sent successfully.
                            We will get back to you soon.
                        </p>

                    </div>

                </form>

            </div>

        </div>

    </div>

</section>