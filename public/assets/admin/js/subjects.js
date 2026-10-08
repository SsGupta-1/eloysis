const Subject = {

    modal: null,
    table: null,

    init() {

        this.modal = new bootstrap.Modal(
            document.getElementById('subjectModal')
        );

        this.initDataTable();
        this.bindEvents();

    },

    /*
    |--------------------------------------------------------------------------
    | DataTable
    |--------------------------------------------------------------------------
    */

    initDataTable() {

        this.table = $('#subjectTable').DataTable({

            processing: true,
            serverSide: true,

            ajax: {
                url: SUBJECT_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.filter_status = $('#filter_status').val();
                },
                error: function (xhr) {
                    Toast.error(
                        xhr.responseJSON?.message ?? 'Unable to load Academic Class Subjects.'
                    );
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
                    name: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'subject_name',
                    name: 'subject_name'
                },
                {
                    data: 'subject_code',
                    name: 'subject_code'
                },
                {
                    data: 'description',
                    name: 'description',
                    render: function (data, type, row) {
                        return row.description ?? '-';
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: true,
                    searchable: false,
                    render: function (data, type, row) {
                        const canEdit = typeof window.can === 'function' ? window.can('subjects.edit') : true;
                        return Helper.statusSwitch(row.id, row.status, canEdit);
                    }
                },
                {
                    data: null,
                    name: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const canEdit = typeof window.can === 'function' ? window.can('subjects.edit') : true;
                        const canDelete = typeof window.can === 'function' ? window.can('subjects.delete') : true;

                        let buttons = '';
                        if (canEdit) {
                            buttons += `
                                <button
                                    type="button"
                                    class="btn btn-sm btn-warning btn-edit"
                                    data-id="${row.id}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            `;
                        }
                        if (canDelete) {
                            buttons += `
                                <button
                                    type="button"
                                    class="btn btn-sm btn-danger btn-delete"
                                    data-id="${row.id}">
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

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    bindEvents() {

        // Filter
        $('#filterForm').on('submit', (e) => {
            e.preventDefault();
            this.table.ajax.reload();
        });

        // Reset
        $('#btnReset').on('click', () => {
            $('#filterForm')[0].reset();
            this.table.search('').ajax.reload();
        });

        // Add Subject
        $('#btnAddSubject').on('click', () => {
            this.openCreate();
        });

        // Save Form
        $('#subjectForm').on('submit', (e) => {
            e.preventDefault();
            if ($('#subject_id').val() == '') {
                this.store();
            } else {
                this.update();
            }
        });

        // Edit (Dynamic Button)
        $(document).on('click', '.btn-edit', (e) => {
            this.edit($(e.currentTarget).data('id'));
        });

        // Delete 
        $(document).on('click', '.btn-delete', (e) => {
            this.destroy($(e.currentTarget).data('id'));
        });

        // Change status
        $(document).on('change', '.btn-status', (e) => {
            this.changeStatus(e.currentTarget);
        });

        // Status Filter
        $('#filter_status').on('change', () => {
            this.table.ajax.reload();
        });

    },

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    openCreate() {

        $('#subjectForm')[0].reset();
        Helper.clearErrors('#subjectForm');
        $('#subject_id').val('');
        $('#subjectModalTitle').text('Add Academic Class Subject');
        $('#btnSaveSubject').html(
            '<i class="bi bi-check-lg"></i> Save Subject'
        );
        this.modal.show();

    },

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    store() {

        Ajax.request({
            form: '#subjectForm',
            url: SUBJECT_STORE_URL,
            method: 'POST',
            success: (response) => {
                this.modal.hide();
                $('#subjectForm')[0].reset();
                this.table.ajax.reload(null, false);
            }
        });

    },

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    edit(id) {

        Ajax.request({
            form: '#subjectForm',
            url: SUBJECT_EDIT_URL.replace(':id', id),
            method: 'GET',
            success: (response) => {
                const subject = response.data;

                Helper.clearErrors('#subjectForm');
                $('#subject_id').val(subject.id);
                $('#subject_name').val(subject.subject_name);
                $('#subject_code').val(subject.subject_code);
                $('#description').val(subject.description);

                $('#subjectModalTitle').text('Edit Academic Class Subject');
                $('#btnSaveSubject').html(
                    '<i class="bi bi-check-lg"></i> Update Subject'
                );

                this.modal.show();
            }
        });

    },

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    update() {
        const id = $('#subject_id').val();
        let url = SUBJECT_UPDATE_URL.replace(':id', id);

        Ajax.request({
            form: '#subjectForm',
            url: url,
            method: 'POST',
            extraData: {
                _method: 'PUT'
            },
            success: (response) => {
                this.modal.hide();
                $('#subjectForm')[0].reset();
                this.table.ajax.reload(null, false);
            }
        });
    },

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    destroy(id) {

        Swal.fire({
            title: 'Delete Subject?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            Ajax.request({
                url: SUBJECT_DELETE_URL.replace(':id', id),
                method: 'POST',
                data: (() => {
                    let formData = new FormData();
                    formData.append('_method', 'DELETE');
                    return formData;
                })(),
                success: (response) => {
                    this.table.ajax.reload(null, false);
                }
            });
        });

    },

    /*
    |--------------------------------------------------------------------------
    | Change status
    |--------------------------------------------------------------------------
    */

    changeStatus(element) {

        const id = $(element).data('id');

        Ajax.request({
            url: SUBJECT_STATUS_URL.replace(':id', id),
            method: 'POST',
            data: (() => {
                let formData = new FormData();
                formData.append('_method', 'PATCH');
                return formData;
            })(),
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
    Subject.init();
});