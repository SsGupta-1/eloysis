@extends('layouts.admin.master')

@section('title', 'News & Notices')

@section('content')

<div class="container-fluid">

    <x-ui.page-header
        title="News & Announcements"
        subtitle="Publish school news, notices, and press releases">

        <x-slot:actions>
            <x-ui.button icon="bi-plus-lg" id="btnAddNews">
                Add News
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
    <x-ui.datatable id="newsTable">

        <x-ui.table.thead>
            <x-ui.table.col width="60">#</x-ui.table.col>
            <x-ui.table.col width="90">Image</x-ui.table.col>
            <x-ui.table.col>Headline & Summary</x-ui.table.col>
            <x-ui.table.col width="130">Date</x-ui.table.col>
            <x-ui.table.col width="100">Status</x-ui.table.col>
            <x-ui.table.col width="140">Action</x-ui.table.col>
        </x-ui.table.thead>

        <x-ui.table.tbody id="newsTableBody">
        </x-ui.table.tbody>

    </x-ui.datatable>

</div>

@include('admin.website.news.partials.modal')

@endsection

@push('scripts')

    <script>
    const NEWS_LIST_URL = "{{ route('admin.news.list') }}";
    const NEWS_STORE_URL = "{{ route('admin.news.store') }}";
    const NEWS_EDIT_URL = "{{ route('admin.news.edit', ':id') }}";
    const NEWS_UPDATE_URL = "{{ route('admin.news.update', ':id') }}";
    const NEWS_DELETE_URL = "{{ route('admin.news.destroy', ':id') }}";
    const NEWS_STATUS_URL = "{{ route('admin.news.status', ':id') }}";
    </script>

    <script src="{{ asset('assets/admin/js/news.js') }}"></script>

@endpush
