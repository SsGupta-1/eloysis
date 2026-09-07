@extends('layouts.admin.master')

@section('title', 'Student Attendance')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <x-ui.page-header
        title="Teacher Attendance"
        subtitle="Manage daily teacher attendance">
    </x-ui.page-header>


    {{-- Filters --}}
    <x-ui.card class="mb-4">

        <form id="filterForm">

            <div class="row g-3">

                {{-- Attendance Date --}}
                <div class="col-md-3">

                    <x-ui.form-input
                        type="date"
                        name="attendance_date"
                        id="attendance_date"
                        value="{{ date('Y-m-d') }}"
                        label="Attendance Date"
                        required />

                </div>


                {{-- Load Button --}}
                <div class="col-md-12">

                    <x-ui.button
                        type="submit"
                        id="btnLoadStudents">

                        <i class="bi bi-search me-1"></i>

                        Load teachers

                    </x-ui.button>

                </div>

            </div>

        </form>

    </x-ui.card>


    {{-- Attendance Card --}}
    <x-ui.card>

        {{-- Header --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">

            <div>

                <h5 class="mb-1">
                    Teacher Attendance
                </h5>

                <div
                    id="attendanceSummary"
                    class="text-muted small">

                    No teacher loaded.

                </div>

            </div>


            <div class="d-flex gap-2 flex-wrap">
                <button
                    type="button"
                    class="btn btn-primary"
                    id="btnSaveAttendance"
                    disabled>

                    <i class="bi bi-check2-circle me-1"></i>

                    Save Attendance

                </button>

            </div>

        </div>


        {{-- Table --}}
        <div class="attendance-table-wrapper">

            <table
                class="table table-hover align-middle mb-0"
                id="attendanceTable">

                <thead>

                    <tr>

                        <th width="50">
                            #
                        </th>

                        <th width="70">
                            Photo
                        </th>

                        <th>
                            Teacher
                        </th>

                        <th>
                            Employee ID
                        </th>
                        <th>
                            mobile 
                        </th>

                        <th width="330">
                            Attendance
                        </th>

                        <th width="250">
                            Remarks
                        </th>

                    </tr>

                </thead>


                <tbody id="attendanceTableBody">

                    <tr class="attendance-row">

                        <td
                            colspan="7"
                            class="text-center text-muted py-5">

                            <i class="bi bi-people fs-3 d-block mb-2"></i>

                            Select date to load teachers.

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>


        {{-- Bottom Save --}}
        <div
            class="d-flex justify-content-end mt-3 pt-3 border-top">

            <button
                type="button"
                class="btn btn-primary"
                id="btnSaveAttendanceBottom"
                disabled>

                <i class="bi bi-check2-circle me-1"></i>

                Save Attendance

            </button>

        </div>

    </x-ui.card>

</div>

@endsection


@push('styles')

<style>

    .attendance-table-wrapper {
        max-height: 600px;
        overflow-y: auto;
        overflow-x: auto;
        border: 1px solid #e9ecef;
        border-radius: 8px;
    }

    .attendance-table-wrapper thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #f8f9fa;
        white-space: nowrap;
        border-bottom: 1px solid #dee2e6;
    }


    .attendance-remarks {
        min-width: 200px;
    }

</style>

@endpush


@push('scripts')

<script>

    const TEACHER_ATTENDANCE_LIST_URL =
        "{{ route('admin.teacher_attendance.list') }}";

    const TEACHER_ATTENDANCE_SAVE_URL =
        "{{ route('admin.teacher_attendance.save') }}";

</script>

<script src="{{ asset('assets/admin/js/teacherAttendance.js') }}"></script>

@endpush