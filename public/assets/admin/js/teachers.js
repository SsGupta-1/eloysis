const Teacher = {

    modal: null,
    viewModal: null,
    table: null,

    init() {

        this.modal = new bootstrap.Modal(
            document.getElementById('teacherModal')
        );

        this.viewModal = new bootstrap.Modal(
            document.getElementById('teacherViewModal')
        );

        this.initDataTable();

        if (typeof UserPermissionManager !== 'undefined') {
            UserPermissionManager.init({
                getUrl: TEACHER_PERMISSIONS_URL,
                updateUrl: TEACHER_PERMISSIONS_UPDATE_URL,
                table: this.table,
                defaultAvatar: typeof DEFAULT_AVATAR !== 'undefined' ? DEFAULT_AVATAR : '',
                titlePrefix: 'Teacher'
            });
        }

        this.bindEvents();

    },

    /*
    |--------------------------------------------------------------------------
    | DataTable
    |--------------------------------------------------------------------------
    */

    initDataTable() {

        this.table = $('#teacherTable').DataTable({

            processing: true,
            serverSide: true,

            ajax: {
                url: TEACHER_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.filter_status = $('#filter_status').val();
                },
                error: function (xhr) {
                    Toast.error(
                        xhr.responseJSON?.message ?? 'Unable to load teachers.'
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
                    data: 'user.name',
                    name: 'user_id',
                    render: function (data, type, row) {
                        const user = row.user || {};
                        const profileImage = user.profile_image_url ?? DEFAULT_AVATAR;
                        const customBadge = user.has_custom_permissions
                            ? '<span class="badge bg-warning-subtle text-warning border border-warning-subtle ms-1" style="font-size:10px;">Custom</span>'
                            : '';

                        return `
                            <div class="d-flex align-items-center">
                                <img
                                    src="${profileImage}"
                                    width="38"
                                    height="38"
                                    class="rounded-circle me-2"
                                    style="object-fit: cover;">
                                <div>
                                    <div class="fw-semibold d-flex align-items-center">
                                        ${user.name ?? '-'}
                                        ${customBadge}
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    data: 'user.email',
                    name: 'email',
                    orderable: false,
                    render: function (data, type, row) {
                        return row.user?.email ?? '-';
                    }
                },
                {
                    data: 'employee_id',
                    name: 'employee_id',
                    render: function (data, type, row) {
                        return row.employee_id ?? '-';
                    }
                },
                {
                    data: 'user.mobile',
                    name: 'mobile',
                    orderable: false,
                    render: function (data, type, row) {
                        return row.user?.mobile ?? '-';
                    }
                },
                {
                    data: 'specialization',
                    name: 'specialization',
                    render: function (data, type, row) {
                        return row.specialization ?? '-';
                    }
                },
                {
                    data: 'user.status',
                    name: 'status',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const canEdit = typeof window.can === 'function' ? window.can('teachers.edit') : true;
                        return Helper.statusSwitch(row.id, row.user?.status, canEdit);
                    }
                },
                {
                    data: null,
                    name: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const canEdit = typeof window.can === 'function' ? window.can('teachers.edit') : true;
                        const canDelete = typeof window.can === 'function' ? window.can('teachers.delete') : true;
                        const canView = typeof window.can === 'function' ? window.can('teachers.view') : true;

                        let buttons = '';

                        if (canEdit) {
                            buttons += `
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary btn-permissions me-1"
                                    data-id="${row.id}"
                                    title="Custom Permissions">
                                    <i class="bi bi-person-lock"></i>
                                </button>
                            `;
                        }

                        if (canView) {
                            buttons += `
                                <button
                                    type="button"
                                    class="btn btn-sm btn-view"
                                    data-id="${row.id}"
                                    title="View Details">
                                    <i class="bi bi-eye"></i>
                                </button>
                            `;
                        }

                        if (canEdit) {
                            buttons += `
                                <button
                                    type="button"
                                    class="btn btn-sm btn-edit"
                                    data-id="${row.id}"
                                    title="Edit Teacher">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            `;
                        }

                        if (canDelete) {
                            buttons += `
                                <button
                                    type="button"
                                    class="btn btn-sm btn-delete"
                                    data-id="${row.id}"
                                    title="Delete Teacher">
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

        // Filter Submit
        $('#filterForm').on('submit', (e) => {
            e.preventDefault();
            this.table.ajax.reload();
        });

        // Reset Filter
        $('#btnReset').on('click', () => {
            $('#filterForm')[0].reset();
            this.table.search('').ajax.reload();
        });

        // Add Teacher
        $('#btnAddTeacher').on('click', () => {
            this.openCreate();
        });

        // Form Submit
        $('#teacherForm').on('submit', (e) => {
            e.preventDefault();
            this.save();
        });

        // Edit Teacher
        $(document).on('click', '.btn-edit', (e) => {
            this.edit($(e.currentTarget).data('id'));
        });

        // View Teacher
        $(document).on('click', '.btn-view', (e) => {
            this.view($(e.currentTarget).data('id'));
        });

        // Custom Permissions Modal
        $(document).on('click', '.btn-permissions', (e) => {
            const id = $(e.currentTarget).data('id');
            if (typeof UserPermissionManager !== 'undefined') {
                UserPermissionManager.open(id);
            }
        });

        // Change status
        $(document).on('change', '.btn-status', (e) => {
            this.changeStatus(e.currentTarget);
        });

        // Status Filter
        $('#filter_status').on('change', () => {
            this.table.ajax.reload();
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
    | Open Create Modal
    |--------------------------------------------------------------------------
    */

    openCreate() {

        $('#teacherForm')[0].reset();
        $('#teacher_id').val('');
        $('#teacherModalLabel').text('Add Teacher');
        $('#profilePreview').attr('src', DEFAULT_AVATAR);

        Helper.clearErrors($('#teacherForm'));
        this.modal.show();

    },

    /*
    |--------------------------------------------------------------------------
    | Edit Teacher
    |--------------------------------------------------------------------------
    */

    edit(id) {

        const url = TEACHER_SHOW_URL.replace(':id', id);

        $.ajax({
            url: url,
            type: 'GET',
            success: (response) => {
                const teacher = response.data;
                const user = teacher.user;

                $('#teacher_id').val(teacher.id);
                $('#name').val(user.name);
                $('#email').val(user.email);
                $('#mobile').val(user.mobile);
                $('#employee_id').val(teacher.employee_id);
                $('#qualification').val(teacher.qualification);
                $('#specialization').val(teacher.specialization);
                $('#joining_date').val(teacher.joining_date ? teacher.joining_date.substring(0, 10) : '');
                $('#experience_years').val(teacher.experience_years);
                $('#status').val(user.status ? 1 : 0);
                $('#address').val(teacher.address);
                $('#city').val(teacher.city);
                $('#state').val(teacher.state);
                $('#pincode').val(teacher.pincode);
                $('#emergency_contact_name').val(teacher.emergency_contact_name);
                $('#emergency_contact_mobile').val(teacher.emergency_contact_mobile);
                $('#password').val('');
                $('#password_confirmation').val('');
                $('#profilePreview').attr('src', user.profile_image_url ?? DEFAULT_AVATAR);
                $('#dob').val(teacher.dob ? teacher.dob.substring(0, 10) : '');
                $('#gender').val(teacher.gender);

                $('#teacherModalTitle').text('Edit Teacher');
                $('#btnSaveTeacher').text('Update Teacher');
                this.modal.show();
            },
            error: (xhr) => {
                Toast.error(xhr.responseJSON?.message ?? 'Unable to load teacher.');
            }
        });

    },

    /*
    |--------------------------------------------------------------------------
    | Save Teacher
    |--------------------------------------------------------------------------
    */

    save() {

        const id = $('#teacher_id').val();
        const isEdit = id !== '';

        const url = isEdit
            ? TEACHER_UPDATE_URL.replace(':id', id)
            : TEACHER_STORE_URL;

        Ajax.request({
            form: '#teacherForm',
            url: url,
            method: 'POST',
            extraData: isEdit ? { _method: 'PUT' } : {},
            success: (response) => {
                this.modal.hide();
                $('#teacherForm')[0].reset();
                Toast.success(
                    response.message ?? (isEdit ? 'Teacher updated successfully.' : 'Teacher created successfully.')
                );
                this.table.ajax.reload(null, false);
            }
        });

    },

    /*
    |--------------------------------------------------------------------------
    | View Teacher
    |--------------------------------------------------------------------------
    */

    view(id) {

        const url = TEACHER_SHOW_URL.replace(':id', id);

        $.ajax({
            url: url,
            type: 'GET',
            success: (response) => {
                const teacher = response.data;
                const user = teacher.user;

                $('#viewName').text(user.name);
                $('#viewEmail').text(user.email ?? '-');
                $('#viewMobile').text(user.mobile ?? '-');
                $('#viewEmployeeId').text(teacher.employee_id ?? '-');
                $('#viewEmployee').text(teacher.employee_id ?? '-');
                $('#viewQualification').text(teacher.qualification ?? '-');
                $('#viewSpecialization').text(teacher.specialization ?? '-');
                $('#viewJoiningDate').text(Helper.formatDate(teacher.joining_date));
                $('#viewExperience').text(
                    teacher.experience_years ? teacher.experience_years + ' Years' : '-'
                );
                $('#viewStatus').html(
                    user.status
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>'
                );

                $('#viewProfileImage').attr(
                    'src',
                    user.profile_image_url ? user.profile_image_url : DEFAULT_AVATAR
                );

                $('.teacher-profile-header').css(
                    'background-image',
                    `url(${user.profile_image_url})`
                );

                $('#viewDob').text(Helper.formatDate(teacher.dob));
                $('#viewCity').text(teacher.city ?? '-');
                $('#viewState').text(teacher.state ?? '-');
                $('#viewPincode').text(teacher.pincode ?? '-');
                $('#viewAddress').text(teacher.address ?? '-');
                $('#viewGender').text(teacher.gender ? Helper.capitalize(teacher.gender) : '-');
                $('#viewEmergencyContactName').text(teacher.emergency_contact_name ?? '-');
                $('#viewEmergencyContactMobile').text(teacher.emergency_contact_mobile ?? '-');

                this.viewModal.show();
            },
            error: (xhr) => {
                Toast.error(xhr.responseJSON?.message ?? 'Unable to load teacher details.');
            }
        });

    },

    /*
    |--------------------------------------------------------------------------
    | Delete Teacher
    |--------------------------------------------------------------------------
    */

    destroy(id) {

        Swal.fire({
            title: 'Delete Teacher?',
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
                url: TEACHER_DELETE_URL.replace(':id', id),
                method: 'POST',
                data: (() => {
                    let formData = new FormData();
                    formData.append('_method', 'DELETE');
                    return formData;
                })(),
                success: (response) => {
                    Toast.success(response.message ?? 'Teacher deleted successfully.');
                    this.table.ajax.reload(null, false);
                }
            });
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
            url: TEACHER_STATUS_URL.replace(':id', id),
            method: 'POST',
            data: (() => {
                let formData = new FormData();
                formData.append('_method', 'PATCH');
                return formData;
            })(),
            success: () => {
                Toast.success('Status updated successfully.');
                this.table.ajax.reload(null, false);
            },
            error: () => {
                element.checked = !element.checked;
            }
        });

    }

};

$(function () {
    Teacher.init();
});