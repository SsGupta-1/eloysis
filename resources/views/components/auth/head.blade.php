<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">

    <meta name="description" content="Admin Authentication Portal - {{ config('app.name', 'Eloysis') }}">
    <meta name="author" content="{{ config('app.name', 'Eloysis') }}">

    <title>
        @yield('title', 'Admin Login') | {{ config('app.name', 'Eloysis') }}
    </title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/common/images/favicon.png') }}">

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    {{-- Theme CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/admin/css/theme.css') }}?v={{ time() }}">

    {{-- Auth CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/admin/css/auth.css') }}?v={{ time() }}">

    {{-- Inline Theme Script to prevent flash of wrong theme --}}
    <script>
        (() => {
            const theme = localStorage.getItem('admin_theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    @stack('styles')
</head>
