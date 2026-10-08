const Classes = {

    modal: null,
    init() {

        this.modal = new bootstrap.Modal(
            document.getElementById('classModal')
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

        this.table = $('#classTable').DataTable({

            processing: true,

            serverSide: true,

            ajax: {

                url: CLASSES_LIST_URL,

                type: 'GET',

                data: function (d) {

                    /*
                    |--------------------------------------------------------------------------
                    | Custom Filters
                    |--------------------------------------------------------------------------
                    */

                    d.filter_status =
                        $('#filter_status').val();

                },

                error: function (xhr) {

                    Toast.error(
                        xhr.responseJSON?.message ??
                        'Unable to load Academic Classes.'
                    );

                }

            },

            /*
            |--------------------------------------------------------------------------
            | Default Page Length
            |--------------------------------------------------------------------------
            */

            pageLength: 10,

            /*
            |--------------------------------------------------------------------------
            | Per Page Options
            |--------------------------------------------------------------------------
            */

            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],

            /*
            |--------------------------------------------------------------------------
            | Search
            |--------------------------------------------------------------------------
            */

            searching: true,

            /*
            |--------------------------------------------------------------------------
            | Ordering
            |--------------------------------------------------------------------------
            */

            ordering: true,

            /*
            |--------------------------------------------------------------------------
            | Columns
            |--------------------------------------------------------------------------
            */

            columns: [

                /*
                |--------------------------------------------------------------------------
                | #
                |--------------------------------------------------------------------------
                */

                {
                    data: null,

                    name: null,

                    orderable: false,

                    searchable: false,

                    render: function (
                        data,
                        type,
                        row,
                        meta
                    ) {

                        return (
                            meta.row +
                            meta.settings._iDisplayStart +
                            1
                        );

                    }
                },


                /*
                |--------------------------------------------------------------------------
                | Class Name
                |--------------------------------------------------------------------------
                */

                {
                    data: 'class_name',

                    name: 'class_name'
                },


                /*
                |--------------------------------------------------------------------------
                | Class Code
                |--------------------------------------------------------------------------
                */

                {
                    data: 'class_code',

                    name: 'class_code'
                },


                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                {
                    data: 'status',

                    name: 'status',

                    orderable: true,

                    searchable: false,

                    render: function (
                        data,
                        type,
                        row
                    ) {

                        const canEdit = typeof window.can === 'function' ? window.can('classes.edit') : true;
                        return Helper.statusSwitch(
                            row.id,
                            row.status,
                            canEdit
                        );

                    }
                },


                /*
                |--------------------------------------------------------------------------
                | Actions
                |--------------------------------------------------------------------------
                */

                {
                    data: null,

                    name: null,

                    orderable: false,

                    searchable: false,

                    render: function (
                        data,
                        type,
                        row
                    ) {

                        const canEdit = typeof window.can === 'function' ? window.can('classes.edit') : true;
                        const canDelete = typeof window.can === 'function' ? window.can('classes.delete') : true;

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

            this.table
                .search('')
                .ajax.reload();

        });

        // Add Academic Class
        $('#btnAddClass').on('click', () => {

            this.openCreate();

        });

        // Save Form
        $('#classForm').on('submit', (e) => {

            e.preventDefault();

            if ($('#class_id').val() == '') {

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
    },

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    openCreate() {

        $('#classForm')[0].reset();

        Helper.clearErrors('#classForm');

        $('#class_id').val('');

        $('#classModalTitle').text('Add Academic Classes');

        $('#btnSaveClass').html(
            '<i class="bi bi-check-lg"></i> Save Class'
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

            form: '#classForm',

            url: CLASSES_STORE_URL,

            method: 'POST',

            success: (response) => {

                this.modal.hide();

                $('#classForm')[0].reset();

                this.table.ajax.reload(
                    null,
                    false
                );

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

            form: '#classForm',
            url: CLASSES_EDIT_URL.replace(':id', id),
            method: 'GET',

            success: (response) => {

                const classes = response.data;
                console.log(classes);

                Helper.clearErrors('#classesForm');

                $('#class_id').val(classes.id);

                $('#class_name').val(classes.class_name);
                $('#class_code').val(classes.class_code);
                $('#description').val(classes.description);


                $('#classModalTitle').text('Edit Academic Class');

                $('#btnSaveClass').html(
                    '<i class="bi bi-check-lg"></i> Update Class'
                );

                this.modal.show();

            },

        });


    },

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    update() {
        const id = $('#class_id').val();
        // alert(id);
        let url = CLASSES_UPDATE_URL.replace(':id', id);

        Ajax.request({

            form: '#classForm',

            url: url,

            method: 'POST',

            extraData: {
                _method: 'PUT'
            },

            success: (response) => {

                this.modal.hide();

                $('#classForm')[0].reset();

                this.table.ajax.reload(
                    null,
                    false
                );

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

            title: 'Delete Academic Class?',

            text: 'This action cannot be undone.',

            icon: 'warning',

            showCancelButton: true,

            confirmButtonText: 'Yes, Delete',

            cancelButtonText: 'Cancel',

        }).then((result) => {

            if (!result.isConfirmed) {

                return;

            }

            Ajax.request({

                url: CLASSES_DELETE_URL.replace(':id', id),

                method: 'POST',

                data: (() => {

                    let formData = new FormData();

                    formData.append('_method', 'DELETE');

                    return formData;

                })(),

                success: (response) => {
                    console.log('Delete Success');

                    console.log(response);
                    this.table.ajax.reload(
                        null,
                        false
                    );

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

            url: CLASSES_STATUS_URL.replace(':id', id),

            method: 'POST',

            data: (() => {

                let formData = new FormData();

                formData.append('_method', 'PATCH');

                return formData;

            })(),

            success: () => {

                this.table.ajax.reload(
                    null,
                    false
                );

            },

            error: () => {
                element.checked = !element.checked;
            }

        });

    }

}

$(function () {

    Classes.init();

});