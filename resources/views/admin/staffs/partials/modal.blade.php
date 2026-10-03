<x-ui.modal id="staffModal" title="Add Staff" size="lg">

    <form id="staffForm" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="staff_id" id="staff_id">

        <div class="row g-3">

            {{-- Profile Image --}}
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

            <div class="col-12">
                <h6 class="border-bottom pb-2">
                    Basic Details
                </h6>
            </div>

            {{-- Staff Name --}}
            <div class="col-md-6">
                <x-ui.form-input
                    label="Staff Name"
                    name="name"
                    id="name"
                    required />
            </div>

            {{-- Email --}}
            <div class="col-md-6">
                <x-ui.form-input
                    label="Email"
                    type="email"
                    name="email"
                    id="email"
                    required />
            </div>

            {{-- Mobile --}}
            <div class="col-md-6">
                <x-ui.form-input
                    label="Mobile"
                    name="mobile"
                    id="mobile" />
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
                        'other' => 'Other',
                    ]" />
            </div>

            {{-- Date of Birth --}}
            <div class="col-md-6">
                <x-ui.form-input
                    label="Date of Birth"
                    type="date"
                    name="dob"
                    id="dob" />
            </div>

            {{-- Status --}}
            <div class="col-md-6">
                <x-ui.select
                    label="Status"
                    name="status"
                    id="status"
                    :options="[
                        1 => 'Active',
                        0 => 'Inactive',
                    ]"
                    value="1"
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

            <div class="col-12 mt-3">
                <h6 class="border-bottom pb-2">
                    Professional Details
                </h6>
            </div>

            {{-- Employee ID --}}
            <div class="col-md-6">
                <x-ui.form-input
                    label="Employee ID"
                    name="employee_id"
                    id="employee_id"
                    placeholder="Enter employee ID" />
            </div>

            {{-- Designation --}}
            <div class="col-md-6">
                <x-ui.form-input
                    label="Designation"
                    name="designation"
                    id="designation"
                    placeholder="Enter designation" />
            </div>

            {{-- Department --}}
            <div class="col-md-6">
                <x-ui.form-input
                    label="Department"
                    name="department"
                    id="department"
                    placeholder="Enter department" />
            </div>

            {{-- Joining Date --}}
            <div class="col-md-6">
                <x-ui.form-input
                    type="date"
                    label="Joining Date"
                    name="joining_date"
                    id="joining_date" />
            </div>

            {{-- Address --}}
            <div class="col-12 mt-3">
                <h6 class="border-bottom pb-2">
                    Address Details
                </h6>
            </div>

            <div class="col-md-12">
                <x-ui.textarea
                    label="Address"
                    name="address"
                    id="address"
                    rows="3" />
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
            form="staffForm"
            id="btnSaveStaff">
            Save Staff
        </x-ui.button>
    </x-slot:footer>

</x-ui.modal>