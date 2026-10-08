@extends('layouts.admin.master')

@section('title', 'Role Management')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <x-ui.page-header
        title="Role Management"
        subtitle="Manage system roles">

        <x-slot:actions>

            <x-ui.button
                icon="bi-plus-lg"
                id="btnAddRole">

                Add Role

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
    <x-ui.datatable id="roleTable">

        <x-ui.table.thead>

            <x-ui.table.col width="60">

                #

            </x-ui.table.col>

            <x-ui.table.col>

                Role Name

            </x-ui.table.col>

            <x-ui.table.col>

                Slug

            </x-ui.table.col>

            <x-ui.table.col width="120">

                Status

            </x-ui.table.col>

            <x-ui.table.col width="180">

                Action

            </x-ui.table.col>

        </x-ui.table.thead>

        <x-ui.table.tbody id="roleTableBody">

        </x-ui.table.tbody>

    </x-ui.datatable>

</div>

@include('admin.roles.partials.modal')
@include('admin.roles.partials.permission-modal')

@endsection

@push('scripts')

    <script>

    const ROLE_LIST_URL="{{ route('admin.roles.list') }}";
    const ROLE_STORE_URL="{{ route('admin.roles.store') }}";
    const ROLE_EDIT_URL = "{{ route('admin.roles.edit', ':id') }}";
    const ROLE_UPDATE_URL = "{{ route('admin.roles.update', ':id') }}";
    const ROLE_DELETE_URL = "{{ route('admin.roles.destroy', ':id') }}";
    const ROLE_STATUS_URL = "{{ route('admin.roles.status', ':id') }}";
    const ROLE_PERMISSIONS_URL = "{{ route('admin.roles.permissions', ':id') }}";
    const ROLE_PERMISSIONS_UPDATE_URL = "{{ route('admin.roles.permissions.update', ':id') }}";

    </script>

    <script src="{{ asset('assets/admin/js/roles.js') }}"></script>

@endpush
