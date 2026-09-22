const Student = {

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

        this.table = $('#studentTable').DataTable({

            processing: true,
            serverSide: true,

            ajax: {
                url: STUDENT_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.academic_session_id = $('#academic_session_id').val();
                    d.class_id = $('#class_id').val();
                    d.section_id = $('#section_id').val();
                    d.status = $('#status').val();
                },
                error: function (xhr) {
                    Toast.error(
                        xhr.responseJSON?.message ?? 'Unable to load students.'
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
                    data: 'student.user.name',
                    name: 'student_id',
                    render: function (data, type, row) {
                        const user = row.student?.user ?? {};
                        const profileImage = user.profile_image_url ?? DEFAULT_AVATAR;

                        return `
                            <div class="d-flex align-items-center">
                                <img
                                    src="${profileImage}"
                                    width="38"
                                    height="38"
                                    class="rounded-circle me-2"
                                    style="object-fit: cover;">
                                <div>
                                    <div class="fw-semibold">
                                        ${user.name ?? '-'}
                                    </div>
                                    <small class="text-muted">
                                        ${user.email ?? '-'}
                                    </small>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    data: 'student.user.mobile',
                    name: 'mobile',
                    orderable: false,
                    render: function (data, type, row) {
                        return row.student?.user?.mobile ?? '-';
                    }
                },
                {
                    data: 'academic_session.name',
                    name: 'academic_session_id',
                    render: function (data, type, row) {
                        return row.academic_session?.name ?? '-';
                    }
                },
                {
                    data: 'roll_number',
                    name: 'roll_number',
                    render: function (data, type, row) {
                        return row.roll_number ?? '-';
                    }
                },
                {
                    data: 'student_class.class_name',
                    name: 'class_id',
                    render: function (data, type, row) {
                        return row.student_class?.class_name ?? '-';
                    }
                },
                {
                    data: 'section.name',
                    name: 'section_id',
                    render: function (data, type, row) {
                        return row.section?.name ?? '-';
                    }
                },
                {
                    data: 'student.user.status',
                    name: 'status',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return Helper.statusSwitch(row.id, row.student?.user?.status);
                    }
                },
                {
                    data: null,
                    name: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const viewUrl = STUDENT_SHOW_URL.replace(':id', row.id);
                        const editUrl = STUDENT_UPDATE_URL.replace(':id', row.id) + '/edit';

                        return `
                            <a
                                href="${viewUrl}"
                                class="btn btn-sm btn-view"
                                title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a
                                href="${editUrl}"
                                class="btn btn-sm btn-edit"
                                title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button
                                type="button"
                                class="btn btn-sm btn-delete"
                                data-id="${row.id}"
                                title="Delete">
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

        // Filter Form
        $('#filterForm').on('submit', (e) => {
            e.preventDefault();
            this.table.ajax.reload();
        });

        // Filter changes
        $('#academic_session_id, #class_id, #section_id, #status').on('change', () => {
            this.table.ajax.reload();
        });

        // Class change to load sections
        $('#class_id').on('change', function () {
            const classId = $(this).val();
            const sectionSelect = $('#section_id');

            sectionSelect.html('<option value="">Select Section</option>');

            if (classId) {
                $.ajax({
                    url: SECTION_BY_CLASS_URL.replace(':id', classId),
                    type: 'GET',
                    success: function (response) {
                        if (response.data) {
                            $.each(response.data, function (key, section) {
                                sectionSelect.append(
                                    `<option value="${section.id}">${section.name}</option>`
                                );
                            });
                        }
                    }
                });
            }
        });

        // Reset
        $('#btnReset').on('click', () => {
            $('#filterForm')[0].reset();
            this.table.search('').ajax.reload();
        });

        // Auto-fetch suggested roll number on student create form
        $('#academic_session_id, #form_class_id, #form_section_id').on('change', () => {
            if ($('#studentForm').length && !$('#studentForm').find('input[name="_method"]').val()) {
                const sessionId = $('#academic_session_id').val();
                const classId = $('#form_class_id').val();
                const sectionId = $('#form_section_id').val();

                if (sessionId && classId && sectionId) {
                    $.ajax({
                        url: '/admin/students/suggested-roll-number',
                        type: 'GET',
                        data: {
                            academic_session_id: sessionId,
                            class_id: classId,
                            section_id: sectionId
                        },
                        success: (res) => {
                            if (res && res.status && res.suggested_roll_number) {
                                $('#roll_number').val(res.suggested_roll_number);
                            }
                        }
                    });
                }
            }
        });

        // Student Form Submit (Create / Edit pages)
        $('#studentForm').on('submit', (e) => {
            e.preventDefault();
            const form = $('#studentForm');
            const action = form.attr('action');
            const method = form.find('input[name="_method"]').val() ?? 'POST';

            if (method === 'PUT') {
                this.update(action);
            } else {
                this.store(action);
            }
        });

        // Change status
        $(document).on('change', '.btn-status', (e) => {
            this.changeStatus(e.currentTarget);
        });

        // Delete 
        $(document).on('click', '.btn-delete', (e) => {
            this.destroy($(e.currentTarget).data('id'));
        });

        // Image preview
        $('#profile_image').on('change', function () {
            const file = this.files[0];
            if (!file) {
                $('#profilePreview').attr('src', DEFAULT_AVATAR);
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                $('#profilePreview').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        });

    },

    /*
    |--------------------------------------------------------------------------
    | Create Student
    |--------------------------------------------------------------------------
    */

    store(url) {

        Ajax.request({
            form: '#studentForm',
            url: url,
            method: 'POST',
            success: (response) => {
                $('#studentForm')[0].reset();
                Toast.success(response.message ?? 'Student created successfully.');
                window.location.href = BASE_URL + '/admin/students';
            }
        });

    },

    /*
    |--------------------------------------------------------------------------
    | Update Student
    |--------------------------------------------------------------------------
    */

    update(url) {

        Ajax.request({
            form: '#studentForm',
            url: url,
            method: 'POST',
            extraData: {
                _method: 'PUT'
            },
            success: (response) => {
                $('#studentForm')[0].reset();
                Toast.success(response.message ?? 'Student updated successfully.');
                window.location.href = BASE_URL + '/admin/students';
            }
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
            url: STUDENT_STATUS_URL.replace(':id', id),
            method: 'POST',
            data: (() => {
                let formData = new FormData();
                formData.append('_method', 'PATCH');
                return formData;
            })(),
            success: (response) => {
                Toast.success(response.message ?? 'Student status updated successfully.');
                this.table.ajax.reload(null, false);
            },
            error: () => {
                element.checked = !element.checked;
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
            title: 'Delete Student?',
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
                url: STUDENT_DELETE_URL.replace(':id', id),
                method: 'POST',
                data: (() => {
                    let formData = new FormData();
                    formData.append('_method', 'DELETE');
                    return formData;
                })(),
                success: (response) => {
                    Toast.success(response.message ?? 'Student deleted successfully.');
                    this.table.ajax.reload(null, false);
                }
            });
        });

    }

};

$(function () {
    Student.init();
});