@extends('layouts.admin.master')

@section('title', 'Admin Management')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <x-ui.page-header
        title="Admin Management"
        subtitle="Manage system administrators">

        <x-slot:actions>

            <x-ui.button
                icon="bi-plus-lg"
                id="btnAddStaff">

                Add Admin

            </x-ui.button>

        </x-slot:actions>

    </x-ui.page-header>


    {{-- Filters --}}
    <x-ui.table.filters id="filterForm">

        <div class="col-md-3">

            <x-ui.select
                name="filter_status"
                id="filter_status"
                value=""
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
    <x-ui.datatable id="staffTable">

        <x-ui.table.thead>

            <x-ui.table.col width="60">

                #

            </x-ui.table.col>


            <x-ui.table.col>

                Admin

            </x-ui.table.col>


            <x-ui.table.col>

                Mobile

            </x-ui.table.col>


            <x-ui.table.col>

                Employee ID

            </x-ui.table.col>


            <x-ui.table.col>

                Designation

            </x-ui.table.col>

             <x-ui.table.col>

                Department

            </x-ui.table.col>


            <x-ui.table.col width="120">

                Status

            </x-ui.table.col>


            <x-ui.table.col width="180">

                Action

            </x-ui.table.col>

        </x-ui.table.thead>


        <x-ui.table.tbody id="staffTableBody">

        </x-ui.table.tbody>

    </x-ui.datatable>

</div>


@include('admin.staffs.partials.modal')
@include('admin.staffs.partials.view-modal')
@include('admin.common.user-permission-modal')

@endsection

@push('scripts')

<script>

    const STAFF_LIST_URL =
        "{{ route('admin.staffs.list') }}";

    const STAFF_STORE_URL =
        "{{ route('admin.staffs.store') }}";

    const STAFF_SHOW_URL =
        "{{ route('admin.staffs.show', ':id') }}";

    const STAFF_UPDATE_URL =
        "{{ route('admin.staffs.update', ':id') }}";

    const STAFF_DELETE_URL =
        "{{ route('admin.staffs.destroy', ':id') }}";

    const STAFF_STATUS_URL =
        "{{ route('admin.staffs.status', ':id') }}";

    const STAFF_PERMISSIONS_URL =
        "{{ route('admin.staffs.permissions', ':id') }}";

    const STAFF_PERMISSIONS_UPDATE_URL =
        "{{ route('admin.staffs.permissions.update', ':id') }}";
    const DEFAULT_AVATAR = "{{ asset('assets/images/default-avatar.png') }}";

</script>

<script src="{{ asset('assets/admin/js/user-permissions.js') }}"></script>
<script src="{{ asset('assets/admin/js/staffs.js') }}"></script>

@endpush
