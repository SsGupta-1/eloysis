const FeeAllocation = {

    bulkModal: null,
    singleModal: null,
    table: null,

    init() {
        const bulkModalEl = document.getElementById('feeBulkAllocationModal');
        if (bulkModalEl) {
            this.bulkModal = new bootstrap.Modal(bulkModalEl);
        }

        const singleModalEl = document.getElementById('feeSingleAllocationModal');
        if (singleModalEl) {
            this.singleModal = new bootstrap.Modal(singleModalEl);
        }

        this.initDataTable();
        this.bindEvents();
    },

    initDataTable() {
        this.table = $('#feeAllocationsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: FEE_ALLOCATION_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.academic_session_id = $('#filter_academic_session_id').val();
                    d.class_id = $('#filter_class_id').val();
                    d.section_id = $('#filter_section_id').val();
                    d.fee_head_id = $('#filter_fee_head_id').val();
                    d.status = $('#filter_status').val();
                },
                error: function (xhr) {
                    if (typeof Toast !== 'undefined') {
                        Toast.error(xhr.responseJSON?.message ?? 'Unable to load fee allocations.');
                    }
                }
            },
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            searching: true,
            ordering: true,
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'student.user.name',
                    name: 'student.user.name',
                    render: function (data, type, row) {
                        const name = row.student?.user?.name ?? 'N/A';
                        const admNo = row.student?.admission_no ? `<span class="badge bg-light text-secondary border">Adm: ${row.student.admission_no}</span>` : '';
                        const rollNo = row.enrollment?.roll_number ? `<span class="badge bg-info-subtle text-info border border-info-subtle">Roll: ${row.enrollment.roll_number}</span>` : '';
                        return `
                            <div>
                                <div class="fw-bold text-dark">${name}</div>
                                <div class="small d-flex gap-1 mt-1">${admNo} ${rollNo}</div>
                            </div>
                        `;
                    }
                },
                {
                    data: 'enrollment.student_class.class_name',
                    name: 'enrollment.studentClass.class_name',
                    render: function (data, type, row) {
                        const className = row.enrollment?.student_class?.class_name ?? '-';
                        const secName = row.enrollment?.section?.section_name ?? '';
                        return `<span class="fw-semibold">${className} ${secName ? '(' + secName + ')' : ''}</span>`;
                    }
                },
                {
                    data: 'title',
                    name: 'title',
                    render: function (data, type, row) {
                        const headName = row.fee_head?.name ? `<span class="badge bg-primary-subtle text-primary">${row.fee_head.name}</span>` : '';
                        return `
                            <div>
                                <div class="fw-medium">${row.title ?? '-'}</div>
                                <div class="small mt-1">${headName}</div>
                            </div>
                        `;
                    }
                },
                {
                    data: 'amount',
                    name: 'amount',
                    render: function (data, type, row) {
                        const netPayable = Math.max(0, (parseFloat(row.amount) - parseFloat(row.discount_amount || 0) + parseFloat(row.fine_amount || 0)));
                        const discBadge = parseFloat(row.discount_amount) > 0 ? `<div class="text-danger small" style="font-size:0.75rem;">-₹${parseFloat(row.discount_amount)} disc</div>` : '';
                        return `<div><span class="fw-bold text-dark">₹${netPayable.toFixed(2)}</span>${discBadge}</div>`;
                    }
                },
                {
                    data: 'paid_amount',
                    name: 'paid_amount',
                    render: function (data, type, row) {
                        const paid = parseFloat(row.paid_amount || 0);
                        return `<span class="fw-bold text-success">₹${paid.toFixed(2)}</span>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const netPayable = Math.max(0, (parseFloat(row.amount) - parseFloat(row.discount_amount || 0) + parseFloat(row.fine_amount || 0)));
                        const balance = Math.max(0, netPayable - parseFloat(row.paid_amount || 0));
                        return balance > 0 
                            ? `<span class="fw-bold text-danger">₹${balance.toFixed(2)}</span>`
                            : `<span class="text-muted small">Paid</span>`;
                    }
                },
                {
                    data: 'due_date',
                    name: 'due_date',
                    render: function (data, type, row) {
                        if (!row.due_date) return '-';
                        const isPast = new Date(row.due_date) < new Date() && row.status !== 'paid';
                        const dateFormatted = new Date(row.due_date).toLocaleDateString('en-GB');
                        return isPast 
                            ? `<span class="text-danger fw-semibold"><i class="bi bi-exclamation-circle me-1"></i>${dateFormatted}</span>`
                            : `<span class="text-muted">${dateFormatted}</span>`;
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data, type, row) {
                        const statusMap = {
                            'paid': '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-check-circle me-1"></i>Paid</span>',
                            'partial': '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"><i class="bi bi-hourglass-split me-1"></i>Partial</span>',
                            'unpaid': '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="bi bi-clock me-1"></i>Unpaid</span>',
                            'overdue': '<span class="badge bg-dark-subtle text-dark border border-dark-subtle px-2 py-1"><i class="bi bi-exclamation-triangle me-1"></i>Overdue</span>'
                        };
                        return statusMap[row.status] ?? `<span class="badge bg-secondary">${row.status}</span>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const canDelete = parseFloat(row.paid_amount || 0) === 0;
                        return `
                            <button type="button" class="btn btn-sm btn-warning btn-edit-allocation" data-id="${row.id}" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            ${canDelete ? `
                            <button type="button" class="btn btn-sm btn-danger btn-delete-allocation" data-id="${row.id}" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>` : ''}
                        `;
                    }
                }
            ]
        });
    },

    bindEvents() {
        const self = this;

        // Open Bulk Allocation Modal
        $('#btnAddBulkAllocation').on('click', function () {
            $('#feeBulkAllocationForm')[0].reset();
            Helper.clearErrors($('#feeBulkAllocationForm'));
            $('#bulkStructuresContainer').html('<div class="text-muted small fst-italic">Please select Session and Class first to load configured fee structures.</div>');
            self.bulkModal.show();
        });

        // Open Custom / Single Allocation Modal
        $('#btnAddSingleAllocation').on('click', function () {
            self.resetSingleForm();
            $('#feeSingleAllocationModalTitle').text('Custom Fee Allocation');
            $('#studentSelectWrapper, #studentClassFilterWrapper, #studentEnrollmentWrapper').removeClass('d-none');
            $('#studentDetailDisplay').addClass('d-none').html('');
            self.singleModal.show();
        });

        // Dynamic Fee Structures loading on Bulk Modal Session/Class change
        $('#bulk_academic_session_id, #bulk_academic_class_id').on('change', function () {
            const sessionId = $('#bulk_academic_session_id').val();
            const classId = $('#bulk_academic_class_id').val();

            if (!sessionId || !classId) {
                $('#bulkStructuresContainer').html('<div class="text-muted small fst-italic">Please select Session and Class first to load configured fee structures.</div>');
                return;
            }

            $('#bulkStructuresContainer').html('<div class="text-primary small"><span class="spinner-border spinner-border-sm me-2"></span>Loading fee structures...</div>');

            $.ajax({
                url: FEE_GET_STRUCTURES_URL,
                type: 'GET',
                data: { academic_session_id: sessionId, academic_class_id: classId },
                success(res) {
                    if (res.data && res.data.length > 0) {
                        let html = '<div class="row g-2">';
                        res.data.forEach((s) => {
                            html += `
                                <div class="col-12 col-md-6">
                                    <div class="form-check p-2 border rounded bg-white">
                                        <input class="form-check-input ms-1 me-2" type="checkbox" name="fee_structure_ids[]" value="${s.id}" id="struct_${s.id}" checked>
                                        <label class="form-check-label fw-medium text-dark cursor-pointer" for="struct_${s.id}">
                                            ${s.name}
                                        </label>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        $('#bulkStructuresContainer').html(html);
                    } else {
                        $('#bulkStructuresContainer').html('<div class="text-danger small"><i class="bi bi-info-circle me-1"></i>No fee structures configured for this session & class. Please configure in Fee Structures master first.</div>');
                    }
                },
                error() {
                    $('#bulkStructuresContainer').html('<div class="text-danger small">Failed to load fee structures.</div>');
                }
            });
        });

        // Search Students in Single Allocation Modal
        let studentSearchTimer = null;
        let loadedStudentsMap = {};

        function loadStudents(searchTerm = '') {
            const sessionId = $('#single_academic_session_id').val();
            const classId = $('#single_class_id').val();

            $.ajax({
                url: FEE_SEARCH_STUDENTS_URL,
                type: 'GET',
                data: {
                    q: searchTerm,
                    academic_session_id: sessionId,
                    class_id: classId
                },
                success(res) {
                    let select = $('#student_enrollment_id');
                    select.empty().append('<option value="">Select Student...</option>');
                    loadedStudentsMap = {};
                    if (res.results && res.results.length > 0) {
                        res.results.forEach(item => {
                            loadedStudentsMap[item.id] = item;
                            select.append(`<option value="${item.id}">${item.text}</option>`);
                        });
                    }
                }
            });
        }

        $('#single_academic_session_id, #single_class_id').on('change', function () {
            loadStudents();
        });

        // When Student is picked in Single Modal
        $('#student_enrollment_id').on('change', function () {
            const id = $(this).val();
            const student = loadedStudentsMap[id];
            if (student && student.discount_id) {
                $('#single_fee_discount_id').val(student.discount_id).trigger('change');
            }
        });

        // Dynamic Discount Calculation based on Rule
        function calculateSingleDiscount() {
            const discountId = $('#single_fee_discount_id').val();
            const baseAmount = parseFloat($('#single_amount').val()) || 0;

            if (!discountId || typeof FEE_DISCOUNTS_DATA === 'undefined') {
                return;
            }

            const rule = FEE_DISCOUNTS_DATA.find(d => String(d.id) === String(discountId));
            if (rule) {
                let discAmount = 0;
                if (rule.discount_type === 'percentage') {
                    discAmount = (baseAmount * parseFloat(rule.amount)) / 100;
                } else {
                    discAmount = Math.min(baseAmount, parseFloat(rule.amount));
                }
                $('#single_discount_amount').val(discAmount.toFixed(2));
            }
        }

        $('#single_fee_discount_id, #single_amount').on('input change', function () {
            calculateSingleDiscount();
        });

        // Submit Bulk Allocation
        $('#feeBulkAllocationForm').on('submit', function (e) {
            e.preventDefault();

            const form = this;
            Ajax.request({
                form: form,
                url: FEE_ALLOCATION_BULK_URL,
                method: 'POST',
                success(response) {
                    self.bulkModal.hide();
                    self.table.ajax.reload(null, false);
                }
            });
        });

        // Submit Single / Edit Allocation
        $('#feeSingleAllocationForm').on('submit', function (e) {
            e.preventDefault();

            const form = this;
            const id = $('#allocation_id').val();
            const url = id ? FEE_ALLOCATION_UPDATE_URL.replace(':id', id) : FEE_ALLOCATION_STORE_URL;
            const method = id ? 'PUT' : 'POST';

            Ajax.request({
                form: form,
                url: url,
                method: method,
                success(response) {
                    self.singleModal.hide();
                    self.table.ajax.reload(null, false);
                }
            });
        });

        // Edit Allocation
        $(document).on('click', '.btn-edit-allocation', function () {
            const id = $(this).data('id');
            const url = FEE_ALLOCATION_EDIT_URL.replace(':id', id);

            Ajax.request({
                url: url,
                method: 'GET',
                success(response) {
                    const data = response.data;
                    self.resetSingleForm();

                    $('#allocation_id').val(data.id);
                    $('#single_academic_session_id').val(data.academic_session_id);
                    
                    // Display student information and hide student picker during edit
                    $('#studentSelectWrapper, #studentClassFilterWrapper, #studentEnrollmentWrapper').addClass('d-none');
                    $('#studentDetailDisplay').removeClass('d-none').html(`
                        <strong>Student:</strong> ${data.student_name} (Adm: ${data.admission_no}) | <strong>Class:</strong> ${data.class_name}
                    `);

                    // Put temporary selected value
                    $('#student_enrollment_id').html(`<option value="${data.student_enrollment_id}" selected>${data.student_name}</option>`);

                    $('#single_fee_head_id').val(data.fee_head_id);
                    $('#single_title').val(data.title);
                    $('#single_month').val(data.month ?? '');
                    $('#single_year').val(data.year ?? new Date().getFullYear());
                    $('#single_due_date').val(data.due_date ?? '');
                    $('#single_amount').val(data.amount);
                    $('#single_discount_amount').val(data.discount_amount ?? 0);
                    $('#single_fine_amount').val(data.fine_amount ?? 0);

                    // If paid amount > 0, lock amount & discount
                    if (parseFloat(data.paid_amount) > 0) {
                        $('#single_amount, #single_discount_amount').prop('disabled', true);
                    } else {
                        $('#single_amount, #single_discount_amount').prop('disabled', false);
                    }

                    $('#feeSingleAllocationModalTitle').text('Edit Fee Allocation');
                    self.singleModal.show();
                }
            });
        });

        // Delete Allocation
        $(document).on('click', '.btn-delete-allocation', function () {
            const id = $(this).data('id');
            const url = FEE_ALLOCATION_DELETE_URL.replace(':id', id);

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to delete this Fee Allocation?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Ajax.request({
                        url: url,
                        method: 'DELETE',
                        success(response) {
                            self.table.ajax.reload(null, false);
                        }
                    });
                }
            });
        });

        // Filter form reset
        $('#btnResetFilter').on('click', function () {
            $('#filterForm')[0].reset();
            self.table.ajax.reload();
        });

        $('#filter_academic_session_id, #filter_class_id, #filter_section_id, #filter_fee_head_id, #filter_status').on('change', function () {
            self.table.ajax.reload();
        });
    },

    resetSingleForm() {
        const form = $('#feeSingleAllocationForm');
        form[0].reset();
        $('#allocation_id').val('');
        $('#single_amount, #single_discount_amount').prop('disabled', false);
        $('#single_discount_amount, #single_fine_amount').val('0.00');
        Helper.clearErrors(form);
    }
};

$(function () {
    FeeAllocation.init();
});
