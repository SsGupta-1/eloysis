const Attendance = {

    table: null,

    /*
    |--------------------------------------------------------------------------
    | Attendance State
    |--------------------------------------------------------------------------
    |
    | {
    |     enrollment_id: {
    |         student_enrollment_id: 1,
    |         status: 'present',
    |         remarks: '...'
    |     }
    | }
    |
    |--------------------------------------------------------------------------
    */

    attendanceState: {},


    /*
    |--------------------------------------------------------------------------
    | Init
    |--------------------------------------------------------------------------
    */

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

        this.table = $('#attendanceTable').DataTable({

            processing: true,

            serverSide: true,

            searching: true,

            ordering: false,

            pageLength: 10,

            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],

            ajax: {

                url: TEACHER_ATTENDANCE_LIST_URL,

                type: 'GET',

                data: function (d) {
                    d.attendance_date = $('#attendance_date').val();

                },

                error: function (xhr) {

                    Toast.error(
                        xhr.responseJSON?.message ??
                        'Unable to load students.'
                    );
                }
            },

            /*
            |--------------------------------------------------------------------------
            | DataTable Draw
            |--------------------------------------------------------------------------
            */

            drawCallback: () => {

                this.restoreAttendanceState();
                this.updateSummary();
                this.setActionButtons(true);
            },

            createdRow: function (row, data, dataIndex) {

                $(row).addClass('attendance-row');

            },
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

                {
                    data: 'profile_image_url',

                    name: 'profile_image_url',
                    render: function (data, type, row) {
                        const profileImage = row.profile_image_url ?? DEFAULT_AVATAR;

                        return `
                            <div class="d-flex align-items-center">
                                <img
                                    src="${profileImage}"
                                    width="38"
                                    height="38"
                                    class="rounded-circle me-2"
                                    style="object-fit: cover;">
                            </div>
                        `;
                    }

                },

                /*
               |--------------------------------------------------------------------------
               | Student
               |--------------------------------------------------------------------------
               */

                {
                    data: 'teacher_name',

                    name: 'teacher_name',

                    defaultContent: '-'

                },

                {
                    data: 'employee_id',

                    name: 'employee_id',

                    defaultContent: '-'

                },

                {
                    data: 'mobile',

                    name: 'mobile',

                    defaultContent: '-'

                },

                /*
                |--------------------------------------------------------------------------
                | Attendance Status
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

                            <select
                                class="form-select form-select-sm attendance-status"
                                data-profile-id="${row.id}">

                                <option value="">
                                    Select
                                </option>

                                <option value="present"  ${row.attendance_status == 'present' ? 'selected' : ''}>
                                    Present
                                </option>

                                <option value="absent" ${row.attendance_status == 'absent' ? 'selected' : ''}>
                                    Absent
                                </option>

                                <option value="late" ${row.attendance_status == 'late' ? 'selected' : ''}>
                                    Late
                                </option>

                                <option value="leave" ${row.attendance_status == 'leave' ? 'selected' : ''}>
                                    Leave
                                </option>

                            </select>

                        `;

                    }

                },


                /*
                |--------------------------------------------------------------------------
                | Remarks
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

                            <input
                                type="text"
                                class="form-control form-control-sm attendance-remarks"
                                data-profile-id="${row.id}"
                                maxlength="500"
                                placeholder="Remarks"
                                value="${row.remarks ?? ''}">

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
        | Filter
        |--------------------------------------------------------------------------
        */

        $('#filterForm').on(
            'submit',
            (e) => {
                e.preventDefault();

                this.resetState();

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

                this.resetState();
                this.table.ajax.reload();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Status Change
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'change',
            '.attendance-status',
            (e) => {

                const select =
                    $(e.currentTarget);

                const enrollmentId =
                    select.data('profile-id');

                this.updateState(
                    enrollmentId,
                    {
                        status: select.val()
                    }
                );

                this.updateSummary();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Remarks Change
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'input',
            '.attendance-remarks',
            (e) => {

                const input =
                    $(e.currentTarget);

                const enrollmentId =
                    input.data('profile-id');

                this.updateState(
                    enrollmentId,
                    {
                        remarks: input.val()
                    }
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Mark All Present
        |--------------------------------------------------------------------------
        */

        // $('#btnMarkAllPresent').on(
        //     'click',
        //     () => {

        //         this.markAll('present');

        //     }
        // );


        /*
        |--------------------------------------------------------------------------
        | Mark All Absent
        |--------------------------------------------------------------------------
        */

        // $('#btnMarkAllAbsent').on(
        //     'click',
        //     () => {

        //         this.markAll('absent');

        //     }
        // );


        /*
        |--------------------------------------------------------------------------
        | Save
        |--------------------------------------------------------------------------
        */

        $('#attendanceForm').on(
            'submit',
            (e) => {

                e.preventDefault();

                this.save();

            }
        );

        $('#btnSaveAttendance').on('click', () => {

            this.save();

        });


        /*
        |--------------------------------------------------------------------------
        | Save Attendance - Bottom
        |--------------------------------------------------------------------------
        */

        $('#btnSaveAttendanceBottom').on('click', () => {

            this.save();

        });

    },


    /*
    |--------------------------------------------------------------------------
    | Update Attendance State
    |--------------------------------------------------------------------------
    */

    updateState(
        enrollmentId,
        data
    ) {


        if (!enrollmentId) {

            return;

        }


        enrollmentId =
            String(enrollmentId);


        if (!this.attendanceState[enrollmentId]) {

            this.attendanceState[enrollmentId] = {

                teacher_profile_id: Number(enrollmentId),

                status: '',

                remarks: null

            };

        }


        Object.assign(
            this.attendanceState[enrollmentId],
            data
        );

    },


    /*
    |--------------------------------------------------------------------------
    | Restore State After DataTable Draw
    |--------------------------------------------------------------------------
    */

    restoreAttendanceState() {

        $('#attendanceTable tbody tr')
            .each((index, element) => {

                const row =
                    $(element);


                const statusInput =
                    row.find(
                        '.attendance-status'
                    );


                const enrollmentId =
                    statusInput.data(
                        'profile-id'
                    );


                if (!enrollmentId) {

                    return;

                }


                const state =
                    this.attendanceState[
                    String(enrollmentId)
                    ];


                if (!state) {

                    return;

                }


                row.find(
                    '.attendance-status'
                ).val(
                    state.status
                );


                row.find(
                    '.attendance-remarks'
                ).val(
                    state.remarks ?? ''
                );

            });

    },

    /*
    |--------------------------------------------------------------------------
    | Validate Complete Attendance
    |--------------------------------------------------------------------------
    */

    validateAttendance() {

        const entries =
            Object.values(
                this.attendanceState
            );


        if (!entries.length) {

            Toast.error(
                'No teacher found.'
            );

            return false;

        }


        const missing =
            entries.filter(
                item => !item.status
            );


        if (missing.length) {

            Toast.error(
                `Please mark attendance for all teachers. ${missing.length} teacher(s) are pending.`
            );

            return false;

        }


        return true;

    },


    /*
    |--------------------------------------------------------------------------
    | Save Attendance
    |--------------------------------------------------------------------------
    */

    save() {

        if (!this.validateAttendance()) {

            return;

        }
        const attendance =
            Object.values(
                this.attendanceState
            );


        const formData =
            new FormData();


        formData.append(
            'attendance_date',
            $('#attendance_date').val()
        );


        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        |
        | Send complete class attendance as JSON.
        |
        |--------------------------------------------------------------------------
        */

        formData.append(
            'attendance',
            JSON.stringify(attendance)
        );


        Ajax.request({

            url: TEACHER_ATTENDANCE_SAVE_URL,

            method: 'POST',

            data: formData,

            success: (response) => {

                /*
                |--------------------------------------------------------------------------
                | Clear State
                |--------------------------------------------------------------------------
                */

                this.resetState();

                /*
                |--------------------------------------------------------------------------
                | Reload DataTable
                |--------------------------------------------------------------------------
                */

                this.reload();

            }

        });

    },


    /*
    |--------------------------------------------------------------------------
    | Reset Attendance State
    |--------------------------------------------------------------------------
    */

    resetState() {

        this.attendanceState = {};

    },


    /*
    |--------------------------------------------------------------------------
    | Reload DataTable
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

    updateSummary() {

        const rows = $('.attendance-row');
        const total = rows.length;

        let present = 0;

        let absent = 0;

        let late = 0;

        let leave = 0;


        rows.each(function () {

            const status =
                $(this)
                    .find('.attendance-status')
                    .val();


            switch (status) {

                case 'present':

                    present++;

                    break;


                case 'absent':

                    absent++;

                    break;


                case 'late':

                    late++;

                    break;


                case 'leave':

                    leave++;

                    break;

            }

        });


        $('#attendanceSummary').html(`

            <span class="me-3">
                Total:
                <strong>${total}</strong>
            </span>

            <span class="text-success me-3">
                Present:
                <strong>${present}</strong>
            </span>

            <span class="text-danger me-3">
                Absent:
                <strong>${absent}</strong>
            </span>

            <span class="text-warning me-3">
                Late:
                <strong>${late}</strong>
            </span>

            <span class="text-info">
                Leave:
                <strong>${leave}</strong>
            </span>

        `);

    },

    setActionButtons(enabled) {

        const rows = $('.attendance-row');
        enabled = rows.length == 0 ? !enabled : enabled;
        $('#btnSaveAttendance')
            .prop('disabled', !enabled);

        $('#btnSaveAttendanceBottom')
            .prop('disabled', !enabled);

    },


};


$(function () {

    Attendance.init();

});