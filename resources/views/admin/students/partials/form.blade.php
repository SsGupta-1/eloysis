{{-- ============================================================
    BASIC INFORMATION
============================================================ --}}

<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-white">

        <h5 class="mb-0">

            <i class="bi bi-person me-2"></i>

            Basic Information

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
                        src="{{$enrollment->student->user->profile_image_url ?? asset('assets/uploads/profile/default-avatar.jpg') }}"
                        alt="Profile Preview"
                        class="rounded-circle img-thumbnail"
                        width="120"
                        height="120"
                        style="object-fit: cover;">
                </div>

            </div>


            {{-- Name --}}
            <div class="col-md-6">

                <x-ui.form-input
                    label="Student Name"
                    name="name"
                    id="name"
                    :value="$enrollment->student->user->name ?? old('name')"
                    required />

            </div>


            {{-- Email --}}
            <div class="col-md-6">

                <x-ui.form-input
                    label="Email"
                    type="email"
                    name="email"
                    id="email"
                    :value="$enrollment->student->user->email ?? old('email')"
                    required />

            </div>


            {{-- Mobile --}}
            <div class="col-md-6">

                <x-ui.form-input
                    label="Mobile"
                    name="mobile"
                    id="mobile" 
                    :value="$enrollment->student->user->mobile ?? old('mobile')"/>

            </div>


            {{-- Status --}}
            <div class="col-md-6">

                <x-ui.select
                    label="Status"
                    name="status"
                    id="status"
                    :options="[
                        1 => 'Active',
                        0 => 'Inactive'
                    ]"
                    :value="$enrollment->student->user->status ?? old('status')"
                    required />

            </div>

            {{-- Password --}}

            <div class="col-md-6">

                <x-ui.form-input
                    label="Password"
                    name="password"
                    id="password"
                    type="password" />

            </div>


            <div class="col-md-6">

                <x-ui.form-input
                    label="Confirm Password"
                    name="password_confirmation"
                    id="password_confirmation"
                    type="password" />

            </div>

        </div>

    </div>

</div>


{{-- ============================================================
    ACADEMIC INFORMATION
============================================================ --}}

<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-white">

        <h5 class="mb-0">

            <i class="bi bi-mortarboard me-2"></i>

            Academic Information

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
                    :value="$enrollment->academic_session_id ?? old('academic_session_id')"
                    :options="$academicSessions"
                    required />

            </div>


            {{-- Class --}}
            <div class="col-md-4">

                <x-ui.select
                    label="Class"
                    name="class_id"
                    id="form_class_id"
                    :value="$enrollment->class_id ?? old('class_id')"
                    :options="$classes"
                    data-section-target="#form_section_id"
                    required />

            </div>


            {{-- Section --}}
            <div class="col-md-4">

                <x-ui.select
                    label="Section"
                    name="section_id"
                    id="form_section_id"
                    :value="$enrollment->section_id ?? old('section_id')"
                    :options="$sections"
                    required />

            </div>


            {{-- Admission Number --}}
            <div class="col-md-4">

                <x-ui.form-input
                    label="Admission Number"
                    name="admission_no"
                    id="admission_no"
                    :value="$enrollment->student->admission_no ?? old('admission_no')"
                    required />

            </div>


            {{-- Roll Number --}}
            <div class="col-md-4">

                <x-ui.form-input
                    label="Roll Number"
                    name="roll_number"
                    id="roll_number" 
                    :value="$enrollment->roll_number ?? old('roll_number')"/>

            </div>


            {{-- Admission Date --}}
            <div class="col-md-4">

                <x-ui.form-input
                    label="Admission Date"
                    type="date"
                    name="admission_date"
                    id="admission_date"
                    :value="$enrollment->student->admission_date ?? old('admission_date')" />

            </div>

            {{-- Fee Discount / Concession Category --}}
            <div class="col-md-4">

                <x-ui.select
                    label="Fee Concession Category"
                    name="fee_discount_id"
                    id="fee_discount_id"
                    :value="$enrollment->fee_discount_id ?? old('fee_discount_id')"
                    :options="['' => 'General (No Concession)'] + ($feeDiscounts ?? [])"
                />

            </div>

        </div>

    </div>

</div>


{{-- ============================================================
    PERSONAL INFORMATION
============================================================ --}}

<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-white">

        <h5 class="mb-0">

            <i class="bi bi-person-vcard me-2"></i>

            Personal Information

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
                    :value="isset($enrollment->student) ? \Carbon\Carbon::parse($enrollment->student->dob)->format('Y-m-d') : old('dob')"  />

            </div>


            {{-- Gender --}}
            <div class="col-md-4">

                <x-ui.select
                    label="Gender"
                    name="gender"
                    id="gender"
                    :value="$enrollment->student->gender ?? old('gender')"
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
                    :value="$enrollment->student->blood_group ?? old('blood_group')"
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


{{-- ============================================================
    FAMILY / GUARDIAN INFORMATION
============================================================ --}}

<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-white">

        <h5 class="mb-0">

            <i class="bi bi-people me-2"></i>

            Family / Guardian Information

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
                    :value="$enrollment->student->father_name ?? old('father_name')"/>

            </div>


            {{-- Mother Name --}}
            <div class="col-md-6">

                <x-ui.form-input
                    label="Mother Name"
                    name="mother_name"
                    id="mother_name"
                    :value="$enrollment->student->mother_name ?? old('mother_name')" />

            </div>


            {{-- Guardian Name --}}
            <div class="col-md-4">

                <x-ui.form-input
                    label="Guardian Name"
                    name="guardian_name"
                    id="guardian_name"
                    :value="$enrollment->student->guardian_name ?? old('guardian_name')" />

            </div>


            {{-- Guardian Mobile --}}
            <div class="col-md-4">

                <x-ui.form-input
                    label="Guardian Mobile"
                    name="guardian_mobile"
                    id="guardian_mobile" 
                    :value="$enrollment->student->guardian_mobile ?? old('guardian_mobile')"/>

            </div>


            {{-- Guardian Email --}}
            <div class="col-md-4">

                <x-ui.form-input
                    label="Guardian Email"
                    type="email"
                    name="guardian_email"
                    id="guardian_email"
                    :value="$enrollment->student->guardian_email ?? old('guardian_email')" />

            </div>

        </div>

    </div>

</div>


{{-- ============================================================
    ADDRESS INFORMATION
============================================================ --}}

<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-white">

        <h5 class="mb-0">

            <i class="bi bi-geo-alt me-2"></i>

            Address Information

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
                    rows="3"
                    :value="$enrollment->student->address ?? old('address')" />

            </div>


            {{-- City --}}
            <div class="col-md-4">

                <x-ui.form-input
                    label="City"
                    name="city"
                    id="city" 
                    :value="$enrollment->student->city ?? old('city')"/>

            </div>


            {{-- State --}}
            <div class="col-md-4">

                <x-ui.form-input
                    label="State"
                    name="state"
                    id="state"
                    :value="$enrollment->student->state ?? old('state')" />

            </div>


            {{-- Pincode --}}
            <div class="col-md-4">

                <x-ui.form-input
                    label="Pincode"
                    name="pincode"
                    id="pincode" 
                    :value="$enrollment->student->pincode ?? old('pincode')"/>

            </div>

        </div>

    </div>

</div>