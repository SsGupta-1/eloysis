<x-ui.modal
    id="feeBulkAllocationModal"
    title="Bulk Fee Allocation"
    size="lg">

    <form id="feeBulkAllocationForm" method="POST">
        @csrf

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <x-ui.select
                    name="academic_session_id"
                    id="bulk_academic_session_id"
                    label="Academic Session *"
                    value=""
                    :options="['' => 'Select Session'] + ($academicSessions ?? [])"
                    required
                />
            </div>

            <div class="col-12 col-md-6">
                <x-ui.select
                    name="academic_class_id"
                    id="bulk_academic_class_id"
                    label="Class *"
                    value=""
                    :options="['' => 'Select Class'] + ($academicClasses ?? [])"
                    required
                />
            </div>

            <div class="col-12 col-md-6">
                <x-ui.select
                    name="section_id"
                    id="bulk_section_id"
                    label="Section (Optional)"
                    value=""
                    :options="['' => 'All Sections'] + ($sections ?? [])"
                />
            </div>

            <div class="col-12 col-md-6">
                <x-ui.select
                    name="fee_discount_id"
                    id="bulk_fee_discount_id"
                    label="Discount / Concession Rule"
                    value="student_assigned"
                    :options="[
                        'student_assigned' => 'Auto: Apply Each Student\'s Assigned Concession (Recommended)',
                        'none' => 'No Discount (Full Fee for All)',
                    ] + ($feeDiscounts ?? [])"
                />
            </div>

            <div class="col-12 col-md-4">
                <x-ui.select
                    name="month"
                    id="bulk_month"
                    label="Fee Month / Term (Optional)"
                    value=""
                    :options="[
                        '' => 'None / Annual',
                        'January' => 'January',
                        'February' => 'February',
                        'March' => 'March',
                        'April' => 'April',
                        'May' => 'May',
                        'June' => 'June',
                        'July' => 'July',
                        'August' => 'August',
                        'September' => 'September',
                        'October' => 'October',
                        'November' => 'November',
                        'December' => 'December',
                        'Quarter 1 (Apr-Jun)' => 'Quarter 1 (Apr-Jun)',
                        'Quarter 2 (Jul-Sep)' => 'Quarter 2 (Jul-Sep)',
                        'Quarter 3 (Oct-Dec)' => 'Quarter 3 (Oct-Dec)',
                        'Quarter 4 (Jan-Mar)' => 'Quarter 4 (Jan-Mar)',
                    ]"
                />
            </div>

            <div class="col-12 col-md-4">
                <x-ui.form-input
                    type="number"
                    name="year"
                    id="bulk_year"
                    label="Year"
                    value="{{ date('Y') }}"
                    min="2020"
                    max="2035"
                />
            </div>

            <div class="col-12 col-md-4">
                <x-ui.form-input
                    type="date"
                    name="due_date"
                    id="bulk_due_date"
                    label="Custom Due Date (Optional)"
                    value=""
                />
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Select Fee Structures to Allocate *</label>
                <div id="bulkStructuresContainer" class="p-3 border rounded bg-light min-h-100">
                    <div class="text-muted small fst-italic">Please select Session and Class first to load configured fee structures.</div>
                </div>
            </div>
        </div>

    </form>

    <x-slot:footer>
        <x-ui.button
            variant="secondary"
            data-bs-dismiss="modal">
            Cancel
        </x-ui.button>
        <x-ui.button
            type="submit"
            form="feeBulkAllocationForm"
            id="btnSaveBulkAllocation"
            icon="bi-check2-circle">
            Allocate to Students
        </x-ui.button>
    </x-slot:footer>

</x-ui.modal>
