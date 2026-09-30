const AnnouncementModule = {

    modal: null,
    table: null,

    init() {
        const modalEl = document.getElementById('announcementModal');
        if (modalEl) {
            this.modal = new bootstrap.Modal(modalEl);
        }

        this.initDataTable();
        this.bindEvents();
    },

    initDataTable() {
        this.table = $('#announcementTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: ANNOUNCEMENT_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.badge = $('#filter_badge').val();
                    d.filter_status = $('#filter_status').val();
                },
                error: function (xhr) {
                    if (typeof Toast !== 'undefined' && Toast.error) {
                        Toast.error(xhr.responseJSON?.message ?? 'Unable to load announcements.');
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
                    data: 'badge',
                    name: 'badge',
                    render: function (data, type, row) {
                        const badge = row.badge || 'Announcement';
                        return `<span class="badge bg-primary text-white">${badge}</span>`;
                    }
                },
                {
                    data: 'title',
                    name: 'title',
                    render: function (data, type, row) {
                        let text = row.content ? (row.content.length > 80 ? row.content.substring(0, 80) + '...' : row.content) : '';
                        return `<strong>${row.title}</strong>${text ? `<br><small class="text-muted">${text}</small>` : ''}`;
                    }
                },
                {
                    data: 'link_text',
                    orderable: false,
                    render: function (data, type, row) {
                        if (!row.link_url) return '-';
                        const text = row.link_text || 'View Link';
                        return `<a href="${row.link_url}" target="_blank" class="badge bg-light text-primary border">${text}</a>`;
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

        $('#btnAddAnnouncement').on('click', () => {
            this.openCreate();
        });

        $('#announcementForm').on('submit', (e) => {
            e.preventDefault();
            if ($('#announcement_id').val() === '') {
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

        $('#filter_badge, #filter_status').on('change', () => {
            this.table.ajax.reload();
        });
    },

    openCreate() {
        $('#announcementForm')[0].reset();
        Helper.clearErrors('#announcementForm');
        $('#announcement_id').val('');
        $('#badge').val('Notice');
        $('#sort_order').val('0');
        $('#announcementModalTitle').text('Add Announcement');
        $('#btnSaveAnnouncement').html('<i class="bi bi-check-lg"></i> Save Announcement');
        this.modal.show();
    },

    formatUrl(template, id) {
        if (!template) return '';
        return template.replace(':id', id).replace('%3Aid', id).replace('__ID__', id);
    },

    store() {
        const formData = new FormData($('#announcementForm')[0]);

        Ajax.request({
            form: '#announcementForm',
            url: ANNOUNCEMENT_STORE_URL,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                this.modal.hide();
                $('#announcementForm')[0].reset();
                this.table.ajax.reload(null, false);
            }
        });
    },

    edit(id) {
        Ajax.request({
            url: this.formatUrl(ANNOUNCEMENT_EDIT_URL, id),
            method: 'GET',
            success: (response) => {
                const item = response.data;

                Helper.clearErrors('#announcementForm');
                $('#announcement_id').val(item.id);
                $('#title').val(item.title);
                $('#content').val(item.content);
                $('#badge').val(item.badge || '');
                $('#link_text').val(item.link_text);
                $('#link_url').val(item.link_url);
                $('#start_date').val(item.start_date || '');
                $('#end_date').val(item.end_date || '');
                $('#sort_order').val(item.sort_order);

                $('#announcementModalTitle').text('Edit Announcement');
                $('#btnSaveAnnouncement').html('<i class="bi bi-check-lg"></i> Update Announcement');

                this.modal.show();
            }
        });
    },

    update() {
        const id = $('#announcement_id').val();
        const formData = new FormData($('#announcementForm')[0]);
        formData.append('_method', 'PUT');

        Ajax.request({
            form: '#announcementForm',
            url: this.formatUrl(ANNOUNCEMENT_UPDATE_URL, id),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                this.modal.hide();
                $('#announcementForm')[0].reset();
                this.table.ajax.reload(null, false);
            }
        });
    },

    destroy(id) {
        Swal.fire({
            title: 'Delete Announcement?',
            text: 'This announcement circular will be permanently removed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) return;

            let formData = new FormData();
            formData.append('_method', 'DELETE');

            Ajax.request({
                url: this.formatUrl(ANNOUNCEMENT_DELETE_URL, id),
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
            url: this.formatUrl(ANNOUNCEMENT_STATUS_URL, id),
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
    AnnouncementModule.init();
});
