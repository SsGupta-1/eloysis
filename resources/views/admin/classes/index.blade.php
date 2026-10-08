@extends('layouts.admin.master')

@section('title', 'Class Management')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <x-ui.page-header
        title="Class Management"
        subtitle="Manage Academic Classes">

        <x-slot:actions>

            @hasPermission('classes.create')
            <x-ui.button
                icon="bi-plus-lg"
                id="btnAddClass">

                Add Class

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
    <x-ui.datatable id="classTable">

        <x-ui.table.thead>

            <x-ui.table.col width="60">

                #

            </x-ui.table.col>

            <x-ui.table.col>

                Class Name

            </x-ui.table.col>

            <x-ui.table.col>

                Class Code

            </x-ui.table.col>

            <x-ui.table.col width="120">

                Status

            </x-ui.table.col>

            <x-ui.table.col width="180">

                Action

            </x-ui.table.col>

        </x-ui.table.thead>

        <x-ui.table.tbody id="classTableBody">

        </x-ui.table.tbody>

    </x-ui.datatable>

</div>

@include('admin.classes.partials.modal')

@endsection

@push('scripts')

    <script>

    const CLASSES_LIST_URL="{{ route('admin.classes.list') }}";
    const CLASSES_STORE_URL="{{ route('admin.classes.store') }}";
    const CLASSES_EDIT_URL = "{{ route('admin.classes.edit', ':id') }}";
    const CLASSES_UPDATE_URL = "{{ route('admin.classes.update', ':id') }}";
    const CLASSES_DELETE_URL = "{{ route('admin.classes.destroy', ':id') }}";
    const CLASSES_STATUS_URL = "{{ route('admin.classes.status', ':id') }}";

    </script>

    <script src="{{ asset('assets/admin/js/classes.js') }}"></script>

@endpush
