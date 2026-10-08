const Staff = {

    modal: null,
    viewModal: null,
    permissionModal: null,
    table: null,
    currentRoleDefaultIds: [],

    init() {

        this.modal = new bootstrap.Modal(
            document.getElementById('staffModal')
        );

        this.viewModal = new bootstrap.Modal(
            document.getElementById('staffViewModal')
        );

        this.permissionModal = new bootstrap.Modal(
            document.getElementById('userPermissionModal')
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

        this.table = $('#staffTable').DataTable({

            processing: true,
            serverSide: true,

            ajax: {
                url: STAFF_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.filter_status = $('#filter_status').val();
                },
                error: function (xhr) {
                    Toast.error(
                        xhr.responseJSON?.message ?? 'Unable to load admins.'
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
                            ? '<span class="badge bg-warning-subtle text-warning border border-warning-subtle ms-1" style="font-size:10px;">Custom Perms</span>'
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
                                    <small class="text-muted">
                                        ${user.email ?? '-'}
                                    </small>
                                </div>
                            </div>
                        `;
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
                    data: 'employee_id',
                    name: 'employee_id',
                    render: function (data, type, row) {
                        return row.employee_id ?? '-';
                    }
                },
                {
                    data: 'designation',
                    name: 'designation',
                    render: function (data, type, row) {
                        return row.designation ?? '-';
                    }
                },
                {
                    data: 'department',
                    name: 'department',
                    render: function (data, type, row) {
                        return row.department ?? '-';
                    }
                },
                {
                    data: 'user.status',
                    name: 'status',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const canEdit = typeof window.can === 'function' ? window.can('admins.edit') : true;
                        return Helper.statusSwitch(row.id, row.user?.status, canEdit);
                    }
                },
                {
                    data: null,
                    name: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const canEdit = typeof window.can === 'function' ? window.can('admins.edit') : true;
                        const canDelete = typeof window.can === 'function' ? window.can('admins.delete') : true;
                        const canView = typeof window.can === 'function' ? window.can('admins.view') : true;

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
                                    title="Edit Admin">
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
                                    title="Delete Admin">
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

        // Add Staff
        $('#btnAddStaff').on('click', () => {
            this.openCreate();
        });

        // Save Form
        $('#staffForm').on('submit', (e) => {
            e.preventDefault();
            this.save();
        });

        // Edit
        $(document).on('click', '.btn-edit', (e) => {
            this.edit($(e.currentTarget).data('id'));
        });

        // View
        $(document).on('click', '.btn-view', (e) => {
            this.view($(e.currentTarget).data('id'));
        });

        // Permissions
        $(document).on('click', '.btn-permissions', (e) => {
            this.openPermissions($(e.currentTarget).data('id'));
        });

        // Save User Permissions Form
        $('#userPermissionForm').on('submit', (e) => {
            e.preventDefault();
            this.savePermissions();
        });

        // Permission Mode Change (Inherit vs Custom)
        $('input[name="has_custom_permissions"]').on('change', function () {
            Staff.handleModeChange($(this).val() === '1');
        });

        // Copy Role Defaults button
        $('#btnCopyRolePerms').on('click', function () {
            $('.user-perm-checkbox').prop('checked', false);
            Staff.currentRoleDefaultIds.forEach(id => {
                $(`#user_perm_${id}`).prop('checked', true);
            });
            Staff.syncUserSelectAllStates();
            Staff.updateUserCounter();
            Toast.success('Copied role default permissions into custom matrix.');
        });

        // Clear All button
        $('#btnClearAllPerms').on('click', function () {
            $('.user-perm-checkbox').prop('checked', false);
            $('.user-module-select-all').prop('checked', false);
            $('#userSelectAllPerms').prop('checked', false);
            Staff.updateUserCounter();
        });

        // Reset to Role button
        $('#btnResetToRole').on('click', function () {
            Swal.fire({
                title: 'Reset to Role Defaults?',
                text: 'This will remove all user-specific overrides and revert back to inheriting permissions from the role.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Reset',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#modeInheritRole').prop('checked', true).trigger('change');
                    Staff.savePermissions();
                }
            });
        });

        // Global Select All switch for user perms
        $('#userSelectAllPerms').on('change', function () {
            const isChecked = $(this).is(':checked');
            $('.user-perm-checkbox').prop('checked', isChecked);
            $('.user-module-select-all').prop('checked', isChecked);
            Staff.updateUserCounter();
        });

        // Module-level select all switch
        $(document).on('change', '.user-module-select-all', function () {
            const moduleName = $(this).data('module');
            const isChecked = $(this).is(':checked');
            $(`.user-perm-checkbox[data-module="${moduleName}"]`).prop('checked', isChecked);
            Staff.syncUserSelectAllStates();
            Staff.updateUserCounter();
        });

        // Individual checkbox change
        $(document).on('change', '.user-perm-checkbox', function () {
            const moduleName = $(this).data('module');
            const totalInModule = $(`.user-perm-checkbox[data-module="${moduleName}"]`).length;
            const checkedInModule = $(`.user-perm-checkbox[data-module="${moduleName}"]:checked`).length;
            $(`.user-module-select-all[data-module="${moduleName}"]`).prop('checked', totalInModule === checkedInModule);
            Staff.syncUserSelectAllStates();
            Staff.updateUserCounter();
        });

        // Delete
        $(document).on('click', '.btn-delete', (e) => {
            this.delete($(e.currentTarget).data('id'));
        });

        // Status Toggle
        $(document).on('change', '.btn-status', (e) => {
            this.toggleStatus($(e.currentTarget).data('id'), e.currentTarget);
        });

        // Status Filter
        $('#filter_status').on('change', () => {
            this.table.ajax.reload();
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
    | User Permissions Management
    |--------------------------------------------------------------------------
    */

    openPermissions(staffId) {
        $('#perm_staff_id').val(staffId);
        $('#userPermissionLoading').removeClass('d-none');
        $('#userPermissionContainer').addClass('d-none').empty();
        this.permissionModal.show();

        const url = STAFF_PERMISSIONS_URL.replace(':id', staffId);

        $.ajax({
            url: url,
            type: 'GET',
            success: (response) => {
                const data = response.data;
                const user = data.user;
                const role = data.role || {};
                const hasCustom = data.has_custom_permissions;
                const rolePermIds = data.role_permission_ids || [];
                const directPermIds = data.direct_permission_ids || [];
                const grouped = data.grouped_permissions || {};

                Staff.currentRoleDefaultIds = rolePermIds;

                $('#userPermAvatar').attr('src', user.profile_image_url || DEFAULT_AVATAR);
                $('#userPermName').text(user.name);
                $('#userPermEmail').text(user.email ?? '-');
                $('#userPermRole').text(`Role: ${role.role_name || 'Admin'}`);

                if (hasCustom) {
                    $('#modeCustomUser').prop('checked', true);
                } else {
                    $('#modeInheritRole').prop('checked', true);
                }

                Staff.renderUserPermissionsMatrix(grouped, hasCustom ? directPermIds : rolePermIds, rolePermIds, hasCustom);
                Staff.handleModeChange(hasCustom);

                $('#userPermissionLoading').addClass('d-none');
                $('#userPermissionContainer').removeClass('d-none');
            },
            error: (xhr) => {
                Toast.error(xhr.responseJSON?.message ?? 'Unable to fetch user permissions.');
                this.permissionModal.hide();
            }
        });
    },

    handleModeChange(isCustom) {
        if (isCustom) {
            $('#userPermModeBadge')
                .removeClass('bg-secondary-subtle text-secondary border')
                .addClass('bg-warning-subtle text-warning border border-warning-subtle')
                .text('Customized');
            $('#userCustomControls').removeClass('d-none');
            $('.user-perm-checkbox').prop('disabled', false);
            $('.user-module-select-all').prop('disabled', false);
            $('#userSelectAllPerms').prop('disabled', false);
            $('#btnResetToRole').removeClass('d-none');
        } else {
            $('#userPermModeBadge')
                .removeClass('bg-warning-subtle text-warning border border-warning-subtle')
                .addClass('bg-secondary-subtle text-secondary border')
                .text('Inherited from Role');
            $('#userCustomControls').addClass('d-none');

            // Reset checkboxes to show role defaults in disabled/view mode
            $('.user-perm-checkbox').prop('checked', false).prop('disabled', true);
            $('.user-module-select-all').prop('checked', false).prop('disabled', true);
            $('#userSelectAllPerms').prop('disabled', true);
            Staff.currentRoleDefaultIds.forEach(id => {
                $(`#user_perm_${id}`).prop('checked', true);
            });
            $('#btnResetToRole').addClass('d-none');
        }

        Staff.syncUserSelectAllStates();
        Staff.updateUserCounter();
    },

    renderUserPermissionsMatrix(groupedPermissions, selectedIds, roleDefaultIds, isCustom) {
        let html = '<div class="row g-3">';

        const moduleIcons = {
            'dashboard': 'bi-speedometer2',
            'admins': 'bi-person-gear',
            'roles': 'bi-shield-lock',
            'academic_sessions': 'bi-calendar3',
            'classes': 'bi-building',
            'sections': 'bi-diagram-3',
            'class_sections': 'bi-diagram-2',
            'subjects': 'bi-book',
            'class_subjects': 'bi-journal-bookmark',
            'teachers': 'bi-person-workspace',
            'teacher_subjects': 'bi-person-video2',
            'teacher_attendance': 'bi-calendar-check',
            'periods': 'bi-clock-history',
            'class_timetables': 'bi-calendar-week',
            'students': 'bi-people',
            'student_promotions': 'bi-mortarboard',
            'attendance': 'bi-check2-circle',
            'admission_enquiry': 'bi-person-lines-fill',
            'questions': 'bi-patch-question',
            'question_papers': 'bi-file-earmark-text',
            'exams': 'bi-journal-text',
            'results': 'bi-award',
            'fees': 'bi-cash-stack',
            'website': 'bi-globe',
            'logs': 'bi-file-text'
        };

        for (const [moduleName, permissions] of Object.entries(groupedPermissions)) {
            const formattedModuleName = Helper.capitalize(moduleName.replace(/_/g, ' '));
            const icon = moduleIcons[moduleName] || 'bi-folder';
            const totalInModule = permissions.length;
            const checkedInModule = permissions.filter(p => selectedIds.includes(p.id)).length;
            const isAllChecked = totalInModule > 0 && totalInModule === checkedInModule;

            html += `
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border shadow-none" style="background-color: var(--bs-body-bg);">
                        <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center bg-light-subtle border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi ${icon} text-primary"></i>
                                <span class="fw-bold fs-7 text-uppercase">${formattedModuleName}</span>
                            </div>
                            <div class="form-check form-switch m-0 ps-0">
                                <input class="form-check-input user-module-select-all ms-0" 
                                       type="checkbox" 
                                       role="switch" 
                                       data-module="${moduleName}"
                                       ${isAllChecked ? 'checked' : ''}
                                       ${!isCustom ? 'disabled' : ''}
                                       title="Select all in ${formattedModuleName}">
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex flex-column gap-2">
            `;

            permissions.forEach(perm => {
                const isChecked = selectedIds.includes(perm.id);
                const isRoleDefault = roleDefaultIds.includes(perm.id);
                const roleBadge = isRoleDefault
                    ? '<span class="badge bg-light text-muted border ms-1" style="font-size:9px;" title="Included in Role Defaults">Role</span>'
                    : '';

                html += `
                    <div class="form-check d-flex align-items-center justify-content-between">
                        <div>
                            <input class="form-check-input user-perm-checkbox" 
                                   type="checkbox" 
                                   name="permissions[]" 
                                   value="${perm.id}" 
                                   id="user_perm_${perm.id}"
                                   data-module="${moduleName}"
                                   ${isChecked ? 'checked' : ''}
                                   ${!isCustom ? 'disabled' : ''}>
                            <label class="form-check-label small cursor-pointer ms-1" for="user_perm_${perm.id}">
                                ${perm.name}
                            </label>
                        </div>
                        ${roleBadge}
                    </div>
                `;
            });

            html += `
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        html += '</div>';
        $('#userPermissionContainer').html(html);
    },

    syncUserSelectAllStates() {
        const totalPerms = $('.user-perm-checkbox').length;
        const totalChecked = $('.user-perm-checkbox:checked').length;
        $('#userSelectAllPerms').prop('checked', totalPerms > 0 && totalPerms === totalChecked);
    },

    updateUserCounter() {
        const totalPerms = $('.user-perm-checkbox').length;
        const totalChecked = $('.user-perm-checkbox:checked').length;
        $('#userPermissionCounter').text(`${totalChecked} / ${totalPerms} Selected`);
    },

    savePermissions() {
        const staffId = $('#perm_staff_id').val();
        const url = STAFF_PERMISSIONS_UPDATE_URL.replace(':id', staffId);

        Ajax.request({
            form: '#userPermissionForm',
            url: url,
            method: 'POST',
            success: (response) => {
                Toast.success(response.message ?? 'User permissions updated successfully.');
                this.permissionModal.hide();
                this.table.ajax.reload(null, false);
            }
        });
    },

    /*
    |--------------------------------------------------------------------------
    | Open Create Modal
    |--------------------------------------------------------------------------
    */

    openCreate() {

        $('#staffForm')[0].reset();
        $('#staff_id').val('');
        $('#staffModalLabel').text('Add Admin');
        $('#profilePreview').attr('src', DEFAULT_AVATAR);

        Helper.clearErrors($('#staffForm'));
        this.modal.show();

    },

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    edit(id) {

        const url = STAFF_SHOW_URL.replace(':id', id);

        $.ajax({
            url: url,
            type: 'GET',
            success: (response) => {
                const staff = response.data;
                const user = staff.user;

                $('#staff_id').val(staff.id);
                $('#name').val(user.name);
                $('#email').val(user.email);
                $('#mobile').val(user.mobile);
                $('#employee_id').val(staff.employee_id);
                $('#designation').val(staff.designation);
                $('#department').val(staff.department);
                $('#joining_date').val(staff.joining_date);
                $('#status').val(user.status ? 1 : 0);
                $('#address').val(staff.address);
                $('#city').val(staff.city);
                $('#state').val(staff.state);
                $('#pincode').val(staff.pincode);
                $('#password').val('');
                $('#password_confirmation').val('');
                $('#profilePreview').attr('src', user.profile_image_url ?? DEFAULT_AVATAR);
                $('#dob').val(staff.dob ? staff.dob.substring(0, 10) : '');
                $('#gender').val(staff.gender);

                $('#staffModalLabel').text('Edit Admin');
                this.modal.show();
            },
            error: (xhr) => {
                Toast.error(xhr.responseJSON?.message ?? 'Unable to load admin.');
            }
        });

    },

    /*
    |--------------------------------------------------------------------------
    | Save
    |--------------------------------------------------------------------------
    */

    save() {

        const id = $('#staff_id').val();
        const isEdit = id !== '';

        const url = isEdit
            ? STAFF_UPDATE_URL.replace(':id', id)
            : STAFF_STORE_URL;

        Ajax.request({
            form: '#staffForm',
            url: url,
            method: 'POST',
            extraData: isEdit ? { _method: 'PUT' } : {},
            success: (response) => {
                this.modal.hide();
                $('#staffForm')[0].reset();
                Toast.success(
                    response.message ?? (isEdit ? 'Admin updated successfully.' : 'Admin created successfully.')
                );
                this.table.ajax.reload(null, false);
            }
        });

    },

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    delete(id) {

        Swal.fire({
            title: 'Delete Admin?',
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
                url: STAFF_DELETE_URL.replace(':id', id),
                method: 'POST',
                data: (() => {
                    let formData = new FormData();
                    formData.append('_method', 'DELETE');
                    return formData;
                })(),
                success: (response) => {
                    Toast.success(response.message ?? 'Admin deleted successfully.');
                    this.table.ajax.reload(null, false);
                }
            });
        });

    },

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    toggleStatus(id, element) {

        const url = STAFF_STATUS_URL.replace(':id', id);

        $.ajax({
            url: url,
            type: 'PATCH',
            success: (response) => {
                Toast.success(response.message ?? 'Status updated successfully.');
                this.table.ajax.reload(null, false);
            },
            error: () => {
                $(element).prop('checked', !$(element).prop('checked'));
                Toast.error('Unable to update status.');
            }
        });

    },

    /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

    view(id) {

        const url = STAFF_SHOW_URL.replace(':id', id);

        $.ajax({
            url: url,
            type: 'GET',
            success: (response) => {
                const staff = response.data;
                const user = staff.user;

                $('#viewName').text(user.name);
                $('#viewEmail').text(user.email ?? '-');
                $('#viewMobile').text(user.mobile ?? '-');
                $('#viewEmployeeId').text(staff.employee_id ?? '-');
                $('#viewEmployee').text(staff.employee_id ?? '-');
                $('#viewDesignation').text(staff.designation ?? '-');
                $('#viewDepartment').text(staff.department ?? '-');
                $('#viewJoiningDate').text(staff.joining_date ?? '-');

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

                $('#viewDob').text(Helper.formatDate(staff.dob));
                $('#viewCity').text(staff.city ?? '-');
                $('#viewState').text(staff.state ?? '-');
                $('#viewPincode').text(staff.pincode ?? '-');
                $('#viewAddress').text(staff.address ?? '-');
                $('#viewGender').text(staff.gender ? Helper.capitalize(staff.gender) : '-');

                this.viewModal.show();
            },
            error: (xhr) => {
                Toast.error(xhr.responseJSON?.message ?? 'Unable to load admin details.');
            }
        });

    }

};

$(function () {
    Staff.init();
});