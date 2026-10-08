const FeeStructure = {

    modal: null,
    table: null,

    init() {
        const modalEl = document.getElementById('feeStructureModal');
        if (modalEl) {
            this.modal = new bootstrap.Modal(modalEl);
        }

        this.initDataTable();
        this.bindEvents();
    },

    initDataTable() {
        this.table = $('#feeStructuresTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: FEE_STRUCTURE_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.academic_session_id = $('#filter_academic_session_id').val();
                    d.academic_class_id = $('#filter_academic_class_id').val();
                    d.frequency = $('#filter_frequency').val();
                    d.filter_status = $('#filter_status').val();
                },
                error: function (xhr) {
                    if (typeof Toast !== 'undefined') {
                        Toast.error(xhr.responseJSON?.message ?? 'Unable to load Fee Structures.');
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
                    data: 'academic_session.name',
                    name: 'academicSession.name',
                    render: function (data, type, row) {
                        return `<span class="badge bg-primary-subtle text-primary border border-primary-subtle">${row.academic_session?.name ?? '-'}</span>`;
                    }
                },
                {
                    data: 'academic_class.class_name',
                    name: 'academicClass.class_name',
                    render: function (data, type, row) {
                        return row.academic_class
                            ? `<span class="fw-semibold text-dark">${row.academic_class.class_name}</span>`
                            : '<span class="badge bg-secondary-subtle text-secondary border">All Classes</span>';
                    }
                },
                {
                    data: 'fee_head.name',
                    name: 'feeHead.name',
                    render: function (data, type, row) {
                        return `<span class="fw-bold text-primary">${row.fee_head?.name ?? '-'}</span>`;
                    }
                },
                {
                    data: 'amount',
                    name: 'amount',
                    render: function (data, type, row) {
                        return `<span class="fw-bold text-success">₹${parseFloat(row.amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>`;
                    }
                },
                {
                    data: 'frequency',
                    name: 'frequency',
                    render: function (data, type, row) {
                        const freqLabels = {
                            'monthly': '<span class="badge bg-info text-dark">Monthly</span>',
                            'quarterly': '<span class="badge bg-warning text-dark">Quarterly</span>',
                            'half_yearly': '<span class="badge bg-primary">Half-Yearly</span>',
                            'annually': '<span class="badge bg-purple text-white" style="background:#7c3aed;">Annually</span>',
                            'one_time': '<span class="badge bg-success">One-Time</span>'
                        };
                        return freqLabels[row.frequency] ?? row.frequency;
                    }
                },
                {
                    data: 'fine_type',
                    name: 'fine_type',
                    render: function (data, type, row) {
                        if (!row.fine_type || row.fine_type === 'none' || parseFloat(row.fine_amount) <= 0) {
                            return '<span class="text-muted small">No Fine</span>';
                        }
                        if (row.fine_type === 'flat') {
                            return `<span class="badge bg-danger-subtle text-danger border">₹${parseFloat(row.fine_amount)} Flat</span>`;
                        }
                        if (row.fine_type === 'daily') {
                            return `<span class="badge bg-danger-subtle text-danger border">₹${parseFloat(row.fine_amount)}/day</span>`;
                        }
                        return `<span class="badge bg-danger-subtle text-danger border">${parseFloat(row.fine_amount)}%</span>`;
                    }
                },
                {
                    data: 'is_active',
                    name: 'is_active',
                    orderable: true,
                    searchable: false,
                    render: function (data, type, row) {
                        const canEdit = typeof window.can === 'function' ? window.can('fees.structures.edit') : true;
                        return Helper.statusSwitch(row.id, row.is_active, canEdit);
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const canEdit = typeof window.can === 'function' ? window.can('fees.structures.edit') : true;
                        const canDelete = typeof window.can === 'function' ? window.can('fees.structures.delete') : true;

                        let buttons = '';

                        if (canEdit) {
                            buttons += `
                                <button type="button" class="btn btn-sm btn-warning btn-edit" data-id="${row.id}" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            `;
                        }

                        if (canDelete) {
                            buttons += `
                                <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="${row.id}" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            `;
                        }

                        return buttons || '<span class="text-muted fs-7">-</span>';
                    }
                }
            ]
        });
    },

    bindEvents() {
        const self = this;

        // Add
        $('#btnAddFeeStructure').on('click', function () {
            self.resetForm();
            $('#feeStructureModalTitle').text('Add Fee Structure');
            self.modal.show();
        });

        // Submit Form (Add / Edit)
        $('#feeStructureForm').on('submit', function (e) {
            e.preventDefault();

            const form = this;
            const id = $('#fee_structure_id').val();
            const url = id ? FEE_STRUCTURE_UPDATE_URL.replace(':id', id) : FEE_STRUCTURE_STORE_URL;
            const method = id ? 'PUT' : 'POST';

            Ajax.request({
                form: form,
                url: url,
                method: method,
                success(response) {
                    self.modal.hide();
                    self.table.ajax.reload(null, false);
                }
            });
        });

        // Edit
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            const url = FEE_STRUCTURE_EDIT_URL.replace(':id', id);

            Ajax.request({
                url: url,
                method: 'GET',
                success(response) {
                    const data = response.data;
                    self.resetForm();

                    $('#fee_structure_id').val(data.id);
                    $('#academic_session_id').val(data.academic_session_id);
                    $('#academic_class_id').val(data.academic_class_id ?? '');
                    $('#fee_head_id').val(data.fee_head_id);
                    $('#fee_group_id').val(data.fee_group_id ?? '');
                    $('#amount').val(data.amount);
                    $('#frequency').val(data.frequency);
                    $('#due_date').val(data.due_date ?? '');
                    $('#fine_type').val(data.fine_type);
                    $('#fine_amount').val(data.fine_amount ?? 0);
                    $('#is_active').val(data.is_active ? 1 : 0);

                    $('#feeStructureModalTitle').text('Edit Fee Structure');
                    self.modal.show();
                }
            });
        });

        // Delete
        $(document).on('click', '.btn-delete', function () {
            const id = $(this).data('id');
            const url = FEE_STRUCTURE_DELETE_URL.replace(':id', id);

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to delete this Fee Structure?',
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

        // Status Switch
        $(document).on('change', '.btn-status', function () {
            const id = $(this).data('id');
            const url = FEE_STRUCTURE_STATUS_URL.replace(':id', id);

            Ajax.request({
                url: url,
                method: 'PATCH',
                error(xhr) {
                    self.table.ajax.reload(null, false);
                }
            });
        });

        // Filter form reset
        $('#btnResetFilter').on('click', function () {
            $('#filterForm')[0].reset();
            self.table.ajax.reload();
        });

        $('#filter_academic_session_id, #filter_academic_class_id, #filter_frequency, #filter_status').on('change', function () {
            self.table.ajax.reload();
        });
    },

    resetForm() {
        const form = $('#feeStructureForm');
        form[0].reset();
        $('#fee_structure_id').val('');
        $('#frequency').val('monthly');
        $('#fine_type').val('none');
        $('#fine_amount').val('0');
        $('#is_active').val('1');
        Helper.clearErrors(form);
    }
};

$(function () {
    FeeStructure.init();
});
