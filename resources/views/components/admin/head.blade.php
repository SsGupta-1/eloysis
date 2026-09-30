<head>

    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">

    <meta name="description" content="Student Examination Management System">

    <meta name="author" content="{{ config('app.name') }}">

    <title>
        @yield('title', 'Dashboard') | {{ config('app.name') }}
    </title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png"
        href="{{ asset('assets/common/images/favicon.png') }}">

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css">

    {{-- Admin CSS --}}
    <link rel="stylesheet"
        href="{{ asset('assets/admin/css/app.css') }}?v={{ time() }}">

    <link rel="stylesheet"
        href="{{ asset('assets/admin/css/sidebar.css') }}?v={{ time() }}">

    <link rel="stylesheet"
        href="{{ asset('assets/admin/css/responsive.css') }}?v={{ time() }}">

    <script>

        (() => {

            const theme =
                localStorage.getItem('admin_theme')
                || 'light';

            document.documentElement
                .setAttribute(
                    'data-theme',
                    theme
                );

        })();

    </script>
    
    {{-- Theme CSS --}}
    <link rel="stylesheet"
        href="{{ asset('assets/admin/css/theme.css') }}?v={{ time() }}">

    {{-- Page CSS --}}
    @stack('styles')


</head>