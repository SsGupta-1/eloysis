@extends('layouts.admin.master')

@section('title', 'Add Admission Enquiry')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <x-ui.page-header
        title="Add Admission Enquiry"
        subtitle="Record a new student admission enquiry directly in the admin panel">
        <x-slot:actions>
            <a href="{{ route('admin.admission-enquiry.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Enquiries
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <form
        id="createEnquiryForm"
        method="POST"
        action="{{ route('admin.admission-enquiry.store') }}">
        @csrf

        {{-- Student Information --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-person me-2"></i> Student Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- Student Name --}}
                    <div class="col-md-6">
                        <x-ui.form-input
                            label="Student Full Name"
                            name="student_name"
                            id="student_name"
                            :value="old('student_name')"
                            placeholder="Enter student's full name"
                            required />
                    </div>

                    {{-- Student Email --}}
                    <div class="col-md-6">
                        <x-ui.form-input
                            label="Student Email Address"
                            type="email"
                            name="student_email"
                            id="student_email"
                            :value="old('student_email')"
                            placeholder="example@student.com"
                            required />
                    </div>

                    {{-- Student Phone --}}
                    <div class="col-md-6">
                        <x-ui.form-input
                            label="Student Mobile Number"
                            name="student_phone"
                            id="student_phone"
                            :value="old('student_phone')"
                            placeholder="10-digit mobile number"
                            required />
                    </div>

                    {{-- Alternate Phone --}}
                    <div class="col-md-6">
                        <x-ui.form-input
                            label="Alternate Phone (Optional)"
                            name="alternate_phone"
                            id="alternate_phone"
                            :value="old('alternate_phone')"
                            placeholder="Secondary contact number" />
                    </div>

                    {{-- Date of Birth --}}
                    <div class="col-md-6">
                        <x-ui.form-input
                            label="Date of Birth"
                            type="date"
                            name="date_of_birth"
                            id="date_of_birth"
                            :value="old('date_of_birth')" />
                    </div>

                    {{-- Gender --}}
                    <div class="col-md-6">
                        <x-ui.select
                            label="Gender"
                            name="gender"
                            id="gender"
                            :options="[
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other'
                            ]"
                            placeholder="Select Gender"
                            :value="old('gender')" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Parent / Guardian Information --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-people me-2"></i> Parent / Guardian Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- Parent Name --}}
                    <div class="col-md-6">
                        <x-ui.form-input
                            label="Parent / Guardian Name"
                            name="parent_name"
                            id="parent_name"
                            :value="old('parent_name')"
                            placeholder="Enter parent/guardian full name"
                            required />
                    </div>

                    {{-- Parent Phone --}}
                    <div class="col-md-6">
                        <x-ui.form-input
                            label="Parent / Guardian Mobile"
                            name="parent_phone"
                            id="parent_phone"
                            :value="old('parent_phone')"
                            placeholder="10-digit mobile number"
                            required />
                    </div>

                    {{-- Reference Type --}}
                    <div class="col-md-4">
                        <x-ui.select
                            label="Reference Type (Optional)"
                            name="reference_type"
                            id="reference_type"
                            :options="[
                                'student' => 'Existing Student',
                                'parent' => 'Parent',
                                'teacher' => 'Teacher',
                                'staff' => 'Staff Member',
                                'other' => 'Other'
                            ]"
                            placeholder="Select Reference Type"
                            :value="old('reference_type')" />
                    </div>

                    {{-- Reference Name --}}
                    <div class="col-md-4">
                        <x-ui.form-input
                            label="Reference Person Name"
                            name="reference_name"
                            id="reference_name"
                            :value="old('reference_name')"
                            placeholder="Name of referrer" />
                    </div>

                    {{-- Reference Phone --}}
                    <div class="col-md-4">
                        <x-ui.form-input
                            label="Reference Phone Number"
                            name="reference_phone"
                            id="reference_phone"
                            :value="old('reference_phone')"
                            placeholder="Referrer phone number" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Academic & Enquiry Routing Details --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-mortarboard me-2"></i> Academic & Enquiry Routing
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- Academic Session --}}
                    <div class="col-md-4">
                        <x-ui.select
                            label="Academic Session"
                            name="academic_session_id"
                            id="academic_session_id"
                            :options="$academicSessions"
                            :value="old('academic_session_id', array_key_first($academicSessions))"
                            required />
                    </div>

                    {{-- Class --}}
                    <div class="col-md-4">
                        <x-ui.select
                            label="Interested Class"
                            name="class_id"
                            id="class_id"
                            :options="$classes"
                            placeholder="Select Class"
                            :value="old('class_id')"
                            required />
                    </div>

                    {{-- Source --}}
                    <div class="col-md-4">
                        <x-ui.select
                            label="Enquiry Source"
                            name="source"
                            id="source"
                            :options="$sources"
                            :value="old('source', 'walk_in')"
                            required />
                    </div>

                    {{-- Assigned To Staff --}}
                    <div class="col-md-6">
                        <x-ui.select
                            label="Assign to Staff / Counsellor"
                            name="assigned_to"
                            id="assigned_to"
                            :options="$users"
                            placeholder="-- Leave Unassigned --"
                            :value="old('assigned_to')" />
                    </div>

                    {{-- Next Follow-up Date/Time --}}
                    <div class="col-md-6">
                        <label class="form-label">Next Scheduled Follow-up (Optional)</label>
                        <input
                            type="datetime-local"
                            name="next_follow_up_at"
                            id="next_follow_up_at"
                            class="form-control"
                            value="{{ old('next_follow_up_at') }}" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Message & Internal Remarks --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-chat-left-text me-2"></i> Message & Internal Remarks
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- Message --}}
                    <div class="col-md-6">
                        <x-ui.textarea
                            label="Student / Parent Message"
                            name="message"
                            id="message"
                            rows="3"
                            placeholder="Specific queries or comments from student/parent..."
                            :value="old('message')" />
                    </div>

                    {{-- Internal Remarks --}}
                    <div class="col-md-6">
                        <x-ui.textarea
                            label="Internal Staff Remarks / Notes"
                            name="remarks"
                            id="remarks"
                            rows="3"
                            placeholder="Initial observations or internal notes..."
                            :value="old('remarks')" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('admin.admission-enquiry.index') }}" class="btn btn-secondary">
                Cancel
            </a>

            <button type="submit" class="btn btn-primary px-4" id="btnSaveEnquiry">
                <span class="btn-text">
                    <i class="bi bi-check2-circle me-1"></i> Save Admission Enquiry
                </span>
                <span class="spinner-border spinner-border-sm d-none"></span>
            </button>
        </div>

    </form>

</div>

@endsection

@push('scripts')
<script>
$(function () {
    $('#createEnquiryForm').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const submitBtn = $('#btnSaveEnquiry');
        const spinner = submitBtn.find('.spinner-border');
        const btnText = submitBtn.find('.btn-text');

        // Clear previous validation errors
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');

        const formData = new FormData(form[0]);

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
            },
            success: function (response) {
                if (typeof Toast !== 'undefined') {
                    Toast.success(response.message || 'Admission enquiry recorded successfully.');
                } else {
                    alert(response.message || 'Admission enquiry recorded successfully.');
                }

                setTimeout(function () {
                    window.location.href = response.redirect_url || "{{ route('admin.admission-enquiry.index') }}";
                }, 600);
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON?.errors ?? {};
                    $.each(errors, function (field, messages) {
                        const input = form.find(`[name="${field}"]`);
                        input.addClass('is-invalid');
                        input.after(`<div class="invalid-feedback d-block">${messages[0]}</div>`);
                    });

                    const firstError = form.find('.is-invalid').first();
                    if (firstError.length) {
                        $('html, body').animate({
                            scrollTop: firstError.offset().top - 120
                        }, 300);
                    }
                } else {
                    const errorMsg = xhr.responseJSON?.message || 'Failed to save enquiry. Please check your input.';
                    if (typeof Toast !== 'undefined') {
                        Toast.error(errorMsg);
                    } else {
                        alert(errorMsg);
                    }
                }
            }
        });
    });
});
</script>
@endpush
