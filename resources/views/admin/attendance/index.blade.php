@extends('layouts.admin.master')

@section('title', 'Student Attendance')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <x-ui.page-header
        title="Student Attendance"
        subtitle="Manage daily student attendance">
    </x-ui.page-header>


    {{-- Filters --}}
    <x-ui.card class="mb-4">

        <form id="filterForm">

            <div class="row g-3">

                {{-- Academic Session --}}
                <div class="col-md-3">

                    <x-ui.select
                        name="academic_session_id"
                        id="academic_session_id"
                        :options="$academicSessions"
                        placeholder="Select Session"
                        required>

                        Academic Session

                    </x-ui.select>

                </div>


                {{-- Class --}}
                <div class="col-md-3">

                    <x-ui.select
                        name="class_id"
                        id="class_id"
                        :options="$classes"
                        placeholder="Select Class"
                        required>

                        Class

                    </x-ui.select>

                </div>


                {{-- Section --}}
                <div class="col-md-3">

                    <x-ui.select
                        name="section_id"
                        id="section_id"
                        :options="$sections"
                        placeholder="Select Section">

                        Section

                    </x-ui.select>

                </div>


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

                        Load Students

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
                    Student Attendance
                </h5>

                <div
                    id="attendanceSummary"
                    class="text-muted small">

                    No students loaded.

                </div>

            </div>


            <div class="d-flex gap-2 flex-wrap">

                <button
                    type="button"
                    class="btn btn-outline-success"
                    id="btnMarkAllPresent"
                    disabled>

                    <i class="bi bi-check2-all me-1"></i>

                    Mark All Present

                </button>


                <button
                    type="button"
                    class="btn btn-outline-danger"
                    id="btnMarkAllAbsent"
                    disabled>

                    <i class="bi bi-x-circle me-1"></i>

                    Mark All Absent

                </button>


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
                            Student
                        </th>

                        <th>
                            Admission No
                        </th>

                        <th width="100">
                            Roll No
                        </th>
                        <th>
                            class 
                        </th>
                        <th>
                            section
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
                            colspan="9"
                            class="text-center text-muted py-5">

                            <i class="bi bi-people fs-3 d-block mb-2"></i>

                            Select session, class, section and date
                            to load students.

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

    .attendance-status-group {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }

    .attendance-status-btn {
        min-width: 75px;
        border-radius: 6px;
        font-size: 13px;
        padding: 6px 10px;
        transition: all 0.15s ease;
    }

    .attendance-status-btn.active {
        color: #fff !important;
        font-weight: 600;
    }

    .attendance-status-btn[data-status="present"].active {
        background: #198754;
        border-color: #198754;
    }

    .attendance-status-btn[data-status="absent"].active {
        background: #dc3545;
        border-color: #dc3545;
    }

    .attendance-status-btn[data-status="late"].active {
        background: #ffc107;
        border-color: #ffc107;
        color: #212529 !important;
    }

    .attendance-status-btn[data-status="leave"].active {
        background: #0dcaf0;
        border-color: #0dcaf0;
        color: #212529 !important;
    }

    .attendance-status-btn[data-status="present"]:not(.active) {
        color: #198754;
    }

    .attendance-status-btn[data-status="absent"]:not(.active) {
        color: #dc3545;
    }

    .attendance-status-btn[data-status="late"]:not(.active) {
        color: #856404;
    }

    .attendance-status-btn[data-status="leave"]:not(.active) {
        color: #087990;
    }

    .student-avatar {
        width: 42px;
        height: 42px;
        object-fit: cover;
        border-radius: 50%;
    }

    .student-avatar-placeholder {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #f1f3f5;
        color: #6c757d;
        font-size: 20px;
    }

    .attendance-remarks {
        min-width: 200px;
    }

</style>

@endpush


@push('scripts')

<script>

    const ATTENDANCE_STUDENTS_LIST_URL =
        "{{ route('admin.attendance.students') }}";

    const ATTENDANCE_SAVE_URL =
        "{{ route('admin.attendance.save') }}";

</script>

<script src="{{ asset('assets/admin/js/attendance.js') }}"></script>

@endpush