<x-ui.modal id="newsModal" title="Add News / Notice" size="lg">

    <form id="newsForm" enctype="multipart/form-data">

        @csrf

        <input type="hidden" name="news_id" id="news_id">

        <div class="row g-3">

            <div class="col-md-8">
                <x-ui.form-input
                    name="title"
                    id="title"
                    label="News Headline / Title"
                    placeholder="Enter news title"
                    required
                />
            </div>

            <div class="col-md-4">
                <x-ui.form-input
                    type="date"
                    name="published_date"
                    id="published_date"
                    label="Published Date"
                    value="{{ date('Y-m-d') }}"
                    required
                />
            </div>

            <div class="col-md-12">
                <x-ui.textarea
                    name="summary"
                    id="summary"
                    label="Short Summary / Excerpt"
                    rows="2"
                    placeholder="Brief summary of news"
                />
            </div>

            <div class="col-md-12">
                <x-ui.textarea
                    name="content"
                    id="content"
                    label="Full Content / Details"
                    rows="4"
                    placeholder="Detailed content"
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-file
                    name="image"
                    id="news_image"
                    label="Cover Image"
                    accept="image/*"
                />
                <div id="newsImagePreviewContainer" class="mt-2 d-none">
                    <img id="newsImagePreview" src="" alt="Preview" class="img-thumbnail" style="max-height: 80px;">
                </div>
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    name="url"
                    id="url"
                    label="External Link / Document URL (Optional)"
                    placeholder="e.g. https://... or /notice.pdf"
                />
            </div>

        </div>

    </form>

    <x-slot:footer>

        <x-ui.button variant="secondary" data-bs-dismiss="modal">
            Cancel
        </x-ui.button>

        <x-ui.button type="submit" form="newsForm" id="btnSaveNews">
            <i class="bi bi-check-lg"></i> Save News
        </x-ui.button>

    </x-slot:footer>

</x-ui.modal>
