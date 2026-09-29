const ResultManager = {
    table: null,

    init() {
        if ($('#resultsTable').length) {
            this.initDataTable();
        }
        this.bindEvents();
    },

    initDataTable() {
        this.table = $('#resultsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: RESULT_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.academic_session_id = $('#filter_academic_session_id').val();
                    d.class_id = $('#filter_class_id').val();
                    d.is_published = $('#filter_is_published').val();
                },
                error: function (xhr) {
                    if (typeof Toast !== 'undefined') {
                        Toast.error(xhr.responseJSON?.message ?? 'Unable to load results list.');
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
                        const broadsheetUrl = `${RESULT_BASE_URL}/exam/${row.id}`;
                        return `
                            <div>
                                <a href="${broadsheetUrl}" class="fw-bold text-dark text-decoration-none">${data}</a>
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
                    data: 'schedules_count',
                    name: 'schedules_count',
                    render: function (data) {
                        return `<span class="badge bg-light text-dark border">${data || 0} Subject(s)</span>`;
                    }
                },
                {
                    data: 'enrolled_students_count',
                    name: 'enrolled_students_count',
                    render: function (data) {
                        return `<span class="badge bg-primary-subtle text-primary border border-primary-subtle">${data || 0} Candidates</span>`;
                    }
                },
                {
                    data: 'is_published',
                    name: 'is_published',
                    render: function (data, type, row) {
                        return `
                            <div class="form-check form-switch">
                                <input class="form-check-input btn-publish-switch-table" type="checkbox" data-id="${row.id}" ${data ? 'checked' : ''}>
                                <label class="form-check-label small ${data ? 'text-success fw-bold' : 'text-muted'}">${data ? 'Published' : 'Draft'}</label>
                            </div>
                        `;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function (data, type, row) {
                        const broadsheetUrl = `${RESULT_BASE_URL}/exam/${row.id}`;
                        const printUrl = `${RESULT_BASE_URL}/exam/${row.id}/tabulation-print`;
                        return `
                            <div class="btn-group btn-group-sm">
                                <a href="${broadsheetUrl}" class="btn btn-outline-primary" title="View Broadsheet & Tabulation">
                                    <i class="bi bi-table me-1"></i> Broadsheet
                                </a>
                                <a href="${printUrl}" class="btn btn-outline-secondary" target="_blank" title="Print Tabulation Sheet">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        `;
                    }
                }
            ]
        });
    },

    bindEvents() {
        const self = this;

        $('#filter_academic_session_id, #filter_class_id, #filter_is_published').on('change', () => {
            if (self.table) self.table.ajax.reload();
        });

        $('#btnResetFilters').on('click', () => {
            $('#filter_academic_session_id, #filter_class_id, #filter_is_published').val('');
            if (self.table) self.table.search('').ajax.reload();
        });

        // Publication Toggle from Table
        $(document).on('change', '.btn-publish-switch-table', function () {
            const id = $(this).data('id');
            const url = PUBLISH_TOGGLE_URL.replace(':id', id);
            const switchEl = $(this);
            const label = switchEl.siblings('label');

            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                },
                success: (res) => {
                    if (res.data.is_published) {
                        label.text('Published').removeClass('text-muted').addClass('text-success fw-bold');
                    } else {
                        label.text('Draft').removeClass('text-success fw-bold').addClass('text-muted');
                    }

                    if (typeof Toast !== 'undefined') {
                        Toast.success(res.message);
                    }
                },
                error: (xhr) => {
                    switchEl.prop('checked', !switchEl.is(':checked'));
                    alert(xhr.responseJSON?.message || 'Failed to update publication status.');
                }
            });
        });
    }
};

$(function () {
    ResultManager.init();
});
