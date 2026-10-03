@extends('layouts.admin.master')

@section('title', 'Fee Heads')

@section('content')

<div class="container-fluid">

    <x-ui.page-header
        title="Fee Heads Management"
        subtitle="Configure different fee types and categories (Tuition, Admission, Transport, Exams, etc.)">

        <x-slot:actions>
            <x-ui.button icon="bi-plus-lg" id="btnAddFeeHead">
                Add Fee Head
            </x-ui.button>
        </x-slot:actions>

    </x-ui.page-header>

    {{-- Filters --}}
    <x-ui.table.filters id="filterForm">

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
    <x-ui.datatable id="feeHeadsTable">
        <x-ui.table.thead>
            <x-ui.table.col width="60">#</x-ui.table.col>
            <x-ui.table.col>Fee Head Name</x-ui.table.col>
            <x-ui.table.col width="150">Code</x-ui.table.col>
            <x-ui.table.col>Description</x-ui.table.col>
            <x-ui.table.col width="100">Status</x-ui.table.col>
            <x-ui.table.col width="140">Created At</x-ui.table.col>
            <x-ui.table.col width="120">Action</x-ui.table.col>
        </x-ui.table.thead>
        <x-ui.table.tbody id="feeHeadsTableBody">
        </x-ui.table.tbody>
    </x-ui.datatable>

</div>

@include('admin.fees.heads.partials.modal')

@endsection

@push('scripts')
<script>
    const FEE_HEAD_LIST_URL = "{{ route('admin.fees.heads.list') }}";
    const FEE_HEAD_STORE_URL = "{{ route('admin.fees.heads.store') }}";
    const FEE_HEAD_EDIT_URL = "{{ url('admin/fees/heads') }}/:id/edit";
    const FEE_HEAD_UPDATE_URL = "{{ url('admin/fees/heads') }}/:id";
    const FEE_HEAD_DELETE_URL = "{{ url('admin/fees/heads') }}/:id";
    const FEE_HEAD_STATUS_URL = "{{ url('admin/fees/heads') }}/:id/status";
</script>
<script src="{{ asset('assets/admin/js/fee-heads.js') }}?v={{ time() }}"></script>
@endpush
