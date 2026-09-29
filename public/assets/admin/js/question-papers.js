const QuestionPaperManager = {
    table: null,
    targetSectionIndex: 0,
    selectedQuestionsTemp: [],

    init() {
        if ($('#questionPapersTable').length) {
            this.initDataTable();
        }
        this.bindEvents();
        this.recalculateTotalMarks();
    },

    initDataTable() {
        this.table = $('#questionPapersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: QP_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.class_id = $('#filter_class_id').val();
                    d.subject_id = $('#filter_subject_id').val();
                    d.approval_status = $('#filter_approval_status').val();
                    d.is_locked = $('#filter_is_locked').val();
                },
                error: function (xhr) {
                    if (typeof Toast !== 'undefined') {
                        Toast.error(xhr.responseJSON?.message ?? 'Unable to load question papers.');
                    }
                }
            },
            pageLength: 10,
            searching: true,
            ordering: true,
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'title',
                    name: 'title',
                    render: function (data, type, row) {
                        return `
                            <div>
                                <a href="${QP_SHOW_URL.replace(':id', row.id)}" class="fw-bold text-primary text-decoration-none">${data}</a>
                                <div class="small text-muted"><code>${row.paper_code}</code></div>
                            </div>
                        `;
                    }
                },
                {
                    data: 'subject.subject_name',
                    name: 'subject_id',
                    render: function (data, type, row) {
                        const className = row.academic_class?.class_name ?? '-';
                        const subjectName = row.subject?.subject_name ?? '-';
                        return `
                            <div class="fw-semibold">${subjectName}</div>
                            <small class="text-muted">${className}</small>
                        `;
                    }
                },
                {
                    data: 'total_marks',
                    name: 'total_marks',
                    render: function (data, type, row) {
                        return `
                            <div class="fw-bold text-success">${parseFloat(data).toFixed(0)} Marks</div>
                            <small class="text-muted"><i class="bi bi-clock me-1"></i>${row.duration_minutes} Mins</small>
                        `;
                    }
                },
                {
                    data: 'sections_count',
                    name: 'sections_count',
                    render: function (data, type, row) {
                        const setsLabel = row.has_sets ? '<span class="badge bg-info text-dark ms-1">Sets A/B/C/D</span>' : '';
                        return `<span>${data} Sec (${row.items_count || 0} Q)</span>${setsLabel}`;
                    }
                },
                {
                    data: 'is_locked',
                    name: 'is_locked',
                    render: function (data) {
                        return data
                            ? '<span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-lock-fill me-1"></i>Locked</span>'
                            : '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-unlock me-1"></i>Open</span>';
                    }
                },
                {
                    data: 'approval_status',
                    name: 'approval_status',
                    render: function (data) {
                        const badges = {
                            'draft': '<span class="badge bg-secondary">Draft</span>',
                            'pending_approval': '<span class="badge bg-warning text-dark">Pending</span>',
                            'approved': '<span class="badge bg-success">Approved</span>',
                            'rejected': '<span class="badge bg-danger">Rejected</span>'
                        };
                        return badges[data] || `<span class="badge bg-light text-dark">${data}</span>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function (data, type, row) {
                        const showUrl = QP_SHOW_URL.replace(':id', row.id);
                        const printUrl = showUrl + '/print';
                        const editUrl = QP_UPDATE_URL.replace(':id', row.id) + '/edit';

                        let editBtn = '';
                        if (!row.is_locked) {
                            editBtn = `
                                <a href="${editUrl}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            `;
                        }

                        return `
                            <a href="${showUrl}" class="btn btn-sm btn-outline-info me-1" title="Preview / Details">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="${printUrl}" target="_blank" class="btn btn-sm btn-outline-dark me-1" title="Print Paper">
                                <i class="bi bi-printer"></i>
                            </a>
                            ${editBtn}
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-paper" data-id="${row.id}" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        `;
                    }
                }
            ]
        });
    },

    bindEvents() {
        const self = this;

        // Filters
        $('#filter_class_id, #filter_subject_id, #filter_approval_status, #filter_is_locked').on('change', () => {
            if (self.table) self.table.ajax.reload();
        });

        $('#btnResetFilters').on('click', () => {
            $('#filter_class_id, #filter_subject_id, #filter_approval_status, #filter_is_locked').val('');
            if (self.table) self.table.search('').ajax.reload();
        });

        // Add Section
        $('#btnAddSection').on('click', () => {
            const container = $('#sectionsContainer');
            const newIndex = container.find('.section-card').length;
            const letter = String.fromCharCode(65 + newIndex);

            const sectionHtml = `
                <div class="card shadow-sm border-0 mb-4 section-card" data-section-index="${newIndex}">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-grid-3x3-gap text-muted"></i>
                            <input type="text" name="sections[${newIndex}][section_name]" class="form-control form-control-sm fw-bold section-title-input" value="Section ${letter} - Short Answer" style="width: 320px;">
                            <select name="sections[${newIndex}][section_type]" class="form-select form-select-sm" style="width: 160px;">
                                <option value="short_answer">Short Answer</option>
                                <option value="long_answer">Long Answer</option>
                                <option value="mcq">MCQ (1 Mark)</option>
                                <option value="true_false">True / False</option>
                                <option value="fill_blanks">Fill in Blanks</option>
                                <option value="descriptive">Descriptive</option>
                                <option value="match_following">Match Following</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary section-marks-badge">0 Questions (0 Marks)</span>
                            <button type="button" class="btn btn-sm btn-outline-primary btn-pick-questions" data-section-index="${newIndex}">
                                <i class="bi bi-plus-lg me-1"></i> Select from Bank
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-section" title="Delete Section">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="section-questions-list" data-section-index="${newIndex}">
                            <div class="text-center py-4 text-muted empty-section-placeholder">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                No questions added to this section yet. Click <strong>"Select from Bank"</strong> above to add questions.
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.append(sectionHtml);
            self.recalculateTotalMarks();
        });

        // Delete Section
        $(document).on('click', '.btn-delete-section', function () {
            if ($('#sectionsContainer .section-card').length <= 1) {
                alert('A question paper must have at least one section.');
                return;
            }
            $(this).closest('.section-card').remove();
            self.recalculateTotalMarks();
        });

        // Open Question Picker Modal
        $(document).on('click', '.btn-pick-questions', function () {
            self.targetSectionIndex = $(this).data('section-index');
            const classId = $('#paper_class_id').val();
            const subjectId = $('#paper_subject_id').val();

            if (!classId || !subjectId) {
                if (typeof Toast !== 'undefined') {
                    Toast.warning('Please select Class and Subject first in the Paper Specifications header.');
                } else {
                    alert('Please select Class and Subject first.');
                }
                return;
            }

            self.loadModalQuestions();
            $('#questionSelectorModal').modal('show');
        });

        // Filter modal questions
        $('#btnFilterModalQuestions, #modal_filter_type, #modal_filter_difficulty').on('change click', () => {
            self.loadModalQuestions();
        });

        // Select all checkbox in modal
        $('#selectAllModalQuestions').on('change', function () {
            $('.modal-q-checkbox').prop('checked', $(this).is(':checked'));
            self.updateSelectedCountText();
        });

        $(document).on('change', '.modal-q-checkbox', () => {
            self.updateSelectedCountText();
        });

        // Add selected questions from modal into target section
        $('#btnAddSelectedQuestionsToSection').on('click', () => {
            const selectedCheckboxes = $('.modal-q-checkbox:checked');
            if (selectedCheckboxes.length === 0) {
                alert('Please select at least one question.');
                return;
            }

            const targetContainer = $(`.section-questions-list[data-section-index="${self.targetSectionIndex}"]`);
            targetContainer.find('.empty-section-placeholder').remove();

            selectedCheckboxes.each(function () {
                const qId = $(this).val();
                const qText = $(this).data('text');
                const qType = $(this).data('type');
                const qMarks = $(this).data('marks') || 1;
                const qDifficulty = $(this).data('difficulty');

                // Check if already in section
                if (targetContainer.find(`input[name*="[question_id]"][value="${qId}"]`).length === 0) {
                    const qCount = targetContainer.find('.question-item-card').length;
                    const itemHtml = `
                        <div class="card border mb-2 shadow-none question-item-card p-3" data-qid="${qId}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                    <span class="badge bg-secondary text-uppercase me-1">${qType}</span>
                                    <span class="badge bg-light text-dark border me-1">${qDifficulty.toUpperCase()}</span>
                                    <input type="hidden" name="sections[${self.targetSectionIndex}][questions][${qCount}][question_id]" value="${qId}">
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="input-group input-group-sm" style="width: 120px;">
                                        <span class="input-group-text">Marks</span>
                                        <input type="number" step="0.5" min="0.5" name="sections[${self.targetSectionIndex}][questions][${qCount}][marks]" class="form-control item-marks-input" value="${qMarks}">
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-question-item">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="text-dark small">${qText}</div>
                        </div>
                    `;
                    targetContainer.append(itemHtml);
                }
            });

            $('#questionSelectorModal').modal('hide');
            self.recalculateTotalMarks();
        });

        // Remove question item from section
        $(document).on('click', '.btn-remove-question-item', function () {
            const list = $(this).closest('.section-questions-list');
            $(this).closest('.question-item-card').remove();
            if (list.find('.question-item-card').length === 0) {
                list.html(`
                    <div class="text-center py-4 text-muted empty-section-placeholder">
                        <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                        No questions added to this section yet. Click <strong>"Select from Bank"</strong> above to add questions.
                    </div>
                `);
            }
            self.recalculateTotalMarks();
        });

        // Live marks change
        $(document).on('input', '.item-marks-input, #total_marks', () => {
            self.recalculateTotalMarks();
        });

        // Auto Blueprint Modal Open
        $('#btnOpenBlueprintModal').on('click', () => {
            const classId = $('#paper_class_id').val();
            const subjectId = $('#paper_subject_id').val();

            if (!classId || !subjectId) {
                alert('Please select Class and Subject first in the Paper Specifications header.');
                return;
            }
            $('#autoBlueprintModal').modal('show');
        });

        // Add Blueprint Row
        $('#btnAddBlueprintRow').on('click', () => {
            const container = $('#blueprintRowsContainer');
            const count = container.find('.blueprint-row').length;
            const letter = String.fromCharCode(65 + count);
            const rowHtml = `
                <div class="row g-2 mb-2 blueprint-row align-items-center">
                    <div class="col-md-3">
                        <input type="text" class="form-control form-control-sm bp-name" value="Section ${letter}" placeholder="Section Name">
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
                        <input type="number" class="form-control form-control-sm bp-count" value="5" placeholder="Count" min="1">
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control form-control-sm bp-marks" value="2" placeholder="Marks/Q" min="0.5" step="0.5">
                    </div>
                    <div class="col-md-2 text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-bp-row"><i class="bi bi-trash"></i></button>
                    </div>
                </div>
            `;
            container.append(rowHtml);
        });

        $(document).on('click', '.btn-remove-bp-row', function () {
            $(this).closest('.blueprint-row').remove();
        });

        // Execute Auto Blueprint Generator
        $('#btnExecuteAutoBlueprint').on('click', function () {
            const btn = $(this);
            const classId = $('#paper_class_id').val();
            const subjectId = $('#paper_subject_id').val();

            const sectionsData = [];
            $('#blueprintRowsContainer .blueprint-row').each(function () {
                sectionsData.push({
                    name: $(this).find('.bp-name').val(),
                    type: $(this).find('.bp-type').val(),
                    count: parseInt($(this).find('.bp-count').val()) || 1,
                    marks: parseFloat($(this).find('.bp-marks').val()) || 1
                });
            });

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Auto Generating...');

            $.ajax({
                url: AUTO_BLUEPRINT_URL,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                },
                data: {
                    class_id: classId,
                    subject_id: subjectId,
                    sections: sectionsData
                },
                success: (res) => {
                    btn.prop('disabled', false).html('<i class="bi bi-lightning-charge me-1"></i> Auto-Generate Paper');
                    $('#autoBlueprintModal').modal('hide');

                    if (res && res.data) {
                        const sections = res.data.sections;
                        const secContainer = $('#sectionsContainer');
                        secContainer.empty();

                        sections.forEach((sec, sIdx) => {
                            let itemsHtml = '';
                            if (sec.questions && sec.questions.length > 0) {
                                sec.questions.forEach((q, qIdx) => {
                                    itemsHtml += `
                                        <div class="card border mb-2 shadow-none question-item-card p-3" data-qid="${q.id}">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div>
                                                    <span class="badge bg-secondary text-uppercase me-1">${q.type}</span>
                                                    <span class="badge bg-light text-dark border me-1">${q.difficulty.toUpperCase()}</span>
                                                    <input type="hidden" name="sections[${sIdx}][questions][${qIdx}][question_id]" value="${q.id}">
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="input-group input-group-sm" style="width: 120px;">
                                                        <span class="input-group-text">Marks</span>
                                                        <input type="number" step="0.5" min="0.5" name="sections[${sIdx}][questions][${qIdx}][marks]" class="form-control item-marks-input" value="${q.marks}">
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-question-item">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="text-dark small">${q.text}</div>
                                        </div>
                                    `;
                                });
                            } else {
                                itemsHtml = `
                                    <div class="text-center py-4 text-muted empty-section-placeholder">
                                        <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                        No questions matched in Question Bank for this section criteria. Click <strong>"Select from Bank"</strong> to manually add.
                                    </div>
                                `;
                            }

                            const secCard = `
                                <div class="card shadow-sm border-0 mb-4 section-card" data-section-index="${sIdx}">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-grid-3x3-gap text-muted"></i>
                                            <input type="text" name="sections[${sIdx}][section_name]" class="form-control form-control-sm fw-bold section-title-input" value="${sec.section_name}" style="width: 320px;">
                                            <select name="sections[${sIdx}][section_type]" class="form-select form-select-sm" style="width: 160px;">
                                                <option value="${sec.section_type}" selected>${sec.section_type.toUpperCase()}</option>
                                                <option value="mcq">MCQ</option>
                                                <option value="short_answer">Short Answer</option>
                                                <option value="long_answer">Long Answer</option>
                                            </select>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-secondary section-marks-badge">${sec.total_questions} Questions</span>
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-pick-questions" data-section-index="${sIdx}">
                                                <i class="bi bi-plus-lg me-1"></i> Select from Bank
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-section" title="Delete Section">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="section-questions-list" data-section-index="${sIdx}">
                                            ${itemsHtml}
                                        </div>
                                    </div>
                                </div>
                            `;
                            secContainer.append(secCard);
                        });

                        self.recalculateTotalMarks();
                        if (typeof Toast !== 'undefined') {
                            Toast.success('Question Paper generated from blueprint successfully!');
                        }
                    }
                },
                error: (xhr) => {
                    btn.prop('disabled', false).html('<i class="bi bi-lightning-charge me-1"></i> Auto-Generate Paper');
                    alert(xhr.responseJSON?.message || 'Failed to auto-generate blueprint.');
                }
            });
        });

        // Paper Lock / Unlock Toggle
        $('#btnTogglePaperLock').on('click', function () {
            const paperId = $(this).data('id');
            const url = QP_LOCK_URL.replace(':id', paperId);

            $.ajax({
                url: url,
                type: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                },
                success: (res) => {
                    if (typeof Toast !== 'undefined') {
                        Toast.success(res.message);
                    } else {
                        alert(res.message);
                    }
                    setTimeout(() => location.reload(), 600);
                },
                error: (xhr) => {
                    alert(xhr.responseJSON?.message || 'Failed to update lock state.');
                }
            });
        });

        // Approval Submit
        $('#btnSubmitApproval').on('click', function () {
            const paperId = $(this).data('id');
            const status = $('#approval_status_select').val();
            const remarks = $('#approval_remarks').val();
            const url = QP_APPROVAL_URL.replace(':id', paperId);

            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                },
                data: {
                    status: status,
                    remarks: remarks
                },
                success: (res) => {
                    if (typeof Toast !== 'undefined') {
                        Toast.success(res.message || 'Approval status updated!');
                    }
                    $('#approvalModal').modal('hide');
                    setTimeout(() => location.reload(), 600);
                },
                error: (xhr) => {
                    alert(xhr.responseJSON?.message || 'Failed to update approval.');
                }
            });
        });

        // Generate Sets Submit
        $('#btnExecuteGenerateSets').on('click', function () {
            const paperId = $(this).data('id');
            const selectedSets = [];
            $('input[name="set_names[]"]:checked').each(function () {
                selectedSets.push($(this).val());
            });

            if (selectedSets.length === 0) {
                alert('Please select at least one set name.');
                return;
            }

            const shuffleQ = $('#modalShuffleQuestions').is(':checked');
            const url = QP_GENERATE_SETS_URL.replace(':id', paperId);

            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                },
                data: {
                    set_names: selectedSets,
                    shuffle_questions: shuffleQ ? 1 : 0
                },
                success: (res) => {
                    if (typeof Toast !== 'undefined') {
                        Toast.success(res.message);
                    }
                    $('#generateSetsModal').modal('hide');
                    setTimeout(() => location.reload(), 600);
                },
                error: (xhr) => {
                    alert(xhr.responseJSON?.message || 'Failed to generate sets.');
                }
            });
        });

        // Paper Form Submit
        $('#questionPaperForm').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const btn = $('#btnSavePaper');
            const spinner = btn.find('.spinner-border');
            const btnText = btn.find('.btn-text');

            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').remove();

            btn.prop('disabled', true);
            spinner.removeClass('d-none');

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method') || 'POST',
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                },
                success: (res) => {
                    if (typeof Toast !== 'undefined') {
                        Toast.success(res.message || 'Question paper saved successfully!');
                    }
                    setTimeout(() => {
                        window.location.href = res.data?.redirect_url || QP_INDEX_URL;
                    }, 600);
                },
                error: (xhr) => {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON?.errors ?? {};
                        $.each(errors, (field, messages) => {
                            const input = form.find(`[name="${field}"]`);
                            input.addClass('is-invalid');
                            input.after(`<div class="invalid-feedback d-block">${messages[0]}</div>`);
                        });
                    } else {
                        const msg = xhr.responseJSON?.message || 'Failed to save question paper.';
                        if (typeof Toast !== 'undefined') {
                            Toast.error(msg);
                        } else {
                            alert(msg);
                        }
                    }
                }
            });
        });

        // Delete Paper
        $(document).on('click', '.btn-delete-paper', function () {
            const id = $(this).data('id');
            const url = QP_UPDATE_URL.replace(':id', id);

            if (confirm('Are you sure you want to delete this question paper?')) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                    },
                    success: (res) => {
                        if (typeof Toast !== 'undefined') {
                            Toast.success(res.message || 'Deleted successfully.');
                        }
                        if (self.table) self.table.ajax.reload(null, false);
                    },
                    error: (xhr) => {
                        alert(xhr.responseJSON?.message || 'Failed to delete.');
                    }
                });
            }
        });
    },

    loadModalQuestions() {
        const self = this;
        const classId = $('#paper_class_id').val();
        const subjectId = $('#paper_subject_id').val();
        const type = $('#modal_filter_type').val();
        const difficulty = $('#modal_filter_difficulty').val();
        const search = $('#modal_filter_search').val();

        const body = $('#modalQuestionsBody');
        body.html('<tr><td colspan="5" class="text-center py-4"><span class="spinner-border spinner-border-sm text-primary"></span> Loading questions...</td></tr>');

        $.ajax({
            url: SEARCH_QUESTIONS_URL,
            type: 'GET',
            data: {
                class_id: classId,
                subject_id: subjectId,
                question_type: type,
                difficulty_level: difficulty,
                search: search
            },
            success: (res) => {
                const questions = res.data || [];
                if (questions.length === 0) {
                    body.html('<tr><td colspan="5" class="text-center py-4 text-muted">No questions found in Question Bank matching criteria.</td></tr>');
                    return;
                }

                let html = '';
                questions.forEach((q) => {
                    const preview = q.question_text.length > 90 ? q.question_text.substring(0, 90) + '...' : q.question_text;
                    html += `
                        <tr>
                            <td><input type="checkbox" class="form-check-input modal-q-checkbox" value="${q.id}" data-text="${q.question_text.replace(/"/g, '&quot;')}" data-type="${q.question_type}" data-difficulty="${q.difficulty_level}" data-marks="${q.marks}"></td>
                            <td><div class="fw-semibold small">${preview}</div>${q.chapter_name ? `<small class="text-muted"><i class="bi bi-book me-1"></i>${q.chapter_name}</small>` : ''}</td>
                            <td><span class="badge bg-secondary text-uppercase">${q.question_type}</span></td>
                            <td><span class="badge bg-light text-dark border">${q.difficulty_level.toUpperCase()}</span></td>
                            <td class="fw-bold text-success">${q.marks}M</td>
                        </tr>
                    `;
                });
                body.html(html);
                self.updateSelectedCountText();
            },
            error: () => {
                body.html('<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load questions.</td></tr>');
            }
        });
    },

    updateSelectedCountText() {
        const count = $('.modal-q-checkbox:checked').length;
        $('#selectedCountText').text(`${count} question${count !== 1 ? 's' : ''} selected`);
    },

    recalculateTotalMarks() {
        let liveTotal = 0;
        $('.section-card').each(function () {
            let sectionMarks = 0;
            const qCards = $(this).find('.question-item-card');
            qCards.each(function () {
                const marksVal = parseFloat($(this).find('.item-marks-input').val()) || 0;
                sectionMarks += marksVal;
            });
            liveTotal += sectionMarks;
            $(this).find('.section-marks-badge').text(`${qCards.length} Q (${sectionMarks.toFixed(1)} M)`);
        });

        $('#counterTotalMarks').text(`${liveTotal.toFixed(2)} M`);
        const targetVal = parseFloat($('#total_marks').val()) || 0;
        $('#targetTotalMarksText').text(`${targetVal.toFixed(2)} M`);

        if (liveTotal === targetVal && targetVal > 0) {
            $('#counterTotalMarks').removeClass('text-danger text-warning').addClass('text-success');
        } else {
            $('#counterTotalMarks').removeClass('text-success').addClass('text-primary');
        }
    }
};

$(function () {
    QuestionPaperManager.init();
});
