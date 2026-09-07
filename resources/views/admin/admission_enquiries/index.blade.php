@extends('layouts.admin.master')

@section('title', 'Admission Enquiries')

@section('content')

<div class="container-fluid">

    <x-ui.page-header
        title="Admission Enquiries"
        subtitle="Manage admission enquiries and follow-ups">
    </x-ui.page-header>


    {{-- Filters --}}
    <x-ui.card class="mb-4">

        <div class="row g-3">

            <div class="col-md-3">

                <x-ui.select
                    name="academic_session_id"
                    id="academic_session_id"
                    :options="$academicSessions"
                    placeholder="All Sessions">

                    Academic Session

                </x-ui.select>

            </div>


            <div class="col-md-3">

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

        </div>

    </x-ui.card>


    {{-- Table --}}
    <x-ui.card>

        <div class="table-responsive">

            <table
                class="table table-hover align-middle mb-0"
                id="admissionEnquiryTable"
            >

                <thead>

                    <tr>

                        <th width="50">#</th>

                        <th>Application No</th>

                        <th>Student</th>
                        <th>Student Contact</th>

                        <th>Parents Contact</th>

                        <th>Class</th>
                        <th>Session</th>

                        <th>Source</th>

                        <th>Status</th>

                        <th>Assigned To</th>

                        <th>Attempts</th>

                        <th>Follow-up</th>

                        <th width="100">Action</th>

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

    const ADMISSION_ENQUIRY_LIST_URL =
        "{{ route('admin.admission_enquiry.list') }}";

    const ADMISSION_ENQUIRY_SHOW_URL = "{{ route('admin.admission-enquiry.show', ':id') }}";

</script>

<script src="{{ asset('assets/admin/js/admissionEnquiries.js') }}"></script>

@endpush