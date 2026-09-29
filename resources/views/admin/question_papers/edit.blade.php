@extends('layouts.admin.master')

@section('title', 'Edit Question Paper - ' . $paper->title)

@section('content')
<div class="container-fluid">

    <x-ui.page-header
        title="Edit Question Paper"
        subtitle="Modify sections, questions, marks, and configuration for {{ $paper->paper_code }}">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-success me-2" id="btnOpenBlueprintModal">
                <i class="bi bi-magic me-1"></i> Auto-Generate Blueprint
            </button>
            <a href="{{ route('admin.question-papers.show', $paper->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Preview
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <form id="questionPaperForm" method="POST" action="{{ route('admin.question-papers.update', $paper->id) }}">
        @csrf
        @method('PUT')

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
                            :value="old('title', $paper->title)"
                            required />
                    </div>

                    <div class="col-md-3">
                        <x-ui.form-input
                            label="Paper Code"
                            name="paper_code"
                            id="paper_code"
                            :value="old('paper_code', $paper->paper_code)"
                            required />
                    </div>

                    <div class="col-md-3">
                        <x-ui.select
                            label="Academic Session"
                            name="academic_session_id"
                            id="academic_session_id"
                            :value="old('academic_session_id', $paper->academic_session_id)"
                            :options="$academicSessions"
                            placeholder="Select Session" />
                    </div>

                    <div class="col-md-3">
                        <x-ui.select
                            label="Class"
                            name="class_id"
                            id="paper_class_id"
                            :value="old('class_id', $paper->class_id)"
                            :options="$classes"
                            placeholder="Select Class"
                            required />
                    </div>

                    <div class="col-md-3">
                        <x-ui.select
                            label="Subject"
                            name="subject_id"
                            id="paper_subject_id"
                            :value="old('subject_id', $paper->subject_id)"
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
                            :value="old('total_marks', $paper->total_marks)"
                            required />
                    </div>

                    <div class="col-md-3">
                        <x-ui.form-input
                            label="Duration (Minutes)"
                            name="duration_minutes"
                            id="duration_minutes"
                            type="number"
                            step="5"
                            :value="old('duration_minutes', $paper->duration_minutes)"
                            required />
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">General Instructions for Students</label>
                        <textarea
                            name="instructions"
                            id="instructions"
                            class="form-control"
                            rows="3">{{ old('instructions', $paper->instructions) }}</textarea>
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
                            <input class="form-check-input" type="checkbox" name="is_confidential" id="is_confidential" value="1" {{ $paper->is_confidential ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_confidential">
                                Confidential Paper
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-check form-switch pt-2">
                            <input class="form-check-input" type="checkbox" name="has_sets" id="has_sets" value="1" {{ $paper->has_sets ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="has_sets">
                                Generate Multi-Sets (Set A, B, C, D)
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-check form-switch pt-2">
                            <input class="form-check-input" type="checkbox" name="shuffle_questions" id="shuffle_questions" value="1" {{ $paper->shuffle_questions ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="shuffle_questions">
                                Shuffle Question Order
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
                <span class="text-muted ms-2">/ Target: <span id="targetTotalMarksText">{{ $paper->total_marks }} M</span></span>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-primary" id="btnAddSection">
                    <i class="bi bi-plus-circle me-1"></i> Add New Section
                </button>
            </div>
        </div>

        {{-- 4. Sections Container --}}
        <div id="sectionsContainer">
            @forelse($paper->sections as $sIdx => $section)
                @php
                    $sectionItems = $paper->items->where('section_id', $section->id)->where('set_code', 'ALL');
                    if ($sectionItems->isEmpty()) {
                        $sectionItems = $paper->items->where('section_id', $section->id);
                    }
                @endphp
                <div class="card shadow-sm border-0 mb-4 section-card" data-section-index="{{ $sIdx }}">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-grid-3x3-gap text-muted"></i>
                            <input type="text" name="sections[{{ $sIdx }}][section_name]" class="form-control form-control-sm fw-bold section-title-input" value="{{ $section->section_name }}" style="width: 320px;">
                            <select name="sections[{{ $sIdx }}][section_type]" class="form-select form-select-sm" style="width: 160px;">
                                <option value="mcq" {{ $section->section_type === 'mcq' ? 'selected' : '' }}>MCQ (1 Mark)</option>
                                <option value="short_answer" {{ $section->section_type === 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                                <option value="long_answer" {{ $section->section_type === 'long_answer' ? 'selected' : '' }}>Long Answer</option>
                                <option value="true_false" {{ $section->section_type === 'true_false' ? 'selected' : '' }}>True / False</option>
                                <option value="fill_blanks" {{ $section->section_type === 'fill_blanks' ? 'selected' : '' }}>Fill in Blanks</option>
                                <option value="descriptive" {{ $section->section_type === 'descriptive' ? 'selected' : '' }}>Descriptive</option>
                                <option value="match_following" {{ $section->section_type === 'match_following' ? 'selected' : '' }}>Match Following</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary section-marks-badge">{{ $sectionItems->count() }} Questions</span>
                            <button type="button" class="btn btn-sm btn-outline-primary btn-pick-questions" data-section-index="{{ $sIdx }}">
                                <i class="bi bi-plus-lg me-1"></i> Select from Bank
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-section" title="Delete Section">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="section-questions-list" data-section-index="{{ $sIdx }}">
                            @forelse($sectionItems as $qIdx => $item)
                                @php $q = $item->question; @endphp
                                <div class="card border mb-2 shadow-none question-item-card p-3" data-qid="{{ $q->id }}">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div>
                                            <span class="badge bg-secondary text-uppercase me-1">{{ $q->question_type }}</span>
                                            <span class="badge bg-light text-dark border me-1">{{ strtoupper($q->difficulty_level) }}</span>
                                            <input type="hidden" name="sections[{{ $sIdx }}][questions][{{ $qIdx }}][question_id]" value="{{ $q->id }}">
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="input-group input-group-sm" style="width: 120px;">
                                                <span class="input-group-text">Marks</span>
                                                <input type="number" step="0.5" min="0.5" name="sections[{{ $sIdx }}][questions][{{ $qIdx }}][marks]" class="form-control item-marks-input" value="{{ $item->marks }}">
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-question-item">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="text-dark small">{{ $q->question_text }}</div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted empty-section-placeholder">
                                    <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                    No questions added to this section yet. Click <strong>"Select from Bank"</strong> above to add questions.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="card shadow-sm border-0 mb-4 section-card" data-section-index="0">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-grid-3x3-gap text-muted"></i>
                            <input type="text" name="sections[0][section_name]" class="form-control form-control-sm fw-bold section-title-input" value="Section A" style="width: 320px;">
                            <select name="sections[0][section_type]" class="form-select form-select-sm" style="width: 160px;">
                                <option value="mcq">MCQ (1 Mark)</option>
                                <option value="short_answer">Short Answer</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary section-marks-badge">0 Questions</span>
                            <button type="button" class="btn btn-sm btn-outline-primary btn-pick-questions" data-section-index="0">
                                <i class="bi bi-plus-lg me-1"></i> Select from Bank
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="section-questions-list" data-section-index="0">
                            <div class="text-center py-4 text-muted empty-section-placeholder">
                                No questions added.
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Form Actions --}}
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('admin.question-papers.show', $paper->id) }}" class="btn btn-secondary px-4">Cancel</a>
            <button type="submit" class="btn btn-success px-4" id="btnSavePaper">
                <span class="btn-text">
                    <i class="bi bi-check2-circle me-1"></i> Update Question Paper
                </span>
                <span class="spinner-border spinner-border-sm d-none"></span>
            </button>
        </div>
    </form>

</div>

{{-- Modals from create --}}
{{-- Question Selector Modal --}}
<div class="modal fade" id="questionSelectorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-patch-question me-2"></i> Select Questions from Question Bank</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
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
                        <tbody id="modalQuestionsBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div><span class="fw-semibold text-primary" id="selectedCountText">0 questions selected</span></div>
                <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="btnAddSelectedQuestionsToSection">
                        <i class="bi bi-plus-lg me-1"></i> Add to Section
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Auto-Generate Blueprint Modal --}}
<div class="modal fade" id="autoBlueprintModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-magic me-2"></i> Auto-Generate Blueprint from Question Bank</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="blueprintRowsContainer">
                    <div class="row g-2 mb-2 blueprint-row align-items-center">
                        <div class="col-md-3"><input type="text" class="form-control form-control-sm bp-name" value="Section A (MCQ)"></div>
                        <div class="col-md-3">
                            <select class="form-select form-select-sm bp-type">
                                <option value="mcq">MCQ</option>
                                <option value="short_answer">Short Answer</option>
                            </select>
                        </div>
                        <div class="col-md-2"><input type="number" class="form-control form-control-sm bp-count" value="10" min="1"></div>
                        <div class="col-md-2"><input type="number" class="form-control form-control-sm bp-marks" value="1" min="0.5" step="0.5"></div>
                        <div class="col-md-2 text-center"><span class="badge bg-light text-dark border bp-total-badge">10 M</span></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success px-4" id="btnExecuteAutoBlueprint">
                    <i class="bi bi-lightning-charge me-1"></i> Regenerate Paper
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const QP_INDEX_URL = "{{ route('admin.question-papers.index') }}";
    const SEARCH_QUESTIONS_URL = "{{ route('admin.questions.search-selection') }}";
    const AUTO_BLUEPRINT_URL = "{{ route('admin.question-papers.auto-blueprint') }}";
</script>
<script src="{{ asset('assets/admin/js/question-papers.js') }}"></script>
@endpush
