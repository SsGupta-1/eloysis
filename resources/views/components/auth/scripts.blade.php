{{-- JQuery --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Bootstrap --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

{{-- Common JS --}}
<script src="{{ asset('assets/common/js/helper.js') }}?v={{ time() }}"></script>
<script src="{{ asset('assets/common/js/ajax.js') }}?v={{ time() }}"></script>
<script src="{{ asset('assets/common/js/toast.js') }}?v={{ time() }}"></script>

{{-- Admin JS --}}
<script src="{{ asset('assets/admin/js/theme.js') }}?v={{ time() }}"></script>
<script src="{{ asset('assets/admin/js/auth.js') }}?v={{ time() }}"></script>

{{-- Common Flash Message Component --}}
@include('components.common.flash-message')

<script>
    const BASE_URL = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '';

    $(function () {
        if (typeof Theme !== 'undefined' && Theme.init) {
            Theme.init();
        }
    });
</script>
