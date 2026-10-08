/**
 * Common User Permission Manager for Staffs, Teachers, and other User Profiles
 */
const UserPermissionManager = {
    modal: null,
    modalEl: null,
    formEl: null,
    currentData: null,
    currentRoleDefaultIds: [],
    config: {
        modalId: '#userPermissionModal',
        formId: '#userPermissionForm',
        getUrl: '',
        updateUrl: '',
        table: null,
        defaultAvatar: 'https://ui-avatars.com/api/?name=User&background=0284C7&color=fff',
        titlePrefix: 'User'
    },

    moduleIcons: {
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
        'logs': 'bi-file-text',
        'activity_logs': 'bi-shield-check'
    },

    init(options = {}) {
        this.config = Object.assign({}, this.config, options);
        this.modalEl = document.querySelector(this.config.modalId);
        this.formEl = document.querySelector(this.config.formId);

        if (this.modalEl && typeof bootstrap !== 'undefined') {
            this.modal = new bootstrap.Modal(this.modalEl);
        }

        this.bindEvents();
    },

    bindEvents() {
        const self = this;

        // Toggle Permission Mode (Inherit vs Custom)
        $(document).on('change', 'input[name="has_custom_permissions"]', function () {
            const isCustom = $(this).val() === '1';
            self.toggleMode(isCustom);
        });

        // Global Select All in Custom Mode
        $(document).on('change', '#userSelectAllPerms', function () {
            const isChecked = $(this).is(':checked');
            $('.user-perm-checkbox:not(:disabled)').prop('checked', isChecked);
            $('.user-module-select-all:not(:disabled)').prop('checked', isChecked);
            self.updateCounter();
        });

        // Module Select All Switch
        $(document).on('change', '.user-module-select-all', function () {
            const moduleName = $(this).data('module');
            const isChecked = $(this).is(':checked');
            $(`.user-perm-checkbox[data-module="${moduleName}"]:not(:disabled)`).prop('checked', isChecked);
            self.syncSelectAllStates();
            self.updateCounter();
        });

        // Single Permission Checkbox Change
        $(document).on('change', '.user-perm-checkbox', function () {
            const moduleName = $(this).data('module');
            const totalInModule = $(`.user-perm-checkbox[data-module="${moduleName}"]`).length;
            const checkedInModule = $(`.user-perm-checkbox[data-module="${moduleName}"]:checked`).length;
            $(`.user-module-select-all[data-module="${moduleName}"]`).prop('checked', totalInModule > 0 && totalInModule === checkedInModule);

            self.syncSelectAllStates();
            self.updateCounter();
        });

        // Copy Role Defaults button
        $(document).on('click', '#btnCopyRolePerms', function () {
            $('.user-perm-checkbox:not(:disabled)').prop('checked', false);
            self.currentRoleDefaultIds.forEach(id => {
                $(`#user_perm_${id}:not(:disabled)`).prop('checked', true);
            });
            self.syncAllModuleSwitches();
            self.syncSelectAllStates();
            self.updateCounter();
            if (typeof Toast !== 'undefined') {
                Toast.success('Role default permissions applied to checkboxes.');
            }
        });

        // Clear All Permissions button
        $(document).on('click', '#btnClearAllPerms', function () {
            $('.user-perm-checkbox:not(:disabled)').prop('checked', false);
            $('.user-module-select-all:not(:disabled)').prop('checked', false);
            $('#userSelectAllPerms:not(:disabled)').prop('checked', false);
            self.updateCounter();
        });

        // Reset to Role Defaults button (sets mode to Inherit)
        $(document).on('click', '#btnResetToRole', function () {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Reset to Role Defaults?',
                    text: 'Custom permissions will be removed and user will inherit role permissions.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Reset',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#modeInheritRole').prop('checked', true).trigger('change');
                    }
                });
            } else {
                if (confirm('Reset custom permissions to role defaults?')) {
                    $('#modeInheritRole').prop('checked', true).trigger('change');
                }
            }
        });

        // Form Submit
        $(document).on('submit', this.config.formId, function (e) {
            e.preventDefault();
            self.savePermissions();
        });
    },

    open(entityId, customGetUrl = null, customUpdateUrl = null) {
        const self = this;
        const getUrlTemplate = customGetUrl || this.config.getUrl;
        if (!getUrlTemplate) {
            console.error('UserPermissionManager: getUrl is not configured.');
            return;
        }

        const url = getUrlTemplate.replace(':id', entityId);
        this.activeUpdateUrl = (customUpdateUrl || this.config.updateUrl).replace(':id', entityId);

        $('#perm_user_entity_id').val(entityId);
        $('#userPermissionLoading').removeClass('d-none');
        $('#userPermissionContainer').addClass('d-none');
        $('#btnSaveUserPermissions').prop('disabled', true);

        if (this.modal) {
            this.modal.show();
        }

        $.ajax({
            url: url,
            type: 'GET',
            success: (response) => {
                if (!response.status || !response.data) {
                    if (typeof Toast !== 'undefined') Toast.error('Failed to load user permissions.');
                    return;
                }

                const data = response.data;
                self.currentData = data;
                const user = data.user;
                const role = data.role;
                const isCustom = Boolean(data.has_custom_permissions);
                self.currentRoleDefaultIds = data.role_permission_ids || [];
                const activePermIds = isCustom ? (data.direct_permission_ids || []) : self.currentRoleDefaultIds;

                // Populate Header & Info
                $('#userPermissionModalTitle').text(`${self.config.titlePrefix || 'User'} Permissions: ${user.name}`);
                $('#userPermName').text(user.name);
                $('#userPermEmail').text(user.email || 'No email');
                $('#userPermRole').text(`Role: ${role ? role.role_name : 'No Role'}`);
                $('#userPermAvatar').attr('src', user.profile_image_url || self.config.defaultAvatar);

                // Set Radio Mode
                if (isCustom) {
                    $('#modeCustomUser').prop('checked', true);
                } else {
                    $('#modeInheritRole').prop('checked', true);
                }

                // Render Permissions Matrix
                self.renderMatrix(data.grouped_permissions, activePermIds, self.currentRoleDefaultIds, isCustom);

                // Apply Mode Styles & Switch States
                self.toggleMode(isCustom);

                $('#userPermissionLoading').addClass('d-none');
                $('#userPermissionContainer').removeClass('d-none');
                $('#btnSaveUserPermissions').prop('disabled', false);
            },
            error: (xhr) => {
                $('#userPermissionLoading').html('<div class="text-danger py-4"><i class="bi bi-exclamation-triangle fs-3"></i><p class="mt-2">Unable to load permissions matrix.</p></div>');
                if (typeof Toast !== 'undefined') {
                    Toast.error(xhr.responseJSON?.message ?? 'Unable to load user permissions.');
                }
            }
        });
    },

    toggleMode(isCustom) {
        if (isCustom) {
            $('#userPermModeBadge')
                .removeClass('bg-secondary-subtle text-secondary border')
                .addClass('bg-warning-subtle text-warning border border-warning-subtle')
                .text('Customized');
            $('#userCustomControls').removeClass('d-none');
            $('#btnResetToRole').removeClass('d-none');

            $('.user-perm-checkbox').prop('disabled', false);
            $('.user-module-select-all').prop('disabled', false);
            $('#userSelectAllPerms').prop('disabled', false);
        } else {
            $('#userPermModeBadge')
                .removeClass('bg-warning-subtle text-warning border border-warning-subtle')
                .addClass('bg-secondary-subtle text-secondary border')
                .text('Inherited from Role');
            $('#userCustomControls').addClass('d-none');
            $('#btnResetToRole').addClass('d-none');

            // Show role defaults in disabled/view mode
            $('.user-perm-checkbox').prop('checked', false).prop('disabled', true);
            $('.user-module-select-all').prop('checked', false).prop('disabled', true);
            $('#userSelectAllPerms').prop('disabled', true);

            this.currentRoleDefaultIds.forEach(id => {
                $(`#user_perm_${id}`).prop('checked', true);
            });
        }

        this.syncAllModuleSwitches();
        this.syncSelectAllStates();
        this.updateCounter();
    },

    renderMatrix(groupedPermissions, selectedIds, roleDefaultIds, isCustom) {
        const self = this;
        let html = '<div class="row g-3">';

        for (const [moduleName, permissions] of Object.entries(groupedPermissions)) {
            const formattedModuleName = moduleName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            const icon = self.moduleIcons[moduleName] || 'bi-folder';
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

    syncAllModuleSwitches() {
        $('.user-module-select-all').each(function () {
            const moduleName = $(this).data('module');
            const totalInModule = $(`.user-perm-checkbox[data-module="${moduleName}"]`).length;
            const checkedInModule = $(`.user-perm-checkbox[data-module="${moduleName}"]:checked`).length;
            $(this).prop('checked', totalInModule > 0 && totalInModule === checkedInModule);
        });
    },

    syncSelectAllStates() {
        const totalPerms = $('.user-perm-checkbox').length;
        const totalChecked = $('.user-perm-checkbox:checked').length;
        $('#userSelectAllPerms').prop('checked', totalPerms > 0 && totalPerms === totalChecked);
    },

    updateCounter() {
        const totalPerms = $('.user-perm-checkbox').length;
        const totalChecked = $('.user-perm-checkbox:checked').length;
        $('#userPermissionCounter').text(`${totalChecked} / ${totalPerms} Selected`);
    },

    savePermissions() {
        const self = this;
        const url = this.activeUpdateUrl;
        if (!url) {
            console.error('UserPermissionManager: updateUrl is missing.');
            return;
        }

        const btn = $('#btnSaveUserPermissions');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

        if (typeof Ajax !== 'undefined' && typeof Ajax.request === 'function') {
            Ajax.request({
                form: self.config.formId,
                url: url,
                method: 'POST',
                success: (response) => {
                    if (typeof Toast !== 'undefined') {
                        Toast.success(response.message ?? 'User permissions updated successfully.');
                    }
                    if (self.modal) {
                        self.modal.hide();
                    }
                    if (self.config.table && typeof self.config.table.ajax !== 'undefined') {
                        self.config.table.ajax.reload(null, false);
                    }
                },
                complete: () => {
                    btn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> Save Permissions');
                }
            });
        } else {
            const formData = $(self.config.formId).serialize();
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: (response) => {
                    if (typeof Toast !== 'undefined') {
                        Toast.success(response.message ?? 'User permissions updated successfully.');
                    }
                    if (self.modal) {
                        self.modal.hide();
                    }
                    if (self.config.table && typeof self.config.table.ajax !== 'undefined') {
                        self.config.table.ajax.reload(null, false);
                    }
                },
                error: (xhr) => {
                    if (typeof Toast !== 'undefined') {
                        Toast.error(xhr.responseJSON?.message ?? 'Failed to update user permissions.');
                    }
                },
                complete: () => {
                    btn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> Save Permissions');
                }
            });
        }
    }
};
