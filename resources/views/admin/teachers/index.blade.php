@extends('layouts.admin.master')

@section('title', 'Teacher Management')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <x-ui.page-header
        title="Teacher Management"
        subtitle="Manage Teachers">

        <x-slot:actions>

            @hasPermission('teachers.create')
            <x-ui.button
                icon="bi-plus-lg"
                id="btnAddTeacher">

                Add Teacher

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


    {{-- Teacher Table --}}
    <x-ui.datatable id="teacherTable">

        <x-ui.table.thead>

            <x-ui.table.col width="60">

                #

            </x-ui.table.col>


            <x-ui.table.col>

                Teacher Name

            </x-ui.table.col>


            <x-ui.table.col>

                Email

            </x-ui.table.col>


            <x-ui.table.col>

                Employee ID

            </x-ui.table.col>


            <x-ui.table.col>

                Mobile

            </x-ui.table.col>

            <x-ui.table.col>

                Specialization

            </x-ui.table.col>


            <x-ui.table.col width="120">

                Status

            </x-ui.table.col>


            <x-ui.table.col width="180">

                Action

            </x-ui.table.col>

        </x-ui.table.thead>


        <x-ui.table.tbody
            id="teacherTableBody">

        </x-ui.table.tbody>

    </x-ui.datatable>

</div>


@include('admin.teachers.partials.modal')
@include('admin.teachers.partials.view-modal')
@include('admin.common.user-permission-modal')

@endsection

@push('scripts')

    <script>

        const TEACHER_LIST_URL =
            "{{ route('admin.teachers.list') }}";

        const TEACHER_CREATE_URL =
            "{{ route('admin.teachers.create') }}";

        const TEACHER_STORE_URL =
            "{{ route('admin.teachers.store') }}";

        const TEACHER_EDIT_URL =
            "{{ route('admin.teachers.edit', ':id') }}";

        const TEACHER_UPDATE_URL =
            "{{ route('admin.teachers.update', ':id') }}";

        const TEACHER_SHOW_URL =
            "{{ route('admin.teachers.show', ':id') }}";

        const TEACHER_DELETE_URL =
            "{{ route('admin.teachers.destroy', ':id') }}";

        const TEACHER_STATUS_URL =
            "{{ route('admin.teachers.status', ':id') }}";

        const TEACHER_PERMISSIONS_URL =
            "{{ route('admin.teachers.permissions', ':id') }}";

        const TEACHER_PERMISSIONS_UPDATE_URL =
            "{{ route('admin.teachers.permissions.update', ':id') }}";

        const DEFAULT_AVATAR ="{{ asset('assets/uploads/profile/default-avatar.jpg') }}";

    </script>

    <script src="{{ asset('assets/admin/js/user-permissions.js') }}"></script>
    <script src="{{ asset('assets/admin/js/teachers.js') }}"></script>

@endpush