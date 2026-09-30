<x-ui.modal id="announcementModal" title="Add Announcement" size="lg">

    <form id="announcementForm">

        @csrf

        <input type="hidden" name="announcement_id" id="announcement_id">

        <div class="row g-3">

            <div class="col-md-8">
                <x-ui.form-input
                    name="title"
                    id="title"
                    label="Announcement Headline / Title"
                    placeholder="Enter announcement title"
                    required
                />
            </div>

            <div class="col-md-4">
                <x-ui.form-input
                    name="badge"
                    id="badge"
                    label="Badge Label / Tag"
                    placeholder="e.g. Admission, Urgent, New, Exam"
                />
            </div>

            <div class="col-md-12">
                <x-ui.textarea
                    name="content"
                    id="content"
                    label="Description / Details (Optional)"
                    placeholder="Brief details about the announcement"
                    rows="3"
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    name="link_text"
                    id="link_text"
                    label="Action Button / Link Text"
                    placeholder="e.g. Read More / Apply Now"
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    name="link_url"
                    id="link_url"
                    label="Destination URL"
                    placeholder="e.g. /admission or https://..."
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    type="datetime-local"
                    name="start_date"
                    id="start_date"
                    label="Publish Start Date & Time"
                    placeholder="Leave empty for immediate"
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    type="datetime-local"
                    name="end_date"
                    id="end_date"
                    label="Publish End Date & Time (Expiry)"
                    placeholder="Leave empty for indefinite"
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

        </div>

    </form>

    <x-slot:footer>

        <x-ui.button variant="secondary" data-bs-dismiss="modal">
            Cancel
        </x-ui.button>

        <x-ui.button type="submit" form="announcementForm" id="btnSaveAnnouncement">
            <i class="bi bi-check-lg"></i> Save Announcement
        </x-ui.button>

    </x-slot:footer>

</x-ui.modal>
