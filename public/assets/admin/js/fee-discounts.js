const FeeDiscount = {

    modal: null,
    table: null,

    init() {
        const modalEl = document.getElementById('feeDiscountModal');
        if (modalEl) {
            this.modal = new bootstrap.Modal(modalEl);
        }

        this.initDataTable();
        this.bindEvents();
    },

    initDataTable() {
        this.table = $('#feeDiscountsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: FEE_DISCOUNT_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.discount_type = $('#filter_discount_type').val();
                    d.filter_status = $('#filter_status').val();
                },
                error: function (xhr) {
                    if (typeof Toast !== 'undefined') {
                        Toast.error(xhr.responseJSON?.message ?? 'Unable to load Fee Discounts.');
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
                    data: 'name',
                    name: 'name',
                    render: function (data, type, row) {
                        return `<span class="fw-semibold text-primary">${row.name}</span>`;
                    }
                },
                {
                    data: 'code',
                    name: 'code',
                    render: function (data, type, row) {
                        return row.code ? `<span class="badge bg-light text-dark border">${row.code}</span>` : '-';
                    }
                },
                {
                    data: 'discount_type',
                    name: 'discount_type',
                    render: function (data, type, row) {
                        return row.discount_type === 'percentage'
                            ? '<span class="badge bg-info text-dark"><i class="bi bi-percent me-1"></i>Percentage</span>'
                            : '<span class="badge bg-success"><i class="bi bi-currency-rupee me-1"></i>Fixed Amount</span>';
                    }
                },
                {
                    data: 'amount',
                    name: 'amount',
                    render: function (data, type, row) {
                        return row.discount_type === 'percentage'
                            ? `<span class="fw-bold">${parseFloat(row.amount)}%</span>`
                            : `<span class="fw-bold text-success">₹${parseFloat(row.amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>`;
                    }
                },
                {
                    data: 'is_active',
                    name: 'is_active',
                    orderable: true,
                    searchable: false,
                    render: function (data, type, row) {
                        const canEdit = typeof window.can === 'function' ? window.can('fees.discounts.edit') : true;
                        return Helper.statusSwitch(row.id, row.is_active, canEdit);
                    }
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function (data, type, row) {
                        return Helper.formatDate(row.created_at);
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const canEdit = typeof window.can === 'function' ? window.can('fees.discounts.edit') : true;
                        const canDelete = typeof window.can === 'function' ? window.can('fees.discounts.delete') : true;

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
        $('#btnAddFeeDiscount').on('click', function () {
            self.resetForm();
            $('#feeDiscountModalTitle').text('Add Fee Discount');
            self.modal.show();
        });

        // Submit Form (Add / Edit)
        $('#feeDiscountForm').on('submit', function (e) {
            e.preventDefault();

            const form = this;
            const id = $('#fee_discount_id').val();
            const url = id ? FEE_DISCOUNT_UPDATE_URL.replace(':id', id) : FEE_DISCOUNT_STORE_URL;
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
            const url = FEE_DISCOUNT_EDIT_URL.replace(':id', id);

            Ajax.request({
                url: url,
                method: 'GET',
                success(response) {
                    const data = response.data;
                    self.resetForm();

                    $('#fee_discount_id').val(data.id);
                    $('#name').val(data.name);
                    $('#code').val(data.code);
                    $('#discount_type').val(data.discount_type);
                    $('#amount').val(data.amount);
                    $('#description').val(data.description);
                    $('#is_active').val(data.is_active ? 1 : 0);

                    $('#feeDiscountModalTitle').text('Edit Fee Discount');
                    self.modal.show();
                }
            });
        });

        // Delete
        $(document).on('click', '.btn-delete', function () {
            const id = $(this).data('id');
            const url = FEE_DISCOUNT_DELETE_URL.replace(':id', id);

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to delete this Discount Rule?',
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
            const url = FEE_DISCOUNT_STATUS_URL.replace(':id', id);

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

        $('#filter_discount_type, #filter_status').on('change', function () {
            self.table.ajax.reload();
        });
    },

    resetForm() {
        const form = $('#feeDiscountForm');
        form[0].reset();
        $('#fee_discount_id').val('');
        $('#discount_type').val('fixed');
        $('#is_active').val('1');
        Helper.clearErrors(form);
    }
};

$(function () {
    FeeDiscount.init();
});
