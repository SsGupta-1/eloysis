<x-ui.modal id="eventModal" title="Add Event" size="lg">

    <form id="eventForm" enctype="multipart/form-data">

        @csrf

        <input type="hidden" name="event_id" id="event_id">

        <div class="row g-3">

            <div class="col-md-12">
                <x-ui.form-input
                    name="title"
                    id="title"
                    label="Event Title / Name"
                    placeholder="Enter event title"
                    required
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    type="date"
                    name="event_date"
                    id="event_date"
                    label="Event Date"
                    value="{{ date('Y-m-d') }}"
                    required
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    type="text"
                    name="event_time"
                    id="event_time"
                    label="Event Time"
                    placeholder="e.g. 09:30 AM or 10:00 AM - 01:00 PM"
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    type="text"
                    name="location"
                    id="location"
                    label="Venue / Location"
                    placeholder="e.g. School Auditorium / Playground"
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    name="url"
                    id="url"
                    label="Registration Link / Info Link"
                    placeholder="e.g. https://... or #"
                />
            </div>

            <div class="col-md-12">
                <x-ui.textarea
                    name="description"
                    id="description"
                    label="Event Description"
                    rows="3"
                    placeholder="Details about this event"
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-file
                    name="image"
                    id="event_image"
                    label="Event Banner / Photo"
                    accept="image/*"
                />
                <div id="eventImagePreviewContainer" class="mt-2 d-none">
                    <img id="eventImagePreview" src="" alt="Preview" class="img-thumbnail" style="max-height: 80px;">
                </div>
            </div>

        </div>

    </form>

    <x-slot:footer>

        <x-ui.button variant="secondary" data-bs-dismiss="modal">
            Cancel
        </x-ui.button>

        <x-ui.button type="submit" form="eventForm" id="btnSaveEvent">
            <i class="bi bi-check-lg"></i> Save Event
        </x-ui.button>

    </x-slot:footer>

</x-ui.modal>
