@extends('layouts.admin.master')

@section('title', 'Exam Enrollments - ' . $exam->title)

@section('content')
<div class="container-fluid">

    <x-ui.page-header
        title="Student Exam Enrollments"
        subtitle="{{ $exam->title }} ({{ $exam->academicClass?->class_name ?? 'Class' }} - {{ $exam->academicSession?->name ?? 'Session' }})">
        <x-slot:actions>
            <a href="{{ route('admin.exams.admit-cards', ['exam' => $exam->id, 'section_id' => $selectedSection]) }}" class="btn btn-outline-info me-2" target="_blank">
                <i class="bi bi-printer me-1"></i> Print Admit Cards
            </a>
            <a href="{{ route('admin.exams.show', $exam->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Exam
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Filter Bar --}}
    <x-ui.card class="mb-4">
        <form method="GET" action="{{ route('admin.exams.enrollments', $exam->id) }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Filter by Section</label>
                <select name="section_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Sections</option>
                    @foreach($sections as $id => $name)
                        <option value="{{ $id }}" {{ $selectedSection == $id ? 'selected' : '' }}>Section {{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.exams.enrollments', $exam->id) }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
            <div class="col-md-6 text-md-end">
                <span class="badge bg-light text-dark border p-2 fs-6">
                    Total Enrolled: <strong>{{ $students->count() }}</strong>
                </span>
                <span class="badge bg-success-subtle text-success border border-success-subtle p-2 fs-6 ms-2">
                    Eligible: <strong>{{ $students->where('eligibility_status', 'eligible')->count() }}</strong>
                </span>
            </div>
        </form>
    </x-ui.card>

    {{-- Students Table --}}
    <x-ui.card>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">Roll #</th>
                        <th>Student Name</th>
                        <th>Admission No</th>
                        <th>Class & Section</th>
                        <th width="200">Eligibility Status</th>
                        <th>Remarks</th>
                        <th width="100" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $enrollment)
                        @php
                            $st = $enrollment->studentEnrollment;
                            $user = $st?->student?->user;
                        @endphp
                        <tr>
                            <td class="fw-bold text-primary">{{ $st->roll_number ?? '-' }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $user?->name ?? 'Student' }}</div>
                                <small class="text-muted">{{ $user?->mobile ?? '-' }}</small>
                            </td>
                            <td>{{ $st?->student?->admission_no ?? '-' }}</td>
                            <td>
                                {{ $st->studentClass?->class_name ?? '-' }}
                                <span class="badge bg-light text-dark border ms-1">Sec {{ $st->section?->section_name ?? '-' }}</span>
                            </td>
                            <td>
                                <select class="form-select form-select-sm eligibility-select" data-enrollment-id="{{ $st->id }}">
                                    <option value="eligible" {{ $enrollment->eligibility_status === 'eligible' ? 'selected' : '' }}>Eligible</option>
                                    <option value="detained" {{ $enrollment->eligibility_status === 'detained' ? 'selected' : '' }}>Detained (Attendance)</option>
                                    <option value="fee_defaulter" {{ $enrollment->eligibility_status === 'fee_defaulter' ? 'selected' : '' }}>Fee Defaulter</option>
                                    <option value="exempted" {{ $enrollment->eligibility_status === 'exempted' ? 'selected' : '' }}>Exempted</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm eligibility-remarks" data-enrollment-id="{{ $st->id }}" value="{{ $enrollment->remarks }}" placeholder="Optional notes...">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary btn-save-eligibility" data-enrollment-id="{{ $st->id }}" title="Save">
                                    <i class="bi bi-check2"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No students enrolled in this examination.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

</div>
@endsection

@push('scripts')
<script>
    const ELIGIBILITY_URL = "{{ route('admin.exams.enrollment-eligibility', $exam->id) }}";

    $(function () {
        $('.btn-save-eligibility').on('click', function () {
            const enrollmentId = $(this).data('enrollment-id');
            const row = $(this).closest('tr');
            const status = row.find('.eligibility-select').val();
            const remarks = row.find('.eligibility-remarks').val();
            const btn = $(this);

            btn.prop('disabled', true);

            $.ajax({
                url: ELIGIBILITY_URL,
                type: 'POST',
                data: {
                    student_enrollment_id: enrollmentId,
                    eligibility_status: status,
                    remarks: remarks
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                },
                success: (res) => {
                    btn.prop('disabled', false);
                    if (typeof Toast !== 'undefined') {
                        Toast.success(res.message || 'Updated successfully.');
                    } else {
                        alert(res.message || 'Updated successfully.');
                    }
                },
                error: (xhr) => {
                    btn.prop('disabled', false);
                    alert(xhr.responseJSON?.message || 'Failed to update eligibility.');
                }
            });
        });
    });
</script>
@endpush
