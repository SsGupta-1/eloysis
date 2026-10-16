@extends('layouts.student.master')

@section('title', 'Attendance Records')

@section('content')

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">Attendance Records</h3>
            <p class="text-muted small mb-0">Track your daily class attendance and monthly consistency</p>
        </div>
        <div class="no-print">
            <!-- Month & Year Filter Form -->
            <form action="{{ route('student.attendance') }}" method="GET" class="d-flex align-items-center gap-2">
                <select name="month" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>
                <select name="year" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                    @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </form>
        </div>
    </div>

    <!-- Attendance Summary Metrics -->
    <div class="row g-3 mb-4">
        <!-- 1. Percentage -->
        <div class="col-sm-6 col-lg-3">
            <div class="portal-card p-3 h-100">
                <span class="text-muted small">Overall Attendance</span>
                <div class="d-flex align-items-baseline gap-2 mt-1">
                    <h2 class="fw-bold text-dark mb-0">{{ $stats['percentage'] }}%</h2>
                    @if($stats['percentage'] >= 75)
                        <span class="badge bg-success-subtle text-success small">Eligible</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger small">Low Attendance</span>
                    @endif
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-success" style="width: {{ min(100, $stats['percentage']) }}%;"></div>
                </div>
            </div>
        </div>

        <!-- 2. Present Days -->
        <div class="col-sm-6 col-lg-3">
            <div class="portal-card p-3 h-100">
                <span class="text-muted small">Present Days</span>
                <h2 class="fw-bold text-success mb-0 mt-1">{{ $stats['present'] }}</h2>
                <small class="text-muted">Total working sessions attended</small>
            </div>
        </div>

        <!-- 3. Absent Days -->
        <div class="col-sm-6 col-lg-3">
            <div class="portal-card p-3 h-100">
                <span class="text-muted small">Absent Days</span>
                <h2 class="fw-bold text-danger mb-0 mt-1">{{ $stats['absent'] }}</h2>
                <small class="text-muted">Unexcused leaves recorded</small>
            </div>
        </div>

        <!-- 4. Total Days -->
        <div class="col-sm-6 col-lg-3">
            <div class="portal-card p-3 h-100">
                <span class="text-muted small">Total Recorded Sessions</span>
                <h2 class="fw-bold text-primary mb-0 mt-1">{{ $stats['total'] }}</h2>
                <small class="text-muted">{{ $stats['leave'] }} Approved leaves</small>
            </div>
        </div>
    </div>

    <!-- Monthly Attendance Logs -->
    <div class="portal-card">
        <div class="p-3 px-4 border-bottom d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-calendar-check text-primary fs-5"></i>
                <h5 class="fw-bold mb-0">Logs for {{ date('F Y', mktime(0, 0, 0, (int) $month, 1, (int) $year)) }}</h5>
            </div>
            <span class="badge bg-light text-muted border">
                {{ $attendanceLogs->count() }} Record(s) Found
            </span>
        </div>
        <div class="p-3">
            @if($attendanceLogs->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-minus fs-1 opacity-50 d-block mb-2"></i>
                    <h6 class="fw-semibold text-dark">No attendance logs found for this period.</h6>
                    <p class="small text-muted mb-0">Try selecting a different month or year from the filter above.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-custom mb-0">
                        <thead>
                            <tr>
                                <th style="width: 70px;">#</th>
                                <th style="width: 160px;">Date</th>
                                <th style="width: 140px;">Day</th>
                                <th style="width: 140px;">Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendanceLogs as $idx => $log)
                                <tr>
                                    <td class="text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <span class="fw-semibold text-dark">
                                            {{ date('d M, Y', strtotime($log->attendance_date)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted text-capitalize">
                                            {{ date('l', strtotime($log->attendance_date)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($log->status === 'present')
                                            <span class="badge badge-soft-success px-3 py-1">
                                                <i class="bi bi-check-circle-fill me-1"></i> Present
                                            </span>
                                        @elseif($log->status === 'absent')
                                            <span class="badge badge-soft-danger px-3 py-1">
                                                <i class="bi bi-x-circle-fill me-1"></i> Absent
                                            </span>
                                        @elseif($log->status === 'leave')
                                            <span class="badge badge-soft-warning px-3 py-1">
                                                <i class="bi bi-clock-history me-1"></i> Leave
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary px-3 py-1 text-capitalize">
                                                {{ $log->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $log->remarks ?: '-' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

@endsection
