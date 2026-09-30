<x-ui.modal id="quickLinkModal" title="Add Quick Link" size="lg">

    <form id="quickLinkForm">

        @csrf

        <input type="hidden" name="quick_link_id" id="quick_link_id">

        <div class="row g-3">

            <div class="col-md-8">
                <x-ui.form-input
                    name="title"
                    id="title"
                    label="Link Title / Heading"
                    placeholder="e.g. Admission Open / Online Exam / Latest Results"
                    required
                />
            </div>

            <div class="col-md-4">
                <x-ui.select
                    name="color"
                    id="color"
                    label="Card Color Theme"
                    :options="[
                        'primary' => 'Primary (Blue)',
                        'success' => 'Success (Green)',
                        'warning' => 'Warning (Yellow)',
                        'danger' => 'Danger (Red)',
                        'info' => 'Info (Cyan)',
                        'secondary' => 'Secondary (Gray)',
                        'dark' => 'Dark (Black)'
                    ]"
                    value="primary"
                    required
                />
            </div>

            <div class="col-md-12">
                <x-ui.form-input
                    name="description"
                    id="description"
                    label="Short Description / Subtitle"
                    placeholder="e.g. Apply for new admission session 2026-27"
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    name="url"
                    id="url"
                    label="Destination URL / Route"
                    placeholder="e.g. /admission or /admin/login or https://..."
                    required
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    name="icon"
                    id="icon"
                    label="Bootstrap Icon Class"
                    placeholder="e.g. bi bi-mortarboard-fill, bi bi-laptop, bi bi-award-fill"
                    value="bi bi-mortarboard-fill"
                />
                <div class="form-text">
                    Use any Bootstrap Icon (e.g. <code>bi bi-laptop</code>, <code>bi bi-book</code>, <code>bi bi-award-fill</code>).
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

        <x-ui.button type="submit" form="quickLinkForm" id="btnSaveQuickLink">
            <i class="bi bi-check-lg"></i> Save Quick Link
        </x-ui.button>

    </x-slot:footer>

</x-ui.modal>
