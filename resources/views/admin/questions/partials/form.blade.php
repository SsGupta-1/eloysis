@php
    $questionType = old('question_type', $question->question_type ?? 'mcq');
    $correctOption = old('correct_option', $question->correct_option ?? 'a');
    $difficulty = old('difficulty_level', $question->difficulty_level ?? 'medium');
    $taxonomy = old('blooms_taxonomy', $question->blooms_taxonomy ?? '');
@endphp

{{-- 1. Classification & Subject Info --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold text-primary">
            <i class="bi bi-bookmark-check me-2"></i> Academic Classification
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <x-ui.select
                    label="Class"
                    name="class_id"
                    id="class_id"
                    :value="old('class_id', $question->class_id ?? '')"
                    :options="$classes"
                    placeholder="Select Class"
                    required />
            </div>

            <div class="col-md-4">
                <x-ui.select
                    label="Subject"
                    name="subject_id"
                    id="subject_id"
                    :value="old('subject_id', $question->subject_id ?? '')"
                    :options="$subjects"
                    placeholder="Select Subject"
                    required />
            </div>

            <div class="col-md-4">
                <x-ui.select
                    label="Academic Session"
                    name="academic_session_id"
                    id="academic_session_id"
                    :value="old('academic_session_id', $question->academic_session_id ?? '')"
                    :options="$academicSessions"
                    placeholder="Select Session (Optional)" />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    label="Chapter Name"
                    name="chapter_name"
                    id="chapter_name"
                    placeholder="e.g. Thermodynamics / Trigonometry"
                    :value="old('chapter_name', $question->chapter_name ?? '')" />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    label="Topic Name"
                    name="topic_name"
                    id="topic_name"
                    placeholder="e.g. Carnot Cycle / Heights & Distances"
                    :value="old('topic_name', $question->topic_name ?? '')" />
            </div>
        </div>
    </div>
</div>

{{-- 2. Metadata & Grading --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold text-primary">
            <i class="bi bi-sliders me-2"></i> Question Type & Metadata
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <x-ui.select
                    label="Question Type"
                    name="question_type"
                    id="question_type"
                    :value="$questionType"
                    :options="$questionTypes"
                    required />
            </div>

            <div class="col-md-4">
                <x-ui.select
                    label="Difficulty Level"
                    name="difficulty_level"
                    id="difficulty_level"
                    :value="$difficulty"
                    :options="$difficultyLevels"
                    required />
            </div>

            <div class="col-md-4">
                <x-ui.select
                    label="Bloom's Taxonomy Level"
                    name="blooms_taxonomy"
                    id="blooms_taxonomy"
                    :value="$taxonomy"
                    :options="$bloomsTaxonomy"
                    placeholder="Select Taxonomy (Optional)" />
            </div>

            <div class="col-md-4">
                <x-ui.form-input
                    label="Marks"
                    name="marks"
                    id="marks"
                    type="number"
                    step="0.5"
                    min="0.5"
                    :value="old('marks', $question->marks ?? '1.00')"
                    required />
            </div>

            <div class="col-md-4">
                <x-ui.form-input
                    label="Negative Marks (Optional)"
                    name="negative_marks"
                    id="negative_marks"
                    type="number"
                    step="0.25"
                    min="0"
                    :value="old('negative_marks', $question->negative_marks ?? '0.00')" />
            </div>

            <div class="col-md-4">
                <x-ui.select
                    label="Status"
                    name="status"
                    id="status"
                    :value="old('status', isset($question) ? ($question->status ? 1 : 0) : 1)"
                    :options="[1 => 'Active', 0 => 'Inactive']"
                    required />
            </div>
        </div>
    </div>
</div>

{{-- 3. Question Content --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold text-primary">
            <i class="bi bi-chat-left-text me-2"></i> Question Statement
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label fw-semibold">Question Text <span class="text-danger">*</span></label>
                <textarea
                    name="question_text"
                    id="question_text"
                    class="form-control"
                    rows="4"
                    placeholder="Type or paste the complete question text here..."
                    required>{{ old('question_text', $question->question_text ?? '') }}</textarea>
            </div>

            <div class="col-md-6">
                <x-ui.form-file
                    label="Attach Image/Diagram (Optional)"
                    name="image_path"
                    id="image_path"
                    accept="image/*"
                    help="Supported formats: JPG, PNG, WEBP, SVG (Max: 2MB)" />
                @if(!empty($question->image_path))
                    <div class="mt-2">
                        <img src="{{ asset($question->image_path) }}" alt="Question Attachment" class="img-thumbnail" style="max-height: 100px;">
                    </div>
                @endif
            </div>

            <div class="col-md-6">
                <x-ui.form-file
                    label="Reference Document/File (Optional)"
                    name="attachment_url"
                    id="attachment_url"
                    accept=".pdf,.doc,.docx"
                    help="Supported formats: PDF, DOC, DOCX (Max: 5MB)" />
            </div>
        </div>
    </div>
</div>

{{-- 4. Dynamic Options & Answers based on Question Type --}}

{{-- A. MCQ Options Block --}}
<div class="card shadow-sm border-0 mb-4 type-block" id="block_mcq">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-primary">
            <i class="bi bi-ui-radios-grid me-2"></i> Multiple Choice Options
        </h5>
        <span class="badge bg-light text-muted border">Select the radio button for the correct option</span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-text bg-white">
                        <input class="form-check-input mt-0" type="radio" name="correct_option" value="a" {{ $correctOption == 'a' ? 'checked' : '' }}>
                        <span class="ms-2 fw-bold text-primary">A</span>
                    </div>
                    <input type="text" name="option_a" class="form-control" placeholder="Option A text" value="{{ old('option_a', $question->option_a ?? '') }}">
                </div>
            </div>

            <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-text bg-white">
                        <input class="form-check-input mt-0" type="radio" name="correct_option" value="b" {{ $correctOption == 'b' ? 'checked' : '' }}>
                        <span class="ms-2 fw-bold text-primary">B</span>
                    </div>
                    <input type="text" name="option_b" class="form-control" placeholder="Option B text" value="{{ old('option_b', $question->option_b ?? '') }}">
                </div>
            </div>

            <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-text bg-white">
                        <input class="form-check-input mt-0" type="radio" name="correct_option" value="c" {{ $correctOption == 'c' ? 'checked' : '' }}>
                        <span class="ms-2 fw-bold text-primary">C</span>
                    </div>
                    <input type="text" name="option_c" class="form-control" placeholder="Option C text" value="{{ old('option_c', $question->option_c ?? '') }}">
                </div>
            </div>

            <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-text bg-white">
                        <input class="form-check-input mt-0" type="radio" name="correct_option" value="d" {{ $correctOption == 'd' ? 'checked' : '' }}>
                        <span class="ms-2 fw-bold text-primary">D</span>
                    </div>
                    <input type="text" name="option_d" class="form-control" placeholder="Option D text" value="{{ old('option_d', $question->option_d ?? '') }}">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- B. True / False Block --}}
<div class="card shadow-sm border-0 mb-4 type-block d-none" id="block_true_false">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold text-primary">
            <i class="bi bi-check2-circle me-2"></i> True / False Selection
        </h5>
    </div>
    <div class="card-body">
        <label class="form-label fw-semibold">Correct Answer:</label>
        <div class="d-flex gap-4">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="correct_tf" id="tf_true" value="a" {{ ($correctOption == 'a' || old('correct_tf') == 'a') ? 'checked' : '' }}>
                <label class="form-check-label fw-bold text-success" for="tf_true">
                    <i class="bi bi-check-lg"></i> True
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="correct_tf" id="tf_false" value="b" {{ ($correctOption == 'b' || old('correct_tf') == 'b') ? 'checked' : '' }}>
                <label class="form-check-label fw-bold text-danger" for="tf_false">
                    <i class="bi bi-x-lg"></i> False
                </label>
            </div>
        </div>
    </div>
</div>

{{-- C. Fill in the Blanks / Text Answer Block --}}
<div class="card shadow-sm border-0 mb-4 type-block d-none" id="block_fill_blanks">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold text-primary">
            <i class="bi bi-input-cursor-text me-2"></i> Correct Blank Answer
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <label class="form-label fw-semibold">Expected Correct Word / Phrase</label>
                <input type="text" name="correct_blank_answer" id="correct_blank_answer" class="form-control" placeholder="e.g. Carbon Dioxide" value="{{ old('correct_blank_answer', is_string($question->correct_answer_data ?? null) ? $question->correct_answer_data : '') }}">
                <small class="text-muted">For multiple acceptable synonyms, separate by comma.</small>
            </div>
        </div>
    </div>
</div>

{{-- D. Match the Following Block --}}
<div class="card shadow-sm border-0 mb-4 type-block d-none" id="block_match_following">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-primary">
            <i class="bi bi-arrow-left-right me-2"></i> Matching Pairs (Column A ➔ Column B)
        </h5>
        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddMatchPair">
            <i class="bi bi-plus-lg me-1"></i> Add Pair
        </button>
    </div>
    <div class="card-body" id="matchPairsContainer">
        @php
            $pairs = old('match_pairs', $question->options_data ?? [
                ['left' => '', 'right' => ''],
                ['left' => '', 'right' => ''],
            ]);
        @endphp
        @foreach($pairs as $pIdx => $pair)
            <div class="row g-2 mb-2 match-pair-row align-items-center">
                <div class="col-md-5">
                    <input type="text" name="match_pairs[{{ $pIdx }}][left]" class="form-control" placeholder="Column A item {{ $pIdx + 1 }}" value="{{ $pair['left'] ?? '' }}">
                </div>
                <div class="col-md-1 text-center text-muted">
                    <i class="bi bi-arrow-right"></i>
                </div>
                <div class="col-md-5">
                    <input type="text" name="match_pairs[{{ $pIdx }}][right]" class="form-control" placeholder="Matching Column B item" value="{{ $pair['right'] ?? '' }}">
                </div>
                <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-pair" title="Remove Pair">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- E. Descriptive / Solution / Explanation Block --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold text-primary">
            <i class="bi bi-lightbulb me-2"></i> Solution & Step-by-Step Explanation
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label fw-semibold">Explanation / Solution Note (Shown in Answer Key)</label>
                <textarea
                    name="explanation"
                    id="explanation"
                    class="form-control"
                    rows="3"
                    placeholder="Enter detailed solution, calculation steps, or marking rubric...">{{ old('explanation', $question->explanation ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>
