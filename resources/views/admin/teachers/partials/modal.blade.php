<x-ui.modal id="teacherModal" title="Add Teacher" size="lg">

    <form id="teacherForm" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="teacher_id" id="teacher_id">

        <div class="row g-3">

            <div class="col-md-4">
                <x-ui.form-file
                    label="Profile Image"
                    name="profile_image"
                    id="profile_image"
                    accept="image/*"
                    help="Maximum file size: 2 MB"
                />
            </div>

            <div class="col-md-4">
                <div class="text-center">
                    <img
                        id="profilePreview"
                        src="{{ asset('assets/uploads/profile/default-avatar.jpg') }}"
                        alt="Profile Preview"
                        class="rounded-circle img-thumbnail"
                        width="120"
                        height="120"
                        style="object-fit: cover;">
                </div>
            </div>

            {{-- Basic Details --}}
            <div class="col-12">
                <h6 class="border-bottom pb-2">
                    Basic Details
                </h6>
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    label="Teacher Name"
                    name="name"
                    id="name"
                    required />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    label="Employee ID"
                    name="employee_id"
                    id="employee_id"
                    required />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    label="Email"
                    name="email"
                    id="email"
                    type="email"
                    required />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    label="Mobile"
                    name="mobile"
                    id="mobile" />
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

            {{-- Professional Details --}}
            <div class="col-12 mt-3">
                <h6 class="border-bottom pb-2">
                    Professional Details
                </h6>
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    label="Qualification"
                    name="qualification"
                    id="qualification" />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    label="Specialization"
                    name="specialization"
                    id="specialization" />
            </div>

            <div class="col-md-4">
                <x-ui.form-input
                    label="Joining Date"
                    name="joining_date"
                    id="joining_date"
                    type="date" />
            </div>

            <div class="col-md-4">
                <x-ui.form-input
                    label="Date of Birth"
                    name="dob"
                    id="dob"
                    type="date" />
            </div>

            <div class="col-md-4">
                <x-ui.select
                    label="Gender"
                    name="gender"
                    id="gender"
                    :options="[
                        'male' => 'Male',
                        'female' => 'Female',
                        'other' => 'Other'
                    ]"
                    placeholder="Select Gender">
                </x-ui.select>
            </div>

            <div class="col-md-4">
                <x-ui.form-input
                    label="Experience (Years)"
                    name="experience_years"
                    id="experience_years"
                    type="number"
                    min="0" />
            </div>

            {{-- Address --}}
            <div class="col-12 mt-3">
                <h6 class="border-bottom pb-2">
                    Address Details
                </h6>
            </div>

            <div class="col-md-12">
                <x-ui.form-input
                    label="Address"
                    name="address"
                    id="address" />
            </div>

            <div class="col-md-4">
                <x-ui.form-input
                    label="City"
                    name="city"
                    id="city" />
            </div>

            <div class="col-md-4">
                <x-ui.form-input
                    label="State"
                    name="state"
                    id="state" />
            </div>

            <div class="col-md-4">
                <x-ui.form-input
                    label="Pincode"
                    name="pincode"
                    id="pincode" />
            </div>

            {{-- Emergency Contact --}}
            <div class="col-12 mt-3">
                <h6 class="border-bottom pb-2">
                    Emergency Contact
                </h6>
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    label="Contact Name"
                    name="emergency_contact_name"
                    id="emergency_contact_name" />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    label="Contact Mobile"
                    name="emergency_contact_mobile"
                    id="emergency_contact_mobile" />
            </div>

            {{-- Status --}}
            <div class="col-md-4">
                <x-ui.select
                    label="Status"
                    name="status"
                    id="status"
                    required
                    :options="[
                        1 => 'Active',
                        0 => 'Inactive'
                    ]"
                    placeholder="Select Status">
                </x-ui.select>
            </div>

        </div>

    </form>

    <x-slot:footer>
        <x-ui.button
            variant="secondary"
            data-bs-dismiss="modal">
            Close
        </x-ui.button>

        <x-ui.button
            type="submit"
            form="teacherForm"
            id="btnSaveTeacher">
            Save Teacher
        </x-ui.button>
    </x-slot:footer>

</x-ui.modal>