const QuestionManager = {
    table: null,

    init() {
        if ($('#questionsTable').length) {
            this.initDataTable();
        }
        this.bindEvents();
        this.handleTypeSwitcher();
    },

    initDataTable() {
        this.table = $('#questionsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: QUESTION_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.class_id = $('#filter_class_id').val();
                    d.subject_id = $('#filter_subject_id').val();
                    d.question_type = $('#filter_question_type').val();
                    d.difficulty_level = $('#filter_difficulty_level').val();
                    d.status = $('#filter_status').val();
                },
                error: function (xhr) {
                    if (typeof Toast !== 'undefined') {
                        Toast.error(xhr.responseJSON?.message ?? 'Unable to load questions.');
                    }
                }
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
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
                    data: 'question_text',
                    name: 'question_text',
                    render: function (data, type, row) {
                        const preview = data.length > 80 ? data.substring(0, 80) + '...' : data;
                        let badges = '';
                        if (row.chapter_name) {
                            badges += `<span class="badge bg-light text-dark border me-1"><i class="bi bi-book me-1"></i>${row.chapter_name}</span>`;
                        }
                        return `
                            <div>
                                <div class="fw-semibold text-dark mb-1">${preview}</div>
                                <div class="small">${badges}</div>
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
                    data: 'question_type',
                    name: 'question_type',
                    render: function (data) {
                        const typeLabels = {
                            'mcq': '<span class="badge bg-primary">MCQ</span>',
                            'true_false': '<span class="badge bg-info text-dark">True / False</span>',
                            'fill_blanks': '<span class="badge bg-secondary">Fill in Blanks</span>',
                            'short_answer': '<span class="badge bg-warning text-dark">Short Answer</span>',
                            'long_answer': '<span class="badge bg-dark">Long Answer</span>',
                            'descriptive': '<span class="badge bg-purple text-white" style="background:#6f42c1">Descriptive</span>',
                            'match_following': '<span class="badge bg-success">Match Following</span>'
                        };
                        return typeLabels[data] || `<span class="badge bg-light text-dark">${data}</span>`;
                    }
                },
                {
                    data: 'difficulty_level',
                    name: 'difficulty_level',
                    render: function (data) {
                        if (data === 'easy') {
                            return '<span class="badge bg-success-subtle text-success border border-success-subtle">Easy</span>';
                        } else if (data === 'hard') {
                            return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Hard</span>';
                        }
                        return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">Medium</span>';
                    }
                },
                {
                    data: 'marks',
                    name: 'marks',
                    render: function (data, type, row) {
                        let text = `<span class="fw-bold text-success">${parseFloat(data).toFixed(1)} M</span>`;
                        if (row.negative_marks && parseFloat(row.negative_marks) > 0) {
                            text += `<br><small class="text-danger">-${parseFloat(row.negative_marks).toFixed(2)}</small>`;
                        }
                        return text;
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data, type, row) {
                        const canEdit = typeof window.can === 'function' ? window.can('questions.edit') : true;
                        return Helper.statusSwitch(row.id, data, canEdit);
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function (data, type, row) {
                        const canEdit = typeof window.can === 'function' ? window.can('questions.edit') : true;
                        const canDelete = typeof window.can === 'function' ? window.can('questions.delete') : true;
                        const canView = typeof window.can === 'function' ? window.can('questions.view') : true;
                        const editUrl = QUESTION_UPDATE_URL.replace(':id', row.id) + '/edit';

                        let buttons = '';

                        if (canView) {
                            buttons += `
                                <button type="button" class="btn btn-sm btn-outline-info btn-preview-question me-1" data-id="${row.id}" title="Preview">
                                    <i class="bi bi-eye"></i>
                                </button>
                            `;
                        }

                        if (canEdit) {
                            buttons += `
                                <a href="${editUrl}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            `;
                        }

                        if (canDelete) {
                            buttons += `
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-question" data-id="${row.id}" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            `;
                        }

                        return buttons || '<span class="text-muted fs-7">-</span>';
                    }
                }
            ]
        });
    },

    bindEvents() {
        const self = this;

        // Filters reload
        $('#filter_class_id, #filter_subject_id, #filter_question_type, #filter_difficulty_level, #filter_status').on('change', () => {
            if (self.table) self.table.ajax.reload();
        });

        $('#btnResetFilters').on('click', () => {
            $('#filter_class_id, #filter_subject_id, #filter_question_type, #filter_difficulty_level, #filter_status').val('');
            if (self.table) self.table.search('').ajax.reload();
        });

        // Question Type Switcher
        $('#question_type').on('change', function () {
            self.handleTypeSwitcher();
        });

        // Add Match Pair
        $('#btnAddMatchPair').on('click', function () {
            const container = $('#matchPairsContainer');
            const count = container.find('.match-pair-row').length;
            const newRow = `
                <div class="row g-2 mb-2 match-pair-row align-items-center">
                    <div class="col-md-5">
                        <input type="text" name="match_pairs[${count}][left]" class="form-control" placeholder="Column A item ${count + 1}">
                    </div>
                    <div class="col-md-1 text-center text-muted">
                        <i class="bi bi-arrow-right"></i>
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="match_pairs[${count}][right]" class="form-control" placeholder="Matching Column B item">
                    </div>
                    <div class="col-md-1 text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-pair" title="Remove Pair">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.append(newRow);
        });

        // Remove Match Pair
        $(document).on('click', '.btn-remove-pair', function () {
            $(this).closest('.match-pair-row').remove();
        });

        // Status Switch
        $(document).on('change', '.btn-status-question', function () {
            const id = $(this).data('id');
            const url = QUESTION_STATUS_URL.replace(':id', id);
            $.ajax({
                url: url,
                type: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                },
                success: (res) => {
                    if (typeof Toast !== 'undefined') {
                        Toast.success(res.message || 'Status updated successfully.');
                    }
                },
                error: () => {
                    if (typeof Toast !== 'undefined') {
                        Toast.error('Failed to update status.');
                    }
                }
            });
        });

        // Delete Question
        $(document).on('click', '.btn-delete-question', function () {
            const id = $(this).data('id');
            const url = QUESTION_UPDATE_URL.replace(':id', id);

            const proceedDelete = () => {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                    },
                    success: (res) => {
                        if (typeof Toast !== 'undefined') {
                            Toast.success(res.message || 'Question deleted successfully.');
                        }
                        if (self.table) self.table.ajax.reload(null, false);
                    },
                    error: (xhr) => {
                        const msg = xhr.responseJSON?.message || 'Failed to delete question.';
                        if (typeof Toast !== 'undefined') {
                            Toast.error(msg);
                        } else {
                            alert(msg);
                        }
                    }
                });
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete Question?',
                    text: 'Are you sure you want to delete this question? This cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, Delete'
                }).then((result) => {
                    if (result.isConfirmed) {
                        proceedDelete();
                    }
                });
            } else {
                if (confirm('Are you sure you want to delete this question?')) {
                    proceedDelete();
                }
            }
        });

        // Preview Question Modal
        $(document).on('click', '.btn-preview-question', function () {
            const id = $(this).data('id');
            const url = QUESTION_SHOW_URL.replace(':id', id);
            const modal = $('#previewQuestionModal');
            const body = $('#previewQuestionBody');

            body.html('<div class="text-center py-4"><span class="spinner-border text-primary"></span></div>');
            modal.modal('show');

            $.ajax({
                url: url,
                type: 'GET',
                success: (res) => {
                    const q = res.data;
                    let optionsHtml = '';
                    if (q.question_type === 'mcq') {
                        optionsHtml = `
                            <div class="mt-3">
                                <h6>Options:</h6>
                                <div class="list-group">
                                    <div class="list-group-item ${q.correct_option === 'a' ? 'list-group-item-success fw-bold' : ''}"><strong>A.</strong> ${q.option_a || '-'} ${q.correct_option === 'a' ? '<span class="badge bg-success float-end">Correct</span>' : ''}</div>
                                    <div class="list-group-item ${q.correct_option === 'b' ? 'list-group-item-success fw-bold' : ''}"><strong>B.</strong> ${q.option_b || '-'} ${q.correct_option === 'b' ? '<span class="badge bg-success float-end">Correct</span>' : ''}</div>
                                    <div class="list-group-item ${q.correct_option === 'c' ? 'list-group-item-success fw-bold' : ''}"><strong>C.</strong> ${q.option_c || '-'} ${q.correct_option === 'c' ? '<span class="badge bg-success float-end">Correct</span>' : ''}</div>
                                    <div class="list-group-item ${q.correct_option === 'd' ? 'list-group-item-success fw-bold' : ''}"><strong>D.</strong> ${q.option_d || '-'} ${q.correct_option === 'd' ? '<span class="badge bg-success float-end">Correct</span>' : ''}</div>
                                </div>
                            </div>
                        `;
                    } else if (q.question_type === 'true_false') {
                        optionsHtml = `
                            <div class="mt-3">
                                <h6>Correct Answer:</h6>
                                <span class="badge bg-success fs-6">${q.correct_option === 'a' ? 'True' : 'False'}</span>
                            </div>
                        `;
                    }

                    body.html(`
                        <div class="mb-3">
                            <span class="badge bg-primary me-1">${q.academic_class?.class_name ?? 'Class'}</span>
                            <span class="badge bg-secondary me-1">${q.subject?.subject_name ?? 'Subject'}</span>
                            <span class="badge bg-info text-dark me-1">${q.question_type.toUpperCase()}</span>
                            <span class="badge bg-warning text-dark me-1">Marks: ${q.marks}</span>
                            <span class="badge bg-dark">${q.difficulty_level.toUpperCase()}</span>
                        </div>
                        <div class="fs-5 fw-semibold p-3 bg-light rounded border">${q.question_text}</div>
                        ${optionsHtml}
                        ${q.explanation ? `<div class="mt-3 alert alert-info"><strong>Solution Explanation:</strong><br>${q.explanation}</div>` : ''}
                    `);
                },
                error: () => {
                    body.html('<div class="alert alert-danger">Failed to load question details.</div>');
                }
            });
        });

        // Question Form Submission
        $('#questionForm').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const btn = $('#btnSaveQuestion');
            const spinner = btn.find('.spinner-border');
            const btnText = btn.find('.btn-text');

            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').remove();

            btn.prop('disabled', true);
            spinner.removeClass('d-none');

            const formData = new FormData(form[0]);

            // Sync True/False value
            if ($('#question_type').val() === 'true_false') {
                const tfVal = $('input[name="correct_tf"]:checked').val() || 'a';
                formData.set('correct_option', tfVal);
            }

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                },
                success: (res) => {
                    if (typeof Toast !== 'undefined') {
                        Toast.success(res.message || 'Question saved successfully!');
                    } else {
                        alert(res.message || 'Question saved successfully!');
                    }

                    setTimeout(() => {
                        window.location.href = (typeof QUESTION_INDEX_URL !== 'undefined') ? QUESTION_INDEX_URL : '/admin/questions';
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

                        const firstError = form.find('.is-invalid').first();
                        if (firstError.length) {
                            $('html, body').animate({
                                scrollTop: firstError.offset().top - 100
                            }, 300);
                        }
                    } else {
                        const msg = xhr.responseJSON?.message || 'Failed to save question.';
                        if (typeof Toast !== 'undefined') {
                            Toast.error(msg);
                        } else {
                            alert(msg);
                        }
                    }
                }
            });
        });
    },

    handleTypeSwitcher() {
        const type = $('#question_type').val();
        $('.type-block').addClass('d-none');

        if (type === 'mcq') {
            $('#block_mcq').removeClass('d-none');
        } else if (type === 'true_false') {
            $('#block_true_false').removeClass('d-none');
        } else if (type === 'fill_blanks') {
            $('#block_fill_blanks').removeClass('d-none');
        } else if (type === 'match_following') {
            $('#block_match_following').removeClass('d-none');
        }
    }
};

$(function () {
    QuestionManager.init();
});
