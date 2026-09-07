const ClassTimetables = {

    modal: null,

    table: null,

    init() {

        this.modal = new bootstrap.Modal(
            document.getElementById(
                'classTimetableModal'
            )
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

        this.table = $('#classTimetableTable').DataTable({

            processing: true,

            serverSide: true,

            ajax: {

                url: CLASS_TIMETABLE_LIST_URL,

                type: 'GET',

                data: function (d) {

                    /*
                    |--------------------------------------------------------------------------
                    | Filters
                    |--------------------------------------------------------------------------
                    */

                    d.academic_session_id =
                        $('#academic_session_id').val();

                    d.class_id =
                        $('#class_id').val();

                    d.section_id =
                        $('#section_id').val();

                    d.teacher_id =
                        $('#teacher_id').val();

                    d.day =
                        $('#day').val();

                    d.filter_status =
                        $('#filter_status').val();

                },

                error: function (xhr) {

                    Toast.error(
                        xhr.responseJSON?.message ??
                        'Unable to load class timetable.'
                    );

                }

            },


            /*
            |--------------------------------------------------------------------------
            | Page Length
            |--------------------------------------------------------------------------
            */

            pageLength: 10,


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

                    render: function (data, type, row, meta) {

                        return (
                            meta.row +
                            meta.settings._iDisplayStart +
                            1
                        );

                    }

                },


                /*
                |--------------------------------------------------------------------------
                | Day
                |--------------------------------------------------------------------------
                */

                {
                    data: 'day',

                    name: 'day'

                },


                /*
                |--------------------------------------------------------------------------
                | Period
                |--------------------------------------------------------------------------
                */

                {
                    data: 'period',

                    name: 'period',

                    orderable: false,

                    render: function (data) {

                        if (!data) {
                            return '-';
                        }

                        let time = '';

                        if (
                            data.start_time &&
                            data.end_time
                        ) {

                            time =
                                `<small class="text-muted d-flex">
                                    ${data.start_time}
                                    -
                                    ${data.end_time}
                                </small>`;

                        }

                        return `<div><strong>${data.name ?? '-'}</strong>${time}</div>`;

                    }

                },


                /*
                |--------------------------------------------------------------------------
                | Class
                |--------------------------------------------------------------------------
                */

                {
                    data: 'teacher_subject.subject_class.class_name',

                    name: 'class_name',

                    orderable: false,

                    render: function (data, type, row) {

                        return row.teacher_subject?.subject_class?.class_name ?? '-';

                    }

                },


                /*
                |--------------------------------------------------------------------------
                | Section
                |--------------------------------------------------------------------------
                */

                {
                    data: 'teacher_subject.section.name',

                    name: 'section_name',

                    orderable: false,

                    render: function (data, type, row) {

                        return row.teacher_subject?.section?.name ?? '-';

                    }

                },


                /*
                |--------------------------------------------------------------------------
                | Subject
                |--------------------------------------------------------------------------
                */

                {
                    data: 'teacher_subject.subject',

                    name: 'subject_name',

                    orderable: false,

                    render: function (data, type, row) {

                        if (!row.teacher_subject?.subject) {
                            return '-';
                        }

                        return `
                            <div>

                                <strong>
                                    ${row.teacher_subject?.subject?.subject_name ?? '-'}
                                </strong>

                                ${row.teacher_subject?.subject?.subject_code
                                ? `<small class="text-muted d-flex">
                                            ${row.teacher_subject?.subject?.subject_code}
                                           </small>`
                                : ''
                            }

                            </div>
                        `;

                    }

                },


                /*
                |--------------------------------------------------------------------------
                | Teacher
                |--------------------------------------------------------------------------
                */

                {
                    data: 'teacher_subject.teacher.name',

                    name: 'teacher_name',

                    defaultContent: '-',

                    orderable: false

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

                    render: function (data, type, row) {

                        return Helper.statusSwitch(row.id, row.status);

                    }

                },


                /*
                |--------------------------------------------------------------------------
                | Action
                |--------------------------------------------------------------------------
                */

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


        /*
        |--------------------------------------------------------------------------
        | Add
        |--------------------------------------------------------------------------
        */

        $('#btnAddTimetable').on(
            'click',
            () => {

                this.resetForm();

                $('#classTimetableModalTitle')
                    .text('Add Timetable');

                this.modal.show();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Filter
        |--------------------------------------------------------------------------
        */

        $('#filterForm').on(
            'submit',
            (e) => {

                e.preventDefault();

                this.table.ajax.reload();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Filter Change
        |--------------------------------------------------------------------------
        */

        $(
            '#academic_session_id,' +
            '#class_id,' +
            '#section_id,' +
            '#teacher_id,' +
            '#day,' +
            '#filter_status'
        ).on(
            'change',
            () => {

                this.table.ajax.reload();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Reset
        |--------------------------------------------------------------------------
        */

        $('#btnReset').on(
            'click',
            () => {

                $('#filterForm')[0].reset();

                this.table
                    .search('')
                    .ajax.reload();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Save / Update
        |--------------------------------------------------------------------------
        */

        $('#classTimetableForm').on(
            'submit',
            (e) => {

                e.preventDefault();

                this.save();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Edit
        |--------------------------------------------------------------------------
        */

        $('#classTimetableTable').on(
            'click',
            '.btn-edit',
            (e) => {

                const id =
                    $(e.currentTarget).data('id');

                this.edit(id);

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Delete
        |--------------------------------------------------------------------------
        */

        $('#classTimetableTable').on(
            'click',
            '.btn-delete',
            (e) => {

                const id =
                    $(e.currentTarget).data('id');

                this.delete(id);

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        // Change Status
        $(document).on('change', '.btn-status', (e) => {
            this.changeStatus(e.currentTarget);
        });


        /*
        |--------------------------------------------------------------------------
        | Teacher Subject Change
        |--------------------------------------------------------------------------
        */

        $('#teacher_subject_id').on(
            'change',
            (e) => {

                this.showTeacherSubjectInfo(
                    $(e.currentTarget).val()
                );

            }
        );

    },


    /*
    |--------------------------------------------------------------------------
    | Save
    |--------------------------------------------------------------------------
    */

    save() {

        const form =
            $('#classTimetableForm');

        const id =
            $('#timetable_id').val();

        let url =
            CLASS_TIMETABLE_STORE_URL;

        let method = 'POST';

        if (id) {

            url =
                CLASS_TIMETABLE_UPDATE_URL
                    .replace(':id', id);

            method = 'POST';

        }


        let extraData = {};

        if (id) {

            extraData._method = 'PUT';

        }


        Ajax.request({

            url: url,

            method: method,

            form: form,

            extraData: extraData,

            success: (response) => {

                this.modal.hide();

                this.resetForm();

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

        $.ajax({

            url:
                CLASS_TIMETABLE_EDIT_URL
                    .replace(':id', id),

            type: 'GET',

            headers: {

                'Accept': 'application/json'

            },

            success: (response) => {

                const data = response.data;

                $('#timetable_id').val(data.id);

                $('#teacher_subject_id').val(data.teacher_subject_id).trigger('change');

                $('#period_id').val(data.period_id).trigger('change');

                $('#modal_day').val(data.day).trigger('change');

                $('#modal_status').val(data.status).trigger('change');

                $('#classTimetableModalTitle').text('Edit Timetable');

                this.modal.show();

            },

            error: function (xhr) {

                Toast.error(
                    xhr.responseJSON?.message ??
                    'Unable to load timetable.'
                );

            }

        });

    },


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    delete(id) {

        if (
            !confirm(
                'Are you sure you want to delete this timetable?'
            )
        ) {
            return;
        }


        Ajax.request({

            url:
                CLASS_TIMETABLE_DELETE_URL
                    .replace(':id', id),

            method: 'DELETE',

            data: new FormData(),

            success: () => {

                this.table.ajax.reload(
                    null,
                    false
                );

            }

        });

    },


    /*
    |--------------------------------------------------------------------------
    | Change Status
    |--------------------------------------------------------------------------
    */



    changeStatus(element) {

        const id = $(element).data('id');

        Ajax.request({

            url: CLASS_TIMETABLE_STATUS_URL.replace(':id', id),

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

    },


    /*
    |--------------------------------------------------------------------------
    | Teacher Subject Information
    |--------------------------------------------------------------------------
    */

    showTeacherSubjectInfo(id) {

        if (!id) {

            $('#teacherSubjectInfo')
                .addClass('d-none');

            return;

        }

        /*
        |--------------------------------------------------------------
        | Get selected option data
        |--------------------------------------------------------------
        */

        const option =
            $('#teacher_subject_id')
                .find(`option[value="${id}"]`);

        const teacher =
            option.data('teacher');

        const className =
            option.data('class');

        const section =
            option.data('section');

        const subject =
            option.data('subject');


        /*
        |--------------------------------------------------------------
        | Show information
        |--------------------------------------------------------------
        */

        if (
            teacher ||
            className ||
            section ||
            subject
        ) {

            $('#infoTeacher')
                .text(teacher ?? '-');

            $('#infoClass')
                .text(className ?? '-');

            $('#infoSection')
                .text(section ?? '-');

            $('#infoSubject')
                .text(subject ?? '-');

            $('#teacherSubjectInfo')
                .removeClass('d-none');

        }

    },


    /*
    |--------------------------------------------------------------------------
    | Reset Form
    |--------------------------------------------------------------------------
    */

    resetForm() {

        const form =
            $('#classTimetableForm')[0];

        form.reset();

        $('#timetable_id')
            .val('');

        $('#modal_status')
            .val('1');

        $('#teacher_subject_id')
            .val('');

        $('#period_id')
            .val('');

        $('#modal_day')
            .val('');

        $('#teacherSubjectInfo')
            .addClass('d-none');

        Helper.clearErrors(
            $('#classTimetableForm')
        );

    }

};


/*
|--------------------------------------------------------------------------
| Initialize
|--------------------------------------------------------------------------
*/

$(document).ready(function () {

    ClassTimetables.init();

});