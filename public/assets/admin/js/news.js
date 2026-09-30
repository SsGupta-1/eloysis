const News = {

    modal: null,
    table: null,

    init() {
        this.modal = new bootstrap.Modal(
            document.getElementById('newsModal')
        );

        this.initDataTable();
        this.bindEvents();
    },

    initDataTable() {
        this.table = $('#newsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: NEWS_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.filter_status = $('#filter_status').val();
                },
                error: function (xhr) {
                    if (typeof Toast !== 'undefined' && Toast.error) {
                        Toast.error(xhr.responseJSON?.message ?? 'Unable to load news.');
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
                        return `<img src="${row.image_url}" class="rounded" style="width: 60px; height: 45px; object-fit: cover;">`;
                    }
                },
                {
                    data: 'title',
                    name: 'title',
                    render: function (data, type, row) {
                        return `<strong>${row.title}</strong><br><small class="text-muted">${row.summary || '-'}</small>`;
                    }
                },
                {
                    data: 'published_date',
                    name: 'published_date',
                    render: function (data, type, row) {
                        return row.published_date ? new Date(row.published_date).toLocaleDateString('en-GB') : '-';
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

        $('#btnAddNews').on('click', () => {
            this.openCreate();
        });

        $('#newsForm').on('submit', (e) => {
            e.preventDefault();
            if ($('#news_id').val() === '') {
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
        $('#newsForm')[0].reset();
        Helper.clearErrors('#newsForm');
        $('#news_id').val('');
        $('#newsImagePreviewContainer').addClass('d-none');
        $('#newsModalTitle').text('Add News / Notice');
        $('#btnSaveNews').html('<i class="bi bi-check-lg"></i> Save News');
        this.modal.show();
    },

    store() {
        const formData = new FormData($('#newsForm')[0]);

        Ajax.request({
            form: '#newsForm',
            url: NEWS_STORE_URL,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                this.modal.hide();
                $('#newsForm')[0].reset();
                this.table.ajax.reload(null, false);
            }
        });
    },

    edit(id) {
        Ajax.request({
            url: NEWS_EDIT_URL.replace(':id', id),
            method: 'GET',
            success: (response) => {
                const news = response.data;

                Helper.clearErrors('#newsForm');
                $('#news_id').val(news.id);
                $('#title').val(news.title);
                $('#published_date').val(news.published_date);
                $('#summary').val(news.summary);
                $('#content').val(news.content);
                $('#url').val(news.url);

                if (news.image_url) {
                    $('#newsImagePreview').attr('src', news.image_url);
                    $('#newsImagePreviewContainer').removeClass('d-none');
                } else {
                    $('#newsImagePreviewContainer').addClass('d-none');
                }

                $('#newsModalTitle').text('Edit News / Notice');
                $('#btnSaveNews').html('<i class="bi bi-check-lg"></i> Update News');

                this.modal.show();
            }
        });
    },

    update() {
        const id = $('#news_id').val();
        const formData = new FormData($('#newsForm')[0]);
        formData.append('_method', 'PUT');

        Ajax.request({
            form: '#newsForm',
            url: NEWS_UPDATE_URL.replace(':id', id),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                this.modal.hide();
                $('#newsForm')[0].reset();
                this.table.ajax.reload(null, false);
            }
        });
    },

    destroy(id) {
        Swal.fire({
            title: 'Delete News?',
            text: 'This news entry will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) return;

            let formData = new FormData();
            formData.append('_method', 'DELETE');

            Ajax.request({
                url: NEWS_DELETE_URL.replace(':id', id),
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
            url: NEWS_STATUS_URL.replace(':id', id),
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
    News.init();
});
