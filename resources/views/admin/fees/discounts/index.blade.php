@extends('layouts.admin.master')

@section('title', 'Fee Discounts & Concessions')

@section('content')

<div class="container-fluid">

    <x-ui.page-header
        title="Fee Discounts & Scholarships"
        subtitle="Manage concessions, fee waivers, sibling discounts, and scholarship rules">

        <x-slot:actions>
            <x-ui.button icon="bi-plus-lg" id="btnAddFeeDiscount">
                Add Discount Rule
            </x-ui.button>
        </x-slot:actions>

    </x-ui.page-header>

    {{-- Filters --}}
    <x-ui.table.filters id="filterForm">

        <div class="col-12 col-md-4 col-lg-3">
            <x-ui.select
                name="filter_discount_type"
                id="filter_discount_type"
                value=""
                :options="[
                    'fixed' => 'Fixed Amount (₹)',
                    'percentage' => 'Percentage (%)',
                    '' => 'All Discount Types'
                ]"
                placeholder="Discount Type"
            />
        </div>

        <div class="col-12 col-md-4 col-lg-3">
            <x-ui.select
                name="filter_status"
                id="filter_status"
                value=""
                :options="[
                    '1' => 'Active',
                    '0' => 'Inactive',
                    ''  => 'All Status'
                ]"
                placeholder="Select Status"
            />
        </div>

        <div class="col-12 col-md-4 col-lg-2">
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
    <x-ui.datatable id="feeDiscountsTable">
        <x-ui.table.thead>
            <x-ui.table.col width="60">#</x-ui.table.col>
            <x-ui.table.col>Discount Name</x-ui.table.col>
            <x-ui.table.col width="140">Code</x-ui.table.col>
            <x-ui.table.col width="140">Type</x-ui.table.col>
            <x-ui.table.col width="140">Value / Amount</x-ui.table.col>
            <x-ui.table.col width="100">Status</x-ui.table.col>
            <x-ui.table.col width="140">Created At</x-ui.table.col>
            <x-ui.table.col width="120">Action</x-ui.table.col>
        </x-ui.table.thead>
        <x-ui.table.tbody id="feeDiscountsTableBody">
        </x-ui.table.tbody>
    </x-ui.datatable>

</div>

@include('admin.fees.discounts.partials.modal')

@endsection

@push('scripts')
<script>
    const FEE_DISCOUNT_LIST_URL = "{{ route('admin.fees.discounts.list') }}";
    const FEE_DISCOUNT_STORE_URL = "{{ route('admin.fees.discounts.store') }}";
    const FEE_DISCOUNT_EDIT_URL = "{{ url('admin/fees/discounts') }}/:id/edit";
    const FEE_DISCOUNT_UPDATE_URL = "{{ url('admin/fees/discounts') }}/:id";
    const FEE_DISCOUNT_DELETE_URL = "{{ url('admin/fees/discounts') }}/:id";
    const FEE_DISCOUNT_STATUS_URL = "{{ url('admin/fees/discounts') }}/:id/status";
</script>
<script src="{{ asset('assets/admin/js/fee-discounts.js') }}?v={{ time() }}"></script>
@endpush
