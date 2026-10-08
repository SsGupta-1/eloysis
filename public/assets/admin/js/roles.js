const Role = {

    modal: null,
    permissionModal: null,
    table: null,

    init() {

        this.modal = new bootstrap.Modal(
            document.getElementById('roleModal')
        );

        this.permissionModal = new bootstrap.Modal(
            document.getElementById('rolePermissionModal')
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

        this.table = $('#roleTable').DataTable({

            processing: true,
            serverSide: true,

            ajax: {
                url: ROLE_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.filter_status = $('#filter_status').val();
                },
                error: function (xhr) {
                    Toast.error(
                        xhr.responseJSON?.message ?? 'Unable to load roles.'
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
                    data: 'role_name',
                    name: 'role_name'
                },
                {
                    data: 'slug',
                    name: 'slug'
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: true,
                    searchable: false,
                    render: function (data, type, row) {
                        const canEdit = typeof window.can === 'function' ? window.can('roles.edit') : true;
                        return Helper.statusSwitch(row.id, row.status, canEdit);
                    }
                },
                {
                    data: null,
                    name: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const canEdit = typeof window.can === 'function' ? window.can('roles.edit') : true;
                        const canDelete = typeof window.can === 'function' ? window.can('roles.delete') : true;

                        let buttons = '';

                        if (canEdit) {
                            buttons += `
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary btn-permissions me-1"
                                    data-id="${row.id}"
                                    title="Manage Permissions">
                                    <i class="bi bi-shield-lock"></i>
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-edit"
                                    data-id="${row.id}"
                                    title="Edit Role">
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
                                    title="Delete Role">
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

        // Add Role
        $('#btnAddRole').on('click', () => {
            this.openCreate();
        });

        // Save Form
        $('#roleForm').on('submit', (e) => {
            e.preventDefault();
            if ($('#role_id').val() == '') {
                this.store();
            } else {
                this.update();
            }
        });

        // Auto Slug
        $('#role_name').on('keyup', () => {
            if ($('#role_id').val() == '') {
                this.generateSlug();
            }
        });

        // Edit (Dynamic Button)
        $(document).on('click', '.btn-edit', (e) => {
            this.edit($(e.currentTarget).data('id'));
        });

        // Permissions (Dynamic Button)
        $(document).on('click', '.btn-permissions', (e) => {
            this.openPermissions($(e.currentTarget).data('id'));
        });

        // Save Permissions Form
        $('#rolePermissionForm').on('submit', (e) => {
            e.preventDefault();
            this.savePermissions();
        });

        // Global Select All switch
        $('#roleSelectAllPerms').on('change', function () {
            const isChecked = $(this).is(':checked');
            $('.role-perm-checkbox').prop('checked', isChecked);
            $('.module-select-all').prop('checked', isChecked);
            Role.updateCounter();
        });

        // Module-level select all switch
        $(document).on('change', '.module-select-all', function () {
            const moduleName = $(this).data('module');
            const isChecked = $(this).is(':checked');
            $(`.role-perm-checkbox[data-module="${moduleName}"]`).prop('checked', isChecked);
            Role.syncSelectAllStates();
            Role.updateCounter();
        });

        // Individual checkbox change
        $(document).on('change', '.role-perm-checkbox', function () {
            const moduleName = $(this).data('module');
            const totalInModule = $(`.role-perm-checkbox[data-module="${moduleName}"]`).length;
            const checkedInModule = $(`.role-perm-checkbox[data-module="${moduleName}"]:checked`).length;
            $(`.module-select-all[data-module="${moduleName}"]`).prop('checked', totalInModule === checkedInModule);
            Role.syncSelectAllStates();
            Role.updateCounter();
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
    | Permissions
    |--------------------------------------------------------------------------
    */

    openPermissions(roleId) {
        $('#perm_role_id').val(roleId);
        $('#rolePermissionLoading').removeClass('d-none');
        $('#rolePermissionContainer').addClass('d-none').empty();
        this.permissionModal.show();

        const url = ROLE_PERMISSIONS_URL.replace(':id', roleId);

        $.ajax({
            url: url,
            type: 'GET',
            success: (response) => {
                const data = response.data;
                const role = data.role;
                const assigned = data.assigned_permissions || [];
                const grouped = data.grouped_permissions || {};

                $('#rolePermissionModalTitle').text(`Manage Permissions - ${role.role_name}`);
                Role.renderPermissionsMatrix(grouped, assigned);
                $('#rolePermissionLoading').addClass('d-none');
                $('#rolePermissionContainer').removeClass('d-none');
                Role.syncSelectAllStates();
                Role.updateCounter();
            },
            error: (xhr) => {
                Toast.error(xhr.responseJSON?.message ?? 'Unable to fetch role permissions.');
                this.permissionModal.hide();
            }
        });
    },

    renderPermissionsMatrix(groupedPermissions, assignedIds) {
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
            const checkedInModule = permissions.filter(p => assignedIds.includes(p.id)).length;
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
                                <input class="form-check-input module-select-all ms-0" 
                                       type="checkbox" 
                                       role="switch" 
                                       data-module="${moduleName}"
                                       ${isAllChecked ? 'checked' : ''}
                                       title="Select all in ${formattedModuleName}">
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex flex-column gap-2">
            `;

            permissions.forEach(perm => {
                const isChecked = assignedIds.includes(perm.id);
                html += `
                    <div class="form-check">
                        <input class="form-check-input role-perm-checkbox" 
                               type="checkbox" 
                               name="permissions[]" 
                               value="${perm.id}" 
                               id="role_perm_${perm.id}"
                               data-module="${moduleName}"
                               ${isChecked ? 'checked' : ''}>
                        <label class="form-check-label small cursor-pointer" for="role_perm_${perm.id}">
                            ${perm.name}
                        </label>
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
        $('#rolePermissionContainer').html(html);
    },

    syncSelectAllStates() {
        const totalPerms = $('.role-perm-checkbox').length;
        const totalChecked = $('.role-perm-checkbox:checked').length;
        $('#roleSelectAllPerms').prop('checked', totalPerms > 0 && totalPerms === totalChecked);
    },

    updateCounter() {
        const totalPerms = $('.role-perm-checkbox').length;
        const totalChecked = $('.role-perm-checkbox:checked').length;
        $('#rolePermissionCounter').text(`${totalChecked} / ${totalPerms} Selected`);
    },

    savePermissions() {
        const roleId = $('#perm_role_id').val();
        const url = ROLE_PERMISSIONS_UPDATE_URL.replace(':id', roleId);

        Ajax.request({
            form: '#rolePermissionForm',
            url: url,
            method: 'POST',
            success: (response) => {
                Toast.success(response.message ?? 'Role permissions updated successfully.');
                this.permissionModal.hide();
            }
        });
    },

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    openCreate() {

        $('#roleForm')[0].reset();
        Helper.clearErrors('#roleForm');
        $('#role_id').val('');
        $('#roleModalTitle').text('Add Role');
        $('#btnSaveRole').html(
            '<i class="bi bi-check-lg"></i> Save Role'
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
            form: '#roleForm',
            url: ROLE_STORE_URL,
            method: 'POST',
            success: (response) => {
                this.modal.hide();
                $('#roleForm')[0].reset();
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
            form: '#roleForm',
            url: ROLE_EDIT_URL.replace(':id', id),
            method: 'GET',
            success: (response) => {
                const role = response.data;

                Helper.clearErrors('#roleForm');
                $('#role_id').val(role.id);
                $('#role_name').val(role.role_name);
                $('#slug').val(role.slug);

                $('#roleModalTitle').text('Edit Role');
                $('#btnSaveRole').html(
                    '<i class="bi bi-check-lg"></i> Update Role'
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
        const id = $('#role_id').val();
        let url = ROLE_UPDATE_URL.replace(':id', id);

        Ajax.request({
            form: '#roleForm',
            url: url,
            method: 'POST',
            extraData: {
                _method: 'PUT'
            },
            success: (response) => {
                this.modal.hide();
                $('#roleForm')[0].reset();
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
            title: 'Delete Role?',
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
                url: ROLE_DELETE_URL.replace(':id', id),
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
            url: ROLE_STATUS_URL.replace(':id', id),
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

    },

    generateSlug() {
        const role_name = $('#role_name').val();
        const slug = role_name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        $('#slug').val(slug);
    }

};

$(function () {
    Role.init();
});