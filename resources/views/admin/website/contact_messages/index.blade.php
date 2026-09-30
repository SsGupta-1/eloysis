@extends('layouts.admin.master')

@section('title', 'Contact Messages')

@section('content')

<div class="container-fluid">

    <x-ui.page-header
        title="Contact Messages"
        subtitle="Inquiries and messages received from the website contact form">
    </x-ui.page-header>

    {{-- Filters --}}
    <x-ui.table.filters id="filterForm">

        <div class="col-md-3">
            <x-ui.select
                name="status"
                id="filter_status"
                value=''
                :options="[
                    'pending' => 'Pending',
                    'read' => 'Read',
                    'replied' => 'Replied',
                    ''  => 'All Status'
                ]"
                placeholder="Filter Status"
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
    <x-ui.datatable id="messageTable">

        <x-ui.table.thead>
            <x-ui.table.col width="60">#</x-ui.table.col>
            <x-ui.table.col width="160">Sender</x-ui.table.col>
            <x-ui.table.col width="160">Contact Info</x-ui.table.col>
            <x-ui.table.col>Subject & Message</x-ui.table.col>
            <x-ui.table.col width="110">Status</x-ui.table.col>
            <x-ui.table.col width="130">Date</x-ui.table.col>
            <x-ui.table.col width="120">Action</x-ui.table.col>
        </x-ui.table.thead>

        <x-ui.table.tbody id="messageTableBody">
        </x-ui.table.tbody>

    </x-ui.datatable>

</div>

@include('admin.website.contact_messages.partials.modal')

@endsection

@push('scripts')

    <script>
    const MESSAGE_LIST_URL = "{{ route('admin.contact-messages.list') }}";
    const MESSAGE_SHOW_URL = "{{ route('admin.contact-messages.show', ':id') }}";
    const MESSAGE_STATUS_URL = "{{ route('admin.contact-messages.status', ':id') }}";
    const MESSAGE_DELETE_URL = "{{ route('admin.contact-messages.destroy', ':id') }}";
    </script>

    <script src="{{ asset('assets/admin/js/contact_messages.js') }}"></script>

@endpush
