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
                            action="{{ route('admission.store') }}"
                            method="POST"
                        >

                            @csrf


                             <div
                                id="admissionEnquiryFormContent"
                                class="row g-3"
                            >

                                {{-- Student Name --}}
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Student Name <span class="text-danger">*</span>
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

                                {{-- Student Email --}}
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Email <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="email"
                                        name="student_email"
                                        class="form-control"
                                        placeholder="Email Address"
                                    >

                                    <div
                                        class="invalid-feedback"
                                        data-error="student_email"
                                    ></div>

                                </div>


                                {{-- Student Phone --}}
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Phone
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="student_phone"
                                        class="form-control"
                                        placeholder="Phone Number"
                                    >

                                    <div
                                        class="invalid-feedback"
                                        data-error="student_phone"
                                    ></div>

                                </div>

                                 {{-- Alternate Phone --}}
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Alternate Phone
                                    </label>

                                    <input
                                        type="text"
                                        name="alternate_phone"
                                        class="form-control"
                                        placeholder="Alternate Phone Number"
                                    >

                                    <div
                                        class="invalid-feedback"
                                        data-error="student_phone"
                                    ></div>

                                </div>


                                {{-- Date of Birth --}}
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Date of Birth <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="date"
                                        name="date_of_birth"
                                        class="form-control"
                                    >

                                    <div
                                        class="invalid-feedback"
                                        data-error="date_of_birth"
                                    ></div>

                                </div>


                                {{-- Gender --}}
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Gender <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        name="gender"
                                        class="form-select"
                                    >

                                        <option value="">
                                            Select Gender
                                        </option>

                                        <option value="male">
                                            Male
                                        </option>

                                        <option value="female">
                                            Female
                                        </option>

                                        <option value="other">
                                            Other
                                        </option>

                                    </select>

                                    <div
                                        class="invalid-feedback"
                                        data-error="gender"
                                    ></div>

                                </div>

                                {{-- Parent Name --}}
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Parent / Guardian Name
                                        <span class="text-danger">*</span>
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
                                {{-- Parent Phone --}}
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Parent / Guardian Phone
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="parent_phone"
                                        class="form-control"
                                        placeholder="Parent / Guardian Phone"
                                    >

                                    <div
                                        class="invalid-feedback"
                                        data-error="parent_phone"
                                    ></div>

                                </div>


                                {{-- Applying For Class --}}
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Applying For Class <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        name="class_id"
                                        class="form-select"
                                    >

                                        <option value="">
                                            Select Class
                                        </option>

                                        @forelse (class_options() as $class_id => $class_name)

                                            <option value="{{ $class_id }}">
                                                {{ $class_name }}
                                            </option>

                                        @empty

                                            <option value="">
                                                No classes available
                                            </option>

                                        @endforelse

                                    </select>

                                    <div
                                        class="invalid-feedback"
                                        data-error="class_id"
                                    ></div>

                                </div>


                                {{-- How did you hear about us --}}
                                <div class="col-md-12">

                                    <label class="form-label">
                                        How did you hear about us?
                                    </label>

                                    <select
                                        name="source"
                                        id="admissionSource"
                                        class="form-select"
                                    >

                                        <option value="website">
                                            Website
                                        </option>

                                        <option value="google">
                                            Google
                                        </option>

                                        <option value="facebook">
                                            Facebook
                                        </option>

                                        <option value="instagram">
                                            Instagram
                                        </option>

                                        <option value="advertisement">
                                            Advertisement
                                        </option>

                                        <option value="reference">
                                            Reference
                                        </option>

                                        <option value="walk_in">
                                            Walk-in
                                        </option>

                                        <option value="other">
                                            Other
                                        </option>

                                    </select>

                                    <div
                                        class="invalid-feedback"
                                        data-error="source"
                                    ></div>

                                </div>


                                {{-- Reference Type --}}
                                <div
                                    class="col-md-6 d-none"
                                    id="referenceTypeWrapper"
                                >

                                    <label class="form-label">
                                        Reference Type
                                    </label>

                                    <select
                                        name="reference_type"
                                        id="referenceType"
                                        class="form-select"
                                    >

                                        <option value="">
                                            Select Reference Type
                                        </option>

                                        <option value="student">
                                            Student
                                        </option>

                                        <option value="parent">
                                            Parent
                                        </option>

                                        <option value="teacher">
                                            Teacher
                                        </option>

                                        <option value="staff">
                                            Staff
                                        </option>

                                        <option value="other">
                                            Other
                                        </option>

                                    </select>

                                    <div
                                        class="invalid-feedback"
                                        data-error="reference_type"
                                    ></div>

                                </div>


                                {{-- Reference Name --}}
                                <div
                                    class="col-md-6 d-none"
                                    id="referenceNameWrapper"
                                >

                                    <label class="form-label">
                                        Reference Name
                                    </label>

                                    <input
                                        type="text"
                                        name="reference_name"
                                        class="form-control"
                                        placeholder="Reference Person Name"
                                    >

                                    <div
                                        class="invalid-feedback"
                                        data-error="reference_name"
                                    ></div>

                                </div>


                                {{-- Reference Phone --}}
                                <div
                                    class="col-md-6 d-none"
                                    id="referencePhoneWrapper"
                                >

                                    <label class="form-label">
                                        Reference Phone
                                    </label>

                                    <input
                                        type="text"
                                        name="reference_phone"
                                        class="form-control"
                                        placeholder="Reference Phone Number"
                                    >

                                    <div
                                        class="invalid-feedback"
                                        data-error="reference_phone"
                                    ></div>

                                </div>


                                {{-- Message --}}
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


                                {{-- Submit --}}
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
                                            role="status"
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