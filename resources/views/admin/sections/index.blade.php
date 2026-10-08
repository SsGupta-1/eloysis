@extends('layouts.admin.master')

@section('title', 'Class Section Management')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <x-ui.page-header
        title="Class Section Management"
        subtitle="Manage Academic Class Sections">

        <x-slot:actions>

            @hasPermission('sections.create')
            <x-ui.button
                icon="bi-plus-lg"
                id="btnAddSection">

                Add Section

            </x-ui.button>
            @endhasPermission

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
                placeholder="Select Status">

            </x-ui.select>

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
    <x-ui.datatable id="sectionTable">

        <x-ui.table.thead>

            <x-ui.table.col width="60">

                #

            </x-ui.table.col>

            <x-ui.table.col>

                Section Name

            </x-ui.table.col>

             <x-ui.table.col>

                Section Code

            </x-ui.table.col>

            <x-ui.table.col width="120">

                Status

            </x-ui.table.col>

            <x-ui.table.col width="180">

                Action

            </x-ui.table.col>

        </x-ui.table.thead>

        <x-ui.table.tbody id="sectionTableBody">

        </x-ui.table.tbody>

    </x-ui.datatable>

</div>

@include('admin.sections.partials.modal')

@endsection

@push('scripts')

    <script>

    const SECTION_LIST_URL="{{ route('admin.sections.list') }}";
    const SECTION_CREATE_URL="{{route('admin.sections.create')}}";
    const SECTION_STORE_URL="{{ route('admin.sections.store') }}";
    const SECTION_EDIT_URL = "{{ route('admin.sections.edit', ':id') }}";
    const SECTION_UPDATE_URL = "{{ route('admin.sections.update', ':id') }}";
    const SECTION_DELETE_URL = "{{ route('admin.sections.destroy', ':id') }}";
    const SECTION_STATUS_URL = "{{ route('admin.sections.status', ':id') }}";

    </script>

    <script src="{{ asset('assets/admin/js/sections.js') }}"></script>

@endpush
