@extends('layouts.admin.master')

@section('title', 'Admission Enquiry - ' . ($enquiry->application_no ?? 'ENQ-' . $enquiry->id))

@section('content')

{{-- Page Header --}}
<x-ui.page-header
    title="Admission Enquiry Details"
    subtitle="Manage enquiry details, staff assignment, follow-up history, and admission conversion">
    <x-slot:actions>
        <a href="{{ route('admin.admission-enquiry.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Enquiries
        </a>
        @if(!$enquiry->isConverted())
            <a href="{{ route('admin.admission-enquiry.convert', $enquiry->id) }}" class="btn btn-success shadow-sm">
                <i class="bi bi-person-plus-fill me-1"></i> Convert to Admission
            </a>
        @else
            <a href="{{ route('admin.students.show', $enquiry->enrollment_id ?? $enquiry->student_profile_id ?? '') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-mortarboard-fill me-1"></i> View Student Profile
            </a>
        @endif
    </x-slot:actions>
</x-ui.page-header>

{{-- Converted Alert Banner --}}
@if($enquiry->isConverted())
    <div class="alert alert-success d-flex align-items-center justify-content-between mb-3 shadow-sm border-0 py-2 px-3" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-4 text-success me-2"></i>
            <div>
                <strong class="text-success">Enquiry Converted to Student Admission:</strong>
                <span class="text-muted small ms-1">
                    Converted on <strong>{{ $enquiry->converted_at?->format('d M Y, h:i A') ?? '-' }}</strong>
                    @if($enquiry->convertedBy) by <strong>{{ $enquiry->convertedBy->name }}</strong>@endif.
                    Admission No: <strong>{{ $enquiry->studentProfile?->admission_no ?? '-' }}</strong>.
                </span>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.students.show', $enquiry->enrollment_id ?? $enquiry->student_profile_id ?? '') }}" class="btn btn-sm btn-success">
                <i class="bi bi-eye me-1"></i> View Student
            </a>
        </div>
    </div>
@endif

{{-- Duplicate Detection Warning Banner --}}
@if(!empty($duplicates) && count($duplicates) > 0 && !$enquiry->isConverted())
    <div class="alert alert-warning border-warning mb-3 shadow-sm py-2 px-3" role="alert">
        <div class="d-flex align-items-start">
            <i class="bi bi-exclamation-triangle-fill fs-5 text-warning me-2 mt-1"></i>
            <div class="w-100">
                <strong class="text-dark">Possible Duplicate Record Detected:</strong>
                <ul class="mb-0 ps-3 small text-dark mt-1">
                    @foreach($duplicates as $dup)
                        <li><strong>{{ $dup['field'] }}:</strong> {{ $dup['details'] }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

<div class="row g-3">

    {{-- Left Column: 2 Cards (Details Card + Activity History Card) --}}
    <div class="col-lg-8">

        {{-- CARD 1: Complete Admission Enquiry Details --}}
        <div class="card shadow-sm border-0 mb-3" style="height: auto;">
            {{-- Header with Status & Application No --}}
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <span class="badge bg-light text-primary border me-2">{{ $enquiry->application_no ?? 'ENQ-' . $enquiry->id }}</span>
                        <h5 class="mb-0 fw-bold d-inline-block align-middle text-dark">{{ $enquiry->student_name }}</h5>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @php
                            $statusBadgeColors = [
                                'new' => 'bg-secondary',
                                'assigned' => 'bg-primary',
                                'contacted' => 'bg-info text-dark',
                                'follow_up' => 'bg-warning text-dark',
                                'interested' => 'bg-success',
                                'visit_scheduled' => 'bg-purple text-white',
                                'visited' => 'bg-info',
                                'admission_ready' => 'bg-primary',
                                'converted' => 'bg-success',
                                'not_interested' => 'bg-danger',
                                'lost' => 'bg-dark',
                                'cancelled' => 'bg-danger',
                            ];
                            $badgeClass = $statusBadgeColors[$enquiry->status] ?? 'bg-secondary';
                        @endphp
                        <span class="badge {{ $badgeClass }} px-2 py-1 text-uppercase" style="font-size: 0.75rem;">
                            {{ ucwords(str_replace('_', ' ', $enquiry->status)) }}
                        </span>
                        <small class="text-muted">
                            <i class="bi bi-clock me-1"></i>{{ $enquiry->created_at?->format('d M Y, h:i A') }}
                        </small>
                    </div>
                </div>
            </div>

            <div class="card-body p-3">
                {{-- Section 1: Academic & Source Overview Bar --}}
                <div class="bg-light rounded p-2 mb-3">
                    <div class="row g-2 text-center text-sm-start">
                        <div class="col-sm-4 border-end-sm">
                            <small class="text-muted d-block">Academic Session</small>
                            <span class="fw-semibold text-dark">{{ $enquiry->academicSession?->name ?? 'Default Session' }}</span>
                        </div>
                        <div class="col-sm-4 border-end-sm">
                            <small class="text-muted d-block">Interested Class</small>
                            <span class="fw-semibold text-dark">{{ $enquiry->studentClass?->class_name ?? '-' }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block">Enquiry Source</small>
                            <span class="fw-semibold text-capitalize text-dark">{{ str_replace('_', ' ', $enquiry->source ?? 'Website') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Student Information --}}
                <div class="mb-3">
                    <h6 class="fw-bold text-primary mb-2 border-bottom pb-1">
                        <i class="bi bi-person-vcard me-1"></i> Student Information
                    </h6>
                    <div class="row g-2 small">
                        <div class="col-md-6 col-sm-6">
                            <span class="text-muted">Full Name:</span>
                            <span class="fw-semibold text-dark ms-1">{{ $enquiry->student_name }}</span>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span class="text-muted">Email Address:</span>
                            <span class="ms-1">
                                @if($enquiry->student_email)
                                    <a href="mailto:{{ $enquiry->student_email }}" class="text-decoration-none">{{ $enquiry->student_email }}</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </span>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span class="text-muted">Phone / Mobile:</span>
                            <span class="ms-1">
                                @if($enquiry->student_phone)
                                    <a href="tel:{{ $enquiry->student_phone }}" class="text-decoration-none fw-semibold">{{ $enquiry->student_phone }}</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </span>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span class="text-muted">Alternate Phone:</span>
                            <span class="text-dark ms-1">{{ $enquiry->alternate_phone ?: '-' }}</span>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span class="text-muted">Date of Birth:</span>
                            <span class="text-dark ms-1">{{ $enquiry->date_of_birth ? \Carbon\Carbon::parse($enquiry->date_of_birth)->format('d M Y') : '-' }}</span>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span class="text-muted">Gender:</span>
                            <span class="text-capitalize text-dark ms-1">{{ $enquiry->gender ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Parent / Guardian Information --}}
                <div class="mb-3">
                    <h6 class="fw-bold text-primary mb-2 border-bottom pb-1">
                        <i class="bi bi-people me-1"></i> Parent / Guardian Information
                    </h6>
                    <div class="row g-2 small">
                        <div class="col-md-6 col-sm-6">
                            <span class="text-muted">Parent / Guardian Name:</span>
                            <span class="fw-semibold text-dark ms-1">{{ $enquiry->parent_name }}</span>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span class="text-muted">Parent Phone:</span>
                            <span class="ms-1">
                                @if($enquiry->parent_phone)
                                    <a href="tel:{{ $enquiry->parent_phone }}" class="text-decoration-none fw-semibold">{{ $enquiry->parent_phone }}</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </span>
                        </div>
                        @if($enquiry->reference_name || $enquiry->reference_phone)
                            <div class="col-md-6 col-sm-6">
                                <span class="text-muted">Referrer Name:</span>
                                <span class="text-dark ms-1">{{ $enquiry->reference_name ?: '-' }}</span>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <span class="text-muted">Referrer Phone:</span>
                                <span class="text-dark ms-1">{{ $enquiry->reference_phone ?: '-' }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Section 4: Message & Remarks --}}
                <div>
                    <h6 class="fw-bold text-primary mb-2 border-bottom pb-1">
                        <i class="bi bi-chat-left-text me-1"></i> Enquiry Message & Remarks
                    </h6>
                    <div class="row g-2 small">
                        <div class="col-md-6">
                            <div class="text-muted mb-1">Student / Parent Message:</div>
                            <div class="p-2 bg-light rounded text-secondary border">
                                {{ $enquiry->message ?: 'No message provided.' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted mb-1">Internal Remarks:</div>
                            <div class="p-2 bg-light rounded text-secondary border">
                                {{ $enquiry->remarks ?: 'No internal remarks.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 2: Activity & Follow-up History (Dynamic Height / Content-Based) --}}
        <div class="card shadow-sm border-0 mb-3" style="height: auto;">
            <div class="card-header bg-white border-bottom py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-clock-history me-1"></i> Activity & Follow-up History
                </h6>
                <span class="badge bg-secondary rounded-pill">{{ $enquiry->followups->count() }} records</span>
            </div>

            <div class="card-body p-3">
                @if($enquiry->followups->isEmpty())
                    <div class="text-center py-3 text-muted small">
                        <i class="bi bi-inbox fs-3 d-block mb-1 text-secondary"></i>
                        No follow-ups recorded yet. Use the form on the right to log your first contact attempt.
                    </div>
                @else
                    <div class="timeline ps-2 border-start border-2 border-primary-subtle">
                        @foreach($enquiry->followups as $act)
                            <div class="position-relative mb-3 ps-3">
                                <div class="position-absolute bg-primary rounded-circle" style="width: 10px; height: 10px; left: -6px; top: 5px;"></div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center mb-1">
                                    <div class="fw-bold text-dark small">
                                        @if($act->action_type === 'assignment')
                                            <span class="badge bg-primary me-1">Staff Assignment</span>
                                        @elseif($act->action_type === 'converted')
                                            <span class="badge bg-success me-1">Admission Converted</span>
                                        @elseif($act->action_type === 'status_change')
                                            <span class="badge bg-info text-dark me-1">Status Update</span>
                                        @else
                                            <span class="badge bg-warning text-dark me-1">Follow-up Call</span>
                                        @endif

                                        @if($act->attempt_status)
                                            <span class="badge bg-light text-dark border me-1 text-capitalize">{{ str_replace('_', ' ', $act->attempt_status) }}</span>
                                        @endif
                                    </div>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <i class="bi bi-clock me-1"></i>{{ $act->created_at->format('d M Y, h:i A') }}
                                    </small>
                                </div>

                                <div class="text-secondary small mb-1">
                                    {{ $act->remarks ?: 'No remarks entered.' }}
                                </div>

                                <div class="d-flex flex-wrap gap-2 text-muted" style="font-size: 0.75rem;">
                                    @if($act->user)
                                        <span><i class="bi bi-person me-1"></i>By: <strong>{{ $act->user->name }}</strong></span>
                                    @endif
                                    @if($act->next_follow_up_at)
                                        <span><i class="bi bi-calendar-event text-warning me-1"></i>Next Follow-up: <strong>{{ $act->next_follow_up_at->format('d M Y, h:i A') }}</strong></span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Right Column: Compact CRM Action Cards (Height fits content) --}}
    <div class="col-lg-4">

        {{-- Convert to Admission Card --}}
        @if(!$enquiry->isConverted())
            <div class="card border-success border-2 shadow-sm mb-3 bg-success bg-opacity-10" style="height: auto;">
                <div class="card-body p-3">
                    <h6 class="card-title fw-bold text-success mb-1">
                        <i class="bi bi-patch-check-fill me-1"></i> Convert to Admission
                    </h6>
                    <p class="card-text text-muted small mb-2">
                        Create student profile, user login, and enrollment atomically.
                    </p>
                    <a href="{{ route('admin.admission-enquiry.convert', $enquiry->id) }}" class="btn btn-success btn-sm w-100 fw-bold shadow-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Convert to Admission
                    </a>
                </div>
            </div>
        @endif

        {{-- Log Follow-up / Call Attempt Card --}}
        <div class="card shadow-sm border-0 mb-3" style="height: auto;">
            <div class="card-header bg-white border-bottom py-2">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-telephone-outbound me-1 text-primary"></i> Record Follow-up / Call
                </h6>
            </div>
            <div class="card-body p-3">
                <form id="addFollowupForm" action="{{ route('admin.admission-enquiry.followup', $enquiry->id) }}" method="POST">
                    @csrf

                    {{-- Status --}}
                    <div class="mb-2">
                        <label class="form-label small text-muted mb-1">Update Status</label>
                        <select name="status" id="followup_status" class="form-select form-select-sm">
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" @selected($enquiry->status === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Call Attempt Result --}}
                    <div class="mb-2">
                        <label class="form-label small text-muted mb-1">Call / Contact Result</label>
                        <select name="attempt_status" id="followup_attempt_status" class="form-select form-select-sm">
                            <option value="">-- Select Contact Outcome --</option>
                            @foreach($attemptStatuses as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Remarks --}}
                    <div class="mb-2">
                        <label class="form-label small text-muted mb-1">Remarks <span class="text-danger">*</span></label>
                        <textarea name="remarks" class="form-control form-control-sm" rows="2" placeholder="Summary of discussion..." required></textarea>
                    </div>

                    {{-- Next Follow-up Date/Time --}}
                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">Next Follow-up Date & Time</label>
                        <input type="datetime-local" name="next_follow_up_at" class="form-control form-control-sm" value="{{ $enquiry->next_follow_up_at ? $enquiry->next_follow_up_at->format('Y-m-d\TH:i') : '' }}">
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm w-100" id="btnSaveFollowup">
                        <span class="btn-text"><i class="bi bi-save me-1"></i> Save Follow-up</span>
                        <span class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Staff Assignment Card --}}
        <div class="card shadow-sm border-0 mb-3" style="height: auto;">
            <div class="card-header bg-white border-bottom py-2">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-person-badge me-1 text-primary"></i> Staff Assignment
                </h6>
            </div>
            <div class="card-body p-3">
                <form id="assignStaffForm" action="{{ route('admin.admission-enquiry.assign', $enquiry->id) }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small text-muted mb-1">Assign Counsellor / Staff</label>
                        <select name="assigned_to" id="assign_staff_select" class="form-select form-select-sm">
                            <option value="">-- Unassigned --</option>
                            @foreach($users as $id => $name)
                                <option value="{{ $id }}" @selected($enquiry->assigned_to == $id || $enquiry->handled_by == $id)>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small text-muted mb-1">Assignment Note (Optional)</label>
                        <textarea name="remarks" class="form-control form-control-sm" rows="1" placeholder="Note about assignment..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-outline-primary btn-sm w-100" id="btnAssignStaff">
                        <span class="btn-text"><i class="bi bi-check2 me-1"></i> Update Assignment</span>
                        <span class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Summary Stats Card --}}
        <div class="card shadow-sm border-0 mb-3" style="height: auto;">
            <div class="card-header bg-white border-bottom py-2">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-speedometer2 me-1 text-primary"></i> Enquiry Statistics
                </h6>
            </div>
            <div class="card-body p-3 small">
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Total Contact Attempts:</span>
                    <span class="fw-bold badge bg-light text-dark border">{{ $enquiry->attempt_count ?? 0 }}</span>
                </div>

                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Last Contacted:</span>
                    <span class="fw-semibold">{{ $enquiry->last_contacted_at?->format('d M Y, h:i A') ?? '-' }}</span>
                </div>

                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Assigned To:</span>
                    <span class="fw-semibold text-primary">{{ $enquiry->assignedUser?->name ?? 'Unassigned' }}</span>
                </div>

                @if($enquiry->assigned_at)
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Assigned On:</span>
                        <span class="text-muted">{{ $enquiry->assigned_at->format('d M Y, h:i A') }}</span>
                    </div>
                @endif

                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Next Scheduled Follow-up:</span>
                    <span class="fw-semibold text-warning">
                        {{ $enquiry->next_follow_up_at?->format('d M Y, h:i A') ?? 'None' }}
                    </span>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/admin/js/admission-enquiry-show.js') }}"></script>
@endpush