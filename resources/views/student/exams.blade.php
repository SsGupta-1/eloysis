@extends('layouts.student.master')

@section('title', 'Examinations')

@section('content')

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">Examinations</h3>
            <p class="text-muted small mb-0">Check upcoming exam schedules, paper timings, and download your admit cards</p>
        </div>
    </div>

    @if($exams->isEmpty())
        <div class="portal-card p-5 text-center text-muted">
            <i class="bi bi-journal-x fs-1 opacity-50 d-block mb-3"></i>
            <h5 class="fw-bold text-dark">No published examinations found.</h5>
            <p class="small text-muted mb-0">When new examinations are scheduled for your class, they will appear right here.</p>
        </div>
    @else
        <div class="d-flex flex-column gap-4">
            @foreach($exams as $exam)
                <div class="portal-card overflow-hidden">
                    <div class="p-3 px-4 border-bottom bg-light-subtle d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-primary text-uppercase" style="font-size: 0.7rem;">
                                    {{ str_replace('_', ' ', $exam->exam_type) }}
                                </span>
                                <span class="badge bg-secondary-subtle text-secondary small">
                                    {{ $exam->exam_mode }}
                                </span>
                                @if($exam->end_date && now()->gt($exam->end_date))
                                    <span class="badge bg-secondary">Completed</span>
                                @else
                                    <span class="badge bg-success">Active / Upcoming</span>
                                @endif
                            </div>
                            <h5 class="fw-bold text-dark mb-0">{{ $exam->title }}</h5>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('student.exams.admit-card', $exam->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" target="_blank">
                                <i class="bi bi-printer me-1"></i> Print Admit Card
                            </a>
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-sm-3 col-6">
                                <span class="text-muted small d-block">Start Date</span>
                                <span class="fw-semibold text-dark">{{ $exam->start_date ? date('d M, Y', strtotime($exam->start_date)) : 'TBA' }}</span>
                            </div>
                            <div class="col-sm-3 col-6">
                                <span class="text-muted small d-block">End Date</span>
                                <span class="fw-semibold text-dark">{{ $exam->end_date ? date('d M, Y', strtotime($exam->end_date)) : 'TBA' }}</span>
                            </div>
                            <div class="col-sm-3 col-6">
                                <span class="text-muted small d-block">Total Marks</span>
                                <span class="fw-semibold text-dark">{{ number_format($exam->total_marks, 0) }} M</span>
                            </div>
                            <div class="col-sm-3 col-6">
                                <span class="text-muted small d-block">Passing Marks</span>
                                <span class="fw-semibold text-dark">{{ number_format($exam->passing_marks, 0) }} M</span>
                            </div>
                        </div>

                        <!-- Schedule Table -->
                        @if($exam->schedules->isNotEmpty())
                            <h6 class="fw-bold text-dark mb-2 mt-3">Subject Date-Sheet / Schedules:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-custom mb-0 text-center align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th class="text-start">Subject</th>
                                            <th>Date</th>
                                            <th>Time Slot</th>
                                            <th>Duration</th>
                                            <th>Max Marks</th>
                                            <th>Room No</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($exam->schedules as $sIdx => $sch)
                                            <tr>
                                                <td>{{ $sIdx + 1 }}</td>
                                                <td class="text-start fw-bold text-dark">
                                                    {{ $sch->subject?->subject_name ?? '-' }}
                                                    @if($sch->subject?->subject_code)
                                                        <span class="badge bg-light text-muted border ms-1">{{ $sch->subject->subject_code }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary">
                                                        {{ $sch->exam_date ? date('d M, Y (D)', strtotime($sch->exam_date)) : '-' }}
                                                    </span>
                                                </td>
                                                <td class="small">
                                                    {{ $sch->start_time ? date('h:i A', strtotime($sch->start_time)) : '' }}
                                                    -
                                                    {{ $sch->end_time ? date('h:i A', strtotime($sch->end_time)) : '' }}
                                                </td>
                                                <td class="small text-muted">{{ $sch->duration_minutes ?? $exam->duration_minutes ?? 180 }} mins</td>
                                                <td class="fw-bold text-dark">{{ number_format($sch->max_marks ?? 100, 0) }}</td>
                                                <td class="small text-muted">{{ $sch->room_no ?: 'Main Hall' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-muted small fst-italic">
                                Detailed subject date-sheet schedule will be posted soon.
                            </div>
                        @endif

                        @if($exam->instructions)
                            <div class="mt-3 p-3 bg-light rounded-3 small">
                                <strong class="d-block mb-1 text-dark"><i class="bi bi-info-circle me-1 text-primary"></i> Instructions:</strong>
                                <div class="text-muted">{!! nl2br(e($exam->instructions)) !!}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection
