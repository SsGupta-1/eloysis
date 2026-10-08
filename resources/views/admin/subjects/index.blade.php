@extends('layouts.admin.master')

@section('title', 'Subject Management')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <x-ui.page-header
        title="Subject Management"
        subtitle="Manage Academic Subjects">

        <x-slot:actions>

            @hasPermission('subjects.create')
            <x-ui.button
                icon="bi-plus-lg"
                id="btnAddSubject">

                Add Subject

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
    <x-ui.datatable id="subjectTable">

        <x-ui.table.thead>

            <x-ui.table.col width="60">

                #

            </x-ui.table.col>


            <x-ui.table.col>

                Subject Name

            </x-ui.table.col>

             <x-ui.table.col>

                Subject Code

            </x-ui.table.col>
             <x-ui.table.col >

                Description

            </x-ui.table.col>

            <x-ui.table.col width="120">

                Status

            </x-ui.table.col>

            <x-ui.table.col width="180">

                Action

            </x-ui.table.col>

        </x-ui.table.thead>

        <x-ui.table.tbody id="subjectTableBody">

        </x-ui.table.tbody>

    </x-ui.datatable>

</div>

@include('admin.subjects.partials.modal')

@endsection

@push('scripts')

    <script>

    const SUBJECT_LIST_URL="{{ route('admin.subjects.list') }}";
    const SUBJECT_CREATE_URL="{{route('admin.subjects.create')}}";
    const SUBJECT_STORE_URL="{{ route('admin.subjects.store') }}";
    const SUBJECT_EDIT_URL = "{{ route('admin.subjects.edit', ':id') }}";
    const SUBJECT_UPDATE_URL = "{{ route('admin.subjects.update', ':id') }}";
    const SUBJECT_DELETE_URL = "{{ route('admin.subjects.destroy', ':id') }}";
    const SUBJECT_STATUS_URL = "{{ route('admin.subjects.status', ':id') }}";

    </script>

    <script src="{{ asset('assets/admin/js/subjects.js') }}"></script>

@endpush
