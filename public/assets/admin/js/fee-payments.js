const FeePayment = {

    modal: null,
    table: null,

    init() {
        const modalEl = document.getElementById('feeReceiptModal');
        if (modalEl) {
            this.modal = new bootstrap.Modal(modalEl);
        }

        this.initDataTable();
        this.bindEvents();
    },

    initDataTable() {
        this.table = $('#feePaymentsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: FEE_PAYMENT_LIST_URL,
                type: 'GET',
                data: function (d) {
                    d.academic_session_id = $('#filter_academic_session_id').val();
                    d.payment_mode = $('#filter_payment_mode').val();
                    d.status = $('#filter_status').val();
                    d.from_date = $('#filter_from_date').val();
                    d.to_date = $('#filter_to_date').val();
                },
                error: function (xhr) {
                    if (typeof Toast !== 'undefined') {
                        Toast.error(xhr.responseJSON?.message ?? 'Unable to load payment transactions.');
                    }
                }
            },
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
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
                    data: 'receipt_no',
                    name: 'receipt_no',
                    render: function (data, type, row) {
                        return `<span class="fw-bold text-primary">${row.receipt_no}</span>`;
                    }
                },
                {
                    data: 'payment_date',
                    name: 'payment_date',
                    render: function (data, type, row) {
                        if (!row.payment_date) return '-';
                        return new Date(row.payment_date).toLocaleDateString('en-GB');
                    }
                },
                {
                    data: 'student.user.name',
                    name: 'student.user.name',
                    render: function (data, type, row) {
                        const name = row.student?.user?.name ?? 'N/A';
                        const admNo = row.student?.admission_no ? `<span class="badge bg-light text-secondary border">Adm: ${row.student.admission_no}</span>` : '';
                        return `
                            <div>
                                <div class="fw-bold text-dark">${name}</div>
                                <div class="small mt-1">${admNo}</div>
                            </div>
                        `;
                    }
                },
                {
                    data: 'enrollment.student_class.class_name',
                    name: 'enrollment.studentClass.class_name',
                    render: function (data, type, row) {
                        const className = row.enrollment?.student_class?.class_name ?? '-';
                        const secName = row.enrollment?.section?.section_name ?? '';
                        return `<span class="fw-semibold">${className} ${secName ? '(' + secName + ')' : ''}</span>`;
                    }
                },
                {
                    data: 'total_paid',
                    name: 'total_paid',
                    render: function (data, type, row) {
                        return `<span class="fw-bold text-success fs-6">₹${parseFloat(row.total_paid).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>`;
                    }
                },
                {
                    data: 'payment_mode',
                    name: 'payment_mode',
                    render: function (data, type, row) {
                        const modeBadges = {
                            'cash': '<span class="badge bg-success-subtle text-success border border-success-subtle">Cash</span>',
                            'upi': '<span class="badge bg-primary-subtle text-primary border border-primary-subtle">UPI</span>',
                            'card': '<span class="badge bg-info-subtle text-info border border-info-subtle">Card</span>',
                            'net_banking': '<span class="badge bg-purple-subtle text-purple border" style="background:#ede9fe; color:#6b21a8;">Net Banking</span>',
                            'cheque': '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">Cheque</span>',
                            'dd': '<span class="badge bg-secondary-subtle text-secondary border">DD</span>',
                        };
                        return modeBadges[row.payment_mode] ?? `<span class="badge bg-light text-dark">${row.payment_mode}</span>`;
                    }
                },
                {
                    data: 'collector.name',
                    name: 'collector.name',
                    render: function (data, type, row) {
                        return `<span class="small text-muted">${row.collector?.name ?? 'System'}</span>`;
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data, type, row) {
                        return row.status === 'paid'
                            ? '<span class="badge bg-success">Paid</span>'
                            : '<span class="badge bg-danger">Cancelled</span>';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const printUrl = FEE_PAYMENT_PRINT_URL.replace(':id', row.id);
                        const isCancelled = row.status === 'cancelled';
                        return `
                            <a href="${printUrl}" target="_blank" class="btn btn-sm btn-primary" title="Print Receipt">
                                <i class="bi bi-printer"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-info btn-view-receipt text-white" data-id="${row.id}" title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                            ${!isCancelled ? `
                            <button type="button" class="btn btn-sm btn-outline-danger btn-cancel-payment" data-id="${row.id}" title="Cancel Receipt">
                                <i class="bi bi-x-circle"></i>
                            </button>` : ''}
                        `;
                    }
                }
            ]
        });
    },

    bindEvents() {
        const self = this;

        // View Receipt Modal
        $(document).on('click', '.btn-view-receipt', function () {
            const id = $(this).data('id');
            const url = FEE_PAYMENT_SHOW_URL.replace(':id', id);
            const printUrl = FEE_PAYMENT_PRINT_URL.replace(':id', id);

            $('#btnModalPrintReceipt').attr('href', printUrl);
            $('#receiptModalContent').html('<div class="text-center py-4"><span class="spinner-border spinner-border-sm text-primary"></span> Loading receipt...</div>');
            self.modal.show();

            Ajax.request({
                url: url,
                method: 'GET',
                success(response) {
                    const p = response.data;
                    let itemsHtml = '';
                    if (p.items && p.items.length > 0) {
                        p.items.forEach((item, idx) => {
                            itemsHtml += `
                                <tr>
                                    <td>${idx + 1}</td>
                                    <td><strong>${item.allocation?.fee_head?.name ?? 'Fee'}</strong> <span class="text-muted small">(${item.allocation?.title ?? ''})</span></td>
                                    <td class="text-end">₹${parseFloat(item.amount_paid).toFixed(2)}</td>
                                </tr>
                            `;
                        });
                    }

                    const html = `
                        <div class="border rounded p-3 bg-light mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0 text-primary fw-bold">${p.receipt_no}</h5>
                                <span class="badge ${p.status === 'paid' ? 'bg-success' : 'bg-danger'}">${p.status.toUpperCase()}</span>
                            </div>
                            <div class="row g-2 small">
                                <div class="col-6"><strong>Student:</strong> ${p.student?.user?.name ?? 'N/A'} (Adm: ${p.student?.admission_no ?? '-'})</div>
                                <div class="col-6"><strong>Class:</strong> ${p.enrollment?.student_class?.class_name ?? '-'} ${p.enrollment?.section?.section_name ?? ''}</div>
                                <div class="col-6"><strong>Date:</strong> ${p.payment_date}</div>
                                <div class="col-6"><strong>Mode:</strong> ${p.payment_mode.toUpperCase()}</div>
                                ${p.transaction_reference ? `<div class="col-12"><strong>Txn Ref:</strong> ${p.transaction_reference}</div>` : ''}
                            </div>
                        </div>

                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">#</th>
                                    <th>Fee Item</th>
                                    <th class="text-end" width="120">Paid Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHtml}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end">Total Paid:</th>
                                    <th class="text-end text-success fs-6">₹${parseFloat(p.total_paid).toFixed(2)}</th>
                                </tr>
                            </tfoot>
                        </table>
                    `;
                    $('#receiptModalContent').html(html);
                }
            });
        });

        // Cancel Payment
        $(document).on('click', '.btn-cancel-payment', function () {
            const id = $(this).data('id');
            const url = FEE_PAYMENT_CANCEL_URL.replace(':id', id);

            Swal.fire({
                title: 'Cancel Fee Receipt?',
                text: 'This will void the receipt and restore the unpaid balance on student allocations!',
                icon: 'warning',
                input: 'text',
                inputPlaceholder: 'Enter cancellation reason...',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, Cancel Receipt',
                preConfirm: (reason) => {
                    if (!reason) {
                        Swal.showValidationMessage('Please provide a cancellation reason');
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Ajax.request({
                        url: url,
                        method: 'POST',
                        data: { reason: result.value },
                        success(response) {
                            self.table.ajax.reload(null, false);
                        }
                    });
                }
            });
        });

        // Filter Reset
        $('#btnResetFilter').on('click', function () {
            $('#filterForm')[0].reset();
            self.table.ajax.reload();
        });

        $('#filter_academic_session_id, #filter_payment_mode, #filter_status, #filter_from_date, #filter_to_date').on('change', function () {
            self.table.ajax.reload();
        });
    }
};

$(function () {
    FeePayment.init();
});
