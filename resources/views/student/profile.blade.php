@extends('layouts.student.master')

@section('title', 'My Profile')

@section('content')

    <div class="row g-4">
        <!-- Left Column: Student Summary Card -->
        <div class="col-lg-4">
            <div class="portal-card p-4 text-center mb-4">
                <div class="position-relative d-inline-block mb-3">
                    @if($user->profile_image)
                        <img src="{{ $user->profile_image_url }}" alt="{{ $user->name }}" class="rounded-circle border border-4 border-primary shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div class="rounded-circle border border-4 border-primary shadow-sm d-flex align-items-center justify-content-center text-white fw-bold fs-1 mx-auto" style="width: 120px; height: 120px; background: linear-gradient(135deg, #3b82f6, #6366f1);">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <span class="badge bg-success position-absolute bottom-0 end-0 rounded-circle p-2 border border-2 border-white" title="Active Student">
                        <span class="visually-hidden">Active</span>
                    </span>
                </div>

                <h4 class="fw-bold text-dark mb-1">{{ $user->name }}</h4>
                <p class="text-muted small mb-2">{{ $user->email }}</p>

                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge badge-soft-primary px-3 py-2 rounded-pill">
                        Class: {{ $enrollment?->studentClass?->class_name ?? 'N/A' }} {{ $enrollment?->section?->name ? '- '.$enrollment->section->name : '' }}
                    </span>
                    <span class="badge badge-soft-info px-3 py-2 rounded-pill">
                        Roll: {{ $enrollment?->roll_number ?? 'N/A' }}
                    </span>
                </div>

                <hr class="my-3 text-muted">

                <div class="text-start small">
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Admission No:</span>
                        <span class="fw-semibold text-dark">{{ $profile?->admission_no ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Admission Date:</span>
                        <span class="fw-semibold text-dark">{{ $profile?->admission_date ? date('d M, Y', strtotime($profile->admission_date)) : '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Mobile No:</span>
                        <span class="fw-semibold text-dark">{{ $user->mobile ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Account Status:</span>
                        <span class="badge bg-success-subtle text-success">Active</span>
                    </div>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="portal-card p-4">
                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-shield-lock text-primary"></i> Change Password
                </h6>
                <form action="{{ route('student.profile.password') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Current Password</label>
                        <input type="password" name="current_password" class="form-control form-control-sm @error('current_password') is-invalid @enderror" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">New Password</label>
                        <input type="password" name="password" class="form-control form-control-sm @error('password') is-invalid @enderror" required minlength="8">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text small" style="font-size: 0.75rem;">Minimum 8 characters.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control form-control-sm" required minlength="8">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill">
                        Update Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column: Personal & Academic Details -->
        <div class="col-lg-8">
            <!-- 1. Personal Information -->
            <div class="portal-card mb-4">
                <div class="p-3 px-4 border-bottom d-flex align-items-center gap-2">
                    <i class="bi bi-person-vcard text-primary fs-5"></i>
                    <h5 class="fw-bold mb-0">Personal Information</h5>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Full Name</span>
                            <span class="fw-semibold text-dark">{{ $user->name }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Date of Birth</span>
                            <span class="fw-semibold text-dark">{{ $profile?->dob ? date('d M, Y', strtotime($profile->dob)) : '-' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Gender</span>
                            <span class="fw-semibold text-dark text-capitalize">{{ $profile?->gender ?? '-' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Blood Group</span>
                            <span class="fw-semibold text-dark">{{ $profile?->blood_group ?? '-' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Registered Email</span>
                            <span class="fw-semibold text-dark">{{ $user->email }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Primary Contact Mobile</span>
                            <span class="fw-semibold text-dark">{{ $user->mobile ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Parents & Guardian Information -->
            <div class="portal-card mb-4">
                <div class="p-3 px-4 border-bottom d-flex align-items-center gap-2">
                    <i class="bi bi-people text-primary fs-5"></i>
                    <h5 class="fw-bold mb-0">Parents / Guardian Details</h5>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Father's Name</span>
                            <span class="fw-semibold text-dark">{{ $profile?->father_name ?? '-' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Mother's Name</span>
                            <span class="fw-semibold text-dark">{{ $profile?->mother_name ?? '-' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Guardian Name</span>
                            <span class="fw-semibold text-dark">{{ $profile?->guardian_name ?? '-' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Guardian Contact Number</span>
                            <span class="fw-semibold text-dark">{{ $profile?->guardian_mobile ?? '-' }}</span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted small d-block">Guardian Email</span>
                            <span class="fw-semibold text-dark">{{ $profile?->guardian_email ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Address Information -->
            <div class="portal-card mb-4">
                <div class="p-3 px-4 border-bottom d-flex align-items-center gap-2">
                    <i class="bi bi-geo-alt text-primary fs-5"></i>
                    <h5 class="fw-bold mb-0">Residential Address</h5>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <span class="text-muted small d-block">Street Address</span>
                            <span class="fw-semibold text-dark">{{ $profile?->address ?? '-' }}</span>
                        </div>
                        <div class="col-sm-4">
                            <span class="text-muted small d-block">City</span>
                            <span class="fw-semibold text-dark">{{ $profile?->city ?? '-' }}</span>
                        </div>
                        <div class="col-sm-4">
                            <span class="text-muted small d-block">State</span>
                            <span class="fw-semibold text-dark">{{ $profile?->state ?? '-' }}</span>
                        </div>
                        <div class="col-sm-4">
                            <span class="text-muted small d-block">PIN Code</span>
                            <span class="fw-semibold text-dark">{{ $profile?->pincode ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Enrollment & Class History -->
            <div class="portal-card">
                <div class="p-3 px-4 border-bottom d-flex align-items-center gap-2">
                    <i class="bi bi-mortarboard text-primary fs-5"></i>
                    <h5 class="fw-bold mb-0">Academic Enrollment History</h5>
                </div>
                <div class="p-3">
                    <div class="table-responsive">
                        <table class="table table-custom table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Session</th>
                                    <th>Class</th>
                                    <th>Section</th>
                                    <th>Roll Number</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($enrollmentHistory as $history)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $history->academicSession?->name ?? '-' }}</span>
                                        </td>
                                        <td>{{ $history->studentClass?->class_name ?? '-' }}</td>
                                        <td>{{ $history->section?->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $history->roll_number ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @if($history->status)
                                                <span class="badge bg-success-subtle text-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">Completed</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">No enrollment history records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
