@extends('layouts.student.master')

@section('title', 'Class Timetable')

@section('content')

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">Class Timetable</h3>
            <p class="text-muted small mb-0">
                Weekly period schedule for 
                <strong>{{ $enrollment?->studentClass?->class_name ?? 'Class' }}</strong>
                {{ $enrollment?->section?->name ? '- Section '.$enrollment->section->name : '' }}
                ({{ $enrollment?->academicSession?->name ?? 'Current' }})
            </p>
        </div>
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-printer me-1"></i> Print Timetable
            </button>
        </div>
    </div>

    <!-- Daily View Card -->
    <div class="portal-card mb-4">
        <div class="p-3 border-bottom bg-light-subtle">
            <!-- Day Tabs -->
            <ul class="nav nav-pills flex-nowrap overflow-auto gap-2" id="timetableDaysTab" role="tablist">
                @foreach($days as $day)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize rounded-pill px-3 py-2 fw-semibold {{ $day === $today ? 'active' : '' }}" 
                                id="{{ $day }}-tab" 
                                data-bs-toggle="pill" 
                                data-bs-target="#tab-{{ $day }}" 
                                type="button" 
                                role="tab">
                            {{ $day }}
                            @if($day === $today)
                                <span class="badge bg-white text-primary ms-1" style="font-size: 0.65rem;">Today</span>
                            @endif
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="p-3 p-md-4">
            <div class="tab-content" id="timetableDaysTabContent">
                @foreach($days as $day)
                    <div class="tab-pane fade {{ $day === $today ? 'show active' : '' }}" id="tab-{{ $day }}" role="tabpanel">
                        @php
                            $dayHasClasses = false;
                            foreach($periods as $p) {
                                if(isset($timetableGrid[$day][$p->id]) && $timetableGrid[$day][$p->id]) {
                                    $dayHasClasses = true;
                                    break;
                                }
                            }
                        @endphp

                        @if(! $dayHasClasses)
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 opacity-50 d-block mb-2"></i>
                                <h6 class="fw-semibold text-dark">No classes scheduled for {{ ucfirst($day) }}.</h6>
                                <p class="small text-muted mb-0">No period timetable allocated on this day.</p>
                            </div>
                        @else
                            <div class="row g-3">
                                @foreach($periods as $period)
                                    @php
                                        $entry = $timetableGrid[$day][$period->id] ?? null;
                                    @endphp
                                    <div class="col-md-6 col-lg-4">
                                        <div class="p-3 rounded-3 border h-100 {{ $entry ? 'bg-white shadow-sm' : 'bg-light text-muted opacity-75' }}">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-primary-subtle text-primary fw-bold">
                                                    {{ $period->name }}
                                                </span>
                                                <small class="text-muted fw-medium">
                                                    <i class="bi bi-clock text-primary me-1"></i>
                                                    {{ $period->start_time ? date('h:i A', strtotime($period->start_time)) : '' }}
                                                    -
                                                    {{ $period->end_time ? date('h:i A', strtotime($period->end_time)) : '' }}
                                                </small>
                                            </div>

                                            @if($entry)
                                                <h5 class="fw-bold text-dark mb-1">
                                                    {{ $entry->teacherSubject?->subject?->subject_name ?? '-' }}
                                                </h5>
                                                @if($entry->teacherSubject?->subject?->subject_code)
                                                    <span class="badge bg-light text-secondary border mb-2">
                                                        {{ $entry->teacherSubject->subject->subject_code }}
                                                    </span>
                                                @endif
                                                <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top small text-muted">
                                                    <i class="bi bi-person-circle text-secondary"></i>
                                                    <span class="fw-medium text-dark">
                                                        {{ $entry->teacherSubject?->teacher?->name ?? 'Assigned Teacher' }}
                                                    </span>
                                                </div>
                                            @else
                                                <div class="py-3 text-center text-muted small">
                                                    <em>Free Period / Recess</em>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Complete Weekly Grid View -->
    <div class="portal-card">
        <div class="p-3 px-4 border-bottom d-flex align-items-center gap-2">
            <i class="bi bi-grid-3x3 text-primary fs-5"></i>
            <h5 class="fw-bold mb-0">Full Weekly Grid</h5>
        </div>
        <div class="p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-custom text-center align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 110px;">Day</th>
                            @foreach($periods as $period)
                                <th style="min-width: 140px;">
                                    <div>{{ $period->name }}</div>
                                    <small class="text-muted fw-normal" style="font-size: 0.7rem;">
                                        {{ $period->start_time ? date('h:i A', strtotime($period->start_time)) : '' }}
                                    </small>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($days as $day)
                            <tr class="{{ $day === $today ? 'table-primary-subtle' : '' }}">
                                <td class="fw-bold text-capitalize text-start bg-light">
                                    {{ $day }}
                                    @if($day === $today)
                                        <span class="badge bg-primary text-white ms-1" style="font-size: 0.65rem;">Today</span>
                                    @endif
                                </td>
                                @foreach($periods as $period)
                                    @php
                                        $cell = $timetableGrid[$day][$period->id] ?? null;
                                    @endphp
                                    <td>
                                        @if($cell)
                                            <div class="fw-bold text-dark small">
                                                {{ $cell->teacherSubject?->subject?->subject_name ?? '-' }}
                                            </div>
                                            <small class="text-muted d-block" style="font-size: 0.72rem;">
                                                {{ $cell->teacherSubject?->teacher?->name ?? '' }}
                                            </small>
                                        @else
                                            <span class="text-muted opacity-50">-</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
