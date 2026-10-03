@extends('layouts.admin.master')

@section('title', 'Student Fee Allocations')

@section('content')

<div class="container-fluid">

    <x-ui.page-header
        title="Student Fee Allocations"
        subtitle="Manage and track fee assignments, bulk class allocations, due dates, and student balances">

        <x-slot:actions>
            <a href="{{ route('admin.fees.payments.collect') }}" class="btn btn-outline-success">
                <i class="bi bi-cash-stack me-1"></i> Collect Fees (POS)
            </a>
            <x-ui.button variant="secondary" icon="bi-person-plus" id="btnAddSingleAllocation">
                Custom Allocation
            </x-ui.button>
            <x-ui.button icon="bi-collection" id="btnAddBulkAllocation">
                Bulk Class Allocation
            </x-ui.button>
        </x-slot:actions>

    </x-ui.page-header>

    {{-- Filters --}}
    <x-ui.table.filters id="filterForm">

        <div class="col-12 col-md-2">
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
                name="filter_class_id"
                id="filter_class_id"
                value=""
                :options="['' => 'All Classes'] + ($academicClasses ?? [])"
                placeholder="Class"
            />
        </div>

        <div class="col-12 col-md-2">
            <x-ui.select
                name="filter_section_id"
                id="filter_section_id"
                value=""
                :options="['' => 'All Sections'] + ($sections ?? [])"
                placeholder="Section"
            />
        </div>

        <div class="col-12 col-md-2">
            <x-ui.select
                name="filter_fee_head_id"
                id="filter_fee_head_id"
                value=""
                :options="['' => 'All Fee Heads'] + ($feeHeads ?? [])"
                placeholder="Fee Head"
            />
        </div>

        <div class="col-12 col-md-2">
            <x-ui.select
                name="filter_status"
                id="filter_status"
                value=""
                :options="[
                    '' => 'All Status',
                    'unpaid' => 'Unpaid',
                    'partial' => 'Partially Paid',
                    'paid' => 'Fully Paid',
                    'overdue' => 'Overdue',
                ]"
                placeholder="Status"
            />
        </div>

        <div class="col-12 col-md-2">
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
    <x-ui.datatable id="feeAllocationsTable">
        <x-ui.table.thead>
            <x-ui.table.col width="50">#</x-ui.table.col>
            <x-ui.table.col>Student</x-ui.table.col>
            <x-ui.table.col width="120">Class / Sec</x-ui.table.col>
            <x-ui.table.col>Fee Item</x-ui.table.col>
            <x-ui.table.col width="100">Net Fee</x-ui.table.col>
            <x-ui.table.col width="100">Paid</x-ui.table.col>
            <x-ui.table.col width="100">Balance</x-ui.table.col>
            <x-ui.table.col width="110">Due Date</x-ui.table.col>
            <x-ui.table.col width="100">Status</x-ui.table.col>
            <x-ui.table.col width="100">Action</x-ui.table.col>
        </x-ui.table.thead>
        <x-ui.table.tbody id="feeAllocationsTableBody">
        </x-ui.table.tbody>
    </x-ui.datatable>

</div>

@include('admin.fees.allocations.partials.bulk-modal')
@include('admin.fees.allocations.partials.single-modal')

@endsection

@push('scripts')
<script>
    const FEE_ALLOCATION_LIST_URL = "{{ route('admin.fees.allocations.list') }}";
    const FEE_ALLOCATION_STORE_URL = "{{ route('admin.fees.allocations.store') }}";
    const FEE_ALLOCATION_BULK_URL = "{{ route('admin.fees.allocations.bulk') }}";
    const FEE_ALLOCATION_EDIT_URL = "{{ url('admin/fees/allocations') }}/:id/edit";
    const FEE_ALLOCATION_UPDATE_URL = "{{ url('admin/fees/allocations') }}/:id";
    const FEE_ALLOCATION_DELETE_URL = "{{ url('admin/fees/allocations') }}/:id";
    const FEE_GET_STRUCTURES_URL = "{{ route('admin.fees.allocations.structures') }}";
    const FEE_SEARCH_STUDENTS_URL = "{{ route('admin.fees.allocations.search-students') }}";
    const FEE_DISCOUNTS_DATA = @json($feeDiscountsList ?? []);
</script>
<script src="{{ asset('assets/admin/js/fee-allocations.js') }}?v={{ time() }}"></script>
@endpush
