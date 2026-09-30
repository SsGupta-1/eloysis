<x-ui.modal id="galleryModal" title="Add Gallery Photo" size="lg">

    <form id="galleryForm" enctype="multipart/form-data">

        @csrf

        <input type="hidden" name="gallery_id" id="gallery_id">

        <div class="row g-3">

            <div class="col-md-6">
                <x-ui.form-input
                    name="title"
                    id="title"
                    label="Image Caption / Title"
                    placeholder="e.g. Science Fair / Sports Day"
                />
            </div>

            <div class="col-md-6">
                <x-ui.select
                    name="category"
                    id="category"
                    label="Category"
                    :options="[
                        'Campus' => 'Campus & Infrastructure',
                        'Events' => 'Events & Celebrations',
                        'Sports' => 'Sports & Activities',
                        'Academics' => 'Classrooms & Labs',
                        'General' => 'General'
                    ]"
                    value="General"
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-file
                    name="image"
                    id="gallery_image"
                    label="Upload Image"
                    accept="image/*"
                />
                <div id="galleryImagePreviewContainer" class="mt-2 d-none">
                    <img id="galleryImagePreview" src="" alt="Preview" class="img-thumbnail" style="max-height: 80px;">
                </div>
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    type="number"
                    name="sort_order"
                    id="sort_order"
                    label="Sort Order"
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

        <x-ui.button type="submit" form="galleryForm" id="btnSaveGallery">
            <i class="bi bi-check-lg"></i> Save Image
        </x-ui.button>

    </x-slot:footer>

</x-ui.modal>
