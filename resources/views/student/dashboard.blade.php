@extends('layouts.student.master')

@section('title', 'Student Dashboard')

@section('content')

    <!-- Welcome Hero Card -->
    <div class="portal-card p-4 mb-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-md-8 d-flex align-items-center gap-3 gap-md-4">
                @if($user->profile_image)
                    <img src="{{ $user->profile_image_url }}" alt="{{ $user->name }}" class="rounded-circle border border-3 border-white shadow" style="width: 80px; height: 80px; object-fit: cover;">
                @else
                    <div class="rounded-circle border border-3 border-white shadow d-flex align-items-center justify-content-center text-white fw-bold fs-2" style="width: 80px; height: 80px; background: rgba(255,255,255,0.2);">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <span class="badge bg-white text-primary fw-bold text-uppercase px-3 py-1 rounded-pill mb-2 shadow-sm" style="font-size: 0.75rem;">
                        {{ $enrollment?->academicSession?->name ?? date('Y') }} Session
                    </span>
                    <h2 class="fw-bold mb-1">Hello, {{ $user->name }}! 👋</h2>
                    <div class="d-flex flex-wrap align-items-center gap-3 text-white-50 small">
                        <span><strong class="text-white">Class:</strong> {{ $enrollment?->studentClass?->class_name ?? 'N/A' }} {{ $enrollment?->section?->name ? '('.$enrollment->section->name.')' : '' }}</span>
                        <span>•</span>
                        <span><strong class="text-white">Roll No:</strong> {{ $enrollment?->roll_number ?? 'N/A' }}</span>
                        <span>•</span>
                        <span><strong class="text-white">Admission No:</strong> {{ $profile?->admission_no ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('student.profile') }}" class="btn btn-light text-primary fw-semibold rounded-pill px-4 shadow-sm">
                    <i class="bi bi-person-circle me-1"></i> View Full Profile
                </a>
            </div>
        </div>
        <!-- Decorative bg circle -->
        <div class="position-absolute end-0 top-0 translate-middle-y me-n5 mt-n5" style="width: 320px; height: 320px; background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%); pointer-events: none;"></div>
    </div>

    <!-- Quick Stats Metric Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Attendance -->
        <div class="col-6 col-lg-3">
            <div class="portal-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted small fw-medium">Attendance</span>
                        <h3 class="fw-bold text-dark mb-0 mt-1">{{ $attendanceStats['percentage'] }}%</h3>
                    </div>
                    <div class="stat-icon-box badge-soft-success">
                        <i class="bi bi-calendar-check-fill text-success"></i>
                    </div>
                </div>
                <div class="small text-muted d-flex align-items-center justify-content-between pt-1">
                    <span>{{ $attendanceStats['present'] }} / {{ $attendanceStats['total'] }} Days Present</span>
                    <a href="{{ route('student.attendance') }}" class="text-primary text-decoration-none fw-semibold">View <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- 2. Upcoming Exams -->
        <div class="col-6 col-lg-3">
            <div class="portal-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted small fw-medium">Upcoming Exams</span>
                        <h3 class="fw-bold text-dark mb-0 mt-1">{{ $upcomingExams->count() }}</h3>
                    </div>
                    <div class="stat-icon-box badge-soft-primary">
                        <i class="bi bi-journal-bookmark-fill text-primary"></i>
                    </div>
                </div>
                <div class="small text-muted d-flex align-items-center justify-content-between pt-1">
                    <span>Exams Scheduled</span>
                    <a href="{{ route('student.exams') }}" class="text-primary text-decoration-none fw-semibold">Schedule <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- 3. Today's Classes -->
        <div class="col-6 col-lg-3">
            <div class="portal-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted small fw-medium">Today's Periods</span>
                        <h3 class="fw-bold text-dark mb-0 mt-1">{{ $todayClasses->count() }}</h3>
                    </div>
                    <div class="stat-icon-box badge-soft-info">
                        <i class="bi bi-clock-history text-info"></i>
                    </div>
                </div>
                <div class="small text-muted d-flex align-items-center justify-content-between pt-1">
                    <span class="text-capitalize">{{ $todayDay }} Schedule</span>
                    <a href="{{ route('student.timetable') }}" class="text-primary text-decoration-none fw-semibold">Table <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- 4. Fee Due Balance -->
        <div class="col-6 col-lg-3">
            <div class="portal-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted small fw-medium">Fee Balance</span>
                        <h3 class="fw-bold {{ $feeSummary['total_due'] > 0 ? 'text-danger' : 'text-success' }} mb-0 mt-1">
                            ₹{{ number_format($feeSummary['total_due'], 2) }}
                        </h3>
                    </div>
                    <div class="stat-icon-box {{ $feeSummary['total_due'] > 0 ? 'badge-soft-danger' : 'badge-soft-success' }}">
                        <i class="bi bi-wallet2 {{ $feeSummary['total_due'] > 0 ? 'text-danger' : 'text-success' }}"></i>
                    </div>
                </div>
                <div class="small text-muted d-flex align-items-center justify-content-between pt-1">
                    <span>{{ $feeSummary['total_due'] > 0 ? 'Pending Dues' : 'All Clear' }}</span>
                    <a href="{{ route('student.fees') }}" class="text-primary text-decoration-none fw-semibold">Details <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-4 mb-4">
        <!-- Left Column: Today's Timetable & Upcoming Exams -->
        <div class="col-lg-8">

            <!-- Today's Schedule -->
            <div class="portal-card mb-4">
                <div class="p-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-calendar3 text-primary fs-5"></i>
                        <h5 class="fw-bold mb-0">Today's Class Schedule</h5>
                        <span class="badge bg-primary-subtle text-primary text-capitalize ms-2">{{ $todayDay }}</span>
                    </div>
                    <a href="{{ route('student.timetable') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        Full Week Timetable <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="p-3">
                    @if($todayClasses->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-cup-hot fs-1 text-secondary opacity-50 d-block mb-2"></i>
                            <h6 class="fw-semibold text-dark">No classes scheduled for today!</h6>
                            <p class="small text-muted mb-0">Enjoy your day or revise upcoming exam topics.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 100px;">Period</th>
                                        <th style="width: 140px;">Time</th>
                                        <th>Subject</th>
                                        <th>Teacher</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayClasses as $periodClass)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary-subtle text-secondary fw-bold px-2 py-1">
                                                    {{ $periodClass->period?->name ?? 'Period' }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted fw-semibold">
                                                    <i class="bi bi-clock me-1 text-primary"></i>
                                                    {{ $periodClass->period?->start_time ? date('h:i A', strtotime($periodClass->period->start_time)) : '-' }}
                                                    -
                                                    {{ $periodClass->period?->end_time ? date('h:i A', strtotime($periodClass->period->end_time)) : '-' }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark">
                                                    {{ $periodClass->teacherSubject?->subject?->subject_name ?? '-' }}
                                                </div>
                                                @if($periodClass->teacherSubject?->subject?->subject_code)
                                                    <small class="text-muted">{{ $periodClass->teacherSubject->subject->subject_code }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center text-secondary small fw-bold" style="width: 28px; height: 28px;">
                                                        <i class="bi bi-person"></i>
                                                    </div>
                                                    <span class="small fw-medium text-dark">
                                                        {{ $periodClass->teacherSubject?->teacher?->name ?? 'TBA' }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Examinations -->
            <div class="portal-card mb-4">
                <div class="p-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-award-fill text-warning fs-5"></i>
                        <h5 class="fw-bold mb-0">Upcoming Examinations</h5>
                    </div>
                    <a href="{{ route('student.exams') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        View All Exams <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="p-4">
                    @if($upcomingExams->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-check2-circle fs-1 text-success opacity-50 d-block mb-2"></i>
                            <h6 class="fw-semibold text-dark">No upcoming exams at this moment.</h6>
                            <p class="small text-muted mb-0">Keep studying and stay prepared for upcoming schedules.</p>
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($upcomingExams as $exam)
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 bg-light-subtle h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <span class="badge bg-primary text-white text-uppercase" style="font-size: 0.7rem;">
                                                    {{ str_replace('_', ' ', $exam->exam_type) }}
                                                </span>
                                                <span class="badge bg-secondary-subtle text-secondary small">
                                                    {{ $exam->exam_mode }}
                                                </span>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1">{{ $exam->title }}</h6>
                                            <div class="small text-muted mb-2">
                                                <i class="bi bi-calendar-event text-primary me-1"></i>
                                                {{ $exam->start_date ? date('d M, Y', strtotime($exam->start_date)) : 'TBD' }}
                                                @if($exam->end_date && $exam->end_date != $exam->start_date)
                                                    - {{ date('d M, Y', strtotime($exam->end_date)) }}
                                                @endif
                                            </div>
                                            <div class="small text-muted mb-3">
                                                <span class="me-3"><strong>Total Marks:</strong> {{ number_format($exam->total_marks, 0) }}</span>
                                                <span><strong>Passing:</strong> {{ number_format($exam->passing_marks, 0) }}</span>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('student.exams.admit-card', $exam->id) }}" class="btn btn-sm btn-primary rounded-pill flex-grow-1" target="_blank">
                                                <i class="bi bi-printer me-1"></i> Admit Card
                                            </a>
                                            <a href="{{ route('student.exams') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                                                Schedule
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Right Column: Recent Attendance, Results & Announcements -->
        <div class="col-lg-4">

            <!-- Recent Attendance -->
            <div class="portal-card mb-4">
                <div class="p-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-pie-chart-fill text-success"></i> Attendance Activity
                    </h6>
                    <a href="{{ route('student.attendance') }}" class="small text-primary text-decoration-none fw-semibold">
                        Details
                    </a>
                </div>
                <div class="p-3">
                    <div class="d-flex justify-content-around text-center mb-3 p-2 bg-light rounded-3">
                        <div>
                            <div class="fw-bold text-success fs-5">{{ $attendanceStats['present'] }}</div>
                            <small class="text-muted" style="font-size: 0.75rem;">Present</small>
                        </div>
                        <div class="border-start"></div>
                        <div>
                            <div class="fw-bold text-danger fs-5">{{ $attendanceStats['absent'] }}</div>
                            <small class="text-muted" style="font-size: 0.75rem;">Absent</small>
                        </div>
                        <div class="border-start"></div>
                        <div>
                            <div class="fw-bold text-warning fs-5">{{ $attendanceStats['leave'] }}</div>
                            <small class="text-muted" style="font-size: 0.75rem;">Leave</small>
                        </div>
                    </div>

                    @if($recentAttendance->isEmpty())
                        <div class="text-center py-3 text-muted small">
                            No attendance records logged yet.
                        </div>
                    @else
                        <ul class="list-group list-group-flush small">
                            @foreach($recentAttendance as $att)
                                <li class="list-group-flush list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-calendar3 text-muted"></i>
                                        <span>{{ date('d M, Y', strtotime($att->attendance_date)) }}</span>
                                    </div>
                                    @if($att->status === 'present')
                                        <span class="badge badge-soft-success px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i>Present</span>
                                    @elseif($att->status === 'absent')
                                        <span class="badge badge-soft-danger px-2 py-1"><i class="bi bi-x-circle-fill me-1"></i>Absent</span>
                                    @else
                                        <span class="badge badge-soft-warning px-2 py-1 text-capitalize">{{ $att->status }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- School Announcements -->
            <div class="portal-card mb-4">
                <div class="p-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-megaphone-fill text-primary"></i> Notice Board
                    </h6>
                </div>
                <div class="p-3">
                    @if($announcements->isEmpty())
                        <div class="text-center py-3 text-muted small">
                            No notices at this time.
                        </div>
                    @else
                        <div class="d-flex flex-column gap-3">
                            @foreach($announcements as $notice)
                                <div class="p-2 border-bottom pb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-light text-primary border" style="font-size: 0.7rem;">Notice</span>
                                        <small class="text-muted" style="font-size: 0.75rem;">{{ $notice->created_at->format('d M') }}</small>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">{{ $notice->title }}</h6>
                                    <p class="text-muted small mb-0" style="line-height: 1.4;">{{ Str::limit($notice->description ?? $notice->content ?? '', 100) }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Links Menu -->
            <div class="portal-card p-3">
                <h6 class="fw-bold mb-3 px-2">Quick Navigation</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('student.results') }}" class="btn btn-outline-primary btn-sm text-start py-2 px-3 rounded-3 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-award me-2"></i> Exam Results & Marksheet</span>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </a>
                    <a href="{{ route('student.timetable') }}" class="btn btn-outline-primary btn-sm text-start py-2 px-3 rounded-3 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-calendar3 me-2"></i> Weekly Timetable</span>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </a>
                    <a href="{{ route('student.fees') }}" class="btn btn-outline-primary btn-sm text-start py-2 px-3 rounded-3 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-credit-card me-2"></i> Fees Breakdown & Dues</span>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </a>
                    <a href="{{ route('student.profile') }}" class="btn btn-outline-primary btn-sm text-start py-2 px-3 rounded-3 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-shield-lock me-2"></i> Change Password</span>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>

@endsection
