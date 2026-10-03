@extends('layouts.admin.master')

@section('title', 'Question Bank')

@section('content')
<div class="container-fluid">

    <x-ui.page-header
        title="Question Bank"
        subtitle="Manage repository of examination questions, types, difficulties, and taxonomies">
        <x-slot:actions>
            <a href="{{ route('admin.question-papers.create') }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-file-earmark-plus me-1"></i> Create Question Paper
            </a>
            <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Question
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Filters --}}
    <x-ui.card class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-2">
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
                    name="question_type"
                    id="filter_question_type"
                    :options="$questionTypes"
                    placeholder="All Types">
                    Question Type
                </x-ui.select>
            </div>

            <div class="col-md-2">
                <x-ui.select
                    name="difficulty_level"
                    id="filter_difficulty_level"
                    :options="$difficultyLevels"
                    placeholder="All Difficulties">
                    Difficulty
                </x-ui.select>
            </div>

            <div class="col-md-1">
                <x-ui.select
                    name="status"
                    id="filter_status"
                    :options="[1 => 'Active', 0 => 'Inactive']"
                    placeholder="Status">
                    Status
                </x-ui.select>
            </div>

            <div class="col-md-2">
                <button type="button" class="btn btn-outline-secondary w-100" id="btnResetFilters">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                </button>
            </div>
        </div>
    </x-ui.card>

    {{-- Questions Table Card --}}
    <x-ui.card>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="questionsTable" style="width: 100%">
                <thead class="table-light">
                    <tr>
                        <th width="40">#</th>
                        <th>Question</th>
                        <th>Class & Subject</th>
                        <th>Type</th>
                        <th>Difficulty</th>
                        <th>Marks</th>
                        <th>Status</th>
                        <th width="120" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </x-ui.card>

</div>

{{-- Question Preview Modal --}}
<x-ui.modal id="previewQuestionModal" title="Question Details" size="lg">
    <div id="previewQuestionBody">
        <div class="text-center py-4">
            <span class="spinner-border text-primary"></span>
        </div>
    </div>

    <x-slot:footer>
        <x-ui.button variant="secondary" data-bs-dismiss="modal">Close</x-ui.button>
    </x-slot:footer>
</x-ui.modal>
@endsection

@push('scripts')
<script>
    const QUESTION_LIST_URL = "{{ route('admin.questions.list') }}";
    const QUESTION_SHOW_URL = "{{ url('admin/questions') }}/:id";
    const QUESTION_UPDATE_URL = "{{ url('admin/questions') }}/:id";
    const QUESTION_STATUS_URL = "{{ url('admin/questions') }}/:id/status";
</script>
<script src="{{ asset('assets/admin/js/questions.js') }}"></script>
@endpush
