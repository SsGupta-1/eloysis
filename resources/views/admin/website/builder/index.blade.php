@extends('layouts.admin.master')

@section('title', 'Home Page Builder')

@push('styles')
<style>
    .hub-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .hub-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1.2rem rgba(0, 0, 0, 0.08) !important;
    }
    .section-item-card {
        transition: all 0.2s ease-in-out;
        overflow: hidden;
    }
    .section-item-card:hover {
        box-shadow: 0 0.5rem 1.2rem rgba(0, 0, 0, 0.08) !important;
    }
    .cursor-grab {
        cursor: grab;
    }
    .cursor-grab:active {
        cursor: grabbing;
    }
    @media (max-width: 991.98px) {
        .section-toolbar {
            width: 100% !important;
            justify-content: space-between;
            padding-top: 0.75rem;
            margin-top: 0.25rem;
            border-top: 1px dashed rgba(0, 0, 0, 0.08) !important;
            gap: 8px !important;
        }
        .section-toolbar .layout-badge {
            max-width: 140px !important;
            font-size: 11px !important;
        }
    }
    @media (max-width: 575.98px) {
        .section-item-card .card-body {
            padding: 12px 14px !important;
        }
        .section-toolbar {
            gap: 6px !important;
        }
        .section-toolbar .btn-sm {
            font-size: 11px !important;
            padding: 4px 8px !important;
        }
        .section-toolbar .layout-badge {
            max-width: 115px !important;
            font-size: 10.5px !important;
            padding: 3px 6px !important;
        }
        .section-order-badge {
            font-size: 10px !important;
            padding: 2px 6px !important;
        }
    }
</style>
@endpush

@section('content')


<div class="container-fluid">

    {{-- Page Header --}}
    <x-ui.page-header
        title="Home Page Builder"
        subtitle="Manage homepage sections ordering, layouts, custom blocks, alerts, and dynamic components.">

        <x-slot:actions>
            <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-primary rounded-pill">
                <i class="bi bi-box-arrow-up-right me-1"></i> View Live Site
            </a>
            <button type="button" class="btn btn-primary rounded-pill" id="btnAddCustomSection">
                <i class="bi bi-plus-lg me-1"></i> Add Custom Section
            </button>
        </x-slot:actions>

    </x-ui.page-header>

    {{-- Alert notice banner --}}
    <div class="alert alert-info border-0 shadow-sm rounded-4 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4 p-3">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-info-circle-fill fs-3 text-info flex-shrink-0"></i>
            <div>
                <h6 class="fw-bold mb-0">Dynamic Section Rendering Active</h6>
                <small class="text-muted">Drag and drop the cards below to reorganize section display order on your public homepage. All enabled sections appear in this exact hierarchy.</small>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm flex-shrink-0" id="btnSaveAllOrders">
            <i class="bi bi-check2-all me-1"></i> Save Order
        </button>
    </div>

    {{-- Quick Management Hub Cards --}}
    <div class="row g-3 mb-4">
        {{-- Important Messages Hub --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 hub-card">
                <div class="card-body p-3 d-flex flex-column h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2 gap-2">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            <div class="bg-warning-subtle text-warning p-2 rounded-3 flex-shrink-0">
                                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-truncate" title="Urgent Notices">Urgent Notices</h6>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-warning text-dark rounded-pill flex-shrink-0 px-2 py-1" id="btnAddMessage">
                            <i class="bi bi-plus-lg"></i> Add
                        </button>
                    </div>
                    <p class="text-muted small mb-3 flex-grow-1">Top alert marquee ribbons and urgent announcements.</p>
                    <div class="d-flex justify-content-between align-items-center pt-2 mt-auto border-top gap-2">
                        <span class="badge bg-warning-subtle text-dark fw-bold text-nowrap">{{ count($important_messages) }} Active</span>
                        <a href="{{ route('admin.important-messages.index') }}" class="btn btn-link btn-sm text-decoration-none p-0 text-warning text-nowrap fw-semibold">
                            Manage <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Announcements Hub --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 hub-card">
                <div class="card-body p-3 d-flex flex-column h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2 gap-2">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            <div class="bg-primary-subtle text-primary p-2 rounded-3 flex-shrink-0">
                                <i class="bi bi-megaphone-fill fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-truncate" title="Announcements">Announcements</h6>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill flex-shrink-0 px-2 py-1" id="btnAddAnnouncement">
                            <i class="bi bi-plus-lg"></i> Add
                        </button>
                    </div>
                    <p class="text-muted small mb-3 flex-grow-1">Scrolling ticker and bulletin board circulars.</p>
                    <div class="d-flex justify-content-between align-items-center pt-2 mt-auto border-top gap-2">
                        <span class="badge bg-primary-subtle text-primary fw-bold text-nowrap">{{ count($announcements) }} Bulletins</span>
                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-link btn-sm text-decoration-none p-0 text-primary text-nowrap fw-semibold">
                            Manage <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Links Hub --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 hub-card">
                <div class="card-body p-3 d-flex flex-column h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2 gap-2">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            <div class="bg-info-subtle text-info p-2 rounded-3 flex-shrink-0">
                                <i class="bi bi-link-45deg fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-truncate" title="Quick Links">Quick Links</h6>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill flex-shrink-0 px-2 py-1" id="btnAddQuickLink">
                            <i class="bi bi-plus-lg"></i> Add
                        </button>
                    </div>
                    <p class="text-muted small mb-3 flex-grow-1">Shortcut cards (Admissions, Exams, Results).</p>
                    <div class="d-flex justify-content-between align-items-center pt-2 mt-auto border-top gap-2">
                        <span class="badge bg-info-subtle text-info fw-bold text-nowrap">{{ count($quick_links) }} Cards</span>
                        <a href="{{ route('admin.quick-links.index') }}" class="btn btn-link btn-sm text-decoration-none p-0 text-info text-nowrap fw-semibold">
                            Manage <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Testimonials Hub --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 hub-card">
                <div class="card-body p-3 d-flex flex-column h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2 gap-2">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            <div class="bg-success-subtle text-success p-2 rounded-3 flex-shrink-0">
                                <i class="bi bi-chat-square-quote-fill fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-truncate" title="Testimonials">Testimonials</h6>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill flex-shrink-0 px-2 py-1" id="btnAddTestimonial">
                            <i class="bi bi-plus-lg"></i> Add
                        </button>
                    </div>
                    <p class="text-muted small mb-3 flex-grow-1">Parent, student, and alumni reviews with ratings.</p>
                    <div class="d-flex justify-content-between align-items-center pt-2 mt-auto border-top gap-2">
                        <span class="badge bg-success-subtle text-success fw-bold text-nowrap">{{ count($testimonials) }} Reviews</span>
                        <a href="{{ route('admin.testimonials.index') }}" class="btn btn-link btn-sm text-decoration-none p-0 text-success text-nowrap fw-semibold">
                            Manage <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Sortable Sections List --}}
    <x-ui.card>
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4">
            <div>
                <h5 class="fw-bold mb-1">Homepage Sections Hierarchy</h5>
                <p class="text-muted small mb-0">Drag cards by their handle <i class="bi bi-grip-vertical text-muted"></i> to reorganize section display order on the public site.</p>
            </div>
            <span class="badge bg-light text-dark border px-3 py-2 flex-shrink-0">
                Total Sections: <strong id="sectionCountBadge">{{ count($sections) }}</strong>
            </span>
        </div>

        <div id="sortableSectionsList" class="d-flex flex-column gap-3">
            @foreach($sections as $section)
                <div class="card border section-item-card rounded-4 shadow-sm transition-all {{ $section['is_enabled'] ? 'bg-white' : 'bg-light opacity-75' }}"
                     data-id="{{ $section['id'] }}"
                     data-key="{{ $section['section_key'] }}"
                     data-type="{{ $section['section_type'] }}"
                     data-title="{{ $section['title'] }}"
                     data-subtitle="{{ $section['subtitle'] }}"
                     data-layout="{{ $section['layout_key'] }}"
                     data-custom-class="{{ $section['custom_class'] }}"
                     data-settings="{{ json_encode($section['settings'] ?? []) }}"
                     data-is-system="{{ $section['is_system'] ? '1' : '0' }}"
                     draggable="true">

                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3 w-100 min-w-0">
                            
                            {{-- Left Side: Drag Handle, Icon, Title, Subtitle, Badges --}}
                            <div class="d-flex align-items-start align-items-sm-center gap-2 gap-md-3 min-w-0 w-100 flex-grow-1">
                                {{-- Drag Handle & Order Badge --}}
                                <div class="d-flex align-items-center gap-1 gap-md-2 flex-shrink-0 pt-1 pt-sm-0">
                                    <span class="drag-handle p-1 p-md-2 text-muted rounded hover-bg-light cursor-grab" title="Drag to reorder">
                                        <i class="bi bi-grip-vertical fs-4"></i>
                                    </span>
                                    <span class="badge bg-dark text-white rounded-pill section-order-badge px-2 py-1">
                                        #{{ $section['display_order'] }}
                                    </span>
                                </div>

                                {{-- Type Icon --}}
                                <div class="bg-primary-subtle text-primary p-2 p-md-3 rounded-4 text-center flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                    <i class="bi {{ $section['type_icon'] }} fs-5"></i>
                                </div>

                                {{-- Title & Type --}}
                                <div class="min-w-0 flex-grow-1 overflow-hidden">
                                    <div class="d-flex align-items-center gap-1 gap-sm-2 mb-1 flex-wrap">
                                        <h6 class="fw-bold mb-0 text-dark text-truncate">{{ $section['title'] ?? $section['type_name'] }}</h6>
                                        <span class="badge bg-secondary-subtle text-secondary small">{{ $section['type_name'] }}</span>
                                        @if($section['is_system'])
                                            <span class="badge bg-info-subtle text-info small">System</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning small">Custom</span>
                                        @endif
                                    </div>
                                    <p class="text-muted small mb-0 text-break text-wrap" style="word-break: break-word; line-height: 1.4;">
                                        {{ $section['subtitle'] ?? $section['type_description'] }}
                                    </p>
                                </div>
                            </div>

                            {{-- Right Side: Action toolbar (Manage, Layout, Status, Edit/Delete) --}}
                            <div class="d-flex align-items-center flex-wrap gap-2 gap-md-3 w-100 w-lg-auto justify-content-between justify-content-lg-end pt-2 pt-lg-0 border-top border-lg-0 section-toolbar">
                                
                                {{-- Direct Manage Content Action if applicable --}}
                                @if($section['section_type'] === 'important_message')
                                    <a href="{{ route('admin.important-messages.index') }}" class="btn btn-sm btn-outline-warning text-dark rounded-pill flex-shrink-0" title="Manage Urgent Notices">
                                        <i class="bi bi-gear-fill me-1"></i> Manage Notices
                                    </a>
                                @elseif($section['section_type'] === 'announcement')
                                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-outline-primary rounded-pill flex-shrink-0" title="Manage Announcements">
                                        <i class="bi bi-gear-fill me-1"></i> Manage Bulletins
                                    </a>
                                @elseif($section['section_type'] === 'testimonials')
                                    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-sm btn-outline-success rounded-pill flex-shrink-0" title="Manage Testimonials">
                                        <i class="bi bi-chat-quote me-1"></i> Manage Reviews
                                    </a>
                                @elseif($section['section_type'] === 'quick_links')
                                    <a href="{{ route('admin.quick-links.index') }}" class="btn btn-sm btn-outline-info rounded-pill flex-shrink-0" title="Manage Quick Links">
                                        <i class="bi bi-link-45deg me-1"></i> Manage Links
                                    </a>
                                @elseif($section['section_type'] === 'hero_slider')
                                    <a href="{{ route('admin.home-slider.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill flex-shrink-0" title="Manage Carousel Slides">
                                        <i class="bi bi-images me-1"></i> Manage Slides
                                    </a>
                                @elseif($section['section_type'] === 'events')
                                    <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill flex-shrink-0" title="Manage Events">
                                        <i class="bi bi-calendar-event me-1"></i> Manage Events
                                    </a>
                                @elseif($section['section_type'] === 'gallery')
                                    <a href="{{ route('admin.gallery.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill flex-shrink-0" title="Manage Gallery">
                                        <i class="bi bi-camera me-1"></i> Manage Gallery
                                    </a>
                                @elseif($section['section_type'] === 'news')
                                    <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill flex-shrink-0" title="Manage News">
                                        <i class="bi bi-newspaper me-1"></i> Manage News
                                    </a>
                                @endif

                                {{-- Selected Layout Variant --}}
                                <div class="d-flex align-items-center gap-1 gap-md-2 flex-shrink-0">
                                    <div class="text-start">
                                        <small class="text-muted d-block" style="font-size: 10px; line-height: 1;">ACTIVE LAYOUT</small>
                                        <span class="badge bg-primary-subtle text-primary px-2 px-md-3 py-1 rounded-pill layout-badge text-truncate d-inline-block" style="max-width: 170px;">
                                            <i class="bi bi-layout-split me-1"></i> {{ $section['available_layouts'][$section['layout_key']]['name'] ?? $section['layout_key'] }}
                                        </span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-change-layout rounded-pill" title="Switch Layout Variant">
                                        <i class="bi bi-palette"></i>
                                    </button>
                                </div>

                                {{-- Status Switch --}}
                                <div class="text-center px-2 px-md-3 border-start flex-shrink-0">
                                    <small class="text-muted d-block mb-1" style="font-size: 10px; line-height: 1;">STATUS</small>
                                    <div class="form-check form-switch d-inline-block m-0 p-0" style="min-height: auto;">
                                        <input class="form-check-input section-status-switch m-0" type="checkbox" role="switch" {{ $section['is_enabled'] ? 'checked' : '' }}>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="text-end flex-shrink-0">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-edit-section" title="Edit Section Settings">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        @if(! $section['is_system'])
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-custom" title="Delete Custom Section">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-ui.card>


</div>

{{-- MODALS --}}

{{-- 1. Visual Layout Selector Modal --}}
<x-ui.modal id="layoutSelectorModal" title="Select Section Layout" size="lg">
    <input type="hidden" id="layout_section_id">
    <input type="hidden" id="layout_section_type">

    <div class="mb-3">
        <h6 class="fw-bold mb-1" id="layoutModalSectionTitle">Section Name</h6>
        <p class="text-muted small mb-0">Choose a layout variant for this section. The public homepage will render using the selected structure.</p>
    </div>

    <div class="row g-3" id="layoutOptionsContainer">
        {{-- Dynamically populated via JS --}}
    </div>

    <x-slot:footer>
        <x-ui.button variant="secondary" data-bs-dismiss="modal">
            Cancel
        </x-ui.button>
        <x-ui.button type="button" id="btnApplyLayout">
            <i class="bi bi-check-lg"></i> Apply Layout
        </x-ui.button>
    </x-slot:footer>
</x-ui.modal>

{{-- 2. Edit Section Properties Modal --}}
<x-ui.modal id="editSectionModal" title="Edit Section Properties" size="lg">
    <form id="editSectionForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="section_id" id="edit_section_id">

        <div class="row g-3">
            <div class="col-md-8">
                <x-ui.form-input
                    name="title"
                    id="edit_title"
                    label="Section Title"
                    placeholder="Enter section title"
                    required
                />
            </div>

            <div class="col-md-4">
                <x-ui.form-input
                    name="custom_class"
                    id="edit_custom_class"
                    label="Custom CSS Class (Optional)"
                    placeholder="e.g. bg-white, py-5"
                />
            </div>

            <div class="col-12">
                <x-ui.form-input
                    name="subtitle"
                    id="edit_subtitle"
                    label="Section Subtitle / Tagline"
                    placeholder="Optional subtitle / badge text"
                />
            </div>

            {{-- Custom Content specific fields (dynamic visibility) --}}
            <div id="customContentFields" class="col-12 d-none">
                <hr class="my-2">
                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-pencil-square me-1"></i> Custom Content Configuration</h6>

                <div class="row g-3">
                    <div class="col-12">
                        <x-ui.textarea
                            name="settings[content]"
                            id="edit_content"
                            label="Content / Paragraph Text"
                            rows="4"
                            placeholder="Write your custom text or message here..."
                        />
                    </div>

                    <div class="col-md-6">
                        <x-ui.form-input
                            name="settings[button_text]"
                            id="edit_button_text"
                            label="Button Text"
                            placeholder="e.g. Learn More / Register Now"
                        />
                    </div>

                    <div class="col-md-6">
                        <x-ui.form-input
                            name="settings[button_url]"
                            id="edit_button_url"
                            label="Button Link URL"
                            placeholder="e.g. https://... or /admission"
                        />
                    </div>

                    <div class="col-md-6">
                        <x-ui.form-input
                            type="color"
                            name="settings[bg_color]"
                            id="edit_bg_color"
                            label="Background Color"
                            value="#ffffff"
                        />
                    </div>

                    <div class="col-md-6">
                        <x-ui.form-input
                            type="color"
                            name="settings[text_color]"
                            id="edit_text_color"
                            label="Text Color"
                            value="#0f172a"
                        />
                    </div>

                    <div class="col-md-6">
                        <x-ui.form-file
                            name="image"
                            id="edit_image"
                            label="Featured Image"
                            accept="image/*"
                        />
                    </div>

                    <div class="col-md-6">
                        <x-ui.form-file
                            name="bg_image"
                            id="edit_bg_image"
                            label="Background Cover Image"
                            accept="image/*"
                        />
                    </div>
                </div>
            </div>
        </div>
    </form>

    <x-slot:footer>
        <x-ui.button variant="secondary" data-bs-dismiss="modal">
            Cancel
        </x-ui.button>
        <x-ui.button type="submit" form="editSectionForm" id="btnSaveSectionDetails">
            <i class="bi bi-check-lg"></i> Save Changes
        </x-ui.button>
    </x-slot:footer>
</x-ui.modal>

{{-- 3. Add Custom Section Modal --}}
<x-ui.modal id="customSectionModal" title="Create Custom Homepage Section" size="lg">
    <form id="customSectionForm" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">
            <div class="col-md-8">
                <x-ui.form-input
                    name="title"
                    id="create_custom_title"
                    label="Section Title"
                    placeholder="e.g. Special Scholarships / Campus Tour"
                    required
                />
            </div>

            <div class="col-md-4">
                <x-ui.select
                    name="layout_key"
                    id="create_custom_layout"
                    label="Layout Variant"
                    :options="[
                        'custom_content_01' => 'Two-Column (Text + Image)',
                        'custom_content_02' => 'Full-Width Banner',
                        'custom_content_03' => 'Feature Highlight Box'
                    ]"
                    value="custom_content_01"
                />
            </div>

            <div class="col-12">
                <x-ui.form-input
                    name="subtitle"
                    id="create_custom_subtitle"
                    label="Subtitle / Tagline"
                    placeholder="Optional badge / tagline"
                />
            </div>

            <div class="col-12">
                <x-ui.textarea
                    name="settings[content]"
                    id="create_custom_content"
                    label="Section Content"
                    rows="3"
                    placeholder="Enter detailed description or announcement..."
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    name="settings[button_text]"
                    id="create_custom_btn_text"
                    label="Action Button Text"
                    placeholder="e.g. Apply Online"
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-input
                    name="settings[button_url]"
                    id="create_custom_btn_url"
                    label="Action Button URL"
                    placeholder="e.g. /admission"
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-file
                    name="image"
                    id="create_custom_image"
                    label="Featured Image"
                    accept="image/*"
                />
            </div>

            <div class="col-md-6">
                <x-ui.form-file
                    name="bg_image"
                    id="create_custom_bg_image"
                    label="Background Image (Optional)"
                    accept="image/*"
                />
            </div>
        </div>
    </form>

    <x-slot:footer>
        <x-ui.button variant="secondary" data-bs-dismiss="modal">
            Cancel
        </x-ui.button>
        <x-ui.button type="submit" form="customSectionForm" id="btnSubmitCustomSection">
            <i class="bi bi-plus-lg"></i> Create Section
        </x-ui.button>
    </x-slot:footer>
</x-ui.modal>

{{-- 4. Manage Announcements Modal --}}
<x-ui.modal id="announcementsModal" title="Manage School Announcements" size="xl">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Bulletin Announcements</h6>
        <button type="button" class="btn btn-sm btn-primary rounded-pill" id="btnOpenNewAnnouncement">
            <i class="bi bi-plus"></i> New Announcement
        </button>
    </div>

    {{-- Announcement Form --}}
    <div class="card bg-light border p-3 rounded-4 mb-4 d-none" id="announcementFormCard">
        <form id="announcementForm">
            @csrf
            <input type="hidden" name="announcement_id" id="announcement_id">

            <div class="row g-3">
                <div class="col-md-8">
                    <x-ui.form-input
                        name="title"
                        id="ann_title"
                        label="Announcement Title"
                        placeholder="e.g. Annual Examination 2026 Schedule"
                        required
                    />
                </div>
                <div class="col-md-4">
                    <x-ui.form-input
                        name="badge"
                        id="ann_badge"
                        label="Badge Tag"
                        placeholder="e.g. New / Notice"
                        value="Notice"
                    />
                </div>
                <div class="col-md-6">
                    <x-ui.form-input
                        name="link_url"
                        id="ann_link_url"
                        label="Link / Action URL"
                        placeholder="e.g. /news/1"
                    />
                </div>
                <div class="col-md-6">
                    <x-ui.form-input
                        name="link_text"
                        id="ann_link_text"
                        label="Link Button Text"
                        placeholder="e.g. Read Circular"
                        value="Read More"
                    />
                </div>
                <div class="col-12">
                    <x-ui.textarea
                        name="content"
                        id="ann_content"
                        label="Announcement Content"
                        rows="2"
                        placeholder="Detailed message..."
                    />
                </div>
                <div class="col-12 text-end">
                    <button type="button" class="btn btn-sm btn-secondary me-2" id="btnCancelAnnouncement">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="btnSaveAnnouncement">Save Announcement</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Announcements Table --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="announcementsTable">
            <thead class="table-light">
                <tr>
                    <th width="50">#</th>
                    <th>Badge</th>
                    <th>Announcement Title</th>
                    <th>Link</th>
                    <th>Status</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
            <tbody id="announcementsTableBody">
                @forelse($announcements as $index => $ann)
                    <tr data-id="{{ $ann->id }}" data-title="{{ $ann->title }}" data-content="{{ $ann->content }}" data-badge="{{ $ann->badge }}" data-url="{{ $ann->link_url }}" data-link-text="{{ $ann->link_text }}">
                        <td>{{ $index + 1 }}</td>
                        <td><span class="badge bg-primary-subtle text-primary">{{ $ann->badge }}</span></td>
                        <td>
                            <strong>{{ $ann->title }}</strong>
                            @if(!empty($ann->content))
                                <br><small class="text-muted">{{ Str::limit($ann->content, 60) }}</small>
                            @endif
                        </td>
                        <td><a href="{{ $ann->link_url ?? '#' }}" target="_blank" class="small text-truncate d-inline-block" style="max-width: 150px;">{{ $ann->link_url ?? '-' }}</a></td>
                        <td>
                            <span class="badge bg-{{ $ann->status ? 'success' : 'secondary' }}-subtle text-{{ $ann->status ? 'success' : 'secondary' }}">
                                {{ $ann->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-edit-ann" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-ann" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No announcements added yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-slot:footer>
        <x-ui.button variant="secondary" data-bs-dismiss="modal">
            Close
        </x-ui.button>
    </x-slot:footer>
</x-ui.modal>

{{-- 5. Manage Important Messages Modal --}}
<x-ui.modal id="messagesModal" title="Manage Urgent Notices" size="xl">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Urgent Notice Alerts</h6>
        <button type="button" class="btn btn-sm btn-warning text-dark rounded-pill" id="btnOpenNewMessage">
            <i class="bi bi-plus"></i> New Notice
        </button>
    </div>

    {{-- Message Form --}}
    <div class="card bg-light border p-3 rounded-4 mb-4 d-none" id="messageFormCard">
        <form id="importantMessageForm">
            @csrf
            <input type="hidden" name="message_id" id="message_id">

            <div class="row g-3">
                <div class="col-md-8">
                    <x-ui.form-input
                        name="title"
                        id="msg_title"
                        label="Notice Headline"
                        placeholder="e.g. Urgent: School Closed Tomorrow"
                        required
                    />
                </div>
                <div class="col-md-4">
                    <x-ui.select
                        name="type"
                        id="msg_type"
                        label="Alert Severity"
                        :options="[
                            'warning' => 'Warning (Yellow)',
                            'danger' => 'Urgent Alert (Red)',
                            'info' => 'Information (Blue)',
                            'success' => 'Positive Notice (Green)'
                        ]"
                        value="warning"
                    />
                </div>
                <div class="col-12">
                    <x-ui.textarea
                        name="message"
                        id="msg_body"
                        label="Notice Message"
                        rows="3"
                        placeholder="Enter full notice explanation..."
                        required
                    />
                </div>
                <div class="col-md-6">
                    <x-ui.form-input
                        name="action_text"
                        id="msg_action_text"
                        label="Action Button Label"
                        placeholder="e.g. View Circular"
                    />
                </div>
                <div class="col-md-6">
                    <x-ui.form-input
                        name="action_url"
                        id="msg_action_url"
                        label="Action Button Link"
                        placeholder="e.g. /news/notice-123"
                    />
                </div>
                <div class="col-12 text-end">
                    <button type="button" class="btn btn-sm btn-secondary me-2" id="btnCancelMessage">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-warning text-dark" id="btnSaveMessage">Save Notice</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Messages Table --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="messagesTable">
            <thead class="table-light">
                <tr>
                    <th width="50">#</th>
                    <th>Type</th>
                    <th>Notice Headline</th>
                    <th>Message Details</th>
                    <th>Action Link</th>
                    <th>Status</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
            <tbody id="messagesTableBody">
                @forelse($important_messages as $index => $msg)
                    <tr data-id="{{ $msg->id }}" data-title="{{ $msg->title }}" data-message="{{ $msg->message }}" data-type="{{ $msg->type }}" data-action-text="{{ $msg->action_text }}" data-action-url="{{ $msg->action_url }}">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <span class="badge bg-{{ $msg->type }}-subtle text-{{ $msg->type }} text-capitalize">
                                {{ $msg->type }}
                            </span>
                        </td>
                        <td><strong>{{ $msg->title }}</strong></td>
                        <td><small class="text-muted">{{ Str::limit($msg->message, 80) }}</small></td>
                        <td><a href="{{ $msg->action_url ?? '#' }}" target="_blank" class="small text-truncate d-inline-block" style="max-width: 140px;">{{ $msg->action_text ?? ($msg->action_url ?? '-') }}</a></td>
                        <td>
                            <span class="badge bg-{{ $msg->status ? 'success' : 'secondary' }}-subtle text-{{ $msg->status ? 'success' : 'secondary' }}">
                                {{ $msg->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-edit-msg" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-msg" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No important notices added yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-slot:footer>
        <x-ui.button variant="secondary" data-bs-dismiss="modal">
            Close
        </x-ui.button>
    </x-slot:footer>
</x-ui.modal>

{{-- 6. Manage Testimonials Modal --}}
<x-ui.modal id="testimonialsModal" title="Manage Testimonials & Reviews" size="xl">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Parent & Student Reviews</h6>
        <button type="button" class="btn btn-sm btn-success rounded-pill" id="btnOpenNewTestimonial">
            <i class="bi bi-plus"></i> New Testimonial
        </button>
    </div>

    {{-- Testimonial Form --}}
    <div class="card bg-light border p-3 rounded-4 mb-4 d-none" id="testimonialFormCard">
        <form id="testimonialForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="testimonial_id" id="testimonial_id">

            <div class="row g-3">
                <div class="col-md-6">
                    <x-ui.form-input
                        name="name"
                        id="testi_name"
                        label="Reviewer Name"
                        placeholder="e.g. Rajesh Sharma"
                        required
                    />
                </div>
                <div class="col-md-3">
                    <x-ui.form-input
                        name="role"
                        id="testi_role"
                        label="Designation / Role"
                        placeholder="e.g. Parent / Student / Alumnus"
                        value="Parent"
                        required
                    />
                </div>
                <div class="col-md-3">
                    <x-ui.select
                        name="rating"
                        id="testi_rating"
                        label="Star Rating"
                        :options="[
                            '5' => '⭐⭐⭐⭐⭐ (5 Stars)',
                            '4' => '⭐⭐⭐⭐ (4 Stars)',
                            '3' => '⭐⭐⭐ (3 Stars)',
                            '2' => '⭐⭐ (2 Stars)',
                            '1' => '⭐ (1 Star)'
                        ]"
                        value="5"
                    />
                </div>
                <div class="col-md-8">
                    <x-ui.textarea
                        name="message"
                        id="testi_message"
                        label="Testimonial / Review Text"
                        rows="3"
                        placeholder="Write student/parent feedback..."
                        required
                    />
                </div>
                <div class="col-md-4">
                    <x-ui.form-file
                        name="image"
                        id="testi_image"
                        label="Reviewer Avatar / Photo"
                        accept="image/*"
                    />
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="testi_status" value="1" checked>
                        <label class="form-check-label" for="testi_status">Active & Visible</label>
                    </div>
                </div>
                <div class="col-12 text-end">
                    <button type="button" class="btn btn-sm btn-secondary me-2" id="btnCancelTestimonial">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success" id="btnSaveTestimonial">Save Testimonial</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Testimonials Table --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="testimonialsTable">
            <thead class="table-light">
                <tr>
                    <th width="50">#</th>
                    <th width="70">Avatar</th>
                    <th>Author & Role</th>
                    <th>Rating</th>
                    <th>Feedback Review</th>
                    <th>Status</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
            <tbody id="testimonialsTableBody">
                @forelse($testimonials as $index => $testi)
                    <tr data-id="{{ $testi->id }}" data-name="{{ $testi->name }}" data-role="{{ $testi->role }}" data-message="{{ $testi->message }}" data-rating="{{ $testi->rating }}" data-status="{{ $testi->status ? '1' : '0' }}">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <img src="{{ $testi->image_url }}" class="rounded-circle border" width="40" height="40" style="object-fit: cover;" alt="{{ $testi->name }}">
                        </td>
                        <td>
                            <strong>{{ $testi->name }}</strong>
                            <br><small class="text-muted">{{ $testi->role }}</small>
                        </td>
                        <td>
                            <span class="text-warning">
                                @for($i = 0; $i < ($testi->rating ?? 5); $i++)
                                    <i class="bi bi-star-fill"></i>
                                @endfor
                            </span>
                        </td>
                        <td><small class="text-muted">{{ Str::limit($testi->message, 80) }}</small></td>
                        <td>
                            <span class="badge bg-{{ $testi->status ? 'success' : 'secondary' }}-subtle text-{{ $testi->status ? 'success' : 'secondary' }}">
                                {{ $testi->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-edit-testi" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-testi" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No testimonials added yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-slot:footer>
        <x-ui.button variant="secondary" data-bs-dismiss="modal">
            Close
        </x-ui.button>
    </x-slot:footer>
</x-ui.modal>

{{-- 7. Manage Quick Links Modal --}}
<x-ui.modal id="quickLinksModal" title="Manage Quick Action Links" size="xl">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Homepage Floating Action Tiles</h6>
        <button type="button" class="btn btn-sm btn-info text-white rounded-pill" id="btnOpenNewQuickLink">
            <i class="bi bi-plus"></i> New Quick Link
        </button>
    </div>

    {{-- Quick Link Form --}}
    <div class="card bg-light border p-3 rounded-4 mb-4 d-none" id="quickLinkFormCard">
        <form id="quickLinkForm">
            @csrf
            <input type="hidden" name="quick_link_id" id="quick_link_id">

            <div class="row g-3">
                <div class="col-md-6">
                    <x-ui.form-input
                        name="title"
                        id="ql_title"
                        label="Action Title"
                        placeholder="e.g. Online Admission"
                        required
                    />
                </div>
                <div class="col-md-6">
                    <x-ui.form-input
                        name="url"
                        id="ql_url"
                        label="Destination Link URL"
                        placeholder="e.g. /admission or https://..."
                        required
                    />
                </div>
                <div class="col-md-4">
                    <x-ui.form-input
                        name="icon"
                        id="ql_icon"
                        label="Bootstrap Icon Class"
                        placeholder="e.g. bi-mortarboard-fill"
                        value="bi-mortarboard-fill"
                    />
                </div>
                <div class="col-md-4">
                    <x-ui.select
                        name="color"
                        id="ql_color"
                        label="Theme Color"
                        :options="[
                            'primary' => 'Primary (Blue)',
                            'success' => 'Success (Green)',
                            'warning' => 'Warning (Amber)',
                            'info' => 'Info (Cyan)',
                            'danger' => 'Danger (Red)',
                            'dark' => 'Dark (Slate)'
                        ]"
                        value="primary"
                    />
                </div>
                <div class="col-md-4">
                    <x-ui.form-input
                        name="description"
                        id="ql_desc"
                        label="Short Description"
                        placeholder="e.g. Apply for session 2026-27"
                    />
                </div>
                <div class="col-12 text-end">
                    <button type="button" class="btn btn-sm btn-secondary me-2" id="btnCancelQuickLink">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-info text-white" id="btnSaveQuickLink">Save Quick Link</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Quick Links Table --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="quickLinksTable">
            <thead class="table-light">
                <tr>
                    <th width="50">#</th>
                    <th width="80">Icon</th>
                    <th>Title & Subtitle</th>
                    <th>Target Destination</th>
                    <th>Color</th>
                    <th>Status</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
            <tbody id="quickLinksTableBody">
                @forelse($quick_links as $index => $link)
                    <tr data-id="{{ $link->id }}" data-title="{{ $link->title }}" data-desc="{{ $link->description }}" data-icon="{{ $link->icon }}" data-url="{{ $link->url }}" data-color="{{ $link->color }}" data-status="{{ $link->status ? '1' : '0' }}">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="bg-{{ $link->color ?? 'primary' }}-subtle text-{{ $link->color ?? 'primary' }} p-2 rounded-3 text-center" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi {{ $link->icon ?? 'bi-link-45deg' }}"></i>
                            </div>
                        </td>
                        <td>
                            <strong>{{ $link->title }}</strong>
                            @if(!empty($link->description))
                                <br><small class="text-muted">{{ $link->description }}</small>
                            @endif
                        </td>
                        <td><a href="{{ $link->url }}" target="_blank" class="small text-truncate d-inline-block" style="max-width: 180px;">{{ $link->url }}</a></td>
                        <td><span class="badge bg-{{ $link->color ?? 'primary' }} text-capitalize">{{ $link->color ?? 'primary' }}</span></td>
                        <td>
                            <span class="badge bg-{{ $link->status ? 'success' : 'secondary' }}-subtle text-{{ $link->status ? 'success' : 'secondary' }}">
                                {{ $link->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-edit-ql" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-ql" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No quick links added yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-slot:footer>
        <x-ui.button variant="secondary" data-bs-dismiss="modal">
            Close
        </x-ui.button>
    </x-slot:footer>
</x-ui.modal>

@endsection

@push('scripts')
<script>
    const BUILDER_REGISTRY = @json($registry);
    const URL_UPDATE_ORDERS = "{{ route('admin.homepage-builder.orders') }}";
    const URL_TOGGLE_STATUS = "{{ route('admin.homepage-builder.status', ':id') }}";
    const URL_UPDATE_LAYOUT = "{{ route('admin.homepage-builder.layout', ':id') }}";
    const URL_UPDATE_SECTION = "{{ route('admin.homepage-builder.update', ':id') }}";
    const URL_STORE_CUSTOM = "{{ route('admin.homepage-builder.custom-section.store') }}";
    const URL_DELETE_CUSTOM = "{{ route('admin.homepage-builder.custom-section.destroy', ':id') }}";
    const URL_STORE_ANN = "{{ route('admin.homepage-builder.announcements.store') }}";
    const URL_UPDATE_ANN = "{{ route('admin.homepage-builder.announcements.update', ':id') }}";
    const URL_DELETE_ANN = "{{ route('admin.homepage-builder.announcements.destroy', ':id') }}";
    const URL_STORE_MSG = "{{ route('admin.homepage-builder.messages.store') }}";
    const URL_UPDATE_MSG = "{{ route('admin.homepage-builder.messages.update', ':id') }}";
    const URL_DELETE_MSG = "{{ route('admin.homepage-builder.messages.destroy', ':id') }}";
    const URL_STORE_TESTIMONIAL = "{{ route('admin.homepage-builder.testimonials.store') }}";
    const URL_UPDATE_TESTIMONIAL = "{{ route('admin.homepage-builder.testimonials.update', ':id') }}";
    const URL_DELETE_TESTIMONIAL = "{{ route('admin.homepage-builder.testimonials.destroy', ':id') }}";
    const URL_STORE_QUICK_LINK = "{{ route('admin.homepage-builder.quick-links.store') }}";
    const URL_UPDATE_QUICK_LINK = "{{ route('admin.homepage-builder.quick-links.update', ':id') }}";
    const URL_DELETE_QUICK_LINK = "{{ route('admin.homepage-builder.quick-links.destroy', ':id') }}";
</script>
<script src="{{ asset('assets/admin/js/homepage_builder.js') }}"></script>
@endpush
