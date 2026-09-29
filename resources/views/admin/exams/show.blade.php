@extends('layouts.admin.master')

@section('title', 'Exam Details - ' . $exam->title)

@section('content')
<div class="container-fluid">

    <x-ui.page-header
        title="{{ $exam->title }}"
        subtitle="Exam Code: {{ $exam->exam_code }} | Session: {{ $exam->academicSession?->name ?? '-' }}">
        <x-slot:actions>
            <a href="{{ route('admin.exams.enrollments', $exam->id) }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-people me-1"></i> Enrollments ({{ $totalEnrolled }})
            </a>
            <a href="{{ route('admin.exams.admit-cards', $exam->id) }}" class="btn btn-outline-info me-2" target="_blank">
                <i class="bi bi-person-badge me-1"></i> Admit Cards
            </a>
            <a href="{{ route('admin.results.exam', $exam->id) }}" class="btn btn-outline-success me-2">
                <i class="bi bi-award me-1"></i> Broadsheet & Results
            </a>
            <a href="{{ route('admin.exams.edit', $exam->id) }}" class="btn btn-primary me-2">
                <i class="bi bi-pencil me-1"></i> Edit Exam
            </a>
            <a href="{{ route('admin.exams.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Stats Cards Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-1">Total Subjects</h6>
                            <h3 class="fw-bold mb-0">{{ $totalSchedules }}</h3>
                        </div>
                        <div class="fs-1 text-white-50"><i class="bi bi-book"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-1">Enrolled Students</h6>
                            <h3 class="fw-bold mb-0">{{ $totalEnrolled }}</h3>
                        </div>
                        <div class="fs-1 text-white-50"><i class="bi bi-people-fill"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-1">Eligible for Exam</h6>
                            <h3 class="fw-bold mb-0">{{ $totalEligible }}</h3>
                        </div>
                        <div class="fs-1 text-white-50"><i class="bi bi-check-circle-fill"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="card-title text-muted mb-1">Marks Entry Progress</h6>
                            <h3 class="fw-bold mb-0">{{ $marksCompletionPercentage }}%</h3>
                        </div>
                        <div class="fs-1 text-muted"><i class="bi bi-graph-up-arrow"></i></div>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-dark" role="progressbar" style="width: {{ $marksCompletionPercentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Details & Status Card --}}
    <x-ui.card class="mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <small class="text-muted d-block">Class</small>
                <span class="fw-semibold fs-6">{{ $exam->academicClass?->class_name ?? 'All Classes' }}</span>
            </div>

            <div class="col-md-3">
                <small class="text-muted d-block">Exam Type</small>
                <span class="badge bg-primary text-uppercase">{{ str_replace('_', ' ', $exam->exam_type) }}</span>
            </div>

            <div class="col-md-2">
                <small class="text-muted d-block">Mode</small>
                <span class="badge bg-secondary text-uppercase">{{ $exam->exam_mode }}</span>
            </div>

            <div class="col-md-2">
                <small class="text-muted d-block">Total Marks / Passing</small>
                <span class="fw-bold text-success">{{ (float)$exam->total_marks }}</span> / <span class="text-muted">{{ (float)$exam->passing_marks }}</span>
            </div>

            <div class="col-md-2">
                <small class="text-muted d-block">Exam Status</small>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-dark dropdown-toggle text-capitalize" type="button" data-bs-toggle="dropdown">
                        <span class="badge {{ $exam->status === 'published' ? 'bg-success' : ($exam->status === 'closed' ? 'bg-danger' : 'bg-warning text-dark') }} me-1"></span>
                        {{ $exam->status }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item btn-change-status" href="javascript:void(0)" data-status="draft" data-id="{{ $exam->id }}">Draft</a></li>
                        <li><a class="dropdown-item btn-change-status" href="javascript:void(0)" data-status="published" data-id="{{ $exam->id }}">Published / Open</a></li>
                        <li><a class="dropdown-item btn-change-status" href="javascript:void(0)" data-status="closed" data-id="{{ $exam->id }}">Closed</a></li>
                    </ul>
                </div>
            </div>

            @if($exam->instructions)
                <div class="col-12 mt-3 pt-3 border-top">
                    <small class="text-muted d-block mb-1"><strong>Instructions for Candidates:</strong></small>
                    <p class="text-muted mb-0 small">{{ $exam->instructions }}</p>
                </div>
            @endif
        </div>
    </x-ui.card>

    {{-- Timetable Schedules & Marks Entry Card --}}
    <x-ui.card title="Subject Timetable & Marks Entry Management">
        @if($exam->schedules->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted fs-1 mb-2 d-block"></i>
                <h5 class="text-muted">No timetable schedules defined yet.</h5>
                <a href="{{ route('admin.exams.edit', $exam->id) }}" class="btn btn-sm btn-primary mt-2">
                    <i class="bi bi-plus-lg me-1"></i> Add Timetable Schedules
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="40">#</th>
                            <th>Subject</th>
                            <th>Date & Time</th>
                            <th>Room / Hall</th>
                            <th>Invigilator</th>
                            <th>Question Paper</th>
                            <th>Marks Split</th>
                            <th width="150" class="text-center">Marks Entry</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exam->schedules as $idx => $sch)
                            @php
                                $enteredMarksCount = $sch->marks()->count();
                                $isFullyEntered = ($totalEnrolled > 0 && $enteredMarksCount >= $totalEnrolled);
                            @endphp
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $sch->subject?->subject_name ?? '-' }}</div>
                                    <small class="text-muted">{{ $sch->subject?->subject_code ?? '' }}</small>
                                </td>
                                <td>
                                    <div><i class="bi bi-calendar-event me-1 text-primary"></i>{{ $sch->exam_date ? $sch->exam_date->format('d M, Y (D)') : '-' }}</div>
                                    <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }} ({{ $sch->duration_minutes }}m)</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $sch->room_no ?: 'Unassigned' }}</span>
                                </td>
                                <td>
                                    <span class="small">{{ $sch->invigilator?->name ?? 'Not Assigned' }}</span>
                                </td>
                                <td>
                                    @if($sch->questionPaper)
                                        <a href="{{ route('admin.question-papers.show', $sch->questionPaper->id) }}" class="badge bg-info-subtle text-info border border-info-subtle text-decoration-none" title="View Question Paper">
                                            <i class="bi bi-file-earmark-text me-1"></i>{{ $sch->questionPaper->title }}
                                        </a>
                                    @else
                                        <span class="badge bg-light text-muted border">Manual / None</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-success">{{ (float)$sch->total_marks }}M</span>
                                    <small class="text-muted d-block">Pass: {{ (float)$sch->passing_marks }}M</small>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.exams.schedules.marks-entry', $sch->id) }}" class="btn btn-sm {{ $isFullyEntered ? 'btn-outline-success' : 'btn-primary' }}" title="Enter / Update Marks for this subject">
                                        <i class="bi bi-pencil-square me-1"></i>
                                        {{ $enteredMarksCount > 0 ? "Marks ({$enteredMarksCount}/{$totalEnrolled})" : 'Enter Marks' }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-ui.card>

</div>
@endsection

@push('scripts')
<script>
    const EXAM_STATUS_URL = "{{ url('admin/exams') }}/:id/status";

    $(function () {
        $('.btn-change-status').on('click', function () {
            const status = $(this).data('status');
            const id = $(this).data('id');
            const url = EXAM_STATUS_URL.replace(':id', id);

            $.ajax({
                url: url,
                type: 'PATCH',
                data: { status: status },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                },
                success: (res) => {
                    if (typeof Toast !== 'undefined') {
                        Toast.success(res.message || 'Status updated.');
                    }
                    setTimeout(() => window.location.reload(), 500);
                },
                error: (xhr) => {
                    alert(xhr.responseJSON?.message || 'Failed to update status.');
                }
            });
        });
    });
</script>
@endpush
