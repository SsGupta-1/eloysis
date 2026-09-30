const GalleryManager = {

    modal: null,
    table: null,

    init() {
        this.modal = new bootstrap.Modal(
            document.getElementById('galleryModal')
        );

        this.initDataTable();
        this.bindEvents();
    },

    initDataTable() {
        this.table = $('#galleryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: GALLERY_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.category = $('#filter_category').val();
                    d.filter_status = $('#filter_status').val();
                },
                error: function (xhr) {
                    if (typeof Toast !== 'undefined' && Toast.error) {
                        Toast.error(xhr.responseJSON?.message ?? 'Unable to load gallery images.');
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
                        return `<img src="${row.image_url}" class="rounded border" style="width: 70px; height: 50px; object-fit: cover;">`;
                    }
                },
                {
                    data: 'title',
                    name: 'title',
                    render: function (data, type, row) {
                        return row.title ? `<strong>${row.title}</strong>` : '<span class="text-muted">(No caption)</span>';
                    }
                },
                {
                    data: 'category',
                    name: 'category',
                    render: function (data, type, row) {
                        return `<span class="badge bg-secondary-subtle text-secondary border">${row.category || 'General'}</span>`;
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

        $('#btnAddGallery').on('click', () => {
            this.openCreate();
        });

        $('#galleryForm').on('submit', (e) => {
            e.preventDefault();
            if ($('#gallery_id').val() === '') {
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

        $('#filter_category, #filter_status').on('change', () => {
            this.table.ajax.reload();
        });
    },

    openCreate() {
        $('#galleryForm')[0].reset();
        Helper.clearErrors('#galleryForm');
        $('#gallery_id').val('');
        $('#galleryImagePreviewContainer').addClass('d-none');
        $('#galleryModalTitle').text('Add Gallery Photo');
        $('#btnSaveGallery').html('<i class="bi bi-check-lg"></i> Save Image');
        this.modal.show();
    },

    store() {
        const formData = new FormData($('#galleryForm')[0]);

        Ajax.request({
            form: '#galleryForm',
            url: GALLERY_STORE_URL,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                this.modal.hide();
                $('#galleryForm')[0].reset();
                this.table.ajax.reload(null, false);
            }
        });
    },

    edit(id) {
        Ajax.request({
            url: GALLERY_EDIT_URL.replace(':id', id),
            method: 'GET',
            success: (response) => {
                const gallery = response.data;

                Helper.clearErrors('#galleryForm');
                $('#gallery_id').val(gallery.id);
                $('#title').val(gallery.title);
                $('#category').val(gallery.category);
                $('#sort_order').val(gallery.sort_order);

                if (gallery.image_url) {
                    $('#galleryImagePreview').attr('src', gallery.image_url);
                    $('#galleryImagePreviewContainer').removeClass('d-none');
                } else {
                    $('#galleryImagePreviewContainer').addClass('d-none');
                }

                $('#galleryModalTitle').text('Edit Gallery Photo');
                $('#btnSaveGallery').html('<i class="bi bi-check-lg"></i> Update Image');

                this.modal.show();
            }
        });
    },

    update() {
        const id = $('#gallery_id').val();
        const formData = new FormData($('#galleryForm')[0]);
        formData.append('_method', 'PUT');

        Ajax.request({
            form: '#galleryForm',
            url: GALLERY_UPDATE_URL.replace(':id', id),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                this.modal.hide();
                $('#galleryForm')[0].reset();
                this.table.ajax.reload(null, false);
            }
        });
    },

    destroy(id) {
        Swal.fire({
            title: 'Delete Photo?',
            text: 'This photo will be removed from the gallery.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) return;

            let formData = new FormData();
            formData.append('_method', 'DELETE');

            Ajax.request({
                url: GALLERY_DELETE_URL.replace(':id', id),
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
            url: GALLERY_STATUS_URL.replace(':id', id),
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
    GalleryManager.init();
});
