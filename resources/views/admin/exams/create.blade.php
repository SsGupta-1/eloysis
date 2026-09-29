@extends('layouts.admin.master')

@section('title', 'Create Exam')

@section('content')
<div class="container-fluid">

    <x-ui.page-header
        title="Create Examination"
        subtitle="Configure exam details, dates, multi-subject timetable schedules, and marks distribution">
        <x-slot:actions>
            <a href="{{ route('admin.exams.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Exams
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <form action="{{ route('admin.exams.store') }}" method="POST" id="examForm">
        @csrf

        {{-- Basic Information Card --}}
        <x-ui.card class="mb-4" title="1. Examination General Information">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required fw-semibold">Exam Title / Name</label>
                    <input type="text" name="title" id="title" class="form-control" placeholder="e.g., Mid-Term Examination 2026-2027" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Exam Code</label>
                    <input type="text" name="exam_code" id="exam_code" class="form-control font-monospace" value="{{ $suggestedCode }}">
                    <small class="text-muted">Unique tracking identifier</small>
                </div>

                <div class="col-md-3">
                    <label class="form-label required fw-semibold">Academic Session</label>
                    <select name="academic_session_id" id="academic_session_id" class="form-select" required>
                        <option value="">Select Session</option>
                        @foreach($academicSessions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Target Class</label>
                    <select name="class_id" id="class_id" class="form-select">
                        <option value="">All Classes / Multi-Class</option>
                        @foreach($classes as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Students from this class will be auto-enrolled</small>
                </div>

                <div class="col-md-3">
                    <label class="form-label required fw-semibold">Exam Type</label>
                    <select name="exam_type" id="exam_type" class="form-select" required>
                        @foreach($examTypes as $val => $label)
                            <option value="{{ $val }}" {{ $val === 'mid_term' ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label required fw-semibold">Exam Mode</label>
                    <select name="exam_mode" id="exam_mode" class="form-select" required>
                        @foreach($examModes as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label required fw-semibold">Total Exam Marks</label>
                    <input type="number" step="0.5" name="total_marks" id="total_marks" class="form-control" value="100" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Passing Marks</label>
                    <input type="number" step="0.5" name="passing_marks" id="passing_marks" class="form-control" value="33">
                </div>

                <div class="col-md-3">
                    <label class="form-label required fw-semibold">Default Duration (Mins)</label>
                    <input type="number" name="duration_minutes" id="duration_minutes" class="form-control" value="180" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Instructions / Rules for Students</label>
                    <textarea name="instructions" id="instructions" rows="2" class="form-control" placeholder="1. Arrive 15 minutes before exam. 2. Electronic devices are strictly prohibited."></textarea>
                </div>
            </div>
        </x-ui.card>

        {{-- Timetable / Schedule Builder --}}
        <x-ui.card class="mb-4" title="2. Exam Timetable & Subject Schedules">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="text-muted mb-0">Add each subject's date, timing, room, invigilator, and marks distribution.</p>
                <button type="button" class="btn btn-sm btn-primary" id="btnAddScheduleRow">
                    <i class="bi bi-plus-lg me-1"></i> Add Subject Schedule
                </button>
            </div>

            <div id="schedulesContainer">
                {{-- Dynamic schedule cards will be added here --}}
            </div>
        </x-ui.card>

        {{-- Form Actions --}}
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('admin.exams.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary px-4" id="btnSaveExam">
                <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>
                <span class="btn-text"><i class="bi bi-check2-circle me-1"></i> Create Examination</span>
            </button>
        </div>
    </form>

</div>

{{-- Template for Schedule Row --}}
<template id="scheduleRowTemplate">
    <div class="card mb-3 border schedule-card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
            <span class="fw-bold text-primary schedule-title">
                <i class="bi bi-calendar3 me-1"></i> Subject Schedule #__INDEX__
            </span>
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-schedule" title="Remove Schedule">
                <i class="bi bi-trash"></i> Remove
            </button>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label required fw-semibold">Subject</label>
                    <select name="schedules[__KEY__][subject_id]" class="form-select schedule-subject" required>
                        <option value="">Select Subject</option>
                        @foreach($subjects as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Link Approved Question Paper</label>
                    <select name="schedules[__KEY__][question_paper_id]" class="form-select">
                        <option value="">None / Manual Paper</option>
                        @foreach($questionPapers as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label required fw-semibold">Exam Date</label>
                    <input type="date" name="schedules[__KEY__][exam_date]" class="form-control" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Start Time</label>
                    <input type="time" name="schedules[__KEY__][start_time]" class="form-control" value="09:00">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">End Time</label>
                    <input type="time" name="schedules[__KEY__][end_time]" class="form-control" value="12:00">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Room / Hall No</label>
                    <input type="text" name="schedules[__KEY__][room_no]" class="form-control" placeholder="e.g. Hall 101">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Invigilator</label>
                    <select name="schedules[__KEY__][invigilator_id]" class="form-select">
                        <option value="">Assign Invigilator</option>
                        @foreach($invigilators as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-7">
                    <label class="form-label fw-semibold text-muted">Marks Breakdown (Theory + Practical + Internal + Viva = Total)</label>
                    <div class="input-group">
                        <span class="input-group-text small">Theory</span>
                        <input type="number" step="0.5" name="schedules[__KEY__][max_theory_marks]" class="form-control marks-calc mark-theory" value="80">
                        <span class="input-group-text small">Pract</span>
                        <input type="number" step="0.5" name="schedules[__KEY__][max_practical_marks]" class="form-control marks-calc mark-practical" value="0">
                        <span class="input-group-text small">Int</span>
                        <input type="number" step="0.5" name="schedules[__KEY__][max_internal_marks]" class="form-control marks-calc mark-internal" value="20">
                        <span class="input-group-text small">Viva</span>
                        <input type="number" step="0.5" name="schedules[__KEY__][max_viva_marks]" class="form-control marks-calc mark-viva" value="0">
                        <span class="input-group-text fw-bold bg-light">Total</span>
                        <input type="number" step="0.5" name="schedules[__KEY__][total_marks]" class="form-control fw-bold text-primary mark-total" value="100" readonly>
                        <span class="input-group-text small">Pass</span>
                        <input type="number" step="0.5" name="schedules[__KEY__][passing_marks]" class="form-control mark-pass" value="33">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
    const EXAM_INDEX_URL = "{{ route('admin.exams.index') }}";
</script>
<script src="{{ asset('assets/admin/js/exams.js') }}"></script>
@endpush
