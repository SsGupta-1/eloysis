const HomeSlider = {

    modal: null,
    table: null,

    init() {
        this.modal = new bootstrap.Modal(
            document.getElementById('sliderModal')
        );

        this.initDataTable();
        this.bindEvents();
    },

    initDataTable() {
        this.table = $('#sliderTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: SLIDER_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.filter_status = $('#filter_status').val();
                },
                error: function (xhr) {
                    if (typeof Toast !== 'undefined' && Toast.error) {
                        Toast.error(xhr.responseJSON?.message ?? 'Unable to load sliders.');
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
                    data: 'image',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `<img src="${row.image_url}" class="rounded" style="width: 70px; height: 45px; object-fit: cover;">`;
                    }
                },
                {
                    data: 'title',
                    name: 'title',
                    render: function (data, type, row) {
                        return `<strong>${row.title}</strong><br><small class="text-muted">${row.subtitle || '-'}</small>`;
                    }
                },
                {
                    data: 'button_text',
                    orderable: false,
                    render: function (data, type, row) {
                        return row.button_text ? `<span class="badge bg-light text-dark border">${row.button_text}</span>` : '-';
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

        $('#btnAddSlider').on('click', () => {
            this.openCreate();
        });

        $('#sliderForm').on('submit', (e) => {
            e.preventDefault();
            if ($('#slider_id').val() === '') {
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
        $('#sliderForm')[0].reset();
        Helper.clearErrors('#sliderForm');
        $('#slider_id').val('');
        $('#imagePreviewContainer').addClass('d-none');
        $('#sliderModalTitle').text('Add Home Slider');
        $('#btnSaveSlider').html('<i class="bi bi-check-lg"></i> Save Slider');
        this.modal.show();
    },

    store() {
        const formData = new FormData($('#sliderForm')[0]);

        Ajax.request({
            form: '#sliderForm',
            url: SLIDER_STORE_URL,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                this.modal.hide();
                $('#sliderForm')[0].reset();
                this.table.ajax.reload(null, false);
            }
        });
    },

    edit(id) {
        Ajax.request({
            url: SLIDER_EDIT_URL.replace(':id', id),
            method: 'GET',
            success: (response) => {
                const slider = response.data;

                Helper.clearErrors('#sliderForm');
                $('#slider_id').val(slider.id);
                $('#title').val(slider.title);
                $('#subtitle').val(slider.subtitle);
                $('#button_text').val(slider.button_text);
                $('#button_url').val(slider.button_url);
                $('#sort_order').val(slider.sort_order);

                if (slider.image_url) {
                    $('#imagePreview').attr('src', slider.image_url);
                    $('#imagePreviewContainer').removeClass('d-none');
                } else {
                    $('#imagePreviewContainer').addClass('d-none');
                }

                $('#sliderModalTitle').text('Edit Home Slider');
                $('#btnSaveSlider').html('<i class="bi bi-check-lg"></i> Update Slider');

                this.modal.show();
            }
        });
    },

    update() {
        const id = $('#slider_id').val();
        const formData = new FormData($('#sliderForm')[0]);
        formData.append('_method', 'PUT');

        Ajax.request({
            form: '#sliderForm',
            url: SLIDER_UPDATE_URL.replace(':id', id),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                this.modal.hide();
                $('#sliderForm')[0].reset();
                this.table.ajax.reload(null, false);
            }
        });
    },

    destroy(id) {
        Swal.fire({
            title: 'Delete Slider?',
            text: 'This slider banner will be removed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) return;

            let formData = new FormData();
            formData.append('_method', 'DELETE');

            Ajax.request({
                url: SLIDER_DELETE_URL.replace(':id', id),
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
            url: SLIDER_STATUS_URL.replace(':id', id),
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
    HomeSlider.init();
});
