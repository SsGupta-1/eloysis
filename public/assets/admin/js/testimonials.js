const TestimonialModule = {

    modal: null,
    table: null,

    init() {
        const modalEl = document.getElementById('testimonialModal');
        if (modalEl) {
            this.modal = new bootstrap.Modal(modalEl);
        }

        this.initDataTable();
        this.bindEvents();
    },

    initDataTable() {
        this.table = $('#testimonialTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: TESTIMONIAL_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.rating = $('#filter_rating').val();
                    d.filter_status = $('#filter_status').val();
                },
                error: function (xhr) {
                    if (typeof Toast !== 'undefined' && Toast.error) {
                        Toast.error(xhr.responseJSON?.message ?? 'Unable to load testimonials.');
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
                        return `<img src="${row.image_url}" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">`;
                    }
                },
                {
                    data: 'name',
                    name: 'name',
                    render: function (data, type, row) {
                        return `<strong>${row.name}</strong><br><small class="text-muted">${row.role || '-'}</small>`;
                    }
                },
                {
                    data: 'message',
                    name: 'message',
                    render: function (data, type, row) {
                        let text = row.message ? (row.message.length > 90 ? row.message.substring(0, 90) + '...' : row.message) : '';
                        return `<span class="fst-italic">"${text}"</span>`;
                    }
                },
                {
                    data: 'rating',
                    name: 'rating',
                    render: function (data, type, row) {
                        let stars = '';
                        let r = parseInt(row.rating) || 5;
                        for (let i = 1; i <= 5; i++) {
                            if (i <= r) {
                                stars += '<i class="bi bi-star-fill text-warning"></i>';
                            } else {
                                stars += '<i class="bi bi-star text-muted"></i>';
                            }
                        }
                        return `<div>${stars}</div><small class="text-muted">${r}/5</small>`;
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

        $('#btnAddTestimonial').on('click', () => {
            this.openCreate();
        });

        $('#testimonialForm').on('submit', (e) => {
            e.preventDefault();
            if ($('#testimonial_id').val() === '') {
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

        $('#filter_rating, #filter_status').on('change', () => {
            this.table.ajax.reload();
        });
    },

    openCreate() {
        $('#testimonialForm')[0].reset();
        Helper.clearErrors('#testimonialForm');
        $('#testimonial_id').val('');
        $('#rating').val('5');
        $('#sort_order').val('0');
        $('#testimonialImagePreviewContainer').addClass('d-none');
        $('#testimonialModalTitle').text('Add Testimonial');
        $('#btnSaveTestimonial').html('<i class="bi bi-check-lg"></i> Save Testimonial');
        this.modal.show();
    },

    formatUrl(template, id) {
        if (!template) return '';
        return template.replace(':id', id).replace('%3Aid', id).replace('__ID__', id);
    },

    store() {
        const formData = new FormData($('#testimonialForm')[0]);

        Ajax.request({
            form: '#testimonialForm',
            url: TESTIMONIAL_STORE_URL,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                this.modal.hide();
                $('#testimonialForm')[0].reset();
                this.table.ajax.reload(null, false);
            }
        });
    },

    edit(id) {
        Ajax.request({
            url: this.formatUrl(TESTIMONIAL_EDIT_URL, id),
            method: 'GET',
            success: (response) => {
                const item = response.data;

                Helper.clearErrors('#testimonialForm');
                $('#testimonial_id').val(item.id);
                $('#name').val(item.name);
                $('#role').val(item.role);
                $('#message').val(item.message);
                $('#rating').val(item.rating || 5);
                $('#sort_order').val(item.sort_order);

                if (item.image_url) {
                    $('#testimonialImagePreview').attr('src', item.image_url);
                    $('#testimonialImagePreviewContainer').removeClass('d-none');
                } else {
                    $('#testimonialImagePreviewContainer').addClass('d-none');
                }

                $('#testimonialModalTitle').text('Edit Testimonial');
                $('#btnSaveTestimonial').html('<i class="bi bi-check-lg"></i> Update Testimonial');

                this.modal.show();
            }
        });
    },

    update() {
        const id = $('#testimonial_id').val();
        const formData = new FormData($('#testimonialForm')[0]);
        formData.append('_method', 'PUT');

        Ajax.request({
            form: '#testimonialForm',
            url: this.formatUrl(TESTIMONIAL_UPDATE_URL, id),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                this.modal.hide();
                $('#testimonialForm')[0].reset();
                this.table.ajax.reload(null, false);
            }
        });
    },

    destroy(id) {
        Swal.fire({
            title: 'Delete Testimonial?',
            text: 'This feedback review will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) return;

            let formData = new FormData();
            formData.append('_method', 'DELETE');

            Ajax.request({
                url: this.formatUrl(TESTIMONIAL_DELETE_URL, id),
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
            url: this.formatUrl(TESTIMONIAL_STATUS_URL, id),
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
    TestimonialModule.init();
});
