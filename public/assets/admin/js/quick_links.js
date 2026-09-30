const QuickLinkModule = {

    modal: null,
    table: null,

    init() {
        const modalEl = document.getElementById('quickLinkModal');
        if (modalEl) {
            this.modal = new bootstrap.Modal(modalEl);
        }

        this.initDataTable();
        this.bindEvents();
    },

    initDataTable() {
        this.table = $('#quickLinkTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: QUICK_LINK_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.color = $('#filter_color').val();
                    d.filter_status = $('#filter_status').val();
                },
                error: function (xhr) {
                    if (typeof Toast !== 'undefined' && Toast.error) {
                        Toast.error(xhr.responseJSON?.message ?? 'Unable to load quick links.');
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
                    data: 'icon',
                    orderable: false,
                    render: function (data, type, row) {
                        let iconClass = row.icon || 'bi bi-link-45deg';
                        if (!iconClass.startsWith('bi ')) {
                            iconClass = 'bi ' + iconClass;
                        }
                        return `<span class="badge bg-${row.color || 'primary'} p-2 rounded-circle"><i class="${iconClass} fs-6 text-white"></i></span>`;
                    }
                },
                {
                    data: 'title',
                    name: 'title',
                    render: function (data, type, row) {
                        return `<strong>${row.title}</strong>${row.description ? `<br><small class="text-muted">${row.description}</small>` : ''}`;
                    }
                },
                {
                    data: 'url',
                    name: 'url',
                    render: function (data, type, row) {
                        return row.url ? `<a href="${row.url}" target="_blank" class="text-decoration-none"><small>${row.url}</small> <i class="bi bi-box-arrow-up-right fs-xs"></i></a>` : '-';
                    }
                },
                {
                    data: 'color',
                    name: 'color',
                    render: function (data, type, row) {
                        return `<span class="badge bg-${row.color || 'primary'} text-white text-capitalize">${row.color || 'primary'}</span>`;
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

        $('#btnAddQuickLink').on('click', () => {
            this.openCreate();
        });

        $('#quickLinkForm').on('submit', (e) => {
            e.preventDefault();
            if ($('#quick_link_id').val() === '') {
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

        $('#filter_color, #filter_status').on('change', () => {
            this.table.ajax.reload();
        });
    },

    openCreate() {
        $('#quickLinkForm')[0].reset();
        Helper.clearErrors('#quickLinkForm');
        $('#quick_link_id').val('');
        $('#icon').val('bi bi-mortarboard-fill');
        $('#color').val('primary');
        $('#sort_order').val('0');
        $('#quickLinkModalTitle').text('Add Quick Link');
        $('#btnSaveQuickLink').html('<i class="bi bi-check-lg"></i> Save Quick Link');
        this.modal.show();
    },

    formatUrl(template, id) {
        if (!template) return '';
        return template.replace(':id', id).replace('%3Aid', id).replace('__ID__', id);
    },

    store() {
        const formData = new FormData($('#quickLinkForm')[0]);

        Ajax.request({
            form: '#quickLinkForm',
            url: QUICK_LINK_STORE_URL,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                this.modal.hide();
                $('#quickLinkForm')[0].reset();
                this.table.ajax.reload(null, false);
            }
        });
    },

    edit(id) {
        Ajax.request({
            url: this.formatUrl(QUICK_LINK_EDIT_URL, id),
            method: 'GET',
            success: (response) => {
                const link = response.data;

                Helper.clearErrors('#quickLinkForm');
                $('#quick_link_id').val(link.id);
                $('#title').val(link.title);
                $('#description').val(link.description);
                $('#icon').val(link.icon || 'bi bi-mortarboard-fill');
                $('#url').val(link.url);
                $('#color').val(link.color || 'primary');
                $('#sort_order').val(link.sort_order);

                $('#quickLinkModalTitle').text('Edit Quick Link');
                $('#btnSaveQuickLink').html('<i class="bi bi-check-lg"></i> Update Quick Link');

                this.modal.show();
            }
        });
    },

    update() {
        const id = $('#quick_link_id').val();
        const formData = new FormData($('#quickLinkForm')[0]);
        formData.append('_method', 'PUT');

        Ajax.request({
            form: '#quickLinkForm',
            url: this.formatUrl(QUICK_LINK_UPDATE_URL, id),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                this.modal.hide();
                $('#quickLinkForm')[0].reset();
                this.table.ajax.reload(null, false);
            }
        });
    },

    destroy(id) {
        Swal.fire({
            title: 'Delete Quick Link?',
            text: 'This quick link card will be removed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) return;

            let formData = new FormData();
            formData.append('_method', 'DELETE');

            Ajax.request({
                url: this.formatUrl(QUICK_LINK_DELETE_URL, id),
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
            url: this.formatUrl(QUICK_LINK_STATUS_URL, id),
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
    QuickLinkModule.init();
});
