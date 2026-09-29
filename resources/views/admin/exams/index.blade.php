@extends('layouts.admin.master')

@section('title', 'Exam Management')

@section('content')
<div class="container-fluid">

    <x-ui.page-header
        title="Exam Management"
        subtitle="Schedule and conduct examinations, manage student enrollments, timetable schedules, and marks entry">
        <x-slot:actions>
            <a href="{{ route('admin.question-papers.index') }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-file-earmark-text me-1"></i> Question Papers
            </a>
            <a href="{{ route('admin.results.index') }}" class="btn btn-outline-success me-2">
                <i class="bi bi-award me-1"></i> Results & Broadsheet
            </a>
            <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Create Exam
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Filters Card --}}
    <x-ui.card class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <x-ui.select
                    name="academic_session_id"
                    id="filter_academic_session_id"
                    :options="$academicSessions"
                    placeholder="All Academic Sessions">
                    Academic Session
                </x-ui.select>
            </div>

            <div class="col-md-2">
                <x-ui.select
                    name="class_id"
                    id="filter_class_id"
                    :options="$classes"
                    placeholder="All Classes">
                    Class
                </x-ui.select>
            </div>

            <div class="col-md-2">
                <x-ui.select
                    name="exam_type"
                    id="filter_exam_type"
                    :options="$examTypes"
                    placeholder="All Types">
                    Exam Type
                </x-ui.select>
            </div>

            <div class="col-md-2">
                <x-ui.select
                    name="exam_mode"
                    id="filter_exam_mode"
                    :options="$examModes"
                    placeholder="All Modes">
                    Mode
                </x-ui.select>
            </div>

            <div class="col-md-2">
                <x-ui.select
                    name="status"
                    id="filter_status"
                    :options="$statuses"
                    placeholder="All Statuses">
                    Status
                </x-ui.select>
            </div>

            <div class="col-md-1">
                <button type="button" class="btn btn-outline-secondary w-100" id="btnResetFilters" title="Reset Filters">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
            </div>
        </div>
    </x-ui.card>

    {{-- Exams Data Table --}}
    <x-ui.card>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="examsTable" style="width: 100%">
                <thead class="table-light">
                    <tr>
                        <th width="40">#</th>
                        <th>Exam Details</th>
                        <th>Class & Session</th>
                        <th>Type & Mode</th>
                        <th>Duration / Marks</th>
                        <th>Subjects</th>
                        <th>Enrolled</th>
                        <th>Status</th>
                        <th width="140" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </x-ui.card>

</div>
@endsection

@push('scripts')
<script>
    const EXAM_LIST_URL = "{{ route('admin.exams.list') }}";
    const EXAM_BASE_URL = "{{ url('admin/exams') }}";
    const EXAM_STATUS_URL = "{{ url('admin/exams') }}/:id/status";
</script>
<script src="{{ asset('assets/admin/js/exams.js') }}"></script>
@endpush
