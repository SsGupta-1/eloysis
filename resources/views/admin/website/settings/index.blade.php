@extends('layouts.admin.master')

@section('title', 'Website Settings')

@section('content')

<div class="container-fluid">

    <x-ui.page-header
        title="Website Settings"
        subtitle="Manage institute details, principal message, contact info, and statistics">
    </x-ui.page-header>

    <x-ui.card>

        <ul class="nav nav-tabs mb-4" id="websiteSettingTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#generalTab" type="button" role="tab">
                    <i class="bi bi-info-circle me-1"></i> General & Institute Info
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="principal-tab" data-bs-toggle="tab" data-bs-target="#principalTab" type="button" role="tab">
                    <i class="bi bi-person-badge me-1"></i> Principal's Desk
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contactTab" type="button" role="tab">
                    <i class="bi bi-telephone me-1"></i> Contact & Social Links
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="about-tab" data-bs-toggle="tab" data-bs-target="#aboutTab" type="button" role="tab">
                    <i class="bi bi-building me-1"></i> About & Statistics
                </button>
            </li>
        </ul>

        <form id="websiteSettingsForm" action="{{ route('admin.website-settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="tab-content" id="websiteSettingTabContent">

                {{-- General Tab --}}
                <div class="tab-pane fade show active" id="generalTab" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-ui.form-input
                                name="institute_name"
                                id="institute_name"
                                label="Institute Name"
                                :value="$settings['institute_name'] ?? 'ABC Public School'"
                                required
                            />
                        </div>
                        <div class="col-md-6">
                            <x-ui.form-input
                                name="institute_tagline"
                                id="institute_tagline"
                                label="Tagline"
                                :value="$settings['institute_tagline'] ?? 'Learn Today, Lead Tomorrow'"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-ui.form-file
                                name="institute_logo"
                                id="institute_logo"
                                label="Institute Logo"
                                accept="image/*"
                            />
                            @if(!empty($settings['institute_logo']))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $settings['institute_logo']) }}" alt="Logo" class="img-thumbnail" style="max-height: 60px;">
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <x-ui.form-input
                                name="footer_copyright"
                                id="footer_copyright"
                                label="Footer Copyright Text"
                                :value="$settings['footer_copyright'] ?? 'ABC Public School. All Rights Reserved.'"
                            />
                        </div>
                        <div class="col-12">
                            <x-ui.textarea
                                name="footer_about"
                                id="footer_about"
                                label="Footer About Summary"
                                rows="3"
                                :value="$settings['footer_about'] ?? 'ABC Public School is committed to providing quality education with modern learning methods and overall student development.'"
                            />
                        </div>
                    </div>
                </div>

                {{-- Principal Tab --}}
                <div class="tab-pane fade" id="principalTab" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-ui.form-input
                                name="principal_name"
                                id="principal_name"
                                label="Principal Name"
                                :value="$settings['principal_name'] ?? 'Dr. Rajesh Kumar'"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-ui.form-input
                                name="principal_designation"
                                id="principal_designation"
                                label="Designation"
                                :value="$settings['principal_designation'] ?? 'Principal'"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-ui.form-file
                                name="principal_image"
                                id="principal_image"
                                label="Principal Photo"
                                accept="image/*"
                            />
                            @if(!empty($settings['principal_image']))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $settings['principal_image']) }}" alt="Principal Photo" class="img-thumbnail" style="max-height: 80px;">
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <x-ui.form-file
                                name="principal_signature"
                                id="principal_signature"
                                label="Signature Image"
                                accept="image/*"
                            />
                            @if(!empty($settings['principal_signature']))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $settings['principal_signature']) }}" alt="Signature" class="img-thumbnail" style="max-height: 50px;">
                                </div>
                            @endif
                        </div>
                        <div class="col-12">
                            <x-ui.textarea
                                name="principal_message"
                                id="principal_message"
                                label="Principal's Message"
                                rows="5"
                                :value="$settings['principal_message'] ?? 'Welcome to ABC Public School. Our mission is to provide quality education that inspires students to become responsible citizens and lifelong learners. We focus on academic excellence, discipline, innovation, and overall personality development.'"
                            />
                        </div>
                    </div>
                </div>

                {{-- Contact & Social Tab --}}
                <div class="tab-pane fade" id="contactTab" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-ui.form-input
                                name="contact_phone"
                                id="contact_phone"
                                label="Contact Phone"
                                :value="$settings['contact_phone'] ?? '+91 9876543210'"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-ui.form-input
                                type="email"
                                name="contact_email"
                                id="contact_email"
                                label="Contact Email"
                                :value="$settings['contact_email'] ?? 'info@abcschool.com'"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-ui.form-input
                                name="contact_working_hours"
                                id="contact_working_hours"
                                label="Office Hours"
                                :value="$settings['contact_working_hours'] ?? 'Mon - Sat : 08:00 AM - 04:00 PM'"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-ui.form-input
                                name="contact_map_url"
                                id="contact_map_url"
                                label="Google Maps URL / Embed"
                                :value="$settings['contact_map_url'] ?? 'https://maps.google.com'"
                            />
                        </div>
                        <div class="col-12">
                            <x-ui.textarea
                                name="contact_address"
                                id="contact_address"
                                label="Address"
                                rows="2"
                                :value="$settings['contact_address'] ?? 'ABC Public School, New Delhi, India'"
                            />
                        </div>
                        <div class="col-12"><hr class="my-2"><h6 class="text-muted">Social Media Links</h6></div>
                        <div class="col-md-3">
                            <x-ui.form-input
                                type="url"
                                name="social_facebook"
                                id="social_facebook"
                                label="Facebook"
                                icon="bi-facebook"
                                :value="$settings['social_facebook'] ?? '#'"
                            />
                        </div>
                        <div class="col-md-3">
                            <x-ui.form-input
                                type="url"
                                name="social_instagram"
                                id="social_instagram"
                                label="Instagram"
                                icon="bi-instagram"
                                :value="$settings['social_instagram'] ?? '#'"
                            />
                        </div>
                        <div class="col-md-3">
                            <x-ui.form-input
                                type="url"
                                name="social_youtube"
                                id="social_youtube"
                                label="YouTube"
                                icon="bi-youtube"
                                :value="$settings['social_youtube'] ?? '#'"
                            />
                        </div>
                        <div class="col-md-3">
                            <x-ui.form-input
                                type="url"
                                name="social_linkedin"
                                id="social_linkedin"
                                label="LinkedIn"
                                icon="bi-linkedin"
                                :value="$settings['social_linkedin'] ?? '#'"
                            />
                        </div>
                    </div>
                </div>

                {{-- About & Statistics Tab --}}
                <div class="tab-pane fade" id="aboutTab" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-ui.form-input
                                name="about_title"
                                id="about_title"
                                label="About Section Title"
                                :value="$settings['about_title'] ?? 'About Our Institute'"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-ui.form-input
                                name="about_subtitle"
                                id="about_subtitle"
                                label="About Subtitle"
                                :value="$settings['about_subtitle'] ?? 'Excellence in Education Since 2010'"
                            />
                        </div>
                        <div class="col-12">
                            <x-ui.textarea
                                name="about_description"
                                id="about_description"
                                label="About Description"
                                rows="4"
                                :value="$settings['about_description'] ?? 'ABC Public School is committed to providing quality education with modern teaching methods, experienced faculty, and a technology-driven learning environment. Our goal is to develop students academically, socially, and morally.'"
                            />
                        </div>
                        <div class="col-12"><hr class="my-2"><h6 class="text-muted">Key Statistics (Counters on Homepage)</h6></div>
                        <div class="col-md-3">
                            <x-ui.form-input
                                name="stat_students"
                                id="stat_students"
                                label="Students Count"
                                :value="$settings['stat_students'] ?? '2500+'"
                            />
                        </div>
                        <div class="col-md-3">
                            <x-ui.form-input
                                name="stat_teachers"
                                id="stat_teachers"
                                label="Teachers Count"
                                :value="$settings['stat_teachers'] ?? '120+'"
                            />
                        </div>
                        <div class="col-md-3">
                            <x-ui.form-input
                                name="stat_classes"
                                id="stat_classes"
                                label="Classes Count"
                                :value="$settings['stat_classes'] ?? '12'"
                            />
                        </div>
                        <div class="col-md-3">
                            <x-ui.form-input
                                name="stat_results"
                                id="stat_results"
                                label="Pass Percentage / Result"
                                :value="$settings['stat_results'] ?? '98%'"
                            />
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-4 pt-3 border-top">
                <x-ui.button type="submit" id="btnSaveSettings">
                    <i class="bi bi-check-lg me-1"></i> Save Website Settings
                </x-ui.button>
            </div>

        </form>

    </x-ui.card>

</div>

@endsection

@push('scripts')
<script>
$(function() {
    $('#websiteSettingsForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#btnSaveSettings');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

        const formData = new FormData(this);

        Ajax.request({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                submitBtn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> Save Website Settings');
                if (typeof Toast !== 'undefined' && Toast.success) {
                    Toast.success('Website settings updated successfully.');
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> Save Website Settings');
            }
        });
    });
});
</script>
@endpush
