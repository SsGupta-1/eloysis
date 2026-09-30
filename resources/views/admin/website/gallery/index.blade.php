@extends('layouts.admin.master')

@section('title', 'Gallery Management')

@section('content')

<div class="container-fluid">

    <x-ui.page-header
        title="Photo Gallery"
        subtitle="Upload and organize campus photos, event moments, and activity pictures">

        <x-slot:actions>
            <x-ui.button icon="bi-plus-lg" id="btnAddGallery">
                Add Photo
            </x-ui.button>
        </x-slot:actions>

    </x-ui.page-header>

    {{-- Filters --}}
    <x-ui.table.filters id="filterForm">

        <div class="col-md-3">
            <x-ui.select
                name="filter_category"
                id="filter_category"
                value=''
                :options="[
                    'Campus' => 'Campus & Infrastructure',
                    'Events' => 'Events & Celebrations',
                    'Sports' => 'Sports & Activities',
                    'Academics' => 'Classrooms & Labs',
                    'General' => 'General',
                    ''  => 'All Categories'
                ]"
                placeholder="Filter Category"
            />
        </div>

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
    <x-ui.datatable id="galleryTable">

        <x-ui.table.thead>
            <x-ui.table.col width="60">#</x-ui.table.col>
            <x-ui.table.col width="100">Photo</x-ui.table.col>
            <x-ui.table.col>Title / Caption</x-ui.table.col>
            <x-ui.table.col width="140">Category</x-ui.table.col>
            <x-ui.table.col width="90">Order</x-ui.table.col>
            <x-ui.table.col width="100">Status</x-ui.table.col>
            <x-ui.table.col width="140">Action</x-ui.table.col>
        </x-ui.table.thead>

        <x-ui.table.tbody id="galleryTableBody">
        </x-ui.table.tbody>

    </x-ui.datatable>

</div>

@include('admin.website.gallery.partials.modal')

@endsection

@push('scripts')

    <script>
    const GALLERY_LIST_URL = "{{ route('admin.gallery.list') }}";
    const GALLERY_STORE_URL = "{{ route('admin.gallery.store') }}";
    const GALLERY_EDIT_URL = "{{ route('admin.gallery.edit', ':id') }}";
    const GALLERY_UPDATE_URL = "{{ route('admin.gallery.update', ':id') }}";
    const GALLERY_DELETE_URL = "{{ route('admin.gallery.destroy', ':id') }}";
    const GALLERY_STATUS_URL = "{{ route('admin.gallery.status', ':id') }}";
    </script>

    <script src="{{ asset('assets/admin/js/gallery.js') }}"></script>

@endpush
