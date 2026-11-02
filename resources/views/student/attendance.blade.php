@extends('layouts.student.master')

@section('title', 'Attendance Records')

@section('content')

    <!-- Header & Month-Year Filter -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">Attendance Records</h3>
            <p class="text-muted small mb-0">Showing attendance for <strong class="text-primary">{{ date('F Y', mktime(0, 0, 0, (int) $month, 1, (int) $year)) }}</strong></p>
        </div>
        <div class="d-flex align-items-center gap-2 no-print">
            <!-- View Mode Switcher -->
            <div class="btn-group btn-group-sm p-1 bg-light rounded-pill border" role="group">
                <button type="button" class="btn btn-sm rounded-pill active px-3" id="btnCalendarView" onclick="switchView('calendar')">
                    <i class="bi bi-calendar3 me-1"></i> Calendar
                </button>
                <button type="button" class="btn btn-sm rounded-pill text-muted px-3" id="btnListView" onclick="switchView('list')">
                    <i class="bi bi-list-ul me-1"></i> List
                </button>
            </div>

            <!-- Month & Year Selector -->
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

    <!-- 75% Low Attendance Alert if Applicable -->
    @if($stats['is_low_attendance'])
        <div class="alert alert-danger d-flex align-items-center rounded-3 p-3 mb-4 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger"></i>
            <div>
                <strong>Attendance Warning!</strong> Your attendance for this month is <strong>{{ $stats['percentage'] }}%</strong> (minimum 75% required). Unrecorded past days have been marked as absent.
            </div>
        </div>
    @endif

    <!-- Current Month Summary Metrics -->
    <div class="row g-3 mb-4">
        <!-- 1. Monthly Percentage -->
        <div class="col-sm-6 col-lg-3">
            <div class="portal-card p-3 h-100">
                <span class="text-muted small fw-semibold">{{ date('F', mktime(0, 0, 0, (int) $month, 1, (int) $year)) }} Rate</span>
                <div class="d-flex align-items-baseline gap-2 mt-1">
                    <h2 class="fw-bold mb-0 {{ $stats['percentage'] >= 75 ? 'text-success' : 'text-danger' }}">
                        {{ $stats['percentage'] }}%
                    </h2>
                    @if($stats['percentage'] >= 75)
                        <span class="badge bg-success-subtle text-success small">Eligible</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger small">Shortage</span>
                    @endif
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar {{ $stats['percentage'] >= 75 ? 'bg-success' : 'bg-danger' }}"
                         style="width: {{ min(100, $stats['percentage']) }}%;"></div>
                </div>
                <small class="text-muted d-block mt-2">Overall session rate: <strong>{{ $overallStats['percentage'] }}%</strong></small>
            </div>
        </div>

        <!-- 2. Present Days -->
        <div class="col-sm-6 col-lg-3">
            <div class="portal-card p-3 h-100">
                <span class="text-muted small fw-semibold">Present Days</span>
                <h2 class="fw-bold text-success mb-0 mt-1">{{ $stats['present_days'] }}</h2>
                <small class="text-muted">Out of {{ $stats['total_working_days'] }} working days</small>
            </div>
        </div>

        <!-- 3. Absent Days -->
        <div class="col-sm-6 col-lg-3">
            <div class="portal-card p-3 h-100">
                <span class="text-muted small fw-semibold">Absent Days</span>
                <h2 class="fw-bold text-danger mb-0 mt-1">{{ $stats['absent_days'] }}</h2>
                <small class="text-muted">Includes unrecorded past days</small>
            </div>
        </div>

        <!-- 4. Sundays & Holidays -->
        <div class="col-sm-6 col-lg-3">
            <div class="portal-card p-3 h-100">
                <span class="text-muted small fw-semibold">Sundays & Holidays</span>
                <div class="d-flex align-items-baseline gap-2 mt-1">
                    <h2 class="fw-bold text-primary mb-0">{{ $stats['sunday_days'] + $stats['holiday_days'] }}</h2>
                    <span class="badge bg-light text-muted border small">{{ $stats['sunday_days'] }} Sun / {{ $stats['holiday_days'] }} Hol</span>
                </div>
                <small class="text-muted">Excluded from working sessions</small>
            </div>
        </div>
    </div>

    <!-- CALENDAR VIEW -->
    <div id="attendanceCalendarView" class="portal-card mb-4 p-3 p-md-4">
        <!-- Calendar Legend -->
        <div class="d-flex flex-wrap align-items-center justify-content-between border-bottom pb-3 mb-3 gap-2">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-calendar3 text-primary me-2"></i> Monthly Calendar Grid
            </h5>
            <div class="d-flex flex-wrap align-items-center gap-3 small">
                <span class="d-flex align-items-center gap-1">
                    <span class="legend-dot bg-success"></span> Present
                </span>
                <span class="d-flex align-items-center gap-1">
                    <span class="legend-dot bg-danger"></span> Absent
                </span>
                <span class="d-flex align-items-center gap-1">
                    <span class="legend-dot bg-indigo" style="background-color: #6366f1;"></span> Sunday
                </span>
                <span class="d-flex align-items-center gap-1">
                    <span class="legend-dot bg-warning"></span> Holiday / Leave
                </span>
                <span class="d-flex align-items-center gap-1">
                    <span class="legend-dot bg-secondary-subtle"></span> Upcoming
                </span>
            </div>
        </div>

        <!-- 7-Day Grid Headers -->
        <div class="calendar-grid">
            <div class="cal-header text-danger">Sun</div>
            <div class="cal-header">Mon</div>
            <div class="cal-header">Tue</div>
            <div class="cal-header">Wed</div>
            <div class="cal-header">Thu</div>
            <div class="cal-header">Fri</div>
            <div class="cal-header text-primary">Sat</div>

            <!-- Blank Day Offsets for Month Start -->
            @for($b = 0; $b < $firstDayOfWeek; $b++)
                <div class="cal-cell cal-cell-blank"></div>
            @endfor

            <!-- Month Days -->
            @foreach($calendar as $dayItem)
                @php
                    $isSunday = $dayItem['status'] === 'sunday';
                    $isPresent = $dayItem['status'] === 'present';
                    $isAbsent = $dayItem['status'] === 'absent';
                    $isHoliday = $dayItem['status'] === 'holiday';
                    $isLeave = $dayItem['status'] === 'leave';
                    $isUpcoming = $dayItem['status'] === 'upcoming';
                @endphp
                <div class="cal-cell {{ $dayItem['is_today'] ? 'cal-cell-today' : '' }} {{ $isSunday ? 'cal-cell-sunday' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="cal-date-number {{ $dayItem['is_today'] ? 'fw-bold text-primary' : '' }}">
                            {{ $dayItem['day'] }}
                        </span>
                        @if($dayItem['is_today'])
                            <span class="badge bg-primary rounded-pill py-0 px-1 small" style="font-size: 9px;">Today</span>
                        @endif
                    </div>

                    <div class="cal-badge-wrap mt-2">
                        @if($isPresent)
                            <span class="badge bg-success-subtle text-success w-100 py-1">
                                <i class="bi bi-check-circle-fill me-1"></i> Present
                            </span>
                        @elseif($isAbsent)
                            <span class="badge bg-danger-subtle text-danger w-100 py-1">
                                <i class="bi bi-x-circle-fill me-1"></i> Absent
                            </span>
                        @elseif($isSunday)
                            <span class="badge w-100 py-1" style="background-color: #EEF2FF; color: #4F46E5;">
                                <i class="bi bi-cup-hot me-1"></i> Sunday
                            </span>
                        @elseif($isHoliday)
                            <span class="badge bg-warning-subtle text-warning-emphasis w-100 py-1">
                                <i class="bi bi-star-fill me-1"></i> Holiday
                            </span>
                        @elseif($isLeave)
                            <span class="badge bg-warning-subtle text-warning w-100 py-1">
                                <i class="bi bi-clock-history me-1"></i> Leave
                            </span>
                        @else
                            <span class="badge bg-light text-muted border w-100 py-1">
                                Upcoming
                            </span>
                        @endif
                    </div>

                    @if($dayItem['remarks'])
                        <div class="cal-remarks text-truncate" title="{{ $dayItem['remarks'] }}">
                            {{ $dayItem['remarks'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- LIST VIEW (Tabular) -->
    <div id="attendanceListView" class="portal-card mb-4" style="display: none;">
        <div class="p-3 px-4 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-list-ul text-primary me-2"></i> Detailed Daily Attendance Log
            </h5>
            <span class="badge bg-light text-muted border">
                {{ count($calendar) }} Days Recorded
            </span>
        </div>
        <div class="p-3">
            <div class="table-responsive">
                <table class="table table-hover table-custom mb-0">
                    <thead>
                        <tr>
                            <th style="width: 70px;">Day #</th>
                            <th style="width: 160px;">Date</th>
                            <th style="width: 140px;">Day</th>
                            <th style="width: 170px;">Status</th>
                            <th>Remarks & Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($calendar as $dayItem)
                            <tr class="{{ $dayItem['is_today'] ? 'table-primary-subtle' : '' }}">
                                <td class="fw-semibold text-muted">{{ $dayItem['day'] }}</td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $dayItem['formatted_date'] }}</span>
                                    @if($dayItem['is_today'])
                                        <span class="badge bg-primary ms-1" style="font-size: 10px;">Today</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-capitalize {{ $dayItem['status'] === 'sunday' ? 'text-danger fw-bold' : 'text-muted' }}">
                                        {{ $dayItem['day_name'] }}
                                    </span>
                                </td>
                                <td>
                                    @if($dayItem['status'] === 'present')
                                        <span class="badge badge-soft-success px-3 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i> Present
                                        </span>
                                    @elseif($dayItem['status'] === 'absent')
                                        <span class="badge badge-soft-danger px-3 py-1">
                                            <i class="bi bi-x-circle-fill me-1"></i> Absent
                                        </span>
                                    @elseif($dayItem['status'] === 'sunday')
                                        <span class="badge px-3 py-1" style="background-color: #EEF2FF; color: #4F46E5;">
                                            <i class="bi bi-cup-hot me-1"></i> Sunday (Off)
                                        </span>
                                    @elseif($dayItem['status'] === 'holiday')
                                        <span class="badge badge-soft-warning px-3 py-1">
                                            <i class="bi bi-star-fill me-1"></i> Holiday
                                        </span>
                                    @elseif($dayItem['status'] === 'leave')
                                        <span class="badge badge-soft-warning px-3 py-1">
                                            <i class="bi bi-clock-history me-1"></i> Leave
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border px-3 py-1">
                                            Upcoming
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $dayItem['remarks'] ?: ($dayItem['status'] === 'absent' && !isset($dayItem['is_db']) ? 'Unmarked day counted as absent' : '-') }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
    }
    .cal-header {
        font-weight: 700;
        font-size: 13px;
        text-align: center;
        padding: 8px 4px;
        background: #f8fafc;
        border-radius: 8px;
        color: #475569;
    }
    .cal-cell {
        min-height: 82px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        justifyContent: space-between;
        transition: all 0.2s ease;
    }
    .cal-cell:hover {
        border-color: #cbd5e1;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    }
    .cal-cell-blank {
        background: #f8fafc;
        border-style: dashed;
        border-color: #f1f5f9;
    }
    .cal-cell-today {
        border: 2px solid #4f46e5 !important;
        background: #faf5ff;
    }
    .cal-cell-sunday {
        background: #fafafa;
    }
    .cal-date-number {
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
    }
    .cal-badge-wrap .badge {
        font-size: 10px;
        font-weight: 600;
    }
    .cal-remarks {
        font-size: 9px;
        color: #94a3b8;
        margin-top: 2px;
    }
    @media (max-width: 768px) {
        .calendar-grid {
            gap: 4px;
        }
        .cal-cell {
            min-height: 68px;
            padding: 4px;
        }
        .cal-badge-wrap .badge {
            font-size: 8px;
            padding: 2px !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function switchView(mode) {
        const calView = document.getElementById('attendanceCalendarView');
        const listView = document.getElementById('attendanceListView');
        const btnCal = document.getElementById('btnCalendarView');
        const btnList = document.getElementById('btnListView');

        if (mode === 'calendar') {
            calView.style.display = 'block';
            listView.style.display = 'none';
            btnCal.classList.add('active');
            btnCal.classList.remove('text-muted');
            btnList.classList.remove('active');
            btnList.classList.add('text-muted');
        } else {
            calView.style.display = 'none';
            listView.style.display = 'block';
            btnList.classList.add('active');
            btnList.classList.remove('text-muted');
            btnCal.classList.remove('active');
            btnCal.classList.add('text-muted');
        }
    }
</script>
@endpush
