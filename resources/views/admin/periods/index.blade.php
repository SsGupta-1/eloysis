@extends('layouts.admin.master')

@section('title', 'Periods Management')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <x-ui.page-header
        title="Period Management"
        subtitle="Manage Academic Periods">

        <x-slot:actions>

            @hasPermission('periods.create')
            <x-ui.button
                icon="bi-plus-lg"
                id="btnAddPeriod">

                Add Period

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
    <x-ui.datatable id="periodTable">

        <x-ui.table.thead>

            <x-ui.table.col width="60">

                #

            </x-ui.table.col>

            <x-ui.table.col>

                Name

            </x-ui.table.col>

            <x-ui.table.col>

                Start Time

            </x-ui.table.col>

            <x-ui.table.col>

                End Time

            </x-ui.table.col>

            <x-ui.table.col>

                Sort Order

            </x-ui.table.col>

            <x-ui.table.col width="120">

                Status

            </x-ui.table.col>

            <x-ui.table.col width="180">

                Action

            </x-ui.table.col>

        </x-ui.table.thead>

        <x-ui.table.tbody id="periodTableBody">

        </x-ui.table.tbody>

    </x-ui.datatable>

</div>

@include('admin.periods.partials.modal')

@endsection

@push('scripts')

    <script>

    const PERIODS_LIST_URL="{{ route('admin.periods.list') }}";
    const PERIODS_STORE_URL="{{ route('admin.periods.store') }}";
    const PERIODS_EDIT_URL = "{{ route('admin.periods.edit', ':id') }}";
    const PERIODS_UPDATE_URL = "{{ route('admin.periods.update', ':id') }}";
    const PERIODS_DELETE_URL = "{{ route('admin.periods.destroy', ':id') }}";
    const PERIODS_STATUS_URL = "{{ route('admin.periods.status', ':id') }}";

    </script>

    <script src="{{ asset('assets/admin/js/periods.js') }}"></script>

@endpush
