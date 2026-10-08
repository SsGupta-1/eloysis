@extends('layouts.admin.master')

@section('title', 'Class Subject Management')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <x-ui.page-header
        title="Class Subject Management"
        subtitle="Manage Academic Class Subjects">

        <x-slot:actions>

            @hasPermission('class_subjects.create')
            <x-ui.button
                icon="bi-plus-lg"
                id="btnAddClassSubject">

                Add Class Subject

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
    <x-ui.datatable id="classSubjectTable">

        <x-ui.table.thead>

            <x-ui.table.col width="60">

                #

            </x-ui.table.col>

            <x-ui.table.col>

                Class Name

            </x-ui.table.col>

            <x-ui.table.col>

                Subject Name

            </x-ui.table.col>

             <x-ui.table.col>

                Subject Code

            </x-ui.table.col>
             

            <x-ui.table.col width="120">

                Status

            </x-ui.table.col>

            <x-ui.table.col width="180">

                Action

            </x-ui.table.col>

        </x-ui.table.thead>

        <x-ui.table.tbody id="classSubjectTableBody">

        </x-ui.table.tbody>

    </x-ui.datatable>

</div>

@include('admin.class_subjects.partials.modal')

@endsection

@push('scripts')

    <script>

    const CLASS_SUBJECT_LIST_URL="{{ route('admin.clsubject.list') }}";
    const CLASS_SUBJECT_CREATE_URL="{{route('admin.clsubject.create')}}";
    const CLASS_SUBJECT_STORE_URL="{{ route('admin.clsubject.store') }}";
    const CLASS_SUBJECT_EDIT_URL = "{{ route('admin.clsubject.edit', ':id') }}";
    const CLASS_SUBJECT_UPDATE_URL = "{{ route('admin.clsubject.update', ':id') }}";
    const CLASS_SUBJECT_DELETE_URL = "{{ route('admin.clsubject.destroy', ':id') }}";
    const CLASS_SUBJECT_STATUS_URL = "{{ route('admin.clsubject.status', ':id') }}";

    </script>

    <script src="{{ asset('assets/admin/js/classSubjects.js') }}"></script>

@endpush
