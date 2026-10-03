const FeeCollect = {

    currentLedger: null,
    searchTimer: null,

    init() {
        this.bindEvents();

        if (typeof PRESELECTED_ENROLLMENT_ID !== 'undefined' && PRESELECTED_ENROLLMENT_ID) {
            this.loadStudentLedger(PRESELECTED_ENROLLMENT_ID);
        }
    },

    bindEvents() {
        const self = this;

        // Search Student Input
        $('#student_search_input').on('keyup', function () {
            const query = $(this).val().trim();
            clearTimeout(self.searchTimer);

            if (query.length < 2) {
                $('#studentSearchResults').removeClass('show').html('');
                return;
            }

            self.searchTimer = setTimeout(() => {
                self.searchStudents(query);
            }, 300);
        });

        // Click on student search result
        $(document).on('click', '.student-search-item', function (e) {
            e.preventDefault();
            const enrollmentId = $(this).data('id');
            $('#studentSearchResults').removeClass('show');
            $('#student_search_input').val($(this).data('name'));
            self.loadStudentLedger(enrollmentId);
        });

        // Close dropdown when clicking outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('#student_search_input, #studentSearchResults').length) {
                $('#studentSearchResults').removeClass('show');
            }
        });

        // Clear / Reset
        $('#btnResetStudent').on('click', function () {
            $('#student_search_input').val('');
            $('#studentSearchResults').removeClass('show').html('');
            $('#noStudentSelected').removeClass('d-none');
            $('#posBillingArea').addClass('d-none');
            $('#payment_student_enrollment_id').val('');
            self.currentLedger = null;
        });

        // Payment Mode Change
        $('#payment_mode').on('change', function () {
            const mode = $(this).val();
            if (mode === 'cash') {
                $('#referenceFieldWrapper').addClass('d-none');
                $('#chequeDetailsWrapper').addClass('d-none');
            } else if (mode === 'cheque' || mode === 'dd') {
                $('#referenceFieldWrapper').removeClass('d-none');
                $('#refLabel').text(mode === 'cheque' ? 'Cheque Number *' : 'DD Number *');
                $('#chequeDetailsWrapper').removeClass('d-none');
            } else {
                $('#referenceFieldWrapper').removeClass('d-none');
                $('#refLabel').text(mode === 'upi' ? 'UPI UTR / Ref Number' : 'Transaction Reference / Card Txn ID');
                $('#chequeDetailsWrapper').addClass('d-none');
            }
        });

        // Select All Dues Checkbox
        $('#selectAllDues').on('change', function () {
            const checked = $(this).is(':checked');
            $('.item-checkbox').prop('checked', checked);
            $('.item-pay-input').each(function () {
                const maxBal = parseFloat($(this).attr('max')) || 0;
                $(this).prop('disabled', !checked);
                if (checked) {
                    if (parseFloat($(this).val()) <= 0) {
                        $(this).val(maxBal.toFixed(2));
                    }
                }
            });
            self.calculateTotals();
        });

        // Individual item checkbox toggle
        $(document).on('change', '.item-checkbox', function () {
            const allocId = $(this).data('id');
            const input = $(`#pay_input_${allocId}`);
            const maxBal = parseFloat(input.attr('max')) || 0;
            const checked = $(this).is(':checked');

            input.prop('disabled', !checked);
            if (checked && parseFloat(input.val()) <= 0) {
                input.val(maxBal.toFixed(2));
            }
            self.calculateTotals();
        });

        // Paying input amount change (Partial Payment)
        $(document).on('input', '.item-pay-input', function () {
            const allocId = $(this).data('id');
            const maxBal = parseFloat($(this).attr('max')) || 0;
            let val = parseFloat($(this).val()) || 0;

            if (val > maxBal) {
                val = maxBal;
                $(this).val(maxBal.toFixed(2));
                if (typeof Toast !== 'undefined') {
                    Toast.warning(`Paying amount cannot exceed the due balance of ₹${maxBal.toFixed(2)}`);
                }
            } else if (val < 0) {
                val = 0;
                $(this).val('0.00');
            }

            const chk = $(`#chk_${allocId}`);
            if (val > 0 && !chk.is(':checked')) {
                chk.prop('checked', true);
            }

            self.calculateTotals();
        });

        // Quick "Pay Full" button on row
        $(document).on('click', '.btn-pay-full', function () {
            const allocId = $(this).data('id');
            const input = $(`#pay_input_${allocId}`);
            const maxBal = parseFloat(input.attr('max')) || 0;
            $(`#chk_${allocId}`).prop('checked', true);
            input.prop('disabled', false).val(maxBal.toFixed(2));
            self.calculateTotals();
        });

        // Quick Partial Cash Distributor (Auto-Split across dues)
        $('#btnApplyQuickPay').on('click', function () {
            let quickAmount = parseFloat($('#quickPayAmount').val()) || 0;
            if (quickAmount <= 0) {
                if (typeof Toast !== 'undefined') {
                    Toast.warning('Please enter a valid partial amount to distribute.');
                }
                return;
            }

            let remainingToDistribute = quickAmount;

            $('.item-pay-input').each(function () {
                const allocId = $(this).data('id');
                const maxBal = parseFloat($(this).attr('max')) || 0;
                const chk = $(`#chk_${allocId}`);

                if (remainingToDistribute <= 0) {
                    $(this).val('0.00').prop('disabled', true);
                    chk.prop('checked', false);
                } else if (remainingToDistribute >= maxBal) {
                    $(this).val(maxBal.toFixed(2)).prop('disabled', false);
                    chk.prop('checked', true);
                    remainingToDistribute -= maxBal;
                } else {
                    // Partial allocation to this row!
                    $(this).val(remainingToDistribute.toFixed(2)).prop('disabled', false);
                    chk.prop('checked', true);
                    remainingToDistribute = 0;
                }
            });

            if (remainingToDistribute > 0) {
                if (typeof Toast !== 'undefined') {
                    Toast.info(`₹${(quickAmount - remainingToDistribute).toFixed(2)} distributed across all dues. Extra ₹${remainingToDistribute.toFixed(2)} left over.`);
                }
            }

            self.calculateTotals();
        });

        // Form Submit (Collect Fee)
        $('#feePaymentForm').on('submit', function (e) {
            e.preventDefault();

            const selectedItems = [];
            $('.item-checkbox:checked').each(function () {
                const allocId = $(this).data('id');
                const amountPaid = parseFloat($(`#pay_input_${allocId}`).val()) || 0;
                const discount = parseFloat($(`#discount_input_${allocId}`).val()) || 0;
                const fine = parseFloat($(`#fine_input_${allocId}`).val()) || 0;

                if (amountPaid > 0) {
                    selectedItems.push({
                        allocation_id: allocId,
                        amount_paid: amountPaid,
                        discount_applied: discount,
                        fine_paid: fine
                    });
                }
            });

            if (selectedItems.length === 0) {
                if (typeof Toast !== 'undefined') {
                    Toast.error('Please select at least one fee item with an amount greater than 0 to pay.');
                }
                return;
            }

            const payload = {
                student_enrollment_id: $('#payment_student_enrollment_id').val(),
                payment_date: $('#payment_date').val(),
                payment_mode: $('#payment_mode').val(),
                transaction_reference: $('#transaction_reference').val(),
                bank_name: $('#bank_name').val(),
                cheque_date: $('#cheque_date').val(),
                remarks: $('#remarks').val(),
                items: selectedItems
            };

            const btn = $('#btnCollectAndPrint');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing & Generating Receipt...');

            $.ajax({
                url: FEE_PAYMENT_STORE_URL,
                type: 'POST',
                data: JSON.stringify(payload),
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success(res) {
                    if (typeof Toast !== 'undefined') {
                        Toast.success(res.message ?? 'Payment collected successfully!');
                    }

                    // Open Receipt Print in a new tab/window
                    if (res.data?.print_url) {
                        window.open(res.data.print_url, '_blank');
                    }

                    // Reload ledger for fresh dues & updated history
                    self.loadStudentLedger($('#payment_student_enrollment_id').val());
                },
                error(xhr) {
                    btn.prop('disabled', false).html('<i class="bi bi-printer-fill me-2"></i> Collect & Print Receipt');
                    const msg = xhr.responseJSON?.message ?? 'Failed to collect payment. Please try again.';
                    if (typeof Toast !== 'undefined') {
                        Toast.error(msg);
                    }
                }
            });
        });
    },

    searchStudents(query) {
        const sessionId = $('#pos_academic_session_id').val();
        $.ajax({
            url: FEE_SEARCH_STUDENTS_URL,
            type: 'GET',
            data: { q: query, academic_session_id: sessionId },
            success(res) {
                let html = '';
                if (res.results && res.results.length > 0) {
                    res.results.forEach(student => {
                        html += `
                            <a href="#" class="dropdown-item student-search-item py-2 border-bottom" data-id="${student.id}" data-name="${student.text}">
                                <div class="fw-bold text-dark"><i class="bi bi-person me-2"></i>${student.text}</div>
                            </a>
                        `;
                    });
                } else {
                    html = '<div class="p-3 text-muted small text-center">No matching students found</div>';
                }
                $('#studentSearchResults').html(html).addClass('show');
            }
        });
    },

    loadStudentLedger(enrollmentId) {
        const self = this;
        $.ajax({
            url: FEE_STUDENT_LEDGER_URL,
            type: 'GET',
            data: { student_enrollment_id: enrollmentId },
            success(res) {
                self.currentLedger = res.data;
                self.renderLedger(res.data);
            },
            error(xhr) {
                if (typeof Toast !== 'undefined') {
                    Toast.error(xhr.responseJSON?.message ?? 'Failed to load student fee ledger.');
                }
            }
        });
    },

    renderLedger(data) {
        const student = data.student;
        const summary = data.summary;
        const dues = data.dues;
        const recentPayments = data.recent_payments;

        $('#payment_student_enrollment_id').val(student.enrollment_id);

        // Student Banner
        $('#stuName').text(student.name);
        $('#stuAvatar').text(student.name.charAt(0).toUpperCase());
        $('#stuClassSec').text(`${student.class_name} ${student.section_name ? '(' + student.section_name + ')' : ''}`);
        $('#stuAdmNo').text(student.admission_no);
        $('#stuRollNo').text(student.roll_number);
        $('#stuFatherName').text(student.father_name);
        $('#stuMobile').text(student.guardian_mobile);
        $('#stuSession').text(student.session_name);

        // Summary Cards
        $('#summaryNetAllocated').text(`₹${parseFloat(summary.net_total).toLocaleString('en-IN', {minimumFractionDigits: 2})}`);
        if (parseFloat(summary.total_discount) > 0) {
            $('#summaryAllocatedSubtext').text(`Base ₹${parseFloat(summary.total_allocated).toFixed(2)} - ₹${parseFloat(summary.total_discount).toFixed(2)} discount`);
        } else {
            $('#summaryAllocatedSubtext').text('Net payable amount');
        }

        $('#summaryTotalPaid').text(`₹${parseFloat(summary.total_paid).toLocaleString('en-IN', {minimumFractionDigits: 2})}`);
        $('#summaryTotalBalance').text(`₹${parseFloat(summary.total_balance).toLocaleString('en-IN', {minimumFractionDigits: 2})}`);

        // Dues Table
        let duesHtml = '';
        let pendingDuesCount = 0;

        if (dues && dues.length > 0) {
            dues.forEach((d) => {
                if (d.balance > 0) {
                    pendingDuesCount++;
                    const isOverdue = d.is_overdue;
                    duesHtml += `
                        <tr class="${isOverdue ? 'table-danger-subtle' : ''}">
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input item-checkbox" data-id="${d.id}" data-balance="${d.balance}" data-discount="${d.discount_amount}" data-fine="${d.fine_amount}" id="chk_${d.id}" checked>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">${d.head_name}</div>
                                <div class="text-muted small">${d.title}</div>
                            </td>
                            <td>
                                <span class="small ${isOverdue ? 'text-danger fw-bold' : 'text-muted'}">
                                    ${d.due_date ? new Date(d.due_date).toLocaleDateString('en-GB') : '-'}
                                    ${isOverdue ? '<br><span class="badge bg-danger" style="font-size:0.65rem;">Overdue</span>' : ''}
                                </span>
                            </td>
                            <td class="text-end fw-semibold">₹${parseFloat(d.net_payable).toFixed(2)}</td>
                            <td class="text-end text-muted small">₹${parseFloat(d.paid_amount).toFixed(2)}</td>
                            <td class="text-end fw-bold text-danger">₹${parseFloat(d.balance).toFixed(2)}</td>
                            <td class="text-end">
                                <input type="number" step="0.01" min="0" max="${d.balance}" class="form-control form-control-sm text-end fw-bold item-pay-input" data-id="${d.id}" id="pay_input_${d.id}" value="${d.balance.toFixed(2)}">
                                <input type="hidden" id="discount_input_${d.id}" value="${d.discount_amount}">
                                <input type="hidden" id="fine_input_${d.id}" value="${d.fine_amount}">
                            </td>
                            <td class="text-end">
                                <span class="fw-semibold text-muted small" id="rem_display_${d.id}">₹0.00</span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-xs btn-outline-secondary btn-pay-full py-0 px-1" data-id="${d.id}" title="Set Full Balance">
                                    Full
                                </button>
                            </td>
                        </tr>
                    `;
                }
            });
        }

        if (pendingDuesCount === 0) {
            duesHtml = `
                <tr>
                    <td colspan="9" class="text-center py-4 text-success fw-semibold">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i> All fees for this student are fully settled! No pending dues.
                    </td>
                </tr>
            `;
            $('#selectAllDues').prop('disabled', true).prop('checked', false);
            $('#quickPayAmount, #btnApplyQuickPay').prop('disabled', true);
        } else {
            $('#selectAllDues').prop('disabled', false).prop('checked', true);
            $('#quickPayAmount, #btnApplyQuickPay').prop('disabled', false);
        }

        $('#duesTableBody').html(duesHtml);

        // Past Receipts Table
        let pastHtml = '';
        if (recentPayments && recentPayments.length > 0) {
            recentPayments.forEach(p => {
                const printUrl = FEE_PAYMENT_PRINT_URL.replace(':id', p.id);
                pastHtml += `
                    <tr>
                        <td class="fw-bold text-primary">${p.receipt_no}</td>
                        <td>${p.date}</td>
                        <td><span class="badge bg-light text-dark border">${p.payment_mode}</span></td>
                        <td class="fw-bold text-success">₹${p.total_paid.toFixed(2)}</td>
                        <td><span class="badge ${p.status === 'paid' ? 'bg-success' : 'bg-danger'}">${p.status.toUpperCase()}</span></td>
                        <td class="text-end">
                            <a href="${printUrl}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2" title="Print Receipt">
                                <i class="bi bi-printer"></i> Print
                            </a>
                        </td>
                    </tr>
                `;
            });
        } else {
            pastHtml = '<tr><td colspan="6" class="text-center py-3 text-muted small">No payment history.</td></tr>';
        }
        $('#pastPaymentsTableBody').html(pastHtml);

        // Show POS Billing area
        $('#noStudentSelected').addClass('d-none');
        $('#posBillingArea').removeClass('d-none');

        this.calculateTotals();
    },

    calculateTotals() {
        let totalSelectedDue = 0;
        let totalPayingNow = 0;
        let selectedCount = 0;
        let totalBalanceOnStudent = this.currentLedger?.summary?.total_balance || 0;

        $('.item-checkbox').each(function () {
            const allocId = $(this).data('id');
            const maxBal = parseFloat($(this).data('balance')) || 0;
            const isChecked = $(this).is(':checked');
            const input = $(`#pay_input_${allocId}`);
            const paying = isChecked ? (parseFloat(input.val()) || 0) : 0;
            const remaining = Math.max(0, maxBal - paying);

            if (!isChecked) {
                input.prop('disabled', true).addClass('bg-light text-muted');
            } else {
                input.prop('disabled', false).removeClass('bg-light text-muted');
            }

            // Update remaining label for row
            const remDisplay = $(`#rem_display_${allocId}`);
            if (!isChecked) {
                remDisplay.html(`<span class="text-muted">₹${maxBal.toFixed(2)}</span>`);
            } else if (paying > 0 && remaining > 0) {
                remDisplay.html(`<span class="text-warning fw-bold">₹${remaining.toFixed(2)}</span><br><span class="badge bg-warning-subtle text-warning" style="font-size:0.65rem;">Partial</span>`);
            } else if (paying >= maxBal) {
                remDisplay.html(`<span class="text-success fw-bold">₹0.00</span><br><span class="badge bg-success-subtle text-success" style="font-size:0.65rem;">Settled</span>`);
            } else {
                remDisplay.html(`<span class="text-muted">₹${maxBal.toFixed(2)}</span>`);
            }

            if (isChecked) {
                totalSelectedDue += maxBal;
                totalPayingNow += paying;
                selectedCount++;
            }
        });

        const remainingAfterPay = Math.max(0, totalBalanceOnStudent - totalPayingNow);

        $('#calcSelectedTotalDue').text(`₹${totalSelectedDue.toFixed(2)}`);
        $('#calcSelectedItemsCount').text(`${selectedCount} item(s)`);
        $('#calcTotalPayable').text(`₹${totalPayingNow.toFixed(2)}`);
        $('#calcRemainingDueAfterPay').text(`₹${remainingAfterPay.toFixed(2)}`);

        if (totalPayingNow > 0) {
            $('#btnCollectAndPrint').prop('disabled', false).html('<i class="bi bi-printer-fill me-2"></i> Collect & Print Receipt');
        } else {
            $('#btnCollectAndPrint').prop('disabled', true).html('<i class="bi bi-printer-fill me-2"></i> Collect & Print Receipt');
        }
    }
};

$(function () {
    FeeCollect.init();
});
