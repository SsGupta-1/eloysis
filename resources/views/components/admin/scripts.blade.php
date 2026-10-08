
{{-- JQuery --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Bootstrap --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

{{-- Common JS --}}
<script src="{{ asset('assets/common/js/helper.js') }}?v={{ time() }}"></script>
<script src="{{ asset('assets/common/js/ajax.js') }}?v={{ time() }}"></script>
<script src="{{ asset('assets/common/js/toast.js') }}?v={{ time() }}"></script>

{{-- Admin JS --}}
<script src="{{ asset('assets/admin/js/sidebar.js') }}?v={{ time() }}"></script>
<script src="{{ asset('assets/admin/js/app.js') }}?v={{ time() }}"></script>
<script src="{{ asset('assets/admin/js/auth.js') }}?v={{ time() }}"></script>
<script src="{{ asset('assets/admin/js/theme.js') }}?v={{ time() }}"></script>


<script> 

    const BASE_URL = document
        .querySelector('meta[name="base-url"]')
        ?.getAttribute('content') || '';

    @php
        $authUser = auth('admin')->user() ?? auth('web')->user();
        $authPermissions = $authUser ? $authUser->permissionSlugs() : [];
        $isSuperAdminUser = $authUser ? ($authUser->isSuperAdmin() && ! $authUser->has_custom_permissions) : false;
    @endphp

    window.UserPermissions = @json($authPermissions);
    window.isSuperAdmin = @json($isSuperAdminUser);

    window.can = function(permission) {
        if (window.isSuperAdmin) {
            return true;
        }
        return Array.isArray(window.UserPermissions) && window.UserPermissions.includes(permission);
    };

    window.canAny = function(permissions) {
        if (window.isSuperAdmin) {
            return true;
        }
        const list = Array.isArray(permissions) ? permissions : (permissions || '').split('|').map(p => p.trim());
        return list.some(p => Array.isArray(window.UserPermissions) && window.UserPermissions.includes(p));
    };

</script>