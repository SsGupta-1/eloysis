@extends('layouts.admin.master')

@section('title', 'Convert Enquiry to Admission')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <x-ui.page-header
        title="Convert Enquiry to Admission"
        subtitle="Review pre-filled details from Enquiry #{{ $enquiry->application_no ?? $enquiry->id }} and create student profile">
        <x-slot:actions>
            <a href="{{ route('admin.admission-enquiry.show', $enquiry->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Enquiry
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Duplicate Detection Warning Banner --}}
    @if(!empty($duplicates) && count($duplicates) > 0)
        <div class="alert alert-warning border-warning mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-start">
                <i class="bi bi-exclamation-triangle-fill fs-4 text-warning me-3 mt-1"></i>
                <div class="w-100">
                    <h6 class="alert-heading mb-1 fw-bold text-dark">Possible Duplicate Student / User Detected</h6>
                    <p class="mb-2 text-muted small">Please verify that this student has not already been admitted to avoid duplicate accounts:</p>
                    <ul class="mb-0 ps-3 small text-dark">
                        @foreach($duplicates as $dup)
                            <li><strong>{{ $dup['field'] }}:</strong> {{ $dup['details'] }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Conversion Notice --}}
    <div class="alert alert-info border-0 shadow-sm mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle-fill fs-4 text-info me-3"></i>
            <div>
                <strong>Reusing Admission Architecture:</strong> Submitting this form will atomically create the student's <strong>User Login</strong>, <strong>Student Profile</strong>, and <strong>Enrollment</strong>, while linking this enquiry and setting its status to <strong>Converted</strong>.
            </div>
        </div>
    </div>

    <form
        id="convertAdmissionForm"
        method="POST"
        action="{{ route('admin.admission-enquiry.convert-store', $enquiry->id) }}"
        data-suggested-roll-url="{{ route('admin.students.suggested-roll-number') }}"
        enctype="multipart/form-data">
        @csrf

        {{-- Basic Information Card --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-person me-2"></i> Basic Login & User Details
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- Profile Image --}}
                    <div class="col-md-6">
                        <x-ui.form-file
                            label="Profile Image"
                            name="profile_image"
                            id="profile_image"
                            accept="image/*"
                            help="Maximum file size: 2 MB"
                        />
                    </div>

                    <div class="col-md-6">
                        <div class="text-center">
                            <img
                                id="profilePreview"
                                src="{{ asset('assets/uploads/profile/default-avatar.jpg') }}"
                                alt="Profile Preview"
                                class="rounded-circle img-thumbnail"
                                width="100"
                                height="100"
                                style="object-fit: cover;">
                        </div>
                    </div>

                    {{-- Name --}}
                    <div class="col-md-6">
                        <x-ui.form-input
                            label="Student Name"
                            name="name"
                            id="name"
                            :value="old('name', $enquiry->student_name)"
                            required />
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <x-ui.form-input
                            label="Email (Login Username)"
                            type="email"
                            name="email"
                            id="email"
                            :value="old('email', $enquiry->student_email)"
                            required />
                    </div>

                    {{-- Mobile --}}
                    <div class="col-md-6">
                        <x-ui.form-input
                            label="Mobile Number"
                            name="mobile"
                            id="mobile"
                            :value="old('mobile', $enquiry->student_phone)" />
                    </div>

                    {{-- Status --}}
                    <div class="col-md-6">
                        <x-ui.select
                            label="Account Status"
                            name="status"
                            id="status"
                            :options="[
                                1 => 'Active',
                                0 => 'Inactive'
                            ]"
                            :value="old('status', 1)"
                            required />
                    </div>

                    {{-- Password --}}
                    <div class="col-md-6">
                        <x-ui.form-input
                            label="Password"
                            name="password"
                            id="password"
                            type="password"
                            value="12345678"
                            help="Default temporary password is set to: 12345678"
                            required />
                    </div>

                    <div class="col-md-6">
                        <x-ui.form-input
                            label="Confirm Password"
                            name="password_confirmation"
                            id="password_confirmation"
                            type="password"
                            value="12345678"
                            required />
                    </div>
                </div>
            </div>
        </div>

        {{-- Academic Information Card --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-mortarboard me-2"></i> Academic & Enrollment Details
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
                            :value="old('academic_session_id', $enquiry->academic_session_id)"
                            :options="$academicSessions"
                            required />
                    </div>

                    {{-- Class --}}
                    <div class="col-md-4">
                        <x-ui.select
                            label="Class"
                            name="class_id"
                            id="form_class_id"
                            :value="old('class_id', $enquiry->class_id)"
                            :options="$classes"
                            required />
                    </div>

                    {{-- Section --}}
                    <div class="col-md-4">
                        <x-ui.select
                            label="Section"
                            name="section_id"
                            id="form_section_id"
                            :value="old('section_id')"
                            :options="$sections"
                            required />
                    </div>

                    {{-- Admission Number --}}
                    <div class="col-md-4">
                        <x-ui.form-input
                            label="Admission Number"
                            name="admission_no"
                            id="admission_no"
                            :value="old('admission_no', $suggestedAdmissionNo)"
                            required />
                    </div>

                    {{-- Roll Number --}}
                    <div class="col-md-4">
                        <x-ui.form-input
                            label="Roll Number"
                            name="roll_number"
                            id="roll_number"
                            :value="old('roll_number', $suggestedRollNo ?? '')" />
                    </div>

                    {{-- Admission Date --}}
                    <div class="col-md-4">
                        <x-ui.form-input
                            label="Admission Date"
                            type="date"
                            name="admission_date"
                            id="admission_date"
                            :value="old('admission_date', date('Y-m-d'))" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Personal Information Card --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-person-lines-fill me-2"></i> Personal Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- Date of Birth --}}
                    <div class="col-md-4">
                        <x-ui.form-input
                            label="Date of Birth"
                            type="date"
                            name="dob"
                            id="dob"
                            :value="old('dob', $enquiry->date_of_birth ? \Carbon\Carbon::parse($enquiry->date_of_birth)->format('Y-m-d') : '')" />
                    </div>

                    {{-- Gender --}}
                    <div class="col-md-4">
                        <x-ui.select
                            label="Gender"
                            name="gender"
                            id="gender"
                            :value="old('gender', $enquiry->gender)"
                            :options="[
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other'
                            ]" />
                    </div>

                    {{-- Blood Group --}}
                    <div class="col-md-4">
                        <x-ui.select
                            label="Blood Group"
                            name="blood_group"
                            id="blood_group"
                            :value="old('blood_group')"
                            :options="[
                                'A+' => 'A+',
                                'A-' => 'A-',
                                'B+' => 'B+',
                                'B-' => 'B-',
                                'AB+' => 'AB+',
                                'AB-' => 'AB-',
                                'O+' => 'O+',
                                'O-' => 'O-'
                            ]"
                            placeholder="Select Blood Group" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Family / Guardian Information Card --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-people me-2"></i> Parent / Guardian Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- Father Name --}}
                    <div class="col-md-6">
                        <x-ui.form-input
                            label="Father Name"
                            name="father_name"
                            id="father_name"
                            :value="old('father_name', $enquiry->parent_name)" />
                    </div>

                    {{-- Mother Name --}}
                    <div class="col-md-6">
                        <x-ui.form-input
                            label="Mother Name"
                            name="mother_name"
                            id="mother_name"
                            :value="old('mother_name')" />
                    </div>

                    {{-- Guardian Name --}}
                    <div class="col-md-4">
                        <x-ui.form-input
                            label="Guardian Name"
                            name="guardian_name"
                            id="guardian_name"
                            :value="old('guardian_name', $enquiry->parent_name)" />
                    </div>

                    {{-- Guardian Mobile --}}
                    <div class="col-md-4">
                        <x-ui.form-input
                            label="Guardian Mobile"
                            name="guardian_mobile"
                            id="guardian_mobile"
                            :value="old('guardian_mobile', $enquiry->parent_phone)" />
                    </div>

                    {{-- Guardian Email --}}
                    <div class="col-md-4">
                        <x-ui.form-input
                            label="Guardian Email"
                            type="email"
                            name="guardian_email"
                            id="guardian_email"
                            :value="old('guardian_email')" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Address Information Card --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-geo-alt me-2"></i> Address Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- Address --}}
                    <div class="col-md-12">
                        <x-ui.textarea
                            label="Address"
                            name="address"
                            id="address"
                            rows="2"
                            :value="old('address')" />
                    </div>

                    {{-- City --}}
                    <div class="col-md-4">
                        <x-ui.form-input
                            label="City"
                            name="city"
                            id="city"
                            :value="old('city')" />
                    </div>

                    {{-- State --}}
                    <div class="col-md-4">
                        <x-ui.form-input
                            label="State"
                            name="state"
                            id="state"
                            :value="old('state')" />
                    </div>

                    {{-- Pincode --}}
                    <div class="col-md-4">
                        <x-ui.form-input
                            label="Pincode"
                            name="pincode"
                            id="pincode"
                            :value="old('pincode')" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('admin.admission-enquiry.show', $enquiry->id) }}" class="btn btn-secondary">
                Cancel
            </a>

            <button type="submit" class="btn btn-success px-4" id="btnSubmitConversion">
                <span class="btn-text">
                    <i class="bi bi-check2-circle me-1"></i> Confirm & Complete Admission
                </span>
                <span class="spinner-border spinner-border-sm d-none"></span>
            </button>
        </div>

    </form>

</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/admin/js/admission-convert.js') }}"></script>
@endpush
