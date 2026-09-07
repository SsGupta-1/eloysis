const AdmissionEnquiry = {

    table: null,


    init() {

        this.initDataTable();

        this.bindEvents();

    },


    /*
    |--------------------------------------------------------------------------
    | DataTable
    |--------------------------------------------------------------------------
    */

    initDataTable() {

        this.table =
            $('#admissionEnquiryTable').DataTable({

                processing: true,

                serverSide: true,

                searching: true,

                ordering: true,

                pageLength: 10,

                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],


                ajax: {

                    url:
                        ADMISSION_ENQUIRY_LIST_URL,

                    type: 'GET',

                    data: function (d) {

                        d.academic_session_id =
                            $('#academic_session_id').val();

                        d.class_id =
                            $('#class_id').val();

                        d.status =
                            $('#status').val();

                        d.source =
                            $('#source').val();

                        d.assigned_to =
                            $('#assigned_to').val();

                    },


                    error: function (xhr) {

                        Toast.error(
                            xhr.responseJSON?.message ??
                            'Unable to load enquiries.'
                        );

                    }

                },


                columns: [

                    /*
                    |--------------------------------------------------------------------------
                    | #
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: null,

                        searchable: false,

                        orderable: false,

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
                    | Application No
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'application_no',

                        name: 'application_no',

                        render: function (
                            data,
                            type,
                            row
                        ) {

                            return `
                                <a
                                    href="${ADMISSION_ENQUIRY_SHOW_URL}/${row.id}"
                                    class="fw-semibold text-decoration-none"
                                >
                                    ${data ?? '-'}
                                </a>
                            `;

                        }

                    },


                    /*
                    |--------------------------------------------------------------------------
                    | Student
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'student_name',

                        name: 'student_name',

                        render: function (data) {

                            return `
                                <div class="fw-semibold">
                                    ${data ?? '-'}
                                </div>
                            `;

                        }

                    },


                    /*
                    |--------------------------------------------------------------------------
                    | Contact
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: null,

                        orderable: false,

                        render: function (
                            data,
                            type,
                            row
                        ) {

                            return `
                                <div>
                                    <div>
                                        ${row.student_email ?? '-'}
                                    </div>

                                    <small class="text-muted">
                                        ${row.student_phone ?? ''}
                                    </small>
                                </div>
                            `;

                        }

                    },

                    {
                        data: null,

                        orderable: false,

                        render: function (
                            data,
                            type,
                            row
                        ) {

                            return `
                                <div>
                                    <div>
                                        ${row.parent_name ?? '-'}
                                    </div>

                                    <small class="text-muted">
                                        ${row.parent_phone ?? ''}
                                    </small>
                                </div>
                            `;

                        }

                    },


                    /*
                    |--------------------------------------------------------------------------
                    | Class
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'class.class_name',

                        name: 'class.class_name',

                        defaultContent: '-'

                    },

                    {
                        data: 'academic_session.name',

                        name: 'academic_session.name',

                        defaultContent: '-'

                    },


                    /*
                    |--------------------------------------------------------------------------
                    | Source
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'source',

                        name: 'source',

                        render: function (data) {

                            if (!data) {

                                return '-';

                            }

                            return `
                                <span class="badge bg-light text-dark">
                                    ${data.replaceAll('_', ' ')}
                                </span>
                            `;

                        }

                    },


                    /*
                    |--------------------------------------------------------------------------
                    | Status
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'status',

                        name: 'status',

                        render: function (data) {

                            const colors = {

                                new: 'secondary',

                                contacted: 'primary',

                                follow_up: 'warning',

                                interested: 'info',

                                not_interested: 'danger',

                                visit_scheduled: 'primary',

                                visited: 'info',

                                converted: 'success',

                                lost: 'dark'

                            };


                            const color =
                                colors[data] ??
                                'secondary';


                            return `
                                <span class="badge bg-${color}">
                                    ${(data ?? '-')
                                    .replaceAll('_', ' ')}
                                </span>
                            `;

                        }

                    },


                    /*
                    |--------------------------------------------------------------------------
                    | Assigned
                    |--------------------------------------------------------------------------
                    */

                    {
                        data:
                            'assigned_user.name',

                        name:
                            'assigned_to',

                        defaultContent:
                            '<span class="text-muted">Unassigned</span>'

                    },


                    /*
                    |--------------------------------------------------------------------------
                    | Attempts
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'attempt_count',

                        name: 'attempt_count',

                        defaultContent: '0',

                        className: 'text-center'

                    },


                    /*
                    |--------------------------------------------------------------------------
                    | Follow Up
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'next_followup_at',

                        name: 'next_followup_at',

                        defaultContent: '-',

                        render: function (data) {

                            if (!data) {

                                return '-';

                            }

                            return `
                                <span>
                                    ${data}
                                </span>
                            `;

                        }

                    },


                    /*
                    |--------------------------------------------------------------------------
                    | Action
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: null,

                        orderable: false,

                        searchable: false,

                        render: function (
                            data,
                            type,
                            row
                        ) {
                            const viewUrl = ADMISSION_ENQUIRY_SHOW_URL.replace(':id', row.id);
                            return `
                                <a
                                    href="${viewUrl}"
                                    class="btn btn-sm btn-outline-primary"
                                    title="View"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>
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

        $(
            '#academic_session_id,' +
            '#class_id,' +
            '#status,' +
            '#source,' +
            '#assigned_to'
        ).on(
            'change',
            () => {

                this.reload();

            }
        );

        $(document).on(
            'submit',
            '#admissionEnquiryForm',
            (e) => {

                e.preventDefault();

                this.save();

            }
        );

    },


    /*
    |--------------------------------------------------------------------------
    | Reload
    |--------------------------------------------------------------------------
    */

    reload() {

        if (!this.table) {

            return;

        }

        this.table.ajax.reload(
            null,
            true
        );

    },

    save() {

        const form =
            $('#admissionEnquiryForm');

        const submitBtn =
            $('#btnSaveEnquiry');

        const spinner =
            submitBtn.find('.spinner-border');

        const btnText =
            submitBtn.find('.btn-text');


        /*
        |--------------------------------------------------------------------------
        | Clear Errors
        |--------------------------------------------------------------------------
        */

        form.find('.is-invalid')
            .removeClass('is-invalid');

        form.find('.invalid-feedback')
            .text('');


        /*
        |--------------------------------------------------------------------------
        | Loading
        |--------------------------------------------------------------------------
        */

        submitBtn.prop(
            'disabled',
            true
        );

        btnText.text(
            'Saving...'
        );

        spinner.removeClass(
            'd-none'
        );


        /*
        |--------------------------------------------------------------------------
        | AJAX
        |--------------------------------------------------------------------------
        */

        Ajax.request({

            form: form,

            url:
                ADMISSION_ENQUIRY_UPDATE_URL,

            method: 'POST',


            success(response) {

                Toast.success(
                    response.message ??
                    'Enquiry updated successfully.'
                );


                /*
                |--------------------------------------------------------------------------
                | Reload
                |--------------------------------------------------------------------------
                */

                setTimeout(() => {

                    window.location.reload();

                }, 500);

            },


            error(xhr) {

                if (xhr.status === 422) {

                    const errors =
                        xhr.responseJSON?.errors ?? {};


                    $.each(
                        errors,
                        function (
                            field,
                            messages
                        ) {

                            const input =
                                form.find(
                                    `[name="${field}"]`
                                );


                            input.addClass(
                                'is-invalid'
                            );


                            form.find(
                                `[data-error="${field}"]`
                            ).text(
                                messages[0]
                            );

                        }
                    );


                    return;

                }


                Toast.error(
                    xhr.responseJSON?.message ??
                    'Something went wrong.'
                );

            },


            complete() {

                submitBtn.prop(
                    'disabled',
                    false
                );

                btnText.text(
                    'Save Changes'
                );

                spinner.addClass(
                    'd-none'
                );

            }

        });

    }

};


$(function () {

    AdmissionEnquiry.init();

});