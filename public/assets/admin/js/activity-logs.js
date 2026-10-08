const ActivityLogs = {
    table: null,
    detailsModal: null,
    clearModal: null,

    init() {
        const detailsModalEl = document.getElementById('logDetailsModal');
        if (detailsModalEl) {
            this.detailsModal = new bootstrap.Modal(detailsModalEl);
        }

        const clearModalEl = document.getElementById('clearLogsModal');
        if (clearModalEl) {
            this.clearModal = new bootstrap.Modal(clearModalEl);
        }

        this.initDataTable();
        this.bindEvents();
    },

    /*
    |--------------------------------------------------------------------------
    | DataTable
    |--------------------------------------------------------------------------
    */
    initDataTable() {
        this.table = $('#activityLogTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: ACTIVITY_LOG_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.user_id = $('#filter_user_id').val();
                    d.module = $('#filter_module').val();
                    d.action = $('#filter_action').val();
                    d.start_date = $('#filter_start_date').val();
                    d.end_date = $('#filter_end_date').val();
                },
                error: function (xhr) {
                    if (typeof Toast !== 'undefined') {
                        Toast.error(xhr.responseJSON?.message ?? 'Unable to load activity logs.');
                    }
                }
            },
            pageLength: 15,
            lengthMenu: [
                [15, 30, 50, 100],
                [15, 30, 50, 100]
            ],
            searching: true,
            ordering: true,
            order: [[1, 'desc']], // Sort by Date & Time descending
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
                    data: 'created_at',
                    name: 'created_at',
                    render: function (data) {
                        if (!data) return '-';
                        const date = new Date(data);
                        return `<div class="fw-semibold small text-dark">${date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</div>
                                <div class="text-muted small">${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true })}</div>`;
                    }
                },
                {
                    data: 'user_name',
                    name: 'user_name',
                    render: function (data, type, row) {
                        const name = row.user_name || 'System / Guest';
                        const email = row.user_email ? `<small class="text-muted d-block">${row.user_email}</small>` : '';
                        const guardBadge = row.guard ? `<span class="badge bg-light text-secondary border px-1 py-0 small">${row.guard}</span>` : '';
                        return `<div class="d-flex align-items-center gap-2">
                                    <div>
                                        <div class="fw-medium text-dark">${name} ${guardBadge}</div>
                                        ${email}
                                    </div>
                                </div>`;
                    }
                },
                {
                    data: 'module',
                    name: 'module',
                    render: function (data) {
                        if (!data) return '-';
                        const formatted = data.replace(/_/g, ' ').toUpperCase();
                        return `<span class="badge bg-light text-dark border fw-medium px-2 py-1">${formatted}</span>`;
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    render: function (data) {
                        if (!data) return '-';
                        let badgeClass = 'bg-secondary';
                        const act = data.toUpperCase();

                        if (act.includes('CREATE') || act.includes('STORE')) {
                            badgeClass = 'bg-success text-white';
                        } else if (act.includes('UPDATE') || act.includes('EDIT')) {
                            badgeClass = 'bg-primary text-white';
                        } else if (act.includes('DELETE') || act.includes('DESTROY')) {
                            badgeClass = 'bg-danger text-white';
                        } else if (act.includes('STATUS')) {
                            badgeClass = 'bg-warning text-dark';
                        } else if (act.includes('LOGIN')) {
                            badgeClass = 'bg-info text-dark';
                        } else if (act.includes('LOGOUT')) {
                            badgeClass = 'bg-dark text-white';
                        }

                        return `<span class="badge ${badgeClass} px-2 py-1 fw-semibold">${data}</span>`;
                    }
                },
                {
                    data: 'description',
                    name: 'description',
                    render: function (data, type, row) {
                        const desc = data || `${row.method} ${row.url}`;
                        return `<div class="text-dark small text-truncate" style="max-width: 280px;" title="${desc}">${desc}</div>`;
                    }
                },
                {
                    data: 'ip',
                    name: 'ip',
                    render: function (data, type, row) {
                        let methodColor = 'secondary';
                        const m = (row.method || 'GET').toUpperCase();
                        if (m === 'POST') methodColor = 'success';
                        else if (m === 'PUT' || m === 'PATCH') methodColor = 'primary';
                        else if (m === 'DELETE') methodColor = 'danger';

                        return `<div class="font-monospace small text-dark">${data || '-'}</div>
                                <span class="badge bg-${methodColor}-subtle text-${methodColor} px-1">${m}</span>`;
                    }
                },
                {
                    data: 'status_code',
                    name: 'status_code',
                    render: function (data) {
                        if (!data) return '-';
                        let badge = 'bg-success-subtle text-success';
                        if (data >= 400 && data < 500) badge = 'bg-warning-subtle text-warning';
                        else if (data >= 500) badge = 'bg-danger-subtle text-danger';

                        return `<span class="badge ${badge} fw-semibold">${data}</span>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function (data, type, row) {
                        return `<button type="button" class="btn btn-sm btn-light border btn-view-log" data-id="${row.id}" title="View Details">
                                    <i class="bi bi-eye text-primary"></i>
                                </button>`;
                    }
                }
            ]
        });
    },

    /*
    |--------------------------------------------------------------------------
    | Events & Handlers
    |--------------------------------------------------------------------------
    */
    bindEvents() {
        const self = this;

        // Filters Change
        $('#filter_user_id, #filter_module, #filter_action, #filter_start_date, #filter_end_date').on('change', function () {
            self.table.ajax.reload();
        });

        // Reset Filter
        $('#btnResetFilter').on('click', function () {
            $('#filter_user_id').val('');
            $('#filter_module').val('');
            $('#filter_action').val('');
            $('#filter_start_date').val('');
            $('#filter_end_date').val('');
            self.table.ajax.reload();
        });

        // View Log Details Click
        $(document).on('click', '.btn-view-log', function () {
            const logId = $(this).data('id');
            self.showLogDetails(logId);
        });

        // Open Clear Logs Modal
        $('#btnOpenClearLogsModal').on('click', function () {
            if (self.clearModal) {
                self.clearModal.show();
            }
        });

        // Clear Logs Form Submit
        $('#clearLogsForm').on('submit', function (e) {
            e.preventDefault();
            self.handleClearLogs();
        });

        // Copy Payload JSON
        $('#btnCopyPayload').on('click', function () {
            const jsonText = $('#modalPayloadJson').text();
            navigator.clipboard.writeText(jsonText).then(() => {
                if (typeof Toast !== 'undefined') {
                    Toast.success('JSON Payload copied to clipboard.');
                } else {
                    alert('Copied to clipboard!');
                }
            });
        });
    },

    /*
    |--------------------------------------------------------------------------
    | Show Log Details Modal
    |--------------------------------------------------------------------------
    */
    showLogDetails(logId) {
        const self = this;
        const url = ACTIVITY_LOG_SHOW_URL.replace(':id', logId);

        $.ajax({
            url: url,
            type: 'GET',
            success: function (res) {
                if (res.status && res.data?.log) {
                    const log = res.data.log;

                    $('#modalUserName').text(log.user_name || 'System / Guest');
                    $('#modalUserEmail').text(log.user_email || 'No email');
                    $('#modalModule').html(`<span class="badge bg-light text-dark border">${(log.module || '-').toUpperCase()}</span>`);
                    $('#modalAction').html(`<span class="badge bg-primary text-white">${log.action || '-'}</span>`);
                    $('#modalRouteMethod').text(`${log.method || '-'} | Route: ${log.route_name || 'N/A'}`);
                    $('#modalIp').text(log.ip || '-');
                    $('#modalDurationStatus').html(`<span class="badge bg-success-subtle text-success me-1">${log.status_code || 200}</span> <span class="small text-muted">${log.duration_ms ? log.duration_ms + ' ms' : '-'}</span>`);
                    $('#modalDescription').text(log.description || '-');
                    $('#modalUrl').text(log.url || '-');
                    $('#modalUserAgent').text(log.user_agent || '-');

                    // Payload JSON formatting
                    let payloadStr = '{}';
                    if (log.payload) {
                        try {
                            payloadStr = JSON.stringify(typeof log.payload === 'string' ? JSON.parse(log.payload) : log.payload, null, 2);
                        } catch (e) {
                            payloadStr = JSON.stringify(log.payload, null, 2);
                        }
                    }
                    $('#modalPayloadJson').text(payloadStr);

                    if (self.detailsModal) {
                        self.detailsModal.show();
                    }
                }
            },
            error: function (xhr) {
                if (typeof Toast !== 'undefined') {
                    Toast.error(xhr.responseJSON?.message ?? 'Failed to fetch log details.');
                }
            }
        });
    },

    /*
    |--------------------------------------------------------------------------
    | Handle Clear Logs
    |--------------------------------------------------------------------------
    */
    handleClearLogs() {
        const self = this;
        const days = $('#clearLogsDays').val();

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Are you sure?',
                text: `All activity logs older than ${days} days will be permanently deleted.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete logs!'
            }).then((result) => {
                if (result.isConfirmed) {
                    self.executeClearLogs(days);
                }
            });
        } else {
            if (confirm(`Are you sure you want to delete logs older than ${days} days?`)) {
                self.executeClearLogs(days);
            }
        }
    },

    executeClearLogs(days) {
        const self = this;
        const btn = $('#btnConfirmClearLogs');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Deleting...');

        $.ajax({
            url: ACTIVITY_LOG_CLEAR_URL,
            type: 'POST',
            data: {
                _token: $('input[name="_token"]').val(),
                days: days
            },
            success: function (res) {
                if (self.clearModal) {
                    self.clearModal.hide();
                }
                if (typeof Toast !== 'undefined') {
                    Toast.success(res.message || 'Old activity logs cleared.');
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire('Deleted!', res.message, 'success');
                }
                self.table.ajax.reload();
            },
            error: function (xhr) {
                if (typeof Toast !== 'undefined') {
                    Toast.error(xhr.responseJSON?.message ?? 'Failed to clear activity logs.');
                }
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="bi bi-trash3 me-1"></i> Confirm & Delete');
            }
        });
    }
};

$(document).ready(function () {
    ActivityLogs.init();
});
