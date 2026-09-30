@extends('layouts.admin.master')

@section('title', 'Testimonials')

@section('content')

<div class="container-fluid">

    <x-ui.page-header
        title="Testimonials & Reviews"
        subtitle="Manage student, parent, and alumni testimonials displayed on the website">

        <x-slot:actions>
            <x-ui.button icon="bi-plus-lg" id="btnAddTestimonial">
                Add Testimonial
            </x-ui.button>
        </x-slot:actions>

    </x-ui.page-header>

    {{-- Filters --}}
    <x-ui.table.filters id="filterForm">

        <div class="col-12 col-md-4 col-lg-3">
            <x-ui.select
                name="filter_rating"
                id="filter_rating"
                value=''
                :options="[
                    '5' => '5 Stars (★★★★★)',
                    '4' => '4 Stars (★★★★☆)',
                    '3' => '3 Stars (★★★☆☆)',
                    '2' => '2 Stars (★★☆☆☆)',
                    '1' => '1 Star (★☆☆☆☆)',
                    ''  => 'All Ratings'
                ]"
                placeholder="Filter by Rating"
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
    <x-ui.datatable id="testimonialTable">

        <x-ui.table.thead>
            <x-ui.table.col width="60">#</x-ui.table.col>
            <x-ui.table.col width="80">Avatar</x-ui.table.col>
            <x-ui.table.col width="180">Name & Role</x-ui.table.col>
            <x-ui.table.col>Testimonial Feedback</x-ui.table.col>
            <x-ui.table.col width="120">Rating</x-ui.table.col>
            <x-ui.table.col width="80">Order</x-ui.table.col>
            <x-ui.table.col width="100">Status</x-ui.table.col>
            <x-ui.table.col width="140">Action</x-ui.table.col>
        </x-ui.table.thead>

        <x-ui.table.tbody id="testimonialTableBody">
        </x-ui.table.tbody>

    </x-ui.datatable>

</div>

@include('admin.website.testimonials.partials.modal')

@endsection

@push('scripts')

    <script>
    const TESTIMONIAL_LIST_URL = "{{ route('admin.testimonials.list') }}";
    const TESTIMONIAL_STORE_URL = "{{ route('admin.testimonials.store') }}";
    const TESTIMONIAL_EDIT_URL = "{{ route('admin.testimonials.edit', ':id') }}";
    const TESTIMONIAL_UPDATE_URL = "{{ route('admin.testimonials.update', ':id') }}";
    const TESTIMONIAL_DELETE_URL = "{{ route('admin.testimonials.destroy', ':id') }}";
    const TESTIMONIAL_STATUS_URL = "{{ route('admin.testimonials.status', ':id') }}";
    </script>

    <script src="{{ asset('assets/admin/js/testimonials.js') }}"></script>

@endpush
