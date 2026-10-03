@extends('layouts.admin.master')

@section('title', 'Fee Structure Master')

@section('content')

<div class="container-fluid">

    <x-ui.page-header
        title="Fee Structures & Slabs"
        subtitle="Configure class-wise, session-wise fee amounts, frequencies, and late fine rules">

        <x-slot:actions>
            <x-ui.button icon="bi-plus-lg" id="btnAddFeeStructure">
                Add Fee Structure
            </x-ui.button>
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

        <div class="col-12 col-md-3">
            <x-ui.select
                name="filter_academic_class_id"
                id="filter_academic_class_id"
                value=""
                :options="['' => 'All Classes'] + ($academicClasses ?? [])"
                placeholder="Class"
            />
        </div>

        <div class="col-12 col-md-2">
            <x-ui.select
                name="filter_frequency"
                id="filter_frequency"
                value=""
                :options="[
                    '' => 'All Frequencies',
                    'monthly' => 'Monthly',
                    'quarterly' => 'Quarterly',
                    'half_yearly' => 'Half-Yearly',
                    'annually' => 'Annually',
                    'one_time' => 'One-Time'
                ]"
                placeholder="Frequency"
            />
        </div>

        <div class="col-12 col-md-2">
            <x-ui.select
                name="filter_status"
                id="filter_status"
                value=""
                :options="[
                    '1' => 'Active',
                    '0' => 'Inactive',
                    ''  => 'All Status'
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
    <x-ui.datatable id="feeStructuresTable">
        <x-ui.table.thead>
            <x-ui.table.col width="60">#</x-ui.table.col>
            <x-ui.table.col width="140">Session</x-ui.table.col>
            <x-ui.table.col width="140">Class</x-ui.table.col>
            <x-ui.table.col>Fee Head</x-ui.table.col>
            <x-ui.table.col width="120">Amount</x-ui.table.col>
            <x-ui.table.col width="120">Frequency</x-ui.table.col>
            <x-ui.table.col width="120">Fine Rule</x-ui.table.col>
            <x-ui.table.col width="100">Status</x-ui.table.col>
            <x-ui.table.col width="120">Action</x-ui.table.col>
        </x-ui.table.thead>
        <x-ui.table.tbody id="feeStructuresTableBody">
        </x-ui.table.tbody>
    </x-ui.datatable>

</div>

@include('admin.fees.structures.partials.modal')

@endsection

@push('scripts')
<script>
    const FEE_STRUCTURE_LIST_URL = "{{ route('admin.fees.structures.list') }}";
    const FEE_STRUCTURE_STORE_URL = "{{ route('admin.fees.structures.store') }}";
    const FEE_STRUCTURE_EDIT_URL = "{{ url('admin/fees/structures') }}/:id/edit";
    const FEE_STRUCTURE_UPDATE_URL = "{{ url('admin/fees/structures') }}/:id";
    const FEE_STRUCTURE_DELETE_URL = "{{ url('admin/fees/structures') }}/:id";
    const FEE_STRUCTURE_STATUS_URL = "{{ url('admin/fees/structures') }}/:id/status";
</script>
<script src="{{ asset('assets/admin/js/fee-structures.js') }}?v={{ time() }}"></script>
@endpush
