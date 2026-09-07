const ClassSubject = {

    modal: null,
    table: null,

    init() {

        this.modal = new bootstrap.Modal(
            document.getElementById('classSubjectModal')
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

        this.table = $('#classSubjectTable').DataTable({

            processing: true,
            serverSide: true,

            ajax: {
                url: CLASS_SUBJECT_LIST_URL,
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
                    data: 'subject_class.class_name',
                    name: 'class_id',
                    render: function (data, type, row) {
                        return row.subject_class?.class_name ?? '-';
                    }
                },
                {
                    data: 'subject.subject_name',
                    name: 'subject_id',
                    render: function (data, type, row) {
                        return row.subject?.subject_name ?? '-';
                    }
                },
                {
                    data: 'subject.subject_code',
                    name: 'subject_code',
                    orderable: false,
                    render: function (data, type, row) {
                        return row.subject?.subject_code ?? '-';
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: true,
                    searchable: false,
                    render: function (data, type, row) {
                        return Helper.statusSwitch(row.id, row.status);
                    }
                },
                {
                    data: null,
                    name: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                            <button
                                type="button"
                                class="btn btn-sm btn-edit"
                                data-id="${row.id}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button
                                type="button"
                                class="btn btn-sm btn-delete"
                                data-id="${row.id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        `;
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

        // Add Class Subject
        $('#btnAddClassSubject').on('click', () => {
            this.openCreate();
        });

        // Save Form
        $('#classSubjectForm').on('submit', (e) => {
            e.preventDefault();
            if ($('#class_subject_id').val() == '') {
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

        const form = $('#classSubjectForm')[0];
        form.reset();

        $('#class_subject_id').val('');
        $('#classSubjectModalTitle').text('Add Academic Class Subject');
        $('#btnSaveClassSubject').html(
            '<i class="bi bi-check-lg"></i> Save Class Subject'
        );

        Helper.clearErrors(form);
        this.modal.show();

    },

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    store() {

        Ajax.request({
            form: '#classSubjectForm',
            url: CLASS_SUBJECT_STORE_URL,
            method: 'POST',
            success: (response) => {
                this.modal.hide();
                $('#classSubjectForm')[0].reset();
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
            form: '#classSubjectForm',
            url: CLASS_SUBJECT_EDIT_URL.replace(':id', id),
            method: 'GET',
            success: (response) => {
                const classSubject = response.data;

                Helper.clearErrors('#classSubjectForm');
                $('#class_subject_id').val(classSubject.id);
                $('#sub_class_id').val(classSubject.class_id);
                $('#sub_subject_id').val(classSubject.subject_id);
                $('#status').val(classSubject.status == true ? 1 : 0).trigger('change');

                $('#classSubjectModalTitle').text('Edit Academic Class Subject');
                $('#btnSaveClassSubject').html(
                    '<i class="bi bi-check-lg"></i> Update Class Subject'
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
        const id = $('#class_subject_id').val();
        let url = CLASS_SUBJECT_UPDATE_URL.replace(':id', id);

        Ajax.request({
            form: '#classSubjectForm',
            url: url,
            method: 'POST',
            extraData: {
                _method: 'PUT'
            },
            success: (response) => {
                this.modal.hide();
                $('#classSubjectForm')[0].reset();
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
            title: 'Delete Class Subject?',
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
                url: CLASS_SUBJECT_DELETE_URL.replace(':id', id),
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
            url: CLASS_SUBJECT_STATUS_URL.replace(':id', id),
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
    ClassSubject.init();
});