<section
    id="admission-enquiry"
    class="py-5"
>

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-8">

                <div class="text-center mb-4">

                    <h2>
                        Admission Enquiry
                    </h2>

                    <p class="text-muted">
                        Interested in admission? Fill in your details
                        and our team will contact you.
                    </p>

                </div>


                <div class="card border-0 shadow-sm">

                    <div class="card-body p-4 p-lg-5">

                        <form
                            id="admissionEnquiryForm"
                            action="{{ route('admission') }}"
                            method="POST"
                        >

                            @csrf


                            <div class="row g-3">

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Student Name
                                    </label>

                                    <input
                                        type="text"
                                        name="student_name"
                                        class="form-control"
                                        placeholder="Student Name"
                                    >

                                    <div
                                        class="invalid-feedback"
                                        data-error="student_name"
                                    ></div>

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Parent / Guardian Name
                                    </label>

                                    <input
                                        type="text"
                                        name="parent_name"
                                        class="form-control"
                                        placeholder="Parent / Guardian Name"
                                    >

                                    <div
                                        class="invalid-feedback"
                                        data-error="parent_name"
                                    ></div>

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Email
                                    </label>

                                    <input
                                        type="email"
                                        name="email"
                                        class="form-control"
                                        placeholder="Email Address"
                                    >

                                    <div
                                        class="invalid-feedback"
                                        data-error="email"
                                    ></div>

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Phone
                                    </label>

                                    <input
                                        type="text"
                                        name="phone"
                                        class="form-control"
                                        placeholder="Phone Number"
                                    >

                                    <div
                                        class="invalid-feedback"
                                        data-error="phone"
                                    ></div>

                                </div>


                                <div class="col-md-12">

                                    <label class="form-label">
                                        Applying For Class
                                    </label>

                                    <select
                                        name="class_id"
                                        class="form-select"
                                    >

                                        <option value="">
                                            Select Class
                                        </option>

                                        {{-- Classes --}}

                                    </select>

                                    <div
                                        class="invalid-feedback"
                                        data-error="class_id"
                                    ></div>

                                </div>


                                <div class="col-md-12">

                                    <label class="form-label">
                                        Message
                                    </label>

                                    <textarea
                                        name="message"
                                        rows="5"
                                        class="form-control"
                                        placeholder="Any questions or additional information?"
                                    ></textarea>

                                    <div
                                        class="invalid-feedback"
                                        data-error="message"
                                    ></div>

                                </div>


                                <div class="col-md-12">

                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                        id="btnAdmissionSubmit"
                                    >

                                        <span class="btn-text">
                                            Submit Enquiry
                                        </span>

                                        <span
                                            class="spinner-border spinner-border-sm d-none"
                                        ></span>

                                    </button>

                                </div>

                            </div>

                        </form>


                        <div
                            id="admissionThankYou"
                            class="text-center py-5 d-none"
                        >

                            <i
                                class="bi bi-check-circle-fill text-success"
                                style="font-size: 60px;"
                            ></i>

                            <h3 class="mt-3">
                                Thank You!
                            </h3>

                            <p class="text-muted">
                                Your admission enquiry has been submitted
                                successfully. Our admission team will
                                contact you soon.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>