@extends('layouts.admin.master')

@section('title', 'Fee Collection (POS)')

@section('content')

<div class="container-fluid">

    <x-ui.page-header
        title="Fee Collection (Cashier POS)"
        subtitle="Quick student lookup, outstanding dues settlement, partial payments, and instant receipt printing">

        <x-slot:actions>
            <a href="{{ route('admin.fees.payments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-clock-history me-1"></i> Transaction History
            </a>
            <a href="{{ route('admin.fees.allocations.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-list-check me-1"></i> Fee Allocations
            </a>
        </x-slot:actions>

    </x-ui.page-header>

    {{-- Student Search Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-center">
                <div class="col-12 col-md-3">
                    <label for="pos_academic_session_id" class="form-label fw-semibold small mb-1">Academic Session</label>
                    <select id="pos_academic_session_id" class="form-select">
                        @foreach(($academicSessions ?? []) as $sId => $sName)
                            <option value="{{ $sId }}">{{ $sName }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-7">
                    <label for="student_search_input" class="form-label fw-semibold small mb-1">Search Student (Name, Admission No, Roll No, Guardian)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="student_search_input" class="form-control" placeholder="Type name, admission number, or roll number to search..." autocomplete="off">
                        <button class="btn btn-primary" type="button" id="btnSearchStudent">
                            Search
                        </button>
                    </div>
                    <div id="studentSearchResults" class="dropdown-menu w-100 p-0 shadow-lg" style="max-height: 280px; overflow-y: auto;"></div>
                </div>

                <div class="col-12 col-md-2 text-md-end mt-md-4">
                    <button type="button" class="btn btn-outline-secondary w-100" id="btnResetStudent">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Default Placeholder when no student is selected --}}
    <div id="noStudentSelected" class="card border-0 shadow-sm text-center py-5">
        <div class="card-body">
            <div class="mb-3 text-muted">
                <i class="bi bi-person-bounding-box display-4"></i>
            </div>
            <h5 class="text-dark">No Student Selected</h5>
            <p class="text-muted small">Search and select a student above to view pending fee dues, ledger details, and collect full or partial payments.</p>
        </div>
    </div>

    {{-- POS Collection Area (Hidden by default until student is selected) --}}
    <div id="posBillingArea" class="d-none">

        <div class="row g-4 mb-4">
            {{-- Student Information Banner --}}
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-person-badge text-primary me-2"></i>Student Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4 me-3" style="width: 54px; height: 54px;" id="stuAvatar">
                                S
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark" id="stuName">-</h5>
                                <div class="text-muted small" id="stuClassSec">-</div>
                            </div>
                        </div>

                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item px-0 py-2 d-flex justify-content-between">
                                <span class="text-muted">Admission No:</span>
                                <span class="fw-semibold text-dark" id="stuAdmNo">-</span>
                            </li>
                            <li class="list-group-item px-0 py-2 d-flex justify-content-between">
                                <span class="text-muted">Roll Number:</span>
                                <span class="fw-semibold text-dark" id="stuRollNo">-</span>
                            </li>
                            <li class="list-group-item px-0 py-2 d-flex justify-content-between">
                                <span class="text-muted">Father's Name:</span>
                                <span class="fw-semibold text-dark" id="stuFatherName">-</span>
                            </li>
                            <li class="list-group-item px-0 py-2 d-flex justify-content-between">
                                <span class="text-muted">Guardian Mobile:</span>
                                <span class="fw-semibold text-dark" id="stuMobile">-</span>
                            </li>
                            <li class="list-group-item px-0 py-2 d-flex justify-content-between">
                                <span class="text-muted">Academic Session:</span>
                                <span class="fw-semibold text-dark" id="stuSession">-</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Dues Summary Stat Cards --}}
            <div class="col-12 col-lg-8">
                <div class="row g-3 h-100">
                    <div class="col-12 col-sm-4">
                        <div class="card border-0 shadow-sm p-3 h-100 bg-primary-subtle text-primary border-start border-4 border-primary">
                            <div class="text-muted small fw-semibold">NET ALLOCATED FEE</div>
                            <h3 class="fw-bold mb-0 mt-2" id="summaryNetAllocated">₹0.00</h3>
                            <div class="small text-muted mt-1" id="summaryAllocatedSubtext">Net payable after discounts</div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-4">
                        <div class="card border-0 shadow-sm p-3 h-100 bg-success-subtle text-success border-start border-4 border-success">
                            <div class="text-muted small fw-semibold">TOTAL PAID TO DATE</div>
                            <h3 class="fw-bold mb-0 mt-2" id="summaryTotalPaid">₹0.00</h3>
                            <div class="small text-muted mt-1">Settled payments</div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-4">
                        <div class="card border-0 shadow-sm p-3 h-100 bg-danger-subtle text-danger border-start border-4 border-danger">
                            <div class="text-muted small fw-semibold">CURRENT BALANCE DUE</div>
                            <h3 class="fw-bold mb-0 mt-2" id="summaryTotalBalance">₹0.00</h3>
                            <div class="small text-danger fw-semibold mt-1" id="summaryOverdueBadge">Due for collection</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="feePaymentForm" method="POST">
            @csrf
            <input type="hidden" name="student_enrollment_id" id="payment_student_enrollment_id">

            <div class="row g-4">
                {{-- Dues Ledger Table --}}
                <div class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <div class="row g-2 align-items-center justify-content-between">
                                <div class="col-12 col-md-4">
                                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-receipt-cutoff text-primary me-2"></i>Outstanding Dues</h6>
                                </div>
                                {{-- Quick Cash Amount Distributor --}}
                                <div class="col-12 col-md-8">
                                    <div class="d-flex gap-2 justify-content-md-end align-items-center">
                                        <div class="input-group input-group-sm" style="max-width: 250px;">
                                            <span class="input-group-text bg-light fw-semibold">Quick ₹</span>
                                            <input type="number" step="0.01" min="1" id="quickPayAmount" class="form-control text-end fw-bold" placeholder="e.g. 500">
                                            <button type="button" class="btn btn-primary btn-sm" id="btnApplyQuickPay" title="Auto distribute this amount across pending dues">
                                                Apply
                                            </button>
                                        </div>
                                        <div class="form-check m-0 ms-2">
                                            <input class="form-check-input" type="checkbox" id="selectAllDues">
                                            <label class="form-check-label fw-semibold small cursor-pointer" for="selectAllDues">
                                                Select All
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-hover align-middle mb-0" id="duesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="35" class="text-center">#</th>
                                        <th>Fee Item</th>
                                        <th width="90">Due Date</th>
                                        <th width="85" class="text-end">Net Fee</th>
                                        <th width="85" class="text-end">Paid Prior</th>
                                        <th width="95" class="text-end">Due Balance</th>
                                        <th width="120" class="text-end">Paying Now (₹)</th>
                                        <th width="90" class="text-end">Remaining</th>
                                        <th width="50" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="duesTableBody">
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">No pending fee allocations found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-light py-2 text-muted small">
                            <i class="bi bi-info-circle me-1 text-primary"></i> 
                            <strong>Partial Payment:</strong> Check the desired items, then type the partial amount in <strong>Paying Now</strong>, or use <strong>Quick ₹</strong> to auto-distribute cash.
                        </div>
                    </div>

                    {{-- Past Receipts History Accordion --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-clock-history text-secondary me-2"></i>Past Payment Receipts</h6>
                        </div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Receipt No</th>
                                        <th>Date</th>
                                        <th>Mode</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th width="120" class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="pastPaymentsTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted small">No payment history.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Payment Checkout / Cashier Box --}}
                <div class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                        <div class="card-header bg-primary text-white py-3">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-wallet2 me-2"></i>Payment Collection</h6>
                        </div>
                        <div class="card-body p-3">
                            {{-- Calculations --}}
                            <div class="bg-light p-3 rounded mb-3">
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted">Selected Dues Balance:</span>
                                    <span class="fw-bold text-dark" id="calcSelectedTotalDue">₹0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted">Selected Items Count:</span>
                                    <span class="fw-semibold text-primary" id="calcSelectedItemsCount">0 item(s)</span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-dark fs-6">Paying Now Total:</span>
                                    <span class="fs-4 fw-bold text-success" id="calcTotalPayable">₹0.00</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top small">
                                    <span class="text-muted">Remaining Due After Payment:</span>
                                    <span class="fw-bold text-danger" id="calcRemainingDueAfterPay">₹0.00</span>
                                </div>
                            </div>

                            {{-- Payment Mode & Details --}}
                            <div class="mb-3">
                                <label for="payment_date" class="form-label fw-semibold small mb-1">Payment Date *</label>
                                <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="payment_mode" class="form-label fw-semibold small mb-1">Payment Mode *</label>
                                <select name="payment_mode" id="payment_mode" class="form-select" required>
                                    <option value="cash" selected>Cash</option>
                                    <option value="upi">UPI / QR Code</option>
                                    <option value="card">Debit / Credit Card</option>
                                    <option value="net_banking">Net Banking</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="dd">Demand Draft (DD)</option>
                                </select>
                            </div>

                            <div id="referenceFieldWrapper" class="d-none mb-3">
                                <label for="transaction_reference" class="form-label fw-semibold small mb-1" id="refLabel">Transaction Ref / UPI UTR / Card Txn</label>
                                <input type="text" name="transaction_reference" id="transaction_reference" class="form-control" placeholder="Enter reference number">
                            </div>

                            <div id="chequeDetailsWrapper" class="d-none mb-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label for="bank_name" class="form-label fw-semibold small mb-1">Bank Name</label>
                                        <input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="e.g. SBI, HDFC">
                                    </div>
                                    <div class="col-6">
                                        <label for="cheque_date" class="form-label fw-semibold small mb-1">Cheque Date</label>
                                        <input type="date" name="cheque_date" id="cheque_date" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="remarks" class="form-label fw-semibold small mb-1">Payment Note / Remarks (Optional)</label>
                                <textarea name="remarks" id="remarks" rows="2" class="form-control" placeholder="Add cashier note (e.g. Partial payment paid in cash)..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-success btn-lg w-100 py-3 fw-bold shadow-sm" id="btnCollectAndPrint" disabled>
                                <i class="bi bi-printer-fill me-2"></i> Collect & Print Receipt
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>

</div>

@include('admin.fees.payments.partials.receipt-modal')

@endsection

@push('scripts')
<script>
    const FEE_SEARCH_STUDENTS_URL = "{{ route('admin.fees.allocations.search-students') }}";
    const FEE_STUDENT_LEDGER_URL = "{{ route('admin.fees.payments.ledger') }}";
    const FEE_PAYMENT_STORE_URL = "{{ route('admin.fees.payments.store') }}";
    const FEE_PAYMENT_SHOW_URL = "{{ url('admin/fees/payments') }}/:id";
    const FEE_PAYMENT_PRINT_URL = "{{ url('admin/fees/payments') }}/:id/print";
    const PRESELECTED_ENROLLMENT_ID = "{{ $selectedEnrollmentId ?? '' }}";
</script>
<script src="{{ asset('assets/admin/js/fee-collect.js') }}?v={{ time() }}"></script>
@endpush
