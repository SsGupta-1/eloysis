@extends('layouts.admin.master')

@section('title', 'Result Management & Broadsheet')

@section('content')
<div class="container-fluid">

    <x-ui.page-header
        title="Result Management"
        subtitle="Compute broadsheets, tabulation matrices, generate student report cards, and publish results">
        <x-slot:actions>
            <a href="{{ route('admin.exams.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-journal-text me-1"></i> Go to Examinations
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Filters Card --}}
    <x-ui.card class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <x-ui.select
                    name="academic_session_id"
                    id="filter_academic_session_id"
                    :options="$academicSessions"
                    placeholder="All Academic Sessions">
                    Academic Session
                </x-ui.select>
            </div>

            <div class="col-md-3">
                <x-ui.select
                    name="class_id"
                    id="filter_class_id"
                    :options="$classes"
                    placeholder="All Classes">
                    Class
                </x-ui.select>
            </div>

            <div class="col-md-3">
                <x-ui.select
                    name="is_published"
                    id="filter_is_published"
                    :options="[1 => 'Published (Visible to Students)', 0 => 'Unpublished / Draft (Hidden)']"
                    placeholder="Publication Status">
                    Publication Status
                </x-ui.select>
            </div>

            <div class="col-md-2">
                <button type="button" class="btn btn-outline-secondary w-100" id="btnResetFilters">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                </button>
            </div>
        </div>
    </x-ui.card>

    {{-- Results Overview Table --}}
    <x-ui.card>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="resultsTable" style="width: 100%">
                <thead class="table-light">
                    <tr>
                        <th width="40">#</th>
                        <th>Examination</th>
                        <th>Class & Session</th>
                        <th>Subjects</th>
                        <th>Students Enrolled</th>
                        <th>Publication Status</th>
                        <th width="180" class="text-center">Actions</th>
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
    const RESULT_LIST_URL = "{{ route('admin.results.list') }}";
    const RESULT_BASE_URL = "{{ url('admin/results') }}";
    const PUBLISH_TOGGLE_URL = "{{ url('admin/results/exam') }}/:id/publish";
</script>
<script src="{{ asset('assets/admin/js/results.js') }}"></script>
@endpush
