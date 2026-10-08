const ExamManager = {
    table: null,
    scheduleIndex: 0,

    init() {
        if ($('#examsTable').length) {
            this.initDataTable();
        }
        this.bindEvents();
        this.initScheduleBuilder();
    },

    initDataTable() {
        this.table = $('#examsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: EXAM_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.academic_session_id = $('#filter_academic_session_id').val();
                    d.class_id = $('#filter_class_id').val();
                    d.exam_type = $('#filter_exam_type').val();
                    d.exam_mode = $('#filter_exam_mode').val();
                    d.status = $('#filter_status').val();
                },
                error: function (xhr) {
                    if (typeof Toast !== 'undefined') {
                        Toast.error(xhr.responseJSON?.message ?? 'Unable to load exams list.');
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
                    data: 'title',
                    name: 'title',
                    render: function (data, type, row) {
                        const showUrl = `${EXAM_BASE_URL}/${row.id}`;
                        return `
                            <div>
                                <a href="${showUrl}" class="fw-bold text-dark text-decoration-none">${data}</a>
                                <div class="small font-monospace text-muted">${row.exam_code || '-'}</div>
                            </div>
                        `;
                    }
                },
                {
                    data: 'academic_class.class_name',
                    name: 'class_id',
                    render: function (data, type, row) {
                        const className = row.academic_class?.class_name ?? 'All Classes';
                        const sessionName = row.academic_session?.name ?? '-';
                        return `
                            <div class="fw-semibold text-dark">${className}</div>
                            <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>${sessionName}</small>
                        `;
                    }
                },
                {
                    data: 'exam_type',
                    name: 'exam_type',
                    render: function (data, type, row) {
                        const modeBadge = row.exam_mode === 'online' 
                            ? '<span class="badge bg-info text-dark">Online CBT</span>' 
                            : (row.exam_mode === 'both' ? '<span class="badge bg-purple text-white" style="background:#6f42c1">Hybrid</span>' : '<span class="badge bg-secondary">Offline</span>');
                        
                        const formattedType = (data || '').replace('_', ' ').toUpperCase();
                        return `
                            <div class="fw-semibold small">${formattedType}</div>
                            <div>${modeBadge}</div>
                        `;
                    }
                },
                {
                    data: 'total_marks',
                    name: 'total_marks',
                    render: function (data, type, row) {
                        return `
                            <div><span class="fw-bold text-success">${parseFloat(data).toFixed(1)} M</span></div>
                            <small class="text-muted">${row.duration_minutes || 180} mins</small>
                        `;
                    }
                },
                {
                    data: 'schedules_count',
                    name: 'schedules_count',
                    render: function (data) {
                        return `<span class="badge bg-light text-dark border"><i class="bi bi-book me-1"></i>${data || 0} Subject(s)</span>`;
                    }
                },
                {
                    data: 'enrolled_students_count',
                    name: 'enrolled_students_count',
                    render: function (data, type, row) {
                        const enrollUrl = `${EXAM_BASE_URL}/${row.id}/enrollments`;
                        return `<a href="${enrollUrl}" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none"><i class="bi bi-people me-1"></i>${data || 0} Students</a>`;
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data) {
                        if (data === 'published') {
                            return '<span class="badge bg-success">Published</span>';
                        } else if (data === 'closed') {
                            return '<span class="badge bg-danger">Closed</span>';
                        }
                        return '<span class="badge bg-warning text-dark">Draft</span>';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function (data, type, row) {
                        const canEdit = typeof window.can === 'function' ? window.can('exams.edit') : true;
                        const canDelete = typeof window.can === 'function' ? window.can('exams.delete') : true;
                        const canView = typeof window.can === 'function' ? window.can('exams.view') : true;

                        const showUrl = `${EXAM_BASE_URL}/${row.id}`;
                        const editUrl = `${EXAM_BASE_URL}/${row.id}/edit`;
                        const admitCardsUrl = `${EXAM_BASE_URL}/${row.id}/admit-cards`;

                        let buttons = '';

                        if (canView) {
                            buttons += `
                                <a href="${showUrl}" class="btn btn-outline-info" title="View Exam Hub">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="${admitCardsUrl}" class="btn btn-outline-secondary" target="_blank" title="Print Admit Cards">
                                    <i class="bi bi-printer"></i>
                                </a>
                            `;
                        }

                        if (canEdit) {
                            buttons += `
                                <a href="${editUrl}" class="btn btn-outline-primary" title="Edit Exam">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            `;
                        }

                        if (canDelete) {
                            buttons += `
                                <button type="button" class="btn btn-outline-danger btn-delete-exam" data-id="${row.id}" title="Delete Exam">
                                    <i class="bi bi-trash"></i>
                                </button>
                            `;
                        }

                        return buttons ? `<div class="btn-group btn-group-sm">${buttons}</div>` : '<span class="text-muted fs-7">-</span>';
                    }
                }
            ]
        });
    },

    bindEvents() {
        const self = this;

        // Filter triggers
        $('#filter_academic_session_id, #filter_class_id, #filter_exam_type, #filter_exam_mode, #filter_status').on('change', () => {
            if (self.table) self.table.ajax.reload();
        });

        $('#btnResetFilters').on('click', () => {
            $('#filter_academic_session_id, #filter_class_id, #filter_exam_type, #filter_exam_mode, #filter_status').val('');
            if (self.table) self.table.search('').ajax.reload();
        });

        // Delete Exam
        $(document).on('click', '.btn-delete-exam', function () {
            const id = $(this).data('id');
            const url = `${EXAM_BASE_URL}/${id}`;

            const proceedDelete = () => {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                    },
                    success: (res) => {
                        if (typeof Toast !== 'undefined') {
                            Toast.success(res.message || 'Exam deleted successfully.');
                        }
                        if (self.table) self.table.ajax.reload(null, false);
                    },
                    error: (xhr) => {
                        alert(xhr.responseJSON?.message || 'Failed to delete exam.');
                    }
                });
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete Exam?',
                    text: 'This will permanently remove the exam, schedules, and marks records.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, Delete Exam'
                }).then((result) => {
                    if (result.isConfirmed) proceedDelete();
                });
            } else {
                if (confirm('Are you sure you want to delete this exam?')) proceedDelete();
            }
        });

        // Exam Form Submission (Create & Edit)
        $('#examForm').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const btn = $('#btnSaveExam');
            const spinner = btn.find('.spinner-border');

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
                        Toast.success(res.message || 'Exam saved successfully!');
                    } else {
                        alert(res.message || 'Exam saved successfully!');
                    }

                    setTimeout(() => {
                        window.location.href = res.data?.redirect_url || (typeof EXAM_INDEX_URL !== 'undefined' ? EXAM_INDEX_URL : '/admin/exams');
                    }, 600);
                },
                error: (xhr) => {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON?.errors ?? {};
                        $.each(errors, (field, messages) => {
                            // Support nested schedule errors like schedules.0.exam_date
                            const fieldName = field.replace(/\.(\d+)\./g, '[$1][').replace(/\.(\w+)$/, '[$1]');
                            const input = form.find(`[name="${field}"], [name="${fieldName}"]`);
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
                        alert(xhr.responseJSON?.message || 'Failed to save examination.');
                    }
                }
            });
        });
    },

    initScheduleBuilder() {
        const self = this;
        const container = $('#schedulesContainer');
        const template = $('#scheduleRowTemplate');

        if (!container.length || !template.length) return;

        self.scheduleIndex = container.find('.schedule-card').length;

        // If creating new and empty, add 1 initial schedule card
        if (self.scheduleIndex === 0 && $('#examForm').find('input[name="_method"]').length === 0) {
            self.addScheduleRow();
        }

        // Add Schedule Row button
        $('#btnAddScheduleRow').on('click', function () {
            self.addScheduleRow();
        });

        // Remove Schedule Row
        $(document).on('click', '.btn-remove-schedule', function () {
            $(this).closest('.schedule-card').remove();
            self.renumberSchedules();
        });

        // Live Marks Calculator for Schedule rows
        $(document).on('input', '.marks-calc', function () {
            const card = $(this).closest('.schedule-card');
            const theory = parseFloat(card.find('.mark-theory').val()) || 0;
            const pract = parseFloat(card.find('.mark-practical').val()) || 0;
            const intern = parseFloat(card.find('.mark-internal').val()) || 0;
            const viva = parseFloat(card.find('.mark-viva').val()) || 0;

            const total = theory + pract + intern + viva;
            card.find('.mark-total').val(total);
        });
    },

    addScheduleRow() {
        const container = $('#schedulesContainer');
        const template = $('#scheduleRowTemplate').html();
        const key = this.scheduleIndex++;
        const indexNumber = container.find('.schedule-card').length + 1;

        let rowHtml = template.replace(/__KEY__/g, key).replace(/__INDEX__/g, indexNumber);
        container.append(rowHtml);
    },

    renumberSchedules() {
        $('#schedulesContainer').find('.schedule-card').each(function (idx) {
            $(this).find('.schedule-title').html(`<i class="bi bi-calendar3 me-1"></i> Subject Schedule #${idx + 1}`);
        });
    }
};

$(function () {
    ExamManager.init();
});
