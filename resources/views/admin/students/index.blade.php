@extends('layouts.admin.master')

@section('title', 'Student Management')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <x-ui.page-header
        title="Student Management"
        subtitle="Manage students and their academic enrollment">

        <x-slot:actions>

            @hasPermission('students.create')
            <x-ui.button
                icon="bi-plus-lg"
                id="btnAddStudent"
                href="{{ route('admin.students.create') }}">

                Add Student

            </x-ui.button>
            @endhasPermission

        </x-slot:actions>

    </x-ui.page-header>


    {{-- Filters --}}
    <x-ui.table.filters id="filterForm">

        {{-- Academic Session --}}
        <div class="col-md-3">

            <x-ui.select
                name="academic_session_id"
                id="academic_session_id"
                :options="$academicSessions"
                placeholder="Academic Session" />

        </div>


        {{-- Class --}}
        <div class="col-md-3">

            <x-ui.select
                name="class_id"
                id="class_id"
                :options="$classes"
                placeholder="Select Class" />

        </div>


        {{-- Section --}}
        <div class="col-md-2">

            <x-ui.select
                name="section_id"
                id="section_id"
                :options="$sections"
                placeholder="Select Section" />

        </div>


        {{-- Status --}}
        <div class="col-md-2">

            <x-ui.select
                name="status"
                id="status"
                value=''
                :options="[
                    '1' => 'Active',
                    '0' => 'Inactive',
                    ''  => 'All Status'
                ]"
                placeholder="Select Status" />

        </div>


        {{-- Reset --}}
        <div class="col-md-2">

            <x-ui.button
                variant="secondary"
                type="reset"
                id="btnReset"
                block>

                <i class="bi bi-arrow-counterclockwise"></i> Reset

            </x-ui.button>

        </div>

    </x-ui.table.filters>


    {{-- Student Table --}}
    <x-ui.datatable id="studentTable">

        <x-ui.table.thead>

            <x-ui.table.col width="60">

                #

            </x-ui.table.col>


            <x-ui.table.col>

                Student

            </x-ui.table.col>

            <x-ui.table.col>

                Mobile

            </x-ui.table.col>

            <x-ui.table.col>

                Academic Session

            </x-ui.table.col>

            <x-ui.table.col>

                Roll Number

            </x-ui.table.col>


            <x-ui.table.col>

                Class

            </x-ui.table.col>


            <x-ui.table.col>

                Section

            </x-ui.table.col>


            <x-ui.table.col width="120">

                Status

            </x-ui.table.col>


            <x-ui.table.col width="180">

                Action

            </x-ui.table.col>

        </x-ui.table.thead>


        <x-ui.table.tbody
            id="studentTableBody">

        </x-ui.table.tbody>

    </x-ui.datatable>

</div>


@endsection


@push('scripts')

<script>

    const STUDENT_LIST_URL = "{{ route('admin.students.list') }}";

    const STUDENT_STORE_URL = "{{ route('admin.students.store') }}";

    const STUDENT_SHOW_URL = "{{ route('admin.students.show', ':id') }}";

    const STUDENT_UPDATE_URL = "{{ route('admin.students.update', ':id') }}";

    const STUDENT_DELETE_URL = "{{ route('admin.students.destroy', ':id') }}";

    const STUDENT_STATUS_URL = "{{ route('admin.students.status', ':id') }}";

    const DEFAULT_AVATAR = "{{ asset('assets/images/default-avatar.png') }}";
    
    const SECTION_BY_CLASS_URL = "{{ route('admin.sections.byClass', ':id') }}";

</script>


<script src="{{ asset('assets/admin/js/students.js') }}"></script>

@endpush