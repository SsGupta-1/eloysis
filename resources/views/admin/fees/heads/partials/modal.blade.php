<x-ui.modal id="feeHeadModal" title="Add Fee Head">

    <form id="feeHeadForm">
        @csrf

        <input type="hidden" name="fee_head_id" id="fee_head_id">

        <div class="row g-3">

            <div class="col-md-12">
                <x-ui.form-input
                    name="name"
                    id="name"
                    label="Fee Head Name"
                    placeholder="e.g. Tuition Fee / Admission Fee / Exam Fee / Lab Fee"
                    required
                />
            </div>

            <div class="col-md-12">
                <x-ui.form-input
                    name="code"
                    id="code"
                    label="Code / Short Code"
                    placeholder="e.g. TUITION, ADMISSION, EXAM"
                />
            </div>

            <div class="col-md-12">
                <x-ui.textarea
                    name="description"
                    id="description"
                    label="Description"
                    placeholder="Optional notes regarding this fee category"
                    rows="3"
                />
            </div>

            <div class="col-md-12">
                <x-ui.select
                    name="is_active"
                    id="is_active"
                    label="Status"
                    :options="[
                        1 => 'Active',
                        0 => 'Inactive'
                    ]"
                    value="1"
                    required
                />
            </div>

        </div>

    </form>

    <x-slot:footer>
        <x-ui.button variant="secondary" data-bs-dismiss="modal">
            Cancel
        </x-ui.button>

        <x-ui.button type="submit" form="feeHeadForm" id="btnSaveFeeHead">
            <i class="bi bi-check-lg me-1"></i> Save Fee Head
        </x-ui.button>
    </x-slot:footer>

</x-ui.modal>
