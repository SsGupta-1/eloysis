@extends('layouts.admin.master')

@section('title', 'Create Question Paper')

@section('content')
<div class="container-fluid">

    <x-ui.page-header
        title="Question Paper Builder"
        subtitle="Design comprehensive question papers with multiple sections, sets, and live marks breakdown">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-success me-2" id="btnOpenBlueprintModal">
                <i class="bi bi-magic me-1"></i> Auto-Generate Blueprint
            </button>
            <a href="{{ route('admin.question-papers.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <form id="questionPaperForm" method="POST" action="{{ route('admin.question-papers.store') }}">
        @csrf

        {{-- 1. General Paper Details Card --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-file-earmark-text me-2"></i> Paper Specifications & Header
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <x-ui.form-input
                            label="Paper Title"
                            name="title"
                            id="paper_title"
                            placeholder="e.g. Annual Final Examination 2026 - Mathematics"
                            value="{{ old('title') }}"
                            required />
                    </div>

                    <div class="col-md-3">
                        <x-ui.form-input
                            label="Paper Code"
                            name="paper_code"
                            id="paper_code"
                            value="{{ old('paper_code', $suggestedCode) }}"
                            required />
                    </div>

                    <div class="col-md-3">
                        <x-ui.select
                            label="Academic Session"
                            name="academic_session_id"
                            id="academic_session_id"
                            :options="$academicSessions"
                            placeholder="Select Session" />
                    </div>

                    <div class="col-md-3">
                        <x-ui.select
                            label="Class"
                            name="class_id"
                            id="paper_class_id"
                            :options="$classes"
                            placeholder="Select Class"
                            required />
                    </div>

                    <div class="col-md-3">
                        <x-ui.select
                            label="Subject"
                            name="subject_id"
                            id="paper_subject_id"
                            :options="$subjects"
                            placeholder="Select Subject"
                            required />
                    </div>

                    <div class="col-md-3">
                        <x-ui.form-input
                            label="Total Marks"
                            name="total_marks"
                            id="total_marks"
                            type="number"
                            step="1"
                            value="{{ old('total_marks', 100) }}"
                            required />
                    </div>

                    <div class="col-md-3">
                        <x-ui.form-input
                            label="Duration (Minutes)"
                            name="duration_minutes"
                            id="duration_minutes"
                            type="number"
                            step="5"
                            value="{{ old('duration_minutes', 180) }}"
                            help="e.g. 180 min = 3 Hours"
                            required />
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">General Instructions for Students</label>
                        <textarea
                            name="instructions"
                            id="instructions"
                            class="form-control"
                            rows="3"
                            placeholder="1. All questions are compulsory.&#10;2. Use of calculator is strictly prohibited.&#10;3. Section A contains 10 MCQs of 1 mark each...">{{ old('instructions', "1. All questions are compulsory.\n2. Read each question carefully before attempting.\n3. Write your Roll Number clearly on the answer sheet.") }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Security & Sets Configuration Card --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-shield-lock me-2"></i> Confidentiality & Sets Options
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-check form-switch pt-2">
                            <input class="form-check-input" type="checkbox" name="is_confidential" id="is_confidential" value="1" checked>
                            <label class="form-check-label fw-semibold" for="is_confidential">
                                Confidential Paper <small class="text-muted d-block">Restricts view to authorized faculty & administrators</small>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-check form-switch pt-2">
                            <input class="form-check-input" type="checkbox" name="has_sets" id="has_sets" value="1">
                            <label class="form-check-label fw-semibold" for="has_sets">
                                Generate Multi-Sets (Set A, B, C, D) <small class="text-muted d-block">Generates randomized sets upon saving</small>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-check form-switch pt-2">
                            <input class="form-check-input" type="checkbox" name="shuffle_questions" id="shuffle_questions" value="1">
                            <label class="form-check-label fw-semibold" for="shuffle_questions">
                                Shuffle Question Order <small class="text-muted d-block">Permutes question sequence across sets</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Live Marks Summary Banner --}}
        <div class="alert alert-primary shadow-sm border-0 d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="fs-6 fw-bold">Live Marks Counter:</span>
                <span class="ms-2 fs-5 badge bg-white text-primary border" id="counterTotalMarks">0.00 M</span>
                <span class="text-muted ms-2">/ Target: <span id="targetTotalMarksText">100.00 M</span></span>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-primary" id="btnAddSection">
                    <i class="bi bi-plus-circle me-1"></i> Add New Section
                </button>
            </div>
        </div>

        {{-- 4. Sections Container --}}
        <div id="sectionsContainer">
            {{-- Default Section A --}}
            <div class="card shadow-sm border-0 mb-4 section-card" data-section-index="0">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-grid-3x3-gap text-muted"></i>
                        <input type="text" name="sections[0][section_name]" class="form-control form-control-sm fw-bold section-title-input" value="Section A - Multiple Choice Questions" style="width: 320px;">
                        <select name="sections[0][section_type]" class="form-select form-select-sm" style="width: 160px;">
                            <option value="mcq">MCQ (1 Mark)</option>
                            <option value="short_answer">Short Answer</option>
                            <option value="long_answer">Long Answer</option>
                            <option value="true_false">True / False</option>
                            <option value="fill_blanks">Fill in Blanks</option>
                            <option value="descriptive">Descriptive</option>
                            <option value="match_following">Match Following</option>
                        </select>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-secondary section-marks-badge">0 Questions (0 Marks)</span>
                        <button type="button" class="btn btn-sm btn-outline-primary btn-pick-questions" data-section-index="0">
                            <i class="bi bi-plus-lg me-1"></i> Select from Bank
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-section" title="Delete Section">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="section-questions-list" data-section-index="0">
                        <div class="text-center py-4 text-muted empty-section-placeholder">
                            <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                            No questions added to this section yet. Click <strong>"Select from Bank"</strong> above to add questions.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('admin.question-papers.index') }}" class="btn btn-secondary px-4">Cancel</a>
            <button type="submit" class="btn btn-success px-4" id="btnSavePaper">
                <span class="btn-text">
                    <i class="bi bi-check2-circle me-1"></i> Save & Build Question Paper
                </span>
                <span class="spinner-border spinner-border-sm d-none"></span>
            </button>
        </div>
    </form>

</div>

{{-- Question Selector Modal --}}
<x-ui.modal id="questionSelectorModal" title="Select Questions from Question Bank" size="xl">
    {{-- Filter Bar --}}
    <div class="row g-2 mb-3">
        <div class="col-md-3">
            <select id="modal_filter_type" class="form-select form-select-sm">
                <option value="">All Question Types</option>
                <option value="mcq">Multiple Choice (MCQ)</option>
                <option value="true_false">True / False</option>
                <option value="fill_blanks">Fill in Blanks</option>
                <option value="short_answer">Short Answer</option>
                <option value="long_answer">Long Answer</option>
                <option value="descriptive">Descriptive</option>
                <option value="match_following">Match Following</option>
            </select>
        </div>
        <div class="col-md-3">
            <select id="modal_filter_difficulty" class="form-select form-select-sm">
                <option value="">All Difficulties</option>
                <option value="easy">Easy</option>
                <option value="medium">Medium</option>
                <option value="hard">Hard</option>
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" id="modal_filter_search" class="form-control form-control-sm" placeholder="Search question text or chapter...">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-sm btn-primary w-100" id="btnFilterModalQuestions">
                <i class="bi bi-search me-1"></i> Search
            </button>
        </div>
    </div>

    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0" id="modalQuestionsTable">
            <thead class="table-light sticky-top">
                <tr>
                    <th width="40"><input type="checkbox" id="selectAllModalQuestions" class="form-check-input"></th>
                    <th>Question</th>
                    <th>Type</th>
                    <th>Difficulty</th>
                    <th>Marks</th>
                </tr>
            </thead>
            <tbody id="modalQuestionsBody">
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        Select Class and Subject first to load available questions.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <x-slot:footer>
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>
                <span class="fw-semibold text-primary" id="selectedCountText">0 questions selected</span>
            </div>
            <div>
                <x-ui.button variant="secondary" data-bs-dismiss="modal">Cancel</x-ui.button>
                <x-ui.button variant="success" id="btnAddSelectedQuestionsToSection" icon="bi-plus-lg">
                    Add to Section
                </x-ui.button>
            </div>
        </div>
    </x-slot:footer>
</x-ui.modal>

{{-- Auto-Generate Blueprint Modal --}}
<x-ui.modal id="autoBlueprintModal" title="Auto-Generate Blueprint from Question Bank" size="lg">
    <p class="text-muted small">
        Specify the number of questions and marks for each section. The system will automatically pick balanced questions from your Question Bank.
    </p>

    <div id="blueprintRowsContainer">
        <div class="row g-2 mb-2 blueprint-row align-items-center">
            <div class="col-md-3">
                <input type="text" class="form-control form-control-sm bp-name" value="Section A (MCQ)" placeholder="Section Name">
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm bp-type">
                    <option value="mcq">MCQ</option>
                    <option value="short_answer">Short Answer</option>
                    <option value="long_answer">Long Answer</option>
                    <option value="true_false">True / False</option>
                    <option value="fill_blanks">Fill Blanks</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control form-control-sm bp-count" value="10" placeholder="Count" min="1">
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control form-control-sm bp-marks" value="1" placeholder="Marks/Q" min="0.5" step="0.5">
            </div>
            <div class="col-md-2 text-center">
                <span class="badge bg-light text-dark border bp-total-badge">10 M</span>
            </div>
        </div>

        <div class="row g-2 mb-2 blueprint-row align-items-center">
            <div class="col-md-3">
                <input type="text" class="form-control form-control-sm bp-name" value="Section B (Short)" placeholder="Section Name">
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm bp-type">
                    <option value="short_answer" selected>Short Answer</option>
                    <option value="mcq">MCQ</option>
                    <option value="long_answer">Long Answer</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control form-control-sm bp-count" value="5" placeholder="Count" min="1">
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control form-control-sm bp-marks" value="3" placeholder="Marks/Q" min="0.5" step="0.5">
            </div>
            <div class="col-md-2 text-center">
                <span class="badge bg-light text-dark border bp-total-badge">15 M</span>
            </div>
        </div>

        <div class="row g-2 mb-2 blueprint-row align-items-center">
            <div class="col-md-3">
                <input type="text" class="form-control form-control-sm bp-name" value="Section C (Long)" placeholder="Section Name">
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm bp-type">
                    <option value="long_answer" selected>Long Answer</option>
                    <option value="descriptive">Descriptive</option>
                    <option value="short_answer">Short Answer</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control form-control-sm bp-count" value="5" placeholder="Count" min="1">
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control form-control-sm bp-marks" value="5" placeholder="Marks/Q" min="0.5" step="0.5">
            </div>
            <div class="col-md-2 text-center">
                <span class="badge bg-light text-dark border bp-total-badge">25 M</span>
            </div>
        </div>
    </div>

    <div class="mt-2">
        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddBlueprintRow">
            <i class="bi bi-plus-lg me-1"></i> Add Blueprint Section
        </button>
    </div>

    <x-slot:footer>
        <x-ui.button variant="secondary" data-bs-dismiss="modal">Cancel</x-ui.button>
        <x-ui.button variant="success" id="btnExecuteAutoBlueprint" icon="bi-lightning-charge">
            Auto-Generate Paper
        </x-ui.button>
    </x-slot:footer>
</x-ui.modal>
@endsection

@push('scripts')
<script>
    const QP_INDEX_URL = "{{ route('admin.question-papers.index') }}";
    const SEARCH_QUESTIONS_URL = "{{ route('admin.questions.search-selection') }}";
    const AUTO_BLUEPRINT_URL = "{{ route('admin.question-papers.auto-blueprint') }}";
</script>
<script src="{{ asset('assets/admin/js/question-papers.js') }}"></script>
@endpush
