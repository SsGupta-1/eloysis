<x-ui.modal
    id="rolePermissionModal"
    size="xl">

    <x-slot:title>
        <div class="d-flex align-items-center justify-content-between w-100 pe-3">
            <div>
                <i class="bi bi-shield-check text-primary me-2"></i>
                <span id="rolePermissionModalTitle">Manage Role Permissions</span>
            </div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6" id="rolePermissionCounter">
                0 / 0 Selected
            </span>
        </div>
    </x-slot:title>

    <form id="rolePermissionForm" autocomplete="off">
        @csrf
        <input type="hidden" id="perm_role_id" name="role_id">

        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div class="form-check form-switch ps-0 d-flex align-items-center gap-2">
                <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="roleSelectAllPerms">
                <label class="form-check-label fw-bold cursor-pointer" for="roleSelectAllPerms">
                    Grant All Permissions
                </label>
            </div>
            <div class="text-muted small">
                Configure default permissions inherited by users assigned to this role.
            </div>
        </div>

        <div id="rolePermissionLoading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-2">Loading permissions matrix...</p>
        </div>

        <div id="rolePermissionContainer" class="d-none">
            {{-- Dynamic Modules Rendered by JS --}}
        </div>

        <div class="text-end mt-4 pt-3 border-top">
            <x-ui.button
                type="button"
                variant="secondary"
                data-bs-dismiss="modal">
                Cancel
            </x-ui.button>

            <x-ui.button
                type="submit"
                id="btnSaveRolePermissions"
                icon="bi-check-lg">
                Save Permissions
            </x-ui.button>
        </div>
    </form>
</x-ui.modal>
