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
        this.table = $('#admissionEnquiryTable').DataTable({
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
                url: ADMISSION_ENQUIRY_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.academic_session_id = $('#academic_session_id').val();
                    d.class_id = $('#class_id').val();
                    d.status = $('#status').val();
                    d.source = $('#source').val();
                    d.assigned_to = $('#assigned_to').val();
                },
                error: function (xhr) {
                    if (typeof Toast !== 'undefined') {
                        Toast.error(xhr.responseJSON?.message ?? 'Unable to load enquiries.');
                    }
                }
            },
            columns: [
                /* 0: # */
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },

                /* 1: Application No */
                {
                    data: 'application_no',
                    name: 'application_no',
                    render: function (data, type, row) {
                        const showUrl = ADMISSION_ENQUIRY_SHOW_URL.replace(':id', row.id);
                        return `
                            <div>
                                <a href="${showUrl}" class="fw-bold text-primary text-decoration-none">
                                    ${data ?? 'ENQ-' + row.id}
                                </a>
                                <div class="small text-muted">${row.created_at ? new Date(row.created_at).toLocaleDateString('en-GB') : ''}</div>
                            </div>
                        `;
                    }
                },

                /* 2: Student Name */
                {
                    data: 'student_name',
                    name: 'student_name',
                    render: function (data, type, row) {
                        const genderBadge = row.gender ? `<span class="badge bg-light text-secondary ms-1 text-capitalize">${row.gender}</span>` : '';
                        return `
                            <div class="fw-semibold">
                                ${data ?? '-'} ${genderBadge}
                            </div>
                        `;
                    }
                },

                /* 3: Student Contact */
                {
                    data: null,
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                            <div>
                                <div><i class="bi bi-telephone text-muted me-1 small"></i>${row.student_phone ?? '-'}</div>
                                ${row.student_email ? `<small class="text-muted"><i class="bi bi-envelope me-1 small"></i>${row.student_email}</small>` : ''}
                            </div>
                        `;
                    }
                },

                /* 4: Parent Details */
                {
                    data: null,
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                            <div>
                                <div class="fw-semibold">${row.parent_name ?? '-'}</div>
                                <small class="text-muted"><i class="bi bi-telephone text-muted me-1 small"></i>${row.parent_phone ?? '-'}</small>
                            </div>
                        `;
                    }
                },

                /* 5: Class / Session */
                {
                    data: null,
                    orderable: false,
                    render: function (data, type, row) {
                        const className = row.class?.class_name ?? '-';
                        const sessionName = row.academic_session?.name ?? '';
                        return `
                            <div>
                                <span class="badge bg-light text-dark">${className}</span>
                                ${sessionName ? `<div class="small text-muted mt-1">${sessionName}</div>` : ''}
                            </div>
                        `;
                    }
                },

                /* 6: Source */
                {
                    data: 'source',
                    name: 'source',
                    render: function (data) {
                        if (!data) return '-';
                        return `
                            <span class="badge bg-light text-dark text-capitalize">
                                ${data.replaceAll('_', ' ')}
                            </span>
                        `;
                    }
                },

                /* 7: Status */
                {
                    data: 'status',
                    name: 'status',
                    render: function (data) {
                        const badgeClasses = {
                            new: 'bg-secondary',
                            assigned: 'bg-primary',
                            contacted: 'bg-info text-dark',
                            follow_up: 'bg-warning text-dark',
                            interested: 'bg-success',
                            visit_scheduled: 'bg-purple text-white',
                            visited: 'bg-info',
                            admission_ready: 'bg-primary',
                            converted: 'bg-success',
                            not_interested: 'bg-danger',
                            lost: 'bg-dark',
                            cancelled: 'bg-danger'
                        };

                        const badgeClass = badgeClasses[data] ?? 'bg-secondary';
                        const label = (data ?? 'new').replaceAll('_', ' ');

                        return `
                            <span class="badge ${badgeClass} text-uppercase px-2 py-1" style="font-size: 0.75rem;">
                                ${label}
                            </span>
                        `;
                    }
                },

                /* 8: Assigned Staff */
                {
                    data: 'assigned_user.name',
                    name: 'assigned_to',
                    defaultContent: '<span class="text-muted fst-italic">Unassigned</span>',
                    render: function (data, type, row) {
                        const staffName = row.assigned_user?.name ?? row.handled_by?.name;
                        if (staffName) {
                            return `<span class="badge bg-light text-primary border"><i class="bi bi-person me-1"></i>${staffName}</span>`;
                        }
                        return '<span class="badge bg-light text-muted border fst-italic">Unassigned</span>';
                    }
                },

                /* 9: Attempts */
                {
                    data: 'attempt_count',
                    name: 'attempt_count',
                    defaultContent: '0',
                    className: 'text-center',
                    render: function (data) {
                        const count = parseInt(data) || 0;
                        return `<span class="badge rounded-pill ${count > 0 ? 'bg-primary' : 'bg-light text-dark'}">${count}</span>`;
                    }
                },

                /* 10: Next Follow-up */
                {
                    data: 'next_follow_up_at',
                    name: 'next_follow_up_at',
                    defaultContent: '-',
                    render: function (data) {
                        if (!data) return '<span class="text-muted">-</span>';
                        const date = new Date(data);
                        return `<span class="small"><i class="bi bi-calendar-event text-warning me-1"></i>${date.toLocaleDateString('en-GB')}</span>`;
                    }
                },

                /* 11: Actions */
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        const canView = typeof window.can === 'function' ? window.can('admission_enquiry.view') : true;
                        const canCreate = typeof window.can === 'function' ? window.can('admission_enquiry.create') : true;
                        const showUrl = ADMISSION_ENQUIRY_SHOW_URL.replace(':id', row.id);
                        const convertUrl = ADMISSION_ENQUIRY_CONVERT_URL.replace(':id', row.id);
                        const isConverted = row.status === 'converted' || row.converted_at || row.student_profile_id;

                        let buttons = '';

                        if (canView) {
                            buttons += `
                                <a href="${showUrl}" class="btn btn-outline-primary" title="View Details / Follow-up">
                                    <i class="bi bi-eye"></i>
                                </a>
                            `;
                        }

                        if (canCreate) {
                            if (isConverted) {
                                const studentUrl = STUDENT_SHOW_URL.replace(':id', row.enrollment_id || row.student_profile_id || '');
                                buttons += `
                                    <a href="${studentUrl}" class="btn btn-sm btn-outline-success" title="View Student Profile">
                                        <i class="bi bi-mortarboard-fill"></i>
                                    </a>
                                `;
                            } else {
                                buttons += `
                                    <a href="${convertUrl}" class="btn btn-sm btn-outline-success" title="Convert to Admission">
                                        <i class="bi bi-box-arrow-in-right"></i>
                                    </a>
                                `;
                            }
                        }

                        return buttons ? `<div class="btn-group btn-group-sm">${buttons}</div>` : '<span class="text-muted fs-7">-</span>';
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
        $('#academic_session_id, #class_id, #status, #source, #assigned_to').on('change', () => {
            this.reload();
        });

        $('#btnResetFilters').on('click', () => {
            $('#academic_session_id').val('');
            $('#class_id').val('');
            $('#status').val('');
            $('#source').val('');
            $('#assigned_to').val('');
            this.reload();
        });
    },

    /*
    |--------------------------------------------------------------------------
    | Reload
    |--------------------------------------------------------------------------
    */
    reload() {
        if (!this.table) return;
        this.table.ajax.reload(null, true);
    }
};

$(function () {
    AdmissionEnquiry.init();
});