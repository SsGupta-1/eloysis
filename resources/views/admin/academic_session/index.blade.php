@extends('layouts.admin.master')

@section('title', 'Academic Session Management')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <x-ui.page-header
        title="Academic Session Management"
        subtitle="Manage academic sessions">

        <x-slot:actions>

            @hasPermission('academic_sessions.create')
            <x-ui.button
                icon="bi-plus-lg"
                id="btnAddSession">

                Add Session

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
    <x-ui.datatable id="academicTable">

        <x-ui.table.thead>

            <x-ui.table.col width="60">

                #

            </x-ui.table.col>

            <x-ui.table.col>Name</x-ui.table.col>

            <x-ui.table.col>Start Year</x-ui.table.col>
            <x-ui.table.col>End Year</x-ui.table.col>
            <x-ui.table.col>Start Date</x-ui.table.col>
            <x-ui.table.col>End Date</x-ui.table.col>

            <x-ui.table.col width="120">

                Status

            </x-ui.table.col>

            <x-ui.table.col width="180">

                Action

            </x-ui.table.col>

        </x-ui.table.thead>

        <x-ui.table.tbody id="academicTableBody">

        </x-ui.table.tbody>

    </x-ui.datatable>

</div>

@include('admin.academic_session.partials.modal')

@endsection

@push('scripts')

    <script>

    const ACADEMIC_LIST_URL="{{ route('admin.academic.list') }}";
    const ACADEMIC_STORE_URL="{{ route('admin.academic.store') }}";
    const ACADEMIC_EDIT_URL = "{{ route('admin.academic.edit', ':id') }}";
    const ACADEMIC_UPDATE_URL = "{{ route('admin.academic.update', ':id') }}";
    const ACADEMIC_DELETE_URL = "{{ route('admin.academic.destroy', ':id') }}";
    const ACADEMIC_STATUS_URL = "{{ route('admin.academic.status', ':id') }}";

    </script>

    <script src="{{ asset('assets/admin/js/academic.js') }}"></script>

@endpush
