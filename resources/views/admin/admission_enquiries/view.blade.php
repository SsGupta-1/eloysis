@extends('layouts.admin.master')

@section('title', 'Admission Enquiry')

@section('content')

<div class="container-fluid">

    <x-ui.page-header
        title="Admission Enquiry"
        subtitle="Manage enquiry details and follow-up">
    </x-ui.page-header>


    <div class="row g-4">

        {{-- Left --}}
        <div class="col-lg-8">

            <x-ui.card class="mb-4">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <h5 class="mb-1">
                            {{ $enquiry->student_name }}
                        </h5>

                        <div class="text-muted">

                            {{ $enquiry->application_no }}

                        </div>

                    </div>


                    <span class="badge bg-primary">

                        {{ ucwords(str_replace('_', ' ', $enquiry->status)) }}

                    </span>

                </div>

            </x-ui.card>


            {{-- Student --}}
            <x-ui.card class="mb-4">

                <h5 class="mb-3">
                    Student Information
                </h5>

                <div class="row g-3">

                    <div class="col-md-6">

                        <strong>Name</strong>

                        <div>
                            {{ $enquiry->student_name }}
                        </div>

                    </div>


                    <div class="col-md-6">

                        <strong>Email</strong>

                        <div>
                            {{ $enquiry->student_email ?? '-' }}
                        </div>

                    </div>


                    <div class="col-md-6">

                        <strong>Phone</strong>

                        <div>
                            {{ $enquiry->student_phone }}
                        </div>

                    </div>


                    <div class="col-md-6">

                        <strong>Class</strong>

                        <div>
                            {{ $enquiry->studentClass?->class_name ?? '-' }}
                        </div>

                    </div>


                    <div class="col-md-6">

                        <strong>Date of Birth</strong>

                        <div>
                            {{ $enquiry->date_of_birth?->format('d-m-Y') ?? '-' }}
                        </div>

                    </div>


                    <div class="col-md-6">

                        <strong>Gender</strong>

                        <div>
                            {{ ucfirst($enquiry->gender ?? '-') }}
                        </div>

                    </div>

                </div>

            </x-ui.card>


            {{-- Parent --}}
            <x-ui.card class="mb-4">

                <h5 class="mb-3">
                    Parent / Guardian
                </h5>

                <div class="row g-3">

                    <div class="col-md-6">

                        <strong>Name</strong>

                        <div>
                            {{ $enquiry->parent_name }}
                        </div>

                    </div>


                    <div class="col-md-6">

                        <strong>Phone</strong>

                        <div>
                            {{ $enquiry->parent_phone }}
                        </div>

                    </div>

                </div>

            </x-ui.card>


            {{-- Enquiry --}}
            <x-ui.card>

                <h5 class="mb-3">
                    Enquiry Information
                </h5>

                <div class="row g-3">

                    <div class="col-md-6">

                        <strong>Source</strong>

                        <div>
                            {{ ucwords(str_replace('_', ' ', $enquiry->source)) }}
                        </div>

                    </div>


                    <div class="col-md-6">

                        <strong>Reference</strong>

                        <div>
                            {{ $enquiry->reference_name ?? '-' }}
                        </div>

                    </div>


                    <div class="col-12">

                        <strong>Message</strong>

                        <div class="mt-1">

                            {{ $enquiry->message ?? '-' }}

                        </div>

                    </div>

                </div>

            </x-ui.card>

        </div>


        {{-- Right --}}
        <div class="col-lg-4">

            {{-- CRM --}}
            <x-ui.card>

                <h5 class="mb-3">
                    CRM Management
                </h5>


                <form
                    id="admissionEnquiryForm"
                    action="{{ route('admission.enquiries.update', $enquiry->id) }}"
                    method="POST"
                >

                    @csrf

                    @method('PUT')


                    {{-- Status --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            id="status"
                            class="form-select"
                        >

                            @foreach($statuses as $value => $label)

                                <option
                                    value="{{ $value }}"
                                    @selected($enquiry->status === $value)
                                >
                                    {{ $label }}
                                </option>

                            @endforeach

                        </select>

                        <div
                            class="invalid-feedback"
                            data-error="status"
                        ></div>

                    </div>


                    {{-- Assign --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Assigned To
                        </label>

                        <select
                            name="assigned_to"
                            id="assigned_to"
                            class="form-select"
                        >

                            <option value="">
                                Unassigned
                            </option>

                            @foreach($users as $id => $name)

                                <option
                                    value="{{ $id }}"
                                    @selected($enquiry->assigned_to == $id)
                                >
                                    {{ $name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Attempt --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Call / Attempt Result
                        </label>

                        <select
                            name="attempt_status"
                            id="attempt_status"
                            class="form-select"
                        >

                            <option value="">
                                Select Result
                            </option>

                            @foreach($attemptStatuses as $value => $label)

                                <option value="{{ $value }}">
                                    {{ $label }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Attempt Remarks --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Attempt Remarks
                        </label>

                        <textarea
                            name="attempt_remarks"
                            class="form-control"
                            rows="3"
                            placeholder="What happened during the call?"
                        ></textarea>

                    </div>


                    {{-- Follow Up --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Next Follow-up
                        </label>

                        <input
                            type="datetime-local"
                            name="next_followup_at"
                            class="form-control"
                        >

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                        id="btnSaveEnquiry"
                    >

                        <span class="btn-text">
                            Save Changes
                        </span>

                        <span
                            class="spinner-border spinner-border-sm d-none"
                        ></span>

                    </button>

                </form>

            </x-ui.card>


            {{-- Stats --}}
            <x-ui.card class="mt-4">

                <h6 class="mb-3">
                    Enquiry Summary
                </h6>

                <div class="d-flex justify-content-between mb-2">

                    <span>
                        Attempts
                    </span>

                    <strong>
                        {{ $enquiry->attempt_count ?? 0 }}
                    </strong>

                </div>


                <div class="d-flex justify-content-between mb-2">

                    <span>
                        Last Attempt
                    </span>

                    <strong>

                        {{ $enquiry->last_attempt_at?->format('d M Y h:i A') ?? '-' }}

                    </strong>

                </div>


                <div class="d-flex justify-content-between">

                    <span>
                        Assigned
                    </span>

                    <strong>
                        {{ $enquiry->assignedUser?->name ?? 'Unassigned' }}
                    </strong>

                </div>

            </x-ui.card>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

    const ADMISSION_ENQUIRY_UPDATE_URL =
        "{{ route('admission.enquiries.update', $enquiry->id) }}";

</script>

<script src="{{ asset('assets/admin/js/admission-enquiry-show.js') }}"></script>

@endpush