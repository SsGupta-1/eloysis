@extends('layouts.admin.master')

@section('title', 'Important Messages')

@section('content')

<div class="container-fluid">

    <x-ui.page-header
        title="Important Messages & Notices"
        subtitle="Manage prominent announcement banners, alerts, and urgent notices on homepage">

        <x-slot:actions>
            <x-ui.button icon="bi-plus-lg" id="btnAddMessage">
                Add Important Message
            </x-ui.button>
        </x-slot:actions>

    </x-ui.page-header>

    {{-- Filters --}}
    <x-ui.table.filters id="filterForm">

        <div class="col-12 col-md-4 col-lg-3">
            <x-ui.select
                name="filter_type"
                id="filter_type"
                value=''
                :options="[
                    'info' => 'Info (Blue)',
                    'warning' => 'Warning (Yellow)',
                    'danger' => 'Danger (Red)',
                    'success' => 'Success (Green)',
                    ''  => 'All Types'
                ]"
                placeholder="Select Type"
            />
        </div>

        <div class="col-12 col-md-4 col-lg-3">
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

        <div class="col-12 col-md-4 col-lg-2">
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
    <x-ui.datatable id="messageTable">

        <x-ui.table.thead>
            <x-ui.table.col width="60">#</x-ui.table.col>
            <x-ui.table.col>Title & Message</x-ui.table.col>
            <x-ui.table.col width="110">Type</x-ui.table.col>
            <x-ui.table.col width="160">Action Link</x-ui.table.col>
            <x-ui.table.col width="180">Active Duration</x-ui.table.col>
            <x-ui.table.col width="80">Order</x-ui.table.col>
            <x-ui.table.col width="100">Status</x-ui.table.col>
            <x-ui.table.col width="140">Action</x-ui.table.col>
        </x-ui.table.thead>

        <x-ui.table.tbody id="messageTableBody">
        </x-ui.table.tbody>

    </x-ui.datatable>

</div>

@include('admin.website.important_messages.partials.modal')

@endsection

@push('scripts')

    <script>
    const MESSAGE_LIST_URL = "{{ route('admin.important-messages.list') }}";
    const MESSAGE_STORE_URL = "{{ route('admin.important-messages.store') }}";
    const MESSAGE_EDIT_URL = "{{ route('admin.important-messages.edit', ':id') }}";
    const MESSAGE_UPDATE_URL = "{{ route('admin.important-messages.update', ':id') }}";
    const MESSAGE_DELETE_URL = "{{ route('admin.important-messages.destroy', ':id') }}";
    const MESSAGE_STATUS_URL = "{{ route('admin.important-messages.status', ':id') }}";
    </script>

    <script src="{{ asset('assets/admin/js/important_messages.js') }}"></script>

@endpush
