<x-ui.modal id="sliderModal" title="Add Home Slider" size="lg">

    <form id="sliderForm" enctype="multipart/form-data">

        @csrf

        <input type="hidden" name="slider_id" id="slider_id">

        <div class="row g-3">

            <div class="col-md-12">
                <x-ui.form-input
                    name="title"
                    id="title"
                    label="Slider Heading / Title"
                    placeholder="Enter main title"
                    required
                />
            </div>

            <div class="col-md-12">
                <x-ui.form-input
                    name="subtitle"
                    id="subtitle"
                    label="Subtitle / Description"
                    placeholder="Enter short description"
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    name="button_text"
                    id="button_text"
                    label="Button Text"
                    placeholder="e.g. Admission Open / Explore"
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    name="button_url"
                    id="button_url"
                    label="Button URL / Link"
                    placeholder="e.g. /admission or https://..."
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-file
                    name="image"
                    id="slider_image"
                    label="Slider Image"
                    accept="image/*"
                />
                <div id="imagePreviewContainer" class="mt-2 d-none">
                    <img id="imagePreview" src="" alt="Preview" class="img-thumbnail" style="max-height: 80px;">
                </div>
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

        </div>

    </form>

    <x-slot:footer>

        <x-ui.button variant="secondary" data-bs-dismiss="modal">
            Cancel
        </x-ui.button>

        <x-ui.button type="submit" form="sliderForm" id="btnSaveSlider">
            <i class="bi bi-check-lg"></i> Save Slider
        </x-ui.button>

    </x-slot:footer>

</x-ui.modal>
