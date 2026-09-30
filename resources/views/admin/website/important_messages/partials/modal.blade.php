<x-ui.modal id="messageModal" title="Add Important Message" size="lg">

    <form id="messageForm">

        @csrf

        <input type="hidden" name="message_id" id="message_id">

        <div class="row g-3">

            <div class="col-md-8">
                <x-ui.form-input
                    name="title"
                    id="title"
                    label="Notice Title / Headline"
                    placeholder="e.g. Winter Vacation / Important Exam Notice"
                    required
                />
            </div>

            <div class="col-md-4">
                <x-ui.select
                    name="type"
                    id="type"
                    label="Alert Type (Color Theme)"
                    :options="[
                        'info' => 'Info (Blue)',
                        'warning' => 'Warning (Yellow)',
                        'danger' => 'Danger (Red / Urgent)',
                        'success' => 'Success (Green)'
                    ]"
                    value="info"
                    required
                />
            </div>

            <div class="col-md-12">
                <x-ui.textarea
                    name="message"
                    id="message"
                    label="Message Content"
                    placeholder="Enter detailed notice or alert information"
                    rows="3"
                    required
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    name="action_text"
                    id="action_text"
                    label="Button / Link Text"
                    placeholder="e.g. Read Notice / Download PDF"
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    name="action_url"
                    id="action_url"
                    label="Button URL / Destination Link"
                    placeholder="e.g. /news or https://..."
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    type="datetime-local"
                    name="start_date"
                    id="start_date"
                    label="Display Start Date & Time"
                    placeholder="Leave empty for immediate"
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    type="datetime-local"
                    name="end_date"
                    id="end_date"
                    label="Display End Date & Time (Expiry)"
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

        <x-ui.button type="submit" form="messageForm" id="btnSaveMessage">
            <i class="bi bi-check-lg"></i> Save Message
        </x-ui.button>

    </x-slot:footer>

</x-ui.modal>
