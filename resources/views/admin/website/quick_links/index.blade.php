@extends('layouts.admin.master')

@section('title', 'Quick Links')

@section('content')

<div class="container-fluid">

    <x-ui.page-header
        title="Homepage Quick Links"
        subtitle="Manage shortcut links and action cards displayed on the homepage">

        <x-slot:actions>
            <x-ui.button icon="bi-plus-lg" id="btnAddQuickLink">
                Add Quick Link
            </x-ui.button>
        </x-slot:actions>

    </x-ui.page-header>

    {{-- Filters --}}
    <x-ui.table.filters id="filterForm">

        <div class="col-12 col-md-4 col-lg-3">
            <x-ui.select
                name="filter_color"
                id="filter_color"
                value=''
                :options="[
                    'primary' => 'Primary (Blue)',
                    'success' => 'Success (Green)',
                    'warning' => 'Warning (Yellow)',
                    'danger' => 'Danger (Red)',
                    'info' => 'Info (Cyan)',
                    'secondary' => 'Secondary (Gray)',
                    'dark' => 'Dark (Black)',
                    ''  => 'All Themes'
                ]"
                placeholder="Filter Color Theme"
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
    <x-ui.datatable id="quickLinkTable">

        <x-ui.table.thead>
            <x-ui.table.col width="60">#</x-ui.table.col>
            <x-ui.table.col width="80">Icon</x-ui.table.col>
            <x-ui.table.col>Title & Description</x-ui.table.col>
            <x-ui.table.col width="200">Destination URL</x-ui.table.col>
            <x-ui.table.col width="110">Color</x-ui.table.col>
            <x-ui.table.col width="80">Order</x-ui.table.col>
            <x-ui.table.col width="100">Status</x-ui.table.col>
            <x-ui.table.col width="140">Action</x-ui.table.col>
        </x-ui.table.thead>

        <x-ui.table.tbody id="quickLinkTableBody">
        </x-ui.table.tbody>

    </x-ui.datatable>

</div>

@include('admin.website.quick_links.partials.modal')

@endsection

@push('scripts')

    <script>
    const QUICK_LINK_LIST_URL = "{{ route('admin.quick-links.list') }}";
    const QUICK_LINK_STORE_URL = "{{ route('admin.quick-links.store') }}";
    const QUICK_LINK_EDIT_URL = "{{ route('admin.quick-links.edit', ':id') }}";
    const QUICK_LINK_UPDATE_URL = "{{ route('admin.quick-links.update', ':id') }}";
    const QUICK_LINK_DELETE_URL = "{{ route('admin.quick-links.destroy', ':id') }}";
    const QUICK_LINK_STATUS_URL = "{{ route('admin.quick-links.status', ':id') }}";
    </script>

    <script src="{{ asset('assets/admin/js/quick_links.js') }}"></script>

@endpush
