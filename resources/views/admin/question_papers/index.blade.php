@extends('layouts.admin.master')

@section('title', 'Question Papers')

@section('content')
<div class="container-fluid">

    <x-ui.page-header
        title="Question Papers"
        subtitle="Design, approve, manage sets (A/B/C/D), and generate printable question papers">
        <x-slot:actions>
            <a href="{{ route('admin.questions.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-patch-question me-1"></i> Question Bank
            </a>
            <a href="{{ route('admin.question-papers.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Create Question Paper
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Filters --}}
    <x-ui.card class="mb-4">
        <div class="row g-3 align-items-end">
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
                    name="subject_id"
                    id="filter_subject_id"
                    :options="$subjects"
                    placeholder="All Subjects">
                    Subject
                </x-ui.select>
            </div>

            <div class="col-md-2">
                <x-ui.select
                    name="approval_status"
                    id="filter_approval_status"
                    :options="$approvalStatuses"
                    placeholder="All Approval Statuses">
                    Approval
                </x-ui.select>
            </div>

            <div class="col-md-2">
                <x-ui.select
                    name="is_locked"
                    id="filter_is_locked"
                    :options="[1 => 'Locked', 0 => 'Unlocked']"
                    placeholder="Lock Status">
                    Paper Lock
                </x-ui.select>
            </div>

            <div class="col-md-2">
                <button type="button" class="btn btn-outline-secondary w-100" id="btnResetFilters">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                </button>
            </div>
        </div>
    </x-ui.card>

    {{-- Question Papers Table --}}
    <x-ui.card>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="questionPapersTable" style="width: 100%">
                <thead class="table-light">
                    <tr>
                        <th width="40">#</th>
                        <th>Paper Code & Title</th>
                        <th>Class & Subject</th>
                        <th>Marks / Duration</th>
                        <th>Sections & Sets</th>
                        <th>Security / Lock</th>
                        <th>Approval</th>
                        <th width="160" class="text-center">Actions</th>
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
    const QP_LIST_URL = "{{ route('admin.question-papers.list') }}";
    const QP_SHOW_URL = "{{ url('admin/question-papers') }}/:id";
    const QP_UPDATE_URL = "{{ url('admin/question-papers') }}/:id";
    const QP_LOCK_URL = "{{ url('admin/question-papers') }}/:id/lock";
</script>
<script src="{{ asset('assets/admin/js/question-papers.js') }}"></script>
@endpush
