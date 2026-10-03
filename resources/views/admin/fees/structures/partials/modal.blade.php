<x-ui.modal id="feeStructureModal" title="Add Fee Structure" size="lg">

    <form id="feeStructureForm">
        @csrf

        <input type="hidden" name="fee_structure_id" id="fee_structure_id">

        <div class="row g-3">

            <div class="col-md-6">
                <x-ui.select
                    name="academic_session_id"
                    id="academic_session_id"
                    label="Academic Session"
                    :options="$academicSessions ?? []"
                    placeholder="Select Session"
                    required
                />
            </div>

            <div class="col-md-6">
                <x-ui.select
                    name="academic_class_id"
                    id="academic_class_id"
                    label="Applicable Class"
                    :options="['' => 'All Classes (General)'] + ($academicClasses ?? [])"
                    placeholder="Select Class (or leave for All Classes)"
                />
            </div>

            <div class="col-md-6">
                <x-ui.select
                    name="fee_head_id"
                    id="fee_head_id"
                    label="Fee Head"
                    :options="$feeHeads ?? []"
                    placeholder="Select Fee Head"
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
                    label="Fee Amount (₹)"
                    placeholder="e.g. 2500"
                    required
                />
            </div>

            <div class="col-md-6">
                <x-ui.select
                    name="frequency"
                    id="frequency"
                    label="Payment Frequency"
                    :options="[
                        'monthly' => 'Monthly',
                        'quarterly' => 'Quarterly',
                        'half_yearly' => 'Half-Yearly',
                        'annually' => 'Annually',
                        'one_time' => 'One-Time (On Admission)'
                    ]"
                    value="monthly"
                    required
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    type="date"
                    name="due_date"
                    id="due_date"
                    label="Standard Due Date"
                    help="Default due date for fee installment"
                />
            </div>

            <div class="col-md-6">
                <x-ui.select
                    name="fine_type"
                    id="fine_type"
                    label="Late Fine Type"
                    :options="[
                        'none' => 'No Late Fine',
                        'flat' => 'Flat Amount (₹)',
                        'daily' => 'Daily Per-Day (₹/day)',
                        'percentage' => 'Percentage (%)'
                    ]"
                    value="none"
                    required
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    type="number"
                    step="0.01"
                    min="0"
                    name="fine_amount"
                    id="fine_amount"
                    label="Fine Amount / Rate"
                    placeholder="0"
                    value="0"
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

        <x-ui.button type="submit" form="feeStructureForm" id="btnSaveFeeStructure">
            <i class="bi bi-check-lg me-1"></i> Save Fee Structure
        </x-ui.button>
    </x-slot:footer>

</x-ui.modal>
