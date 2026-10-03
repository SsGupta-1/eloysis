@extends('layouts.admin.master')

@section('title', 'Fee Transactions & Receipts')

@section('content')

<div class="container-fluid">

    <x-ui.page-header
        title="Fee Transactions & Receipts"
        subtitle="View cashier payment history, print receipts, and track collection modes">

        <x-slot:actions>
            <a href="{{ route('admin.fees.allocations.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-list-check me-1"></i> Fee Allocations
            </a>
            <a href="{{ route('admin.fees.payments.collect') }}" class="btn btn-success">
                <i class="bi bi-cash-stack me-1"></i> Collect Fees (POS)
            </a>
        </x-slot:actions>

    </x-ui.page-header>

    {{-- Filters --}}
    <x-ui.table.filters id="filterForm">

        <div class="col-12 col-md-3">
            <x-ui.select
                name="filter_academic_session_id"
                id="filter_academic_session_id"
                value=""
                :options="['' => 'All Sessions'] + ($academicSessions ?? [])"
                placeholder="Academic Session"
            />
        </div>

        <div class="col-12 col-md-2">
            <x-ui.select
                name="filter_payment_mode"
                id="filter_payment_mode"
                value=""
                :options="[
                    '' => 'All Modes',
                    'cash' => 'Cash',
                    'upi' => 'UPI',
                    'card' => 'Debit/Credit Card',
                    'net_banking' => 'Net Banking',
                    'cheque' => 'Cheque',
                    'dd' => 'Demand Draft',
                ]"
                placeholder="Payment Mode"
            />
        </div>

        <div class="col-12 col-md-2">
            <x-ui.select
                name="filter_status"
                id="filter_status"
                value=""
                :options="[
                    '' => 'All Status',
                    'paid' => 'Paid',
                    'cancelled' => 'Cancelled',
                ]"
                placeholder="Status"
            />
        </div>

        <div class="col-12 col-md-2">
            <x-ui.form-input
                type="date"
                name="filter_from_date"
                id="filter_from_date"
                placeholder="From Date"
            />
        </div>

        <div class="col-12 col-md-2">
            <x-ui.form-input
                type="date"
                name="filter_to_date"
                id="filter_to_date"
                placeholder="To Date"
            />
        </div>

        <div class="col-12 col-md-1">
            <x-ui.button
                variant="secondary"
                type="reset"
                id="btnResetFilter"
                icon="bi-arrow-counterclockwise">
                Reset
            </x-ui.button>
        </div>

    </x-ui.table.filters>

    {{-- Table --}}
    <x-ui.datatable id="feePaymentsTable">
        <x-ui.table.thead>
            <x-ui.table.col width="50">#</x-ui.table.col>
            <x-ui.table.col width="140">Receipt No</x-ui.table.col>
            <x-ui.table.col width="110">Date</x-ui.table.col>
            <x-ui.table.col>Student</x-ui.table.col>
            <x-ui.table.col width="120">Class / Sec</x-ui.table.col>
            <x-ui.table.col width="110">Total Paid</x-ui.table.col>
            <x-ui.table.col width="100">Mode</x-ui.table.col>
            <x-ui.table.col width="120">Cashier</x-ui.table.col>
            <x-ui.table.col width="90">Status</x-ui.table.col>
            <x-ui.table.col width="120">Action</x-ui.table.col>
        </x-ui.table.thead>
        <x-ui.table.tbody id="feePaymentsTableBody">
        </x-ui.table.tbody>
    </x-ui.datatable>

</div>

@include('admin.fees.payments.partials.receipt-modal')

@endsection

@push('scripts')
<script>
    const FEE_PAYMENT_LIST_URL = "{{ route('admin.fees.payments.list') }}";
    const FEE_PAYMENT_SHOW_URL = "{{ url('admin/fees/payments') }}/:id";
    const FEE_PAYMENT_PRINT_URL = "{{ url('admin/fees/payments') }}/:id/print";
    const FEE_PAYMENT_CANCEL_URL = "{{ url('admin/fees/payments') }}/:id/cancel";
</script>
<script src="{{ asset('assets/admin/js/fee-payments.js') }}?v={{ time() }}"></script>
@endpush
