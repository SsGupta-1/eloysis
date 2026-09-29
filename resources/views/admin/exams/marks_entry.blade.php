@extends('layouts.admin.master')

@section('title', 'Marks Entry - ' . ($schedule->subject?->subject_name ?? 'Subject'))

@section('content')
<div class="container-fluid">

    <x-ui.page-header
        title="Marks Entry: {{ $schedule->subject?->subject_name ?? 'Subject' }}"
        subtitle="{{ $schedule->exam?->title }} | {{ $schedule->academicClass?->class_name ?? 'Class' }} {{ $schedule->section ? '(Section ' . ($schedule->section->name ?? '') . ')' : '' }}">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary me-2" id="btnMarkAllPresent">
                <i class="bi bi-check-all me-1"></i> Reset Absentees
            </button>
            <a href="{{ route('admin.exams.show', $schedule->exam_id) }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-1"></i> Back to Exam
            </a>
            <button type="button" class="btn btn-primary px-4 btn-save-marks-matrix">
                <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>
                <span class="btn-text"><i class="bi bi-save2 me-1"></i> Save Marks</span>
            </button>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Marks Guidelines & Configuration Card --}}
    <div class="card mb-4 border-0 shadow-sm bg-light">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-2">
                    <small class="text-muted d-block">Max Theory</small>
                    <span class="fw-bold fs-6 text-primary" id="maxTheory">{{ (float)$schedule->max_theory_marks }} M</span>
                </div>
                <div class="col-md-2">
                    <small class="text-muted d-block">Max Practical</small>
                    <span class="fw-bold fs-6 text-primary" id="maxPractical">{{ (float)$schedule->max_practical_marks }} M</span>
                </div>
                <div class="col-md-2">
                    <small class="text-muted d-block">Max Internal</small>
                    <span class="fw-bold fs-6 text-primary" id="maxInternal">{{ (float)$schedule->max_internal_marks }} M</span>
                </div>
                <div class="col-md-2">
                    <small class="text-muted d-block">Max Viva</small>
                    <span class="fw-bold fs-6 text-primary" id="maxViva">{{ (float)$schedule->max_viva_marks }} M</span>
                </div>
                <div class="col-md-2">
                    <small class="text-muted d-block">Total Max Marks</small>
                    <span class="fw-bold fs-5 text-success" id="maxTotal">{{ (float)$schedule->total_marks }} M</span>
                </div>
                <div class="col-md-2">
                    <small class="text-muted d-block">Passing Cutoff</small>
                    <span class="fw-bold fs-6 text-danger" id="minPassing">{{ (float)$schedule->passing_marks }} M</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Matrix Marks Entry Table --}}
    <form id="marksEntryForm" action="{{ route('admin.exams.schedules.save-marks', $schedule->id) }}" method="POST">
        @csrf

        <x-ui.card>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="marksMatrixTable">
                    <thead class="table-dark">
                        <tr>
                            <th width="40">#</th>
                            <th width="70">Roll #</th>
                            <th>Student Name</th>
                            <th width="110">Theory ({{ (float)$schedule->max_theory_marks }})</th>
                            <th width="110">Pract ({{ (float)$schedule->max_practical_marks }})</th>
                            <th width="110">Internal ({{ (float)$schedule->max_internal_marks }})</th>
                            <th width="110">Viva ({{ (float)$schedule->max_viva_marks }})</th>
                            <th width="110" class="text-center">Total ({{ (float)$schedule->total_marks }})</th>
                            <th width="80" class="text-center">Grade</th>
                            <th width="70" class="text-center">Absent</th>
                            <th width="70" class="text-center">Exempt</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $idx => $st)
                            @php
                                $mark = $existingMarks->get($st->id);
                                $isAbsent = $mark ? (bool)$mark->is_absent : false;
                                $isExempted = $mark ? (bool)$mark->is_exempted : false;
                                $theory = $mark ? (float)$mark->theory_marks : '';
                                $pract = $mark ? (float)$mark->practical_marks : '';
                                $intern = $mark ? (float)$mark->internal_marks : '';
                                $viva = $mark ? (float)$mark->viva_marks : '';
                                $total = $mark ? (float)$mark->total_marks : '';
                                $grade = $mark?->letter_grade ?? '-';
                            @endphp
                            <tr class="student-mark-row {{ $isAbsent ? 'table-danger-subtle' : '' }}" data-enrollment-id="{{ $st->id }}">
                                <td>{{ $idx + 1 }}</td>
                                <td class="fw-bold text-primary">{{ $st->roll_number ?? '-' }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $st->student?->user?->name ?? 'Student' }}</div>
                                    <small class="text-muted font-monospace">{{ $st->student?->admission_no ?? '-' }}</small>
                                    <input type="hidden" name="marks[{{ $idx }}][student_enrollment_id]" value="{{ $st->id }}">
                                </td>
                                <td>
                                    <input type="number" step="0.5" min="0" max="{{ (float)$schedule->max_theory_marks }}"
                                        name="marks[{{ $idx }}][theory_marks]"
                                        class="form-control form-control-sm mark-input mark-theory-val text-center"
                                        value="{{ $theory }}" {{ $isAbsent || $isExempted ? 'disabled' : '' }}>
                                </td>
                                <td>
                                    <input type="number" step="0.5" min="0" max="{{ (float)$schedule->max_practical_marks }}"
                                        name="marks[{{ $idx }}][practical_marks]"
                                        class="form-control form-control-sm mark-input mark-practical-val text-center"
                                        value="{{ $pract }}" {{ $isAbsent || $isExempted || ((float)$schedule->max_practical_marks <= 0) ? 'disabled' : '' }}>
                                </td>
                                <td>
                                    <input type="number" step="0.5" min="0" max="{{ (float)$schedule->max_internal_marks }}"
                                        name="marks[{{ $idx }}][internal_marks]"
                                        class="form-control form-control-sm mark-input mark-internal-val text-center"
                                        value="{{ $intern }}" {{ $isAbsent || $isExempted || ((float)$schedule->max_internal_marks <= 0) ? 'disabled' : '' }}>
                                </td>
                                <td>
                                    <input type="number" step="0.5" min="0" max="{{ (float)$schedule->max_viva_marks }}"
                                        name="marks[{{ $idx }}][viva_marks]"
                                        class="form-control form-control-sm mark-input mark-viva-val text-center"
                                        value="{{ $viva }}" {{ $isAbsent || $isExempted || ((float)$schedule->max_viva_marks <= 0) ? 'disabled' : '' }}>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-dark mark-total-display">{{ $total !== '' ? number_format($total, 1) : '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $grade === 'AB' ? 'bg-danger' : ($grade === 'F' ? 'bg-warning text-dark' : 'bg-success') }} mark-grade-display">{{ $grade }}</span>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" name="marks[{{ $idx }}][is_absent]" value="1" class="form-check-input chk-absent" {{ $isAbsent ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" name="marks[{{ $idx }}][is_exempted]" value="1" class="form-check-input chk-exempted" {{ $isExempted ? 'checked' : '' }}>
                                </td>
                                <td>
                                    <input type="text" name="marks[{{ $idx }}][remarks]" class="form-control form-control-sm" value="{{ $mark?->remarks ?? '' }}" placeholder="Notes...">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-5 text-muted">No students enrolled in this class/section.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        {{-- Floating Action Bar --}}
        <div class="d-flex justify-content-between align-items-center mt-3 mb-5 p-3 bg-white rounded shadow-sm border">
            <span class="text-muted small">
                <i class="bi bi-info-circle me-1"></i> Tip: Use <strong>Tab</strong> or <strong>Enter</strong> to move quickly between student mark fields.
            </span>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.exams.show', $schedule->exam_id) }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="button" class="btn btn-primary px-4 btn-save-marks-matrix">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>
                    <span class="btn-text"><i class="bi bi-save2 me-1"></i> Save All Marks</span>
                </button>
            </div>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    const MAX_THEORY = parseFloat("{{ (float)$schedule->max_theory_marks }}") || 0;
    const MAX_PRACTICAL = parseFloat("{{ (float)$schedule->max_practical_marks }}") || 0;
    const MAX_INTERNAL = parseFloat("{{ (float)$schedule->max_internal_marks }}") || 0;
    const MAX_VIVA = parseFloat("{{ (float)$schedule->max_viva_marks }}") || 0;
    const MAX_TOTAL = parseFloat("{{ (float)$schedule->total_marks }}") || 100;
    const MIN_PASSING = parseFloat("{{ (float)$schedule->passing_marks }}") || 33;

    function calculateRowGrade(total, isAbsent, isExempted) {
        if (isAbsent) return { grade: 'AB', class: 'bg-danger' };
        if (isExempted) return { grade: 'EX', class: 'bg-secondary' };
        if (total === null || isNaN(total)) return { grade: '-', class: 'bg-light text-dark' };

        const percentage = MAX_TOTAL > 0 ? (total / MAX_TOTAL) * 100 : 0;
        if (percentage >= 90) return { grade: 'A+', class: 'bg-success' };
        if (percentage >= 80) return { grade: 'A', class: 'bg-success' };
        if (percentage >= 70) return { grade: 'B+', class: 'bg-primary' };
        if (percentage >= 60) return { grade: 'B', class: 'bg-info text-dark' };
        if (percentage >= 50) return { grade: 'C', class: 'bg-warning text-dark' };
        if (percentage >= 33) return { grade: 'D', class: 'bg-warning text-dark' };
        return { grade: 'F', class: 'bg-danger' };
    }

    function updateRowCalculation(row) {
        const isAbsent = row.find('.chk-absent').is(':checked');
        const isExempted = row.find('.chk-exempted').is(':checked');

        const inputs = row.find('.mark-input');
        if (isAbsent || isExempted) {
            inputs.prop('disabled', true);
            row.find('.mark-total-display').text('0.0');
            const g = calculateRowGrade(0, isAbsent, isExempted);
            row.find('.mark-grade-display').text(g.grade).attr('class', 'badge mark-grade-display ' + g.class);
            return;
        }

        inputs.each(function () {
            if ($(this).hasClass('mark-practical-val') && MAX_PRACTICAL <= 0) return;
            if ($(this).hasClass('mark-internal-val') && MAX_INTERNAL <= 0) return;
            if ($(this).hasClass('mark-viva-val') && MAX_VIVA <= 0) return;
            $(this).prop('disabled', false);
        });

        const theory = parseFloat(row.find('.mark-theory-val').val()) || 0;
        const pract = parseFloat(row.find('.mark-practical-val').val()) || 0;
        const intern = parseFloat(row.find('.mark-internal-val').val()) || 0;
        const viva = parseFloat(row.find('.mark-viva-val').val()) || 0;

        const total = theory + pract + intern + viva;
        row.find('.mark-total-display').text(total.toFixed(1));

        const g = calculateRowGrade(total, false, false);
        row.find('.mark-grade-display').text(g.grade).attr('class', 'badge mark-grade-display ' + g.class);
    }

    $(function () {
        // Live calculation on input
        $(document).on('input', '.mark-input', function () {
            const row = $(this).closest('tr');
            updateRowCalculation(row);
        });

        // Absent toggle
        $(document).on('change', '.chk-absent', function () {
            const row = $(this).closest('tr');
            if ($(this).is(':checked')) {
                row.find('.chk-exempted').prop('checked', false);
                row.addClass('table-danger-subtle');
            } else {
                row.removeClass('table-danger-subtle');
            }
            updateRowCalculation(row);
        });

        // Exempted toggle
        $(document).on('change', '.chk-exempted', function () {
            const row = $(this).closest('tr');
            if ($(this).is(':checked')) {
                row.find('.chk-absent').prop('checked', false);
            }
            updateRowCalculation(row);
        });

        // Reset Absentees button
        $('#btnMarkAllPresent').on('click', function () {
            $('.chk-absent, .chk-exempted').prop('checked', false);
            $('tr.student-mark-row').removeClass('table-danger-subtle').each(function () {
                updateRowCalculation($(this));
            });
        });

        // Save Marks via AJAX
        $('.btn-save-marks-matrix').on('click', function (e) {
            e.preventDefault();
            const form = $('#marksEntryForm');
            const btns = $('.btn-save-marks-matrix');
            const spinners = btns.find('.spinner-border');

            btns.prop('disabled', true);
            spinners.removeClass('d-none');

            // Temporarily enable disabled inputs so jQuery serializeArray captures values
            const disabledInputs = form.find(':disabled').prop('disabled', false);
            const formData = form.serialize();
            disabledInputs.prop('disabled', true);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                },
                success: (res) => {
                    btns.prop('disabled', false);
                    spinners.addClass('d-none');

                    if (typeof Toast !== 'undefined') {
                        Toast.success(res.message || 'Marks saved successfully!');
                    } else {
                        alert(res.message || 'Marks saved successfully!');
                    }
                },
                error: (xhr) => {
                    btns.prop('disabled', false);
                    spinners.addClass('d-none');
                    alert(xhr.responseJSON?.message || 'Failed to save marks. Please check inputs.');
                }
            });
        });
    });
</script>
@endpush
