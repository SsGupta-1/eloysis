@extends('layouts.admin.master')

@section('title', 'Admission Enquiries')

@section('content')

<div class="container-fluid">

    <x-ui.page-header
        title="Admission Enquiries"
        subtitle="Manage admission enquiries, staff assignments, follow-ups, and student conversions">
        <x-slot:actions>
            <a href="{{ route('admin.admission-enquiry.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Enquiry
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Filters --}}
    <x-ui.card class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-2">
                <x-ui.select
                    name="academic_session_id"
                    id="academic_session_id"
                    :options="$academicSessions"
                    placeholder="All Sessions">
                    Academic Session
                </x-ui.select>
            </div>

            <div class="col-md-2">
                <x-ui.select
                    name="class_id"
                    id="class_id"
                    :options="$classes"
                    placeholder="All Classes">
                    Class
                </x-ui.select>
            </div>

            <div class="col-md-2">
                <x-ui.select
                    name="status"
                    id="status"
                    :options="$statuses"
                    placeholder="All Status">
                    Status
                </x-ui.select>
            </div>

            <div class="col-md-2">
                <x-ui.select
                    name="source"
                    id="source"
                    :options="$sources"
                    placeholder="All Sources">
                    Source
                </x-ui.select>
            </div>

            <div class="col-md-2">
                <x-ui.select
                    name="assigned_to"
                    id="assigned_to"
                    :options="$users"
                    placeholder="All Staff">
                    Assigned To
                </x-ui.select>
            </div>

            <div class="col-md-2">
                <button type="button" class="btn btn-outline-secondary w-100" id="btnResetFilters">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
                </button>
            </div>
        </div>
    </x-ui.card>

    {{-- Table --}}
    <x-ui.card>
        <div class="table-responsive">
            <table
                class="table table-hover align-middle mb-0"
                id="admissionEnquiryTable"
                style="width: 100%"
            >
                <thead class="table-light">
                    <tr>
                        <th width="40">#</th>
                        <th>Application No</th>
                        <th>Student Name</th>
                        <th>Student Contact</th>
                        <th>Parent Details</th>
                        <th>Class / Session</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Assigned Staff</th>
                        <th class="text-center">Attempts</th>
                        <th>Next Follow-up</th>
                        <th width="120" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </x-ui.card>

</div>

@endsection

@push('scripts')
<script>
    const ADMISSION_ENQUIRY_LIST_URL = "{{ route('admin.admission_enquiry.list') }}";
    const ADMISSION_ENQUIRY_SHOW_URL = "{{ route('admin.admission-enquiry.show', ':id') }}";
    const ADMISSION_ENQUIRY_CONVERT_URL = "{{ route('admin.admission-enquiry.convert', ':id') }}";
    const STUDENT_SHOW_URL = "{{ route('admin.students.show', ':id') }}";
</script>
<script src="{{ asset('assets/admin/js/admissionEnquiries.js') }}"></script>
@endpush