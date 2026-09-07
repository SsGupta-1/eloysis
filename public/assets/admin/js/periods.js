const Periods = {

    modal: null,
    init() {

        this.modal = new bootstrap.Modal(
            document.getElementById('periodModal')
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

        this.table = $('#periodTable').DataTable({

            processing: true,

            serverSide: true,

            ajax: {

                url: PERIODS_LIST_URL,

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
                        'Unable to load Periods.'
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
                    data: 'name',

                    name: 'name'
                },


                /*
                |--------------------------------------------------------------------------
                | Start Time
                |--------------------------------------------------------------------------
                */

                {
                    data: 'start_time',

                    name: 'start_time'
                },


                /*
                |--------------------------------------------------------------------------
                | End Time
                |--------------------------------------------------------------------------
                */

                {
                    data: 'end_time',

                    name: 'end_time'
                },


                /*
                |--------------------------------------------------------------------------
                | Sort Order
                |--------------------------------------------------------------------------
                */

                {
                    data: 'sort_order',

                    name: 'sort_order'
                },


                /*
                |--------------------------------------------------------------------------
                |   Status
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

                        return Helper.statusSwitch(
                            row.id,
                            row.status
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

            this.table
                .search('')
                .ajax.reload();

        });

        // Add Academic Class
        $('#btnAddPeriod').on('click', () => {

            this.openCreate();

        });

        // Save Form
        $('#periodForm').on('submit', (e) => {

            e.preventDefault();

            if ($('#period_id').val() == '') {

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

        $('#periodForm')[0].reset();

        Helper.clearErrors('#periodForm');

        $('#period_id').val('');

        $('#periodModalTitle').text('Add Academic Classes');

        $('#btnSavePeriod').html(
            '<i class="bi bi-check-lg"></i> Save Period'
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

            form: '#periodForm',

            url: PERIODS_STORE_URL,

            method: 'POST',

            success: (response) => {

                this.modal.hide();

                $('#periodForm')[0].reset();

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

            form: '#periodForm',
            url: PERIODS_EDIT_URL.replace(':id', id),
            method: 'GET',

            success: (response) => {

                const periods = response.data;
                console.log(periods);

                Helper.clearErrors('#periodForm');

                $('#period_id').val(periods.id);

                $('#period_name').val(periods.name);
                $('#start_time').val(periods.start_time);
                $('#end_time').val(periods.end_time);
                $('#sort_order').val(periods.sort_order);
                $('#status').val(periods.status);


                $('#periodModalTitle').text('Edit Academic Class');

                $('#btnSavePeriod').html(
                    '<i class="bi bi-check-lg"></i> Update Period'
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
        const id = $('#period_id').val();
        // alert(id);
        let url = PERIODS_UPDATE_URL.replace(':id', id);

        Ajax.request({

            form: '#periodForm',

            url: url,

            method: 'POST',

            extraData: {
                _method: 'PUT'
            },

            success: (response) => {

                this.modal.hide();

                $('#periodForm')[0].reset();

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

            title: 'Delete Academic Period?',

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

                url: PERIODS_DELETE_URL.replace(':id', id),

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

            url: PERIODS_STATUS_URL.replace(':id', id),

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

    Periods.init();

});