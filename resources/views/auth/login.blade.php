<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">

    <title>Admin Login | {{ config('app.name', 'Eloysis') }}</title>

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

    {{-- Inline Theme Init to prevent theme flash --}}
    <script>
        (() => {
            const theme = localStorage.getItem('admin_theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>

<body class="auth-page">

    {{-- Dynamic Glowing Ambient Shapes --}}
    <div class="auth-bg-shapes">
        <div class="auth-shape auth-shape-1"></div>
        <div class="auth-shape auth-shape-2"></div>
        <div class="auth-shape auth-shape-3"></div>
    </div>

    {{-- Top Action Bar (Theme Toggle) --}}
    <div class="auth-top-bar">
        <button type="button"
                id="themeToggle"
                class="auth-top-btn"
                title="Toggle Theme"
                aria-label="Toggle Theme">
            <i id="themeToggleIcon" class="bi bi-moon-stars"></i>
        </button>
    </div>

    {{-- Main Container --}}
    <div class="auth-container">
        <div class="auth-card" id="loginCard">

            {{-- Brand Section --}}
            <div class="auth-brand">
                <div class="auth-logo-wrapper">
                    <img src="{{ asset('assets/common/images/logo.png') }}"
                         alt="{{ config('app.name', 'Eloysis') }}"
                         class="auth-logo">
                </div>
                <div>
                    <span class="auth-badge">
                        <span class="auth-badge-dot"></span>
                        Admin Control Portal
                    </span>
                </div>
                <h1 class="auth-title">Welcome Back</h1>
                <p class="auth-subtitle">Sign in to manage your system and dashboard</p>
            </div>

            {{-- Alert Banner for Instant Visual Feedback --}}
            <div id="loginAlert" class="auth-alert" role="alert"></div>

            {{-- Login Form --}}
            <form id="loginForm"
                  action="{{ route('admin.login.submit') }}"
                  method="POST"
                  novalidate>
                @csrf

                {{-- Email / Username Input --}}
                <div class="form-group-custom">
                    <label for="email" class="form-label-custom">Email or Username <span class="text-danger">*</span></label>
                    <div class="input-wrapper">
                        <i class="bi bi-person-fill input-icon-left"></i>
                        <input type="text"
                               id="email"
                               name="email"
                               class="form-control-custom"
                               placeholder="Enter your email or username"
                               autocomplete="username"
                               required
                               autofocus>
                    </div>
                </div>

                {{-- Password Input --}}
                <div class="form-group-custom">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label-custom mb-0">Password <span class="text-danger">*</span></label>
                    </div>
                    <div class="input-wrapper">
                        <i class="bi bi-shield-lock-fill input-icon-left"></i>
                        <input type="password"
                               id="password"
                               name="password"
                               class="form-control-custom has-right-icon"
                               placeholder="Enter your password"
                               autocomplete="current-password"
                               required>
                        <button type="button"
                                class="input-toggle-btn toggle-password-btn"
                                data-target="#password"
                                aria-label="Toggle password visibility"
                                title="Show / Hide Password">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                    </div>
                </div>

                {{-- Options Row --}}
                <div class="auth-options">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="remember" id="remember" value="1">
                        <span>Remember me</span>
                    </label>
                    <a href="javascript:void(0)"
                       class="auth-link"
                       onclick="Swal.fire({
                           icon: 'info',
                           title: 'Password Reset',
                           text: 'Please contact your Super Administrator to reset your admin login credentials.',
                           confirmButtonColor: '#2563eb'
                       });">
                        Forgot password?
                    </a>
                </div>

                {{-- Submit Button with Dynamic State --}}
                <button type="submit"
                        id="loginSubmitBtn"
                        class="btn-auth-submit">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Sign In to Account</span>
                </button>
            </form>

            {{-- Footer Security Note --}}
            <div class="auth-footer">
                <div class="security-badge">
                    <i class="bi bi-shield-check"></i>
                    <span>256-Bit Encrypted Secure Session</span>
                </div>
            </div>

        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Common & Admin JS --}}
    <script src="{{ asset('assets/common/js/helper.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/common/js/toast.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/admin/js/theme.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/admin/js/auth.js') }}?v={{ time() }}"></script>

    @include('components.common.flash-message')

    <script>
        $(function () {
            if (typeof Theme !== 'undefined' && Theme.init) {
                Theme.init();
            }
        });
    </script>
</body>

</html>