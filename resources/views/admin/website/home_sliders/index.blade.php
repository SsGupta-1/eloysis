@extends('layouts.admin.master')

@section('title', 'Home Sliders')

@section('content')

<div class="container-fluid">

    <x-ui.page-header
        title="Home Sliders"
        subtitle="Manage homepage banner sliders and call-to-actions">

        <x-slot:actions>
            <x-ui.button icon="bi-plus-lg" id="btnAddSlider">
                Add Slider
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
    <x-ui.datatable id="sliderTable">

        <x-ui.table.thead>
            <x-ui.table.col width="60">#</x-ui.table.col>
            <x-ui.table.col width="100">Banner</x-ui.table.col>
            <x-ui.table.col>Title & Subtitle</x-ui.table.col>
            <x-ui.table.col width="140">Button</x-ui.table.col>
            <x-ui.table.col width="90">Order</x-ui.table.col>
            <x-ui.table.col width="100">Status</x-ui.table.col>
            <x-ui.table.col width="140">Action</x-ui.table.col>
        </x-ui.table.thead>

        <x-ui.table.tbody id="sliderTableBody">
        </x-ui.table.tbody>

    </x-ui.datatable>

</div>

@include('admin.website.home_sliders.partials.modal')

@endsection

@push('scripts')

    <script>
    const SLIDER_LIST_URL = "{{ route('admin.home-slider.list') }}";
    const SLIDER_STORE_URL = "{{ route('admin.home-slider.store') }}";
    const SLIDER_EDIT_URL = "{{ route('admin.home-slider.edit', ':id') }}";
    const SLIDER_UPDATE_URL = "{{ route('admin.home-slider.update', ':id') }}";
    const SLIDER_DELETE_URL = "{{ route('admin.home-slider.destroy', ':id') }}";
    const SLIDER_STATUS_URL = "{{ route('admin.home-slider.status', ':id') }}";
    </script>

    <script src="{{ asset('assets/admin/js/home_sliders.js') }}"></script>

@endpush
