const ImportantMessageModule = {

    modal: null,
    table: null,

    init() {
        const modalEl = document.getElementById('messageModal');
        if (modalEl) {
            this.modal = new bootstrap.Modal(modalEl);
        }

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
                    d.type = $('#filter_type').val();
                    d.filter_status = $('#filter_status').val();
                },
                error: function (xhr) {
                    if (typeof Toast !== 'undefined' && Toast.error) {
                        Toast.error(xhr.responseJSON?.message ?? 'Unable to load messages.');
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
                    data: 'title',
                    name: 'title',
                    render: function (data, type, row) {
                        let text = row.message ? (row.message.length > 80 ? row.message.substring(0, 80) + '...' : row.message) : '';
                        return `<strong>${row.title}</strong><br><small class="text-muted">${text}</small>`;
                    }
                },
                {
                    data: 'type',
                    name: 'type',
                    render: function (data, type, row) {
                        const badgeClasses = {
                            info: 'bg-info text-dark',
                            warning: 'bg-warning text-dark',
                            danger: 'bg-danger text-white',
                            success: 'bg-success text-white'
                        };
                        const cls = badgeClasses[row.type] || 'bg-secondary text-white';
                        return `<span class="badge ${cls} text-capitalize">${row.type}</span>`;
                    }
                },
                {
                    data: 'action_text',
                    orderable: false,
                    render: function (data, type, row) {
                        if (!row.action_text) return '-';
                        return `<a href="${row.action_url || '#'}" target="_blank" class="badge bg-light text-primary border">${row.action_text}</a>`;
                    }
                },
                {
                    data: 'start_date',
                    name: 'start_date',
                    render: function (data, type, row) {
                        let start = row.start_date ? row.start_date.substring(0, 10) : 'Anytime';
                        let end = row.end_date ? row.end_date.substring(0, 10) : 'Ongoing';
                        return `<small class="text-muted"><i class="bi bi-clock me-1"></i>${start} &rarr; ${end}</small>`;
                    }
                },
                {
                    data: 'sort_order',
                    name: 'sort_order'
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data, type, row) {
                        return Helper.statusSwitch(row.id, row.status);
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                            <button type="button" class="btn btn-sm btn-edit" data-id="${row.id}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-delete" data-id="${row.id}">
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

        $('#btnAddMessage').on('click', () => {
            this.openCreate();
        });

        $('#messageForm').on('submit', (e) => {
            e.preventDefault();
            if ($('#message_id').val() === '') {
                this.store();
            } else {
                this.update();
            }
        });

        $(document).on('click', '.btn-edit', (e) => {
            this.edit($(e.currentTarget).data('id'));
        });

        $(document).on('click', '.btn-delete', (e) => {
            this.destroy($(e.currentTarget).data('id'));
        });

        $(document).on('change', '.btn-status', (e) => {
            this.changeStatus(e.currentTarget);
        });

        $('#filter_type, #filter_status').on('change', () => {
            this.table.ajax.reload();
        });
    },

    openCreate() {
        $('#messageForm')[0].reset();
        Helper.clearErrors('#messageForm');
        $('#message_id').val('');
        $('#type').val('info');
        $('#sort_order').val('0');
        $('#messageModalTitle').text('Add Important Message');
        $('#btnSaveMessage').html('<i class="bi bi-check-lg"></i> Save Message');
        this.modal.show();
    },

    formatUrl(template, id) {
        if (!template) return '';
        return template.replace(':id', id).replace('%3Aid', id).replace('__ID__', id);
    },

    store() {
        const formData = new FormData($('#messageForm')[0]);

        Ajax.request({
            form: '#messageForm',
            url: MESSAGE_STORE_URL,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                this.modal.hide();
                $('#messageForm')[0].reset();
                this.table.ajax.reload(null, false);
            }
        });
    },

    edit(id) {
        Ajax.request({
            url: this.formatUrl(MESSAGE_EDIT_URL, id),
            method: 'GET',
            success: (response) => {
                const msg = response.data;

                Helper.clearErrors('#messageForm');
                $('#message_id').val(msg.id);
                $('#title').val(msg.title);
                $('#message').val(msg.message);
                $('#type').val(msg.type || 'info');
                $('#action_text').val(msg.action_text);
                $('#action_url').val(msg.action_url);
                $('#start_date').val(msg.start_date || '');
                $('#end_date').val(msg.end_date || '');
                $('#sort_order').val(msg.sort_order);

                $('#messageModalTitle').text('Edit Important Message');
                $('#btnSaveMessage').html('<i class="bi bi-check-lg"></i> Update Message');

                this.modal.show();
            }
        });
    },

    update() {
        const id = $('#message_id').val();
        const formData = new FormData($('#messageForm')[0]);
        formData.append('_method', 'PUT');

        Ajax.request({
            form: '#messageForm',
            url: this.formatUrl(MESSAGE_UPDATE_URL, id),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                this.modal.hide();
                $('#messageForm')[0].reset();
                this.table.ajax.reload(null, false);
            }
        });
    },

    destroy(id) {
        Swal.fire({
            title: 'Delete Message?',
            text: 'This notice will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) return;

            let formData = new FormData();
            formData.append('_method', 'DELETE');

            Ajax.request({
                url: this.formatUrl(MESSAGE_DELETE_URL, id),
                method: 'POST',
                data: formData,
                success: () => {
                    this.table.ajax.reload(null, false);
                }
            });
        });
    },

    changeStatus(element) {
        const id = $(element).data('id');
        let formData = new FormData();
        formData.append('_method', 'PATCH');

        Ajax.request({
            url: this.formatUrl(MESSAGE_STATUS_URL, id),
            method: 'POST',
            data: formData,
            success: () => {
                this.table.ajax.reload(null, false);
            },
            error: () => {
                element.checked = !element.checked;
            }
        });
    }

};

$(function () {
    ImportantMessageModule.init();
});
