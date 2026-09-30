@extends('layouts.admin.master')

@section('title', 'Announcements')

@section('content')

<div class="container-fluid">

    <x-ui.page-header
        title="Announcements & Circulars"
        subtitle="Manage flash updates, circular badges, and news ticker highlights">

        <x-slot:actions>
            <x-ui.button icon="bi-plus-lg" id="btnAddAnnouncement">
                Add Announcement
            </x-ui.button>
        </x-slot:actions>

    </x-ui.page-header>

    {{-- Filters --}}
    <x-ui.table.filters id="filterForm">

        <div class="col-12 col-md-4 col-lg-3">
            <x-ui.select
                name="filter_badge"
                id="filter_badge"
                value=''
                :options="[
                    'Admission' => 'Admission',
                    'Notice' => 'Notice',
                    'Event' => 'Event',
                    'Urgent' => 'Urgent',
                    'Exam' => 'Exam',
                    'General' => 'General',
                    ''  => 'All Badges'
                ]"
                placeholder="Filter Badge"
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
    <x-ui.datatable id="announcementTable">

        <x-ui.table.thead>
            <x-ui.table.col width="60">#</x-ui.table.col>
            <x-ui.table.col width="110">Badge</x-ui.table.col>
            <x-ui.table.col>Title & Content</x-ui.table.col>
            <x-ui.table.col width="160">Action Link</x-ui.table.col>
            <x-ui.table.col width="180">Active Duration</x-ui.table.col>
            <x-ui.table.col width="80">Order</x-ui.table.col>
            <x-ui.table.col width="100">Status</x-ui.table.col>
            <x-ui.table.col width="140">Action</x-ui.table.col>
        </x-ui.table.thead>

        <x-ui.table.tbody id="announcementTableBody">
        </x-ui.table.tbody>

    </x-ui.datatable>

</div>

@include('admin.website.announcements.partials.modal')

@endsection

@push('scripts')

    <script>
    const ANNOUNCEMENT_LIST_URL = "{{ route('admin.announcements.list') }}";
    const ANNOUNCEMENT_STORE_URL = "{{ route('admin.announcements.store') }}";
    const ANNOUNCEMENT_EDIT_URL = "{{ route('admin.announcements.edit', ':id') }}";
    const ANNOUNCEMENT_UPDATE_URL = "{{ route('admin.announcements.update', ':id') }}";
    const ANNOUNCEMENT_DELETE_URL = "{{ route('admin.announcements.destroy', ':id') }}";
    const ANNOUNCEMENT_STATUS_URL = "{{ route('admin.announcements.status', ':id') }}";
    </script>

    <script src="{{ asset('assets/admin/js/announcements.js') }}"></script>

@endpush
