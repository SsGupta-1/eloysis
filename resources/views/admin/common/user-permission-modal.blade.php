<x-ui.modal
    id="userPermissionModal"
    size="xl">

    <x-slot:title>
        <div class="d-flex align-items-center justify-content-between w-100 pe-3">
            <div>
                <i class="bi bi-person-lock text-primary me-2"></i>
                <span id="userPermissionModalTitle">User Permissions</span>
            </div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6" id="userPermissionCounter">
                0 / 0 Selected
            </span>
        </div>
    </x-slot:title>

    <form id="userPermissionForm" autocomplete="off">
        @csrf
        <input type="hidden" id="perm_user_entity_id" name="entity_id">

        {{-- User Info Banner & Permission Mode Selector --}}
        <div class="card bg-light-subtle border mb-3">
            <div class="card-body p-3">
                <div class="row align-items-center gy-3">
                    <div class="col-md-5 border-end-md">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-wrapper">
                                <img id="userPermAvatar" src="" class="rounded-circle border" width="48" height="48" style="object-fit: cover;">
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold" id="userPermName">-</h6>
                                <div class="text-muted small" id="userPermEmail">-</div>
                                <div class="mt-1 d-flex align-items-center gap-1">
                                    <span class="badge bg-secondary-subtle text-secondary border" id="userPermRole">Role: -</span>
                                    <span class="badge" id="userPermModeBadge">Inherited</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <label class="form-label fw-semibold mb-2">Permission Configuration Mode:</label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="has_custom_permissions" id="modeInheritRole" value="0">
                                <label class="form-check-label cursor-pointer" for="modeInheritRole">
                                    <strong>Inherit from Role</strong>
                                    <span class="d-block text-muted small">User gets all default permissions of their role</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="has_custom_permissions" id="modeCustomUser" value="1">
                                <label class="form-check-label cursor-pointer" for="modeCustomUser">
                                    <strong>Customize for this User</strong>
                                    <span class="d-block text-muted small">Override and customize individual permissions</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Custom Permission Controls (Visible when custom mode is active) --}}
        <div id="userCustomControls" class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div class="d-flex align-items-center gap-3">
                <div class="form-check form-switch ps-0 d-flex align-items-center gap-2">
                    <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="userSelectAllPerms">
                    <label class="form-check-label fw-bold cursor-pointer" for="userSelectAllPerms">
                        Select All
                    </label>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCopyRolePerms" title="Load role default permissions into custom checkboxes">
                    <i class="bi bi-copy me-1"></i> Copy Role Defaults
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" id="btnClearAllPerms" title="Uncheck all permissions">
                    <i class="bi bi-x-circle me-1"></i> Clear All
                </button>
            </div>
        </div>

        <div id="userPermissionLoading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-2">Loading user permissions matrix...</p>
        </div>

        <div id="userPermissionContainer" class="d-none">
            {{-- Dynamic Modules Rendered by JS --}}
        </div>

        <div class="text-end mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-outline-secondary" id="btnResetToRole">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset to Role Defaults
            </button>

            <div class="d-flex gap-2">
                <x-ui.button
                    type="button"
                    variant="secondary"
                    data-bs-dismiss="modal">
                    Cancel
                </x-ui.button>

                <x-ui.button
                    type="submit"
                    id="btnSaveUserPermissions"
                    icon="bi-check-lg">
                    Save Permissions
                </x-ui.button>
            </div>
        </div>
    </form>
</x-ui.modal>
