<x-ui.modal id="feeDiscountModal" title="Add Fee Discount">

    <form id="feeDiscountForm">
        @csrf

        <input type="hidden" name="fee_discount_id" id="fee_discount_id">

        <div class="row g-3">

            <div class="col-md-8">
                <x-ui.form-input
                    name="name"
                    id="name"
                    label="Discount / Scholarship Name"
                    placeholder="e.g. Sibling Concession / Staff Child / Merit Scholarship"
                    required
                />
            </div>

            <div class="col-md-4">
                <x-ui.form-input
                    name="code"
                    id="code"
                    label="Discount Code"
                    placeholder="e.g. SIBLING10, STAFF50"
                />
            </div>

            <div class="col-md-6">
                <x-ui.select
                    name="discount_type"
                    id="discount_type"
                    label="Discount Type"
                    :options="[
                        'fixed' => 'Fixed Amount (₹)',
                        'percentage' => 'Percentage (%)'
                    ]"
                    value="fixed"
                    required
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    type="number"
                    step="0.01"
                    min="0"
                    name="amount"
                    id="amount"
                    label="Discount Value"
                    placeholder="e.g. 500 (for ₹500) or 15 (for 15%)"
                    required
                />
            </div>

            <div class="col-md-12">
                <x-ui.textarea
                    name="description"
                    id="description"
                    label="Description / Policy Details"
                    placeholder="Optional notes regarding criteria or eligibility"
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

        <x-ui.button type="submit" form="feeDiscountForm" id="btnSaveFeeDiscount">
            <i class="bi bi-check-lg me-1"></i> Save Discount
        </x-ui.button>
    </x-slot:footer>

</x-ui.modal>
