const ContactMessage = {

    modal: null,
    table: null,
    activeId: null,

    init() {
        this.modal = new bootstrap.Modal(
            document.getElementById('messageModal')
        );

        this.initDataTable();
        this.bindEvents();
    },

    initDataTable() {
        this.table = $('#messageTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: MESSAGE_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.status = $('#filter_status').val();
                },
                error: function (xhr) {
                    if (typeof Toast !== 'undefined' && Toast.error) {
                        Toast.error(xhr.responseJSON?.message ?? 'Unable to load contact messages.');
                    }
                }
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
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
                        return `<strong>${row.name}</strong>`;
                    }
                },
                {
                    data: 'email',
                    name: 'email',
                    render: function (data, type, row) {
                        return `<div><i class="bi bi-envelope"></i> ${row.email}</div><small class="text-muted"><i class="bi bi-telephone"></i> ${row.phone || '-'}</small>`;
                    }
                },
                {
                    data: 'subject',
                    name: 'subject',
                    render: function (data, type, row) {
                        const snippet = (row.message && row.message.length > 80) ? row.message.substring(0, 80) + '...' : (row.message || '');
                        return `<strong class="text-primary">${row.subject}</strong><br><small class="text-muted">${snippet}</small>`;
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data, type, row) {
                        const badges = {
                            'pending': '<span class="badge bg-warning text-dark">Pending</span>',
                            'read': '<span class="badge bg-info text-dark">Read</span>',
                            'replied': '<span class="badge bg-success">Replied</span>'
                        };
                        return badges[row.status] || `<span class="badge bg-secondary">${row.status}</span>`;
                    }
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function (data, type, row) {
                        return row.created_at ? new Date(row.created_at).toLocaleDateString('en-GB') : '-';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                            <button type="button" class="btn btn-sm btn-view" data-id="${row.id}" title="View Message">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-delete" data-id="${row.id}" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        `;
                    }
                }
            ]
        });
    },

    bindEvents() {
        $('#filterForm').on('submit', (e) => {
            e.preventDefault();
            this.table.ajax.reload();
        });

        $('#btnReset').on('click', () => {
            $('#filterForm')[0].reset();
            this.table.search('').ajax.reload();
        });

        $(document).on('click', '.btn-view', (e) => {
            this.show($(e.currentTarget).data('id'));
        });

        $(document).on('click', '.btn-delete', (e) => {
            this.destroy($(e.currentTarget).data('id'));
        });

        $('#btnUpdateMsgStatus').on('click', () => {
            this.updateStatus();
        });

        $('#filter_status').on('change', () => {
            this.table.ajax.reload();
        });
    },

    show(id) {
        this.activeId = id;
        Ajax.request({
            url: MESSAGE_SHOW_URL.replace(':id', id),
            method: 'GET',
            success: (response) => {
                const msg = response.data;
                $('#msg_name').text(msg.name);
                $('#msg_email').html(`<a href="mailto:${msg.email}">${msg.email}</a>`);
                $('#msg_phone').html(msg.phone ? `<a href="tel:${msg.phone}">${msg.phone}</a>` : '-');
                $('#msg_created_at').text(msg.created_at ? new Date(msg.created_at).toLocaleString('en-GB') : '-');
                $('#msg_subject').text(msg.subject);
                $('#msg_body').text(msg.message);
                $('#msg_status_select').val(msg.status);

                this.modal.show();
                this.table.ajax.reload(null, false);
            }
        });
    },

    updateStatus() {
        if (!this.activeId) return;

        const newStatus = $('#msg_status_select').val();
        let formData = new FormData();
        formData.append('_method', 'PATCH');
        formData.append('status', newStatus);

        Ajax.request({
            url: MESSAGE_STATUS_URL.replace(':id', this.activeId),
            method: 'POST',
            data: formData,
            success: () => {
                if (typeof Toast !== 'undefined' && Toast.success) {
                    Toast.success('Status updated successfully.');
                }
                this.modal.hide();
                this.table.ajax.reload(null, false);
            }
        });
    },

    destroy(id) {
        Swal.fire({
            title: 'Delete Message?',
            text: 'This contact message will be permanently removed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) return;

            let formData = new FormData();
            formData.append('_method', 'DELETE');

            Ajax.request({
                url: MESSAGE_DELETE_URL.replace(':id', id),
                method: 'POST',
                data: formData,
                success: () => {
                    this.table.ajax.reload(null, false);
                }
            });
        });
    }

};

$(function () {
    ContactMessage.init();
});
