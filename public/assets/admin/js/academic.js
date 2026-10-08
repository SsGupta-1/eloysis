const Academic = {

    modal: null,
    table: null,

    init() {

        this.modal = new bootstrap.Modal(
            document.getElementById('academicModal')
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

        this.table = $('#academicTable').DataTable({

            processing: true,
            serverSide: true,

            ajax: {
                url: ACADEMIC_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.filter_status = $('#filter_status').val();
                },
                error: function (xhr) {
                    Toast.error(
                        xhr.responseJSON?.message ?? 'Unable to load Academic Sessions.'
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
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'start_year',
                    name: 'start_year'
                },
                {
                    data: 'end_year',
                    name: 'end_year'
                },
                {
                    data: 'start_date',
                    name: 'start_date',
                    render: function (data, type, row) {
                        return Helper.formatDate(row.start_date);
                    }
                },
                {
                    data: 'end_date',
                    name: 'end_date',
                    render: function (data, type, row) {
                        return Helper.formatDate(row.end_date);
                    }
                },
                {
                    data: 'is_current',
                    name: 'is_current',
                    orderable: true,
                    searchable: false,
                    render: function (data, type, row) {
                        const canEdit = typeof window.can === 'function' ? window.can('academic_sessions.edit') : true;
                        return Helper.statusSwitch(row.id, row.is_current, canEdit);
                    }
                },
                {
                    data: null,
                    name: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const canEdit = typeof window.can === 'function' ? window.can('academic_sessions.edit') : true;
                        const canDelete = typeof window.can === 'function' ? window.can('academic_sessions.delete') : true;

                        let buttons = '';

                        if (canEdit) {
                            buttons += `
                                <button
                                    type="button"
                                    class="btn btn-sm btn-edit"
                                    data-id="${row.id}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            `;
                        }

                        if (canDelete) {
                            buttons += `
                                <button
                                    type="button"
                                    class="btn btn-sm btn-delete"
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

        // Add Academic Session
        $('#btnAddSession').on('click', () => {
            this.openCreate();
        });

        // Save Form
        $('#academicForm').on('submit', (e) => {
            e.preventDefault();
            if ($('#academic_id').val() == '') {
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

        // Change Status
        $(document).on('change', '.btn-status', (e) => {
            this.changeStatus(e.currentTarget);
        });

        // Status Filter
        $('#filter_status').on('change', () => {
            this.table.ajax.reload();
        });

        $(document).on('input', '#start_year, #end_year', function () {
            $(this).val($(this).val().replace(/\D/g, '').slice(0, 4));
        });

        $(document).on('change', '#end_year', function () {
            let start = parseInt($('#start_year').val());
            let end = parseInt($(this).val());

            if (end < start) {
                $(this).val('');
                Toast.error(
                    'End year cannot be less than start year.'
                );
            }
        });

    },

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    openCreate() {

        $('#academicForm')[0].reset();
        Helper.clearErrors('#academicForm');
        $('#academic_id').val('');
        $('#academicModalTitle').text('Add Academic Session');
        $('#btnSaveAcademic').html(
            '<i class="bi bi-check-lg"></i> Save Session'
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
            form: '#academicForm',
            url: ACADEMIC_STORE_URL,
            method: 'POST',
            success: (response) => {
                this.modal.hide();
                $('#academicForm')[0].reset();
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
            form: '#academicForm',
            url: ACADEMIC_EDIT_URL.replace(':id', id),
            method: 'GET',
            success: (response) => {
                const academic = response.data;

                Helper.clearErrors('#academicForm');

                $('#academic_id').val(academic.id);
                $('#session_name').val(academic.name);
                $('#start_year').val(academic.start_year);
                $('#end_year').val(academic.end_year);
                $('#start_date').val(Helper.formatDate(academic.start_date));
                $('#end_date').val(Helper.formatDate(academic.end_date));

                $('#academicModalTitle').text('Edit Academic Session');
                $('#btnSaveAcademic').html(
                    '<i class="bi bi-check-lg"></i> Update Session'
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
        const id = $('#academic_id').val();
        let url = ACADEMIC_UPDATE_URL.replace(':id', id);

        Ajax.request({
            form: '#academicForm',
            url: url,
            method: 'POST',
            extraData: {
                _method: 'PUT'
            },
            success: (response) => {
                this.modal.hide();
                $('#academicForm')[0].reset();
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
            title: 'Delete Academic Session?',
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
                url: ACADEMIC_DELETE_URL.replace(':id', id),
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
            url: ACADEMIC_STATUS_URL.replace(':id', id),
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
    Academic.init();
});