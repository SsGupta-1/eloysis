<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Portal') - {{ config('app.name', 'Eloysis') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --stu-primary: #3b82f6;
            --stu-primary-dark: #1d4ed8;
            --stu-surface: #ffffff;
            --stu-bg: #f8fafc;
            --stu-border: #e2e8f0;
            --stu-text: #0f172a;
            --stu-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--stu-bg);
            color: var(--stu-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .student-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--stu-border);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .nav-link-student {
            color: var(--stu-muted);
            font-weight: 500;
            padding: 0.6rem 0.9rem !important;
            border-radius: 8px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            text-decoration: none;
        }

        .nav-link-student:hover {
            color: var(--stu-primary);
            background: #eff6ff;
        }

        .nav-link-student.active {
            color: var(--stu-primary-dark);
            background: #dbeafe;
            font-weight: 600;
        }

        .portal-card {
            background: var(--stu-surface);
            border: 1px solid var(--stu-border);
            border-radius: 16px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .portal-card:hover {
            box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.07);
        }

        .avatar-circle {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e2e8f0;
        }

        .avatar-placeholder {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .stat-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .badge-soft-primary { background-color: #dbeafe; color: #1e40af; }
        .badge-soft-success { background-color: #dcfce7; color: #166534; }
        .badge-soft-warning { background-color: #fef3c7; color: #92400e; }
        .badge-soft-danger  { background-color: #fee2e2; color: #991b1b; }
        .badge-soft-info    { background-color: #e0f2fe; color: #075985; }
        .badge-soft-purple  { background-color: #ede9fe; color: #5b21b6; }

        .table-custom th {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--stu-muted);
            background-color: #f8fafc;
            border-bottom: 1px solid var(--stu-border);
            padding: 0.75rem 1rem;
        }

        .table-custom td {
            padding: 0.85rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        @media print {
            .student-navbar, .no-print, footer { display: none !important; }
            body { background: white !important; }
            .portal-card { border: none !important; box-shadow: none !important; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Student Portal Navigation -->
    <nav class="navbar navbar-expand-lg student-navbar py-2">
        <div class="container-fluid px-lg-4">
            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('student.dashboard') }}">
                <img src="{{ asset('assets/common/images/logo.png') }}" alt="Logo" height="36" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2236%22 height=%2236%22><rect width=%2236%22 height=%2236%22 rx=%228%22 fill=%22%233b82f6%22/><text x=%2210%22 y=%2224%22 fill=%22white%22 font-family=%22sans-serif%22 font-weight=%22bold%22 font-size=%2216%22>S</text></svg>'">
                <span class="fw-bold text-dark fs-5 mb-0">Student<span class="text-primary">Portal</span></span>
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#studentNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Links & User info -->
            <div class="collapse navbar-collapse" id="studentNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3 gap-1">
                    <li class="nav-item">
                        <a class="nav-link-student {{ Route::is('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">
                            <i class="bi bi-grid-1x2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-student {{ Route::is('student.timetable') ? 'active' : '' }}" href="{{ route('student.timetable') }}">
                            <i class="bi bi-calendar3"></i> Timetable
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-student {{ Route::is('student.attendance') ? 'active' : '' }}" href="{{ route('student.attendance') }}">
                            <i class="bi bi-calendar-check"></i> Attendance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-student {{ Route::is('student.exams*') ? 'active' : '' }}" href="{{ route('student.exams') }}">
                            <i class="bi bi-card-checklist"></i> Exams
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-student {{ Route::is('student.results*') ? 'active' : '' }}" href="{{ route('student.results') }}">
                            <i class="bi bi-award"></i> Results
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-student {{ Route::is('student.fees') ? 'active' : '' }}" href="{{ route('student.fees') }}">
                            <i class="bi bi-wallet2"></i> Fees
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-student {{ Route::is('student.profile') ? 'active' : '' }}" href="{{ route('student.profile') }}">
                            <i class="bi bi-person"></i> Profile
                        </a>
                    </li>
                </ul>

                <!-- User Dropdown & External Website Link -->
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 d-none d-md-inline-flex align-items-center gap-1" target="_blank">
                        <i class="bi bi-box-arrow-up-right"></i> Website
                    </a>

                    @php
                        $authUser = auth('student')->user();
                        $studentProfile = $authUser?->studentProfile;
                        $enrollment = $authUser?->currentEnrollment;
                    @endphp

                    <div class="dropdown">
                        <button class="btn btn-link text-decoration-none p-0 d-flex align-items-center gap-2 dropdown-toggle text-dark" type="button" data-bs-toggle="dropdown">
                            @if($authUser && $authUser->profile_image)
                                <img src="{{ $authUser->profile_image_url }}" alt="{{ $authUser->name }}" class="avatar-circle">
                            @else
                                <div class="avatar-placeholder">
                                    {{ strtoupper(substr($authUser->name ?? 'S', 0, 1)) }}
                                </div>
                            @endif
                            <div class="d-none d-sm-block text-start">
                                <div class="fw-semibold lh-1 text-truncate" style="max-width: 140px;">{{ $authUser->name ?? 'Student' }}</div>
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    {{ $studentProfile?->admission_no ?? 'Student' }}
                                </small>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="border-radius: 12px; min-width: 200px;">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold text-dark">{{ $authUser->name ?? '' }}</div>
                                <div class="small text-muted text-truncate">{{ $authUser->email ?? '' }}</div>
                                @if($enrollment && $enrollment->studentClass)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle mt-1">
                                        {{ $enrollment->studentClass->class_name }} {{ $enrollment->section?->name ? '- '.$enrollment->section->name : '' }}
                                    </span>
                                @endif
                            </li>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('student.profile') }}">
                                    <i class="bi bi-person-circle text-primary"></i> My Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('student.timetable') }}">
                                    <i class="bi bi-calendar-week text-info"></i> Class Timetable
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form action="{{ route('student.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger d-flex align-items-center gap-2">
                                        <i class="bi bi-box-arrow-right"></i> Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow-1 py-4">
        <div class="container-fluid px-lg-4">

            <!-- Flash alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2 mb-1 fw-bold">
                        <i class="bi bi-exclamation-circle-fill"></i> Please check the form errors:
                    </div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-auto py-3 bg-white border-top text-center text-muted small">
        <div class="container">
            &copy; {{ date('Y') }} {{ config('app.name', 'Eloysis') }} - Student Information & Examination System. All rights reserved.
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @stack('scripts')
</body>
</html>
