@extends('layouts.admin.master')

@section('title', 'Class Timetable')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                Class Timetable
            </h2>

            <p class="text-muted mb-0">
                Manage class-wise daily timetable
            </p>
        </div>

        <x-ui.button
            variant="primary"
            type="button"
            id="btnAddTimetable">

            <i class="bi bi-plus-lg me-1"></i>
            Add Timetable

        </x-ui.button>

    </div>


    {{-- Filters --}}
    <x-ui.table.filters id="filterForm">

        {{-- Academic Session --}}
        <div class="col-md-2">

            <x-ui.select
                name="academic_session_id"
                id="academic_session_id"
                :options="$academicSessions ?? []"
                placeholder="Academic Session">

            </x-ui.select>

        </div>


        {{-- Class --}}
        <div class="col-md-2">

            <x-ui.select
                name="class_id"
                id="class_id"
                :options="$classes ?? []"
                placeholder="Class">

            </x-ui.select>

        </div>


        {{-- Section --}}
        <div class="col-md-2">

            <x-ui.select
                name="section_id"
                id="section_id"
                :options="$sections ?? []"
                placeholder="Section">

            </x-ui.select>

        </div>


        {{-- Teacher --}}
        <div class="col-md-2">

            <x-ui.select
                name="teacher_id"
                id="teacher_id"
                :options="$teachers ?? []"
                placeholder="Teacher">

            </x-ui.select>

        </div>


        {{-- Day --}}
        <div class="col-md-2">

            <x-ui.select
                name="day"
                id="day"
                :options="[
                    'Monday' => 'Monday',
                    'Tuesday' => 'Tuesday',
                    'Wednesday' => 'Wednesday',
                    'Thursday' => 'Thursday',
                    'Friday' => 'Friday',
                    'Saturday' => 'Saturday',
                    'Sunday' => 'Sunday',
                ]"
                placeholder="Day">

            </x-ui.select>

        </div>


        {{-- Status --}}
        <div class="col-md-2">

            <x-ui.select
                name="filter_status"
                id="filter_status"
                :options="[
                    '1' => 'Active',
                    '0' => 'Inactive',
                    ''  => 'All Status',
                ]"
                placeholder="Status">

            </x-ui.select>

        </div>


        {{-- Reset --}}
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


    {{-- DataTable --}}
    <x-ui.datatable id="classTimetableTable">

        <x-ui.table.thead>

            <x-ui.table.col width="60">
                #
            </x-ui.table.col>

            <x-ui.table.col>
                Day
            </x-ui.table.col>

            <x-ui.table.col>
                Period
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

            <x-ui.table.col>
                Teacher
            </x-ui.table.col>

            <x-ui.table.col width="120">
                Status
            </x-ui.table.col>

            <x-ui.table.col width="160">
                Action
            </x-ui.table.col>

        </x-ui.table.thead>

        <x-ui.table.tbody id="classTimetableTableBody">
        </x-ui.table.tbody>

    </x-ui.datatable>

</div>


{{-- ========================================================= --}}
{{-- Add / Edit Modal --}}
{{-- ========================================================= --}}

<x-ui.modal id="classTimetableModal" title="Add Timetable" size="lg">

    <form id="classTimetableForm" method="POST">
        @csrf

        <input type="hidden" name="timetable_id" id="timetable_id">

        <div class="row g-3">

            {{-- Teacher Subject Assignment --}}
            <div class="col-md-12">
                <x-ui.select
                    name="teacher_subject_id"
                    id="teacher_subject_id"
                    :options="$teacherSubjects ?? []"
                    placeholder="Select Teacher / Class / Subject">
                </x-ui.select>

                <div id="teacherSubjectInfo" class="mt-2 d-none">
                    <div class="alert alert-info mb-0">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Teacher:</strong>
                                <span id="infoTeacher">-</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Class:</strong>
                                <span id="infoClass">-</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Section:</strong>
                                <span id="infoSection">-</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Subject:</strong>
                                <span id="infoSubject">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Period --}}
            <div class="col-md-6">
                <x-ui.select
                    name="period_id"
                    id="period_id"
                    :options="$periods ?? []"
                    placeholder="Select Period">
                </x-ui.select>
            </div>

            {{-- Day --}}
            <div class="col-md-6">
                <x-ui.select
                    name="day"
                    id="modal_day"
                    :options="[
                        'Monday' => 'Monday',
                        'Tuesday' => 'Tuesday',
                        'Wednesday' => 'Wednesday',
                        'Thursday' => 'Thursday',
                        'Friday' => 'Friday',
                        'Saturday' => 'Saturday',
                        'Sunday' => 'Sunday',
                    ]"
                    placeholder="Select Day">
                </x-ui.select>
            </div>

            {{-- Status --}}
            <div class="col-md-6">
                <x-ui.select
                    name="status"
                    id="modal_status"
                    :options="[
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]"
                    value="1"
                    placeholder="Select Status">
                </x-ui.select>
            </div>

        </div>

    </form>

    <x-slot:footer>
        <x-ui.button
            variant="secondary"
            type="button"
            data-bs-dismiss="modal">
            Cancel
        </x-ui.button>

        <x-ui.button
            variant="primary"
            type="submit"
            form="classTimetableForm"
            id="btnSaveTimetable">
            Save Timetable
        </x-ui.button>
    </x-slot:footer>

</x-ui.modal>


@endsection

@push('scripts')

<script>

    const CLASS_TIMETABLE_LIST_URL="{{ route('admin.class-timetables.list') }}";
    const CLASS_TIMETABLE_STORE_URL="{{ route('admin.class-timetables.store') }}";
    const CLASS_TIMETABLE_EDIT_URL = "{{ route('admin.class-timetables.edit', ':id') }}";
    const CLASS_TIMETABLE_UPDATE_URL = "{{ route('admin.class-timetables.update', ':id') }}";
    const CLASS_TIMETABLE_DELETE_URL = "{{ route('admin.class-timetables.destroy', ':id') }}";
    const CLASS_TIMETABLE_STATUS_URL = "{{ route('admin.class-timetables.status', ':id') }}";

</script>

<script src="{{ asset('assets/admin/js/classTimetable.js') }}"></script>

@endpush