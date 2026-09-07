const Section = {

    modal: null,
    table: null,

    init() {

        this.modal = new bootstrap.Modal(
            document.getElementById('sectionModal')
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

        this.table = $('#sectionTable').DataTable({

            processing: true,
            serverSide: true,

            ajax: {
                url: SECTION_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.filter_status = $('#filter_status').val();
                },
                error: function (xhr) {
                    Toast.error(
                        xhr.responseJSON?.message ?? 'Unable to load Academic Class Sections.'
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
                    data: 'code',
                    name: 'code'
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

        // Add Section
        $('#btnAddSection').on('click', () => {
            this.openCreate();
        });

        // Save Form
        $('#sectionForm').on('submit', (e) => {
            e.preventDefault();
            if ($('#section_id').val() == '') {
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

        Ajax.request({
            url: SECTION_CREATE_URL,
            method: 'GET',
            data: {},
            success: (response) => {
                $('#sectionForm')[0].reset();
                Helper.clearErrors('#sectionForm');
                $('#section_id').val('');
                $('#sectionModalTitle').text('Add Academic Class Section');
                $('#btnSaveSection').html(
                    '<i class="bi bi-check-lg"></i> Save Section'
                );
                this.modal.show();
            }
        });

    },

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    store() {

        Ajax.request({
            form: '#sectionForm',
            url: SECTION_STORE_URL,
            method: 'POST',
            success: (response) => {
                this.modal.hide();
                $('#sectionForm')[0].reset();
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
            form: '#sectionForm',
            url: SECTION_EDIT_URL.replace(':id', id),
            method: 'GET',
            success: (response) => {
                const section = response.data;

                Helper.clearErrors('#sectionForm');
                $('#section_id').val(section.id);
                $('#section_name').val(section.name);
                $('#section_code').val(section.code);
                $('#status').val(section.status == true ? 1 : 0).trigger('change');

                $('#sectionModalTitle').text('Edit Academic Class Section');
                $('#btnSaveSection').html(
                    '<i class="bi bi-check-lg"></i> Update Section'
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
        const id = $('#section_id').val();
        let url = SECTION_UPDATE_URL.replace(':id', id);

        Ajax.request({
            form: '#sectionForm',
            url: url,
            method: 'POST',
            extraData: {
                _method: 'PUT'
            },
            success: (response) => {
                this.modal.hide();
                $('#sectionForm')[0].reset();
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
            title: 'Delete Section?',
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
                url: SECTION_DELETE_URL.replace(':id', id),
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
            url: SECTION_STATUS_URL.replace(':id', id),
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
    Section.init();
});