<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Portal Login - {{ config('app.name', 'Eloysis') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient glow circles */
        .ambient-glow-1 {
            position: absolute;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.25) 0%, rgba(59, 130, 246, 0) 70%);
            top: -100px;
            right: -100px;
            pointer-events: none;
        }

        .ambient-glow-2 {
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.2) 0%, rgba(99, 102, 241, 0) 70%);
            bottom: -80px;
            left: -80px;
            pointer-events: none;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.98);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);
            max-width: 460px;
            width: 100%;
            position: relative;
            z-index: 10;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }

        .btn-student-login {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border: none;
            color: #ffffff;
            font-weight: 600;
            padding: 0.85rem;
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .btn-student-login:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            box-shadow: 0 8px 20px -4px rgba(37, 99, 235, 0.5);
            color: #ffffff;
            transform: translateY(-1px);
        }

        .info-pill {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            color: #475569;
        }
    </style>
</head>
<body>

    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <div class="login-card p-4 p-sm-5">
        <!-- Logo & Header -->
        <div class="text-center mb-4">
            <a href="{{ route('home') }}" class="d-inline-block mb-3">
                <img src="{{ asset('assets/common/images/logo.png') }}" alt="Logo" height="52" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2252%22 height=%2252%22><rect width=%2252%22 height=%2252%22 rx=%2212%22 fill=%22%233b82f6%22/><text x=%2216%22 y=%2234%22 fill=%22white%22 font-family=%22sans-serif%22 font-weight=%22bold%22 font-size=%2222%22>S</text></svg>'">
            </a>
            <h3 class="fw-bold text-dark mb-1">Student Portal</h3>
            <p class="text-muted small mb-0">Sign in to check your exams, attendance, results & timetable</p>
        </div>

        <!-- Flash messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 py-2 small" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 py-2 small" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('student.login.submit') }}" method="POST" id="studentLoginForm">
            @csrf

            <!-- Login Input (Email / Admission No / Mobile) -->
            <div class="mb-3">
                <label for="loginInput" class="form-label small fw-semibold text-secondary">
                    Admission No / Email / Mobile
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="bi bi-person-badge"></i>
                    </span>
                    <input
                        type="text"
                        name="login"
                        id="loginInput"
                        class="form-control border-start-0 ps-0 @error('login') is-invalid @enderror"
                        placeholder="e.g. Adm01 or your email"
                        value="{{ old('login') }}"
                        required
                        autofocus
                    >
                </div>
                @error('login')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="passwordInput" class="form-label small fw-semibold text-secondary mb-0">
                        Password
                    </label>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input
                        type="password"
                        name="password"
                        id="passwordInput"
                        class="form-control border-start-0 border-end-0 ps-0 @error('password') is-invalid @enderror"
                        placeholder="Enter your password"
                        required
                    >
                    <button class="btn btn-outline-secondary border-start-0 bg-light text-muted" type="button" id="togglePassword">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remember me -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                    <label class="form-check-label small text-muted" for="rememberMe">
                        Remember me
                    </label>
                </div>
                <span class="badge bg-light text-secondary border">Default Pass: 12345678</span>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-student-login w-100 mb-3" id="btnSubmitLogin">
                <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Portal
            </button>

            <!-- Info card -->
            <div class="info-pill d-flex align-items-start gap-2 mb-3">
                <i class="bi bi-info-circle-fill text-primary fs-6 mt-0"></i>
                <div style="font-size: 0.8rem; line-height: 1.35;">
                    Students can sign in using their <strong>Admission Number</strong>, registered <strong>Email</strong>, or <strong>Mobile Number</strong>.
                </div>
            </div>

            <!-- Back to website -->
            <div class="text-center pt-2">
                <a href="{{ route('home') }}" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i> Return to School Website
                </a>
            </div>
        </form>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password toggle
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('passwordInput');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function() {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>
