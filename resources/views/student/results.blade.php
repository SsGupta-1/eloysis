@extends('layouts.student.master')

@section('title', 'Exam Results')

@section('content')

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">Academic Results & Report Cards</h3>
            <p class="text-muted small mb-0">Review your exam performances, subject-wise scores, and download official marksheets</p>
        </div>
    </div>

    @if($examResults->isEmpty())
        <div class="portal-card p-5 text-center text-muted">
            <i class="bi bi-award fs-1 opacity-50 d-block mb-3"></i>
            <h5 class="fw-bold text-dark">No examination results published yet.</h5>
            <p class="small text-muted mb-0">When your teachers enter and publish your examination marks, they will appear right here.</p>
        </div>
    @else
        <div class="d-flex flex-column gap-4">
            @foreach($examResults as $res)
                @php
                    $exam = $res['exam'];
                    $marksList = $res['marks'];
                @endphp
                <div class="portal-card overflow-hidden">
                    <div class="p-3 px-4 border-bottom bg-light-subtle d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-primary text-uppercase" style="font-size: 0.7rem;">
                                    {{ str_replace('_', ' ', $exam->exam_type) }}
                                </span>
                                @if($res['passed'])
                                    <span class="badge badge-soft-success fw-bold px-2 py-1"><i class="bi bi-check-circle me-1"></i> PASSED</span>
                                @else
                                    <span class="badge badge-soft-danger fw-bold px-2 py-1"><i class="bi bi-x-circle me-1"></i> NEEDS IMPROVEMENT</span>
                                @endif
                            </div>
                            <h5 class="fw-bold text-dark mb-0">{{ $exam->title }}</h5>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="text-end d-none d-sm-block">
                                <div class="fs-5 fw-bold text-dark">{{ $res['total_scored'] }} / {{ $res['max_marks'] }}</div>
                                <small class="text-muted">Score: <strong class="text-primary">{{ $res['percentage'] }}%</strong> | Grade: <span class="badge bg-dark">{{ $res['grade'] }}</span></small>
                            </div>
                            <a href="{{ route('student.results.marksheet', $exam->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" target="_blank">
                                <i class="bi bi-file-earmark-text me-1"></i> Full Marksheet
                            </a>
                        </div>
                    </div>

                    <div class="p-3 p-md-4">
                        <!-- Subject-wise breakdown table -->
                        <div class="table-responsive">
                            <table class="table table-hover table-custom mb-0 text-center align-middle">
                                <thead>
                                    <tr>
                                        <th class="text-start" style="width: 250px;">Subject</th>
                                        <th>Theory</th>
                                        <th>Practical</th>
                                        <th>Internal</th>
                                        <th>Max Marks</th>
                                        <th>Marks Obtained</th>
                                        <th>Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($marksList as $mark)
                                        @php
                                            $subjMax = (float) ($mark->schedule?->max_marks ?? 100);
                                            $subjScored = (float) $mark->total_marks;
                                            $subjPct = $subjMax > 0 ? round(($subjScored / $subjMax) * 100, 1) : 0;
                                        @endphp
                                        <tr>
                                            <td class="text-start">
                                                <div class="fw-bold text-dark">
                                                    {{ $mark->schedule?->subject?->subject_name ?? '-' }}
                                                </div>
                                                @if($mark->schedule?->subject?->subject_code)
                                                    <small class="text-muted">{{ $mark->schedule->subject->subject_code }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $mark->theory_marks !== null ? number_format($mark->theory_marks, 1) : '-' }}</td>
                                            <td>{{ $mark->practical_marks !== null ? number_format($mark->practical_marks, 1) : '-' }}</td>
                                            <td>{{ $mark->internal_marks !== null ? number_format($mark->internal_marks, 1) : '-' }}</td>
                                            <td class="text-muted">{{ number_format($subjMax, 0) }}</td>
                                            <td>
                                                <span class="fw-bold text-dark fs-6">{{ number_format($subjScored, 1) }}</span>
                                                <small class="text-muted d-block" style="font-size: 0.72rem;">({{ $subjPct }}%)</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border fw-bold">
                                                    {{ $mark->letter_grade ?: 'Pass' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light fw-bold">
                                    <tr>
                                        <td class="text-start">Total Aggregate:</td>
                                        <td colspan="3"></td>
                                        <td>{{ number_format($res['max_marks'], 0) }}</td>
                                        <td class="text-primary fs-6">{{ number_format($res['total_scored'], 1) }}</td>
                                        <td><span class="badge bg-dark">{{ $res['grade'] }}</span></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection
