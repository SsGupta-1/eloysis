<x-ui.modal
    id="feeSingleAllocationModal"
    title="Custom Fee Allocation"
    size="lg">

    <form id="feeSingleAllocationForm" method="POST">
        @csrf
        <input type="hidden" name="id" id="allocation_id">
        <input type="hidden" name="_method" id="allocation_method" value="POST">

        <div class="row g-3">
            <div class="col-12 col-md-6" id="studentSelectWrapper">
                <x-ui.select
                    name="academic_session_id"
                    id="single_academic_session_id"
                    label="Academic Session *"
                    value=""
                    :options="['' => 'Select Session'] + ($academicSessions ?? [])"
                    required
                />
            </div>

            <div class="col-12 col-md-6" id="studentClassFilterWrapper">
                <x-ui.select
                    name="single_class_id"
                    id="single_class_id"
                    label="Filter by Class"
                    value=""
                    :options="['' => 'All Classes'] + ($academicClasses ?? [])"
                />
            </div>

            <div class="col-12" id="studentEnrollmentWrapper">
                <label for="student_enrollment_id" class="form-label fw-semibold">Student *</label>
                <select name="student_enrollment_id" id="student_enrollment_id" class="form-select" required>
                    <option value="">Type to search student by name, admission no or roll no...</option>
                </select>
                <div id="studentDetailDisplay" class="d-none mt-2 p-2 bg-light border rounded small"></div>
            </div>

            <div class="col-12 col-md-6">
                <x-ui.select
                    name="fee_head_id"
                    id="single_fee_head_id"
                    label="Fee Head *"
                    value=""
                    :options="['' => 'Select Fee Head'] + ($feeHeads ?? [])"
                    required
                />
            </div>

            <div class="col-12 col-md-6">
                <x-ui.form-input
                    type="text"
                    name="title"
                    id="single_title"
                    label="Allocation Title *"
                    placeholder="e.g. Tuition Fee - October 2026"
                    required
                />
            </div>

            <div class="col-12 col-md-4">
                <x-ui.select
                    name="month"
                    id="single_month"
                    label="Month / Term"
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
                    ]"
                />
            </div>

            <div class="col-12 col-md-4">
                <x-ui.form-input
                    type="number"
                    name="year"
                    id="single_year"
                    label="Year"
                    value="{{ date('Y') }}"
                />
            </div>

            <div class="col-12 col-md-4">
                <x-ui.form-input
                    type="date"
                    name="due_date"
                    id="single_due_date"
                    label="Due Date *"
                    value="{{ date('Y-m-d', strtotime('+15 days')) }}"
                    required
                />
            </div>

            <div class="col-12 col-md-4">
                <x-ui.form-input
                    type="number"
                    step="0.01"
                    name="amount"
                    id="single_amount"
                    label="Base Amount (₹) *"
                    placeholder="0.00"
                    required
                />
            </div>

            <div class="col-12 col-md-4">
                <x-ui.select
                    name="fee_discount_id"
                    id="single_fee_discount_id"
                    label="Discount Rule"
                    value=""
                    :options="['' => 'No Discount / Custom'] + ($feeDiscounts ?? [])"
                />
            </div>

            <div class="col-12 col-md-2">
                <x-ui.form-input
                    type="number"
                    step="0.01"
                    name="discount_amount"
                    id="single_discount_amount"
                    label="Discount (₹)"
                    value="0.00"
                />
            </div>

            <div class="col-12 col-md-2">
                <x-ui.form-input
                    type="number"
                    step="0.01"
                    name="fine_amount"
                    id="single_fine_amount"
                    label="Fine (₹)"
                    value="0.00"
                />
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
            form="feeSingleAllocationForm"
            id="btnSaveSingleAllocation"
            icon="bi-check2-circle">
            Save Allocation
        </x-ui.button>
    </x-slot:footer>

</x-ui.modal>
