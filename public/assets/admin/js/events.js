const EventManager = {

    modal: null,
    table: null,

    init() {
        this.modal = new bootstrap.Modal(
            document.getElementById('eventModal')
        );

        this.initDataTable();
        this.bindEvents();
    },

    initDataTable() {
        this.table = $('#eventTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: EVENT_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.filter_status = $('#filter_status').val();
                },
                error: function (xhr) {
                    if (typeof Toast !== 'undefined' && Toast.error) {
                        Toast.error(xhr.responseJSON?.message ?? 'Unable to load events.');
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
                        return `<strong>${row.title}</strong><br><small class="text-muted">${row.description || '-'}</small>`;
                    }
                },
                {
                    data: 'event_date',
                    name: 'event_date',
                    render: function (data, type, row) {
                        return row.event_date ? new Date(row.event_date).toLocaleDateString('en-GB') : '-';
                    }
                },
                {
                    data: 'location',
                    name: 'location',
                    render: function (data, type, row) {
                        return `<div><i class="bi bi-clock"></i> ${row.event_time || '-'}</div><small class="text-muted"><i class="bi bi-geo-alt"></i> ${row.location || '-'}</small>`;
                    }
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

        $('#btnAddEvent').on('click', () => {
            this.openCreate();
        });

        $('#eventForm').on('submit', (e) => {
            e.preventDefault();
            if ($('#event_id').val() === '') {
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

        $('#filter_status').on('change', () => {
            this.table.ajax.reload();
        });
    },

    openCreate() {
        $('#eventForm')[0].reset();
        Helper.clearErrors('#eventForm');
        $('#event_id').val('');
        $('#eventImagePreviewContainer').addClass('d-none');
        $('#eventModalTitle').text('Add Event');
        $('#btnSaveEvent').html('<i class="bi bi-check-lg"></i> Save Event');
        this.modal.show();
    },

    store() {
        const formData = new FormData($('#eventForm')[0]);

        Ajax.request({
            form: '#eventForm',
            url: EVENT_STORE_URL,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                this.modal.hide();
                $('#eventForm')[0].reset();
                this.table.ajax.reload(null, false);
            }
        });
    },

    edit(id) {
        Ajax.request({
            url: EVENT_EDIT_URL.replace(':id', id),
            method: 'GET',
            success: (response) => {
                const event = response.data;

                Helper.clearErrors('#eventForm');
                $('#event_id').val(event.id);
                $('#title').val(event.title);
                $('#event_date').val(event.event_date);
                $('#event_time').val(event.event_time);
                $('#location').val(event.location);
                $('#description').val(event.description);
                $('#url').val(event.url);

                if (event.image_url) {
                    $('#eventImagePreview').attr('src', event.image_url);
                    $('#eventImagePreviewContainer').removeClass('d-none');
                } else {
                    $('#eventImagePreviewContainer').addClass('d-none');
                }

                $('#eventModalTitle').text('Edit Event');
                $('#btnSaveEvent').html('<i class="bi bi-check-lg"></i> Update Event');

                this.modal.show();
            }
        });
    },

    update() {
        const id = $('#event_id').val();
        const formData = new FormData($('#eventForm')[0]);
        formData.append('_method', 'PUT');

        Ajax.request({
            form: '#eventForm',
            url: EVENT_UPDATE_URL.replace(':id', id),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                this.modal.hide();
                $('#eventForm')[0].reset();
                this.table.ajax.reload(null, false);
            }
        });
    },

    destroy(id) {
        Swal.fire({
            title: 'Delete Event?',
            text: 'This event will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) return;

            let formData = new FormData();
            formData.append('_method', 'DELETE');

            Ajax.request({
                url: EVENT_DELETE_URL.replace(':id', id),
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
            url: EVENT_STATUS_URL.replace(':id', id),
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
    EventManager.init();
});
