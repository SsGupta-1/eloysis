@extends('layouts.admin.master')

@section('title', 'Teacher Subject Management')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <x-ui.page-header
        title="Teacher Subject Management"
        subtitle="Manage Teacher Subjects">

        <x-slot:actions>

            @hasPermission('teacher_subjects.create')
            <x-ui.button
                icon="bi-plus-lg"
                id="btnAddTeacherSubject">

                Add Teacher Subject

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
    <x-ui.datatable id="teacherSubjectTable">

        <x-ui.table.thead>

            <x-ui.table.col width="60">

                #

            </x-ui.table.col>

            <x-ui.table.col>

                Teacher Name

            </x-ui.table.col>

            <x-ui.table.col>

                Emp Id

            </x-ui.table.col>

            <x-ui.table.col>

                Class 

            </x-ui.table.col>
            <x-ui.table.col>

                Section 

            </x-ui.table.col>

            <x-ui.table.col>

                Subject 

            </x-ui.table.col>
             

            <x-ui.table.col width="120">

                Status

            </x-ui.table.col>

            <x-ui.table.col width="180">

                Action

            </x-ui.table.col>

        </x-ui.table.thead>

        <x-ui.table.tbody id="teacherSubjectTableBody">

        </x-ui.table.tbody>

    </x-ui.datatable>

</div>

@include('admin.teacher_subjects.partials.modal')

@endsection

@push('scripts')

    <script>

    const TEACHER_SUBJECT_LIST_URL="{{ route('admin.teacher-subject.list') }}";
    const TEACHER_SUBJECT_CREATE_URL="{{route('admin.teacher-subject.create')}}";
    const TEACHER_SUBJECT_STORE_URL="{{ route('admin.teacher-subject.store') }}";
    const TEACHER_SUBJECT_EDIT_URL = "{{ route('admin.teacher-subject.edit', ':id') }}";
    const TEACHER_SUBJECT_UPDATE_URL = "{{ route('admin.teacher-subject.update', ':id') }}";
    const TEACHER_SUBJECT_DELETE_URL = "{{ route('admin.teacher-subject.destroy', ':id') }}";
    const TEACHER_SUBJECT_STATUS_URL = "{{ route('admin.teacher-subject.status', ':id') }}";

    </script>

    <script src="{{ asset('assets/admin/js/teacherSubjects.js') }}"></script>

@endpush
