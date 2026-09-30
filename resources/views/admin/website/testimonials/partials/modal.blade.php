<x-ui.modal id="testimonialModal" title="Add Testimonial" size="lg">

    <form id="testimonialForm" enctype="multipart/form-data">

        @csrf

        <input type="hidden" name="testimonial_id" id="testimonial_id">

        <div class="row g-3">

            <div class="col-md-6">
                <x-ui.form-input
                    name="name"
                    id="name"
                    label="Person's Full Name"
                    placeholder="e.g. Amit Sharma / Priya Verma"
                    required
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    name="role"
                    id="role"
                    label="Designation / Role"
                    placeholder="e.g. Parent of Class 5 / Alumnus / Student"
                />
            </div>

            <div class="col-md-12">
                <x-ui.textarea
                    name="message"
                    id="message"
                    label="Testimonial / Review Message"
                    placeholder="Enter what they say about the school/institution..."
                    rows="3"
                    required
                />
            </div>

            <div class="col-md-6">
                <x-ui.select
                    name="rating"
                    id="rating"
                    label="Rating (1 to 5 Stars)"
                    :options="[
                        '5' => '5 Stars (★★★★★) - Excellent',
                        '4' => '4 Stars (★★★★☆) - Very Good',
                        '3' => '3 Stars (★★★☆☆) - Good',
                        '2' => '2 Stars (★★☆☆☆) - Average',
                        '1' => '1 Star (★☆☆☆☆) - Poor'
                    ]"
                    value="5"
                    required
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    type="number"
                    name="sort_order"
                    id="sort_order"
                    label="Display Sort Order"
                    placeholder="0, 1, 2..."
                    value="0"
                />
            </div>

            <div class="col-md-12">
                <x-ui.form-file
                    name="image"
                    id="testimonial_image"
                    label="Profile Photo / Avatar"
                    accept="image/*"
                />
                <div id="testimonialImagePreviewContainer" class="mt-2 d-none">
                    <img id="testimonialImagePreview" src="" alt="Avatar Preview" class="rounded-circle img-thumbnail" style="width: 70px; height: 70px; object-fit: cover;">
                </div>
            </div>

        </div>

    </form>

    <x-slot:footer>

        <x-ui.button variant="secondary" data-bs-dismiss="modal">
            Cancel
        </x-ui.button>

        <x-ui.button type="submit" form="testimonialForm" id="btnSaveTestimonial">
            <i class="bi bi-check-lg"></i> Save Testimonial
        </x-ui.button>

    </x-slot:footer>

</x-ui.modal>
