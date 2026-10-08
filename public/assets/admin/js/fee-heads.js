const FeeHead = {

    modal: null,
    table: null,

    init() {
        const modalEl = document.getElementById('feeHeadModal');
        if (modalEl) {
            this.modal = new bootstrap.Modal(modalEl);
        }

        this.initDataTable();
        this.bindEvents();
    },

    initDataTable() {
        this.table = $('#feeHeadsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: FEE_HEAD_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.filter_status = $('#filter_status').val();
                },
                error: function (xhr) {
                    if (typeof Toast !== 'undefined') {
                        Toast.error(xhr.responseJSON?.message ?? 'Unable to load Fee Heads.');
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
                    data: 'description',
                    name: 'description',
                    render: function (data, type, row) {
                        return row.description ? `<small class="text-muted">${row.description}</small>` : '-';
                    }
                },
                {
                    data: 'is_active',
                    name: 'is_active',
                    orderable: true,
                    searchable: false,
                    render: function (data, type, row) {
                        const canEdit = typeof window.can === 'function' ? window.can('fees.heads.edit') : true;
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
                        const canEdit = typeof window.can === 'function' ? window.can('fees.heads.edit') : true;
                        const canDelete = typeof window.can === 'function' ? window.can('fees.heads.delete') : true;

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
        $('#btnAddFeeHead').on('click', function () {
            self.resetForm();
            $('#feeHeadModalTitle').text('Add Fee Head');
            self.modal.show();
        });

        // Submit Form (Add / Edit)
        $('#feeHeadForm').on('submit', function (e) {
            e.preventDefault();

            const form = this;
            const id = $('#fee_head_id').val();
            const url = id ? FEE_HEAD_UPDATE_URL.replace(':id', id) : FEE_HEAD_STORE_URL;
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
            const url = FEE_HEAD_EDIT_URL.replace(':id', id);

            Ajax.request({
                url: url,
                method: 'GET',
                success(response) {
                    const data = response.data;
                    self.resetForm();

                    $('#fee_head_id').val(data.id);
                    $('#name').val(data.name);
                    $('#code').val(data.code);
                    $('#description').val(data.description);
                    $('#is_active').val(data.is_active ? 1 : 0);

                    $('#feeHeadModalTitle').text('Edit Fee Head');
                    self.modal.show();
                }
            });
        });

        // Delete
        $(document).on('click', '.btn-delete', function () {
            const id = $(this).data('id');
            const url = FEE_HEAD_DELETE_URL.replace(':id', id);

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to delete this Fee Head?',
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
            const url = FEE_HEAD_STATUS_URL.replace(':id', id);

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

        $('#filter_status').on('change', function () {
            self.table.ajax.reload();
        });
    },

    resetForm() {
        const form = $('#feeHeadForm');
        form[0].reset();
        $('#fee_head_id').val('');
        $('#is_active').val('1');
        Helper.clearErrors(form);
    }
};

$(function () {
    FeeHead.init();
});
