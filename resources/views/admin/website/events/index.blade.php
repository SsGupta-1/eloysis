@extends('layouts.admin.master')

@section('title', 'Events Management')

@section('content')

<div class="container-fluid">

    <x-ui.page-header
        title="Events Management"
        subtitle="Manage upcoming school events, sports meets, and parent-teacher meetings">

        <x-slot:actions>
            <x-ui.button icon="bi-plus-lg" id="btnAddEvent">
                Add Event
            </x-ui.button>
        </x-slot:actions>

    </x-ui.page-header>

    {{-- Filters --}}
    <x-ui.table.filters id="filterForm">

        <div class="col-md-3">
            <x-ui.select
                name="filter_status"
                id="filter_status"
                value=''
                :options="[
                    '1' => 'Active',
                    '0' => 'Inactive',
                    ''  => 'All Status'
                ]"
                placeholder="Select Status"
            />
        </div>

        <div class="col-md-2">
            <x-ui.button
                variant="secondary"
                type="reset"
                id="btnReset"
                block>
                Reset
            </x-ui.button>
        </div>

    </x-ui.table.filters>

    {{-- Table --}}
    <x-ui.datatable id="eventTable">

        <x-ui.table.thead>
            <x-ui.table.col width="60">#</x-ui.table.col>
            <x-ui.table.col>Event Title & Description</x-ui.table.col>
            <x-ui.table.col width="130">Date</x-ui.table.col>
            <x-ui.table.col width="140">Time & Location</x-ui.table.col>
            <x-ui.table.col width="100">Status</x-ui.table.col>
            <x-ui.table.col width="140">Action</x-ui.table.col>
        </x-ui.table.thead>

        <x-ui.table.tbody id="eventTableBody">
        </x-ui.table.tbody>

    </x-ui.datatable>

</div>

@include('admin.website.events.partials.modal')

@endsection

@push('scripts')

    <script>
    const EVENT_LIST_URL = "{{ route('admin.events.list') }}";
    const EVENT_STORE_URL = "{{ route('admin.events.store') }}";
    const EVENT_EDIT_URL = "{{ route('admin.events.edit', ':id') }}";
    const EVENT_UPDATE_URL = "{{ route('admin.events.update', ':id') }}";
    const EVENT_DELETE_URL = "{{ route('admin.events.destroy', ':id') }}";
    const EVENT_STATUS_URL = "{{ route('admin.events.status', ':id') }}";
    </script>

    <script src="{{ asset('assets/admin/js/events.js') }}"></script>

@endpush
