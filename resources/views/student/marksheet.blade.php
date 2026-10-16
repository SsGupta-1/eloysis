<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marksheet - {{ $exam->title }} - {{ $user->name }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            padding: 2rem 0;
        }

        .marksheet-card {
            background: #ffffff;
            max-width: 860px;
            margin: 0 auto;
            border: 2px solid #0f172a;
            border-radius: 12px;
            padding: 2.25rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .school-header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .student-photo {
            width: 100px;
            height: 120px;
            object-fit: cover;
            border: 2px solid #0f172a;
            border-radius: 6px;
        }

        .photo-placeholder {
            width: 100px;
            height: 120px;
            border: 2px dashed #94a3b8;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 0.8rem;
            text-align: center;
            padding: 5px;
        }

        .marks-table th {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            font-size: 0.8rem;
            text-transform: uppercase;
            padding: 9px 12px;
            font-weight: 700;
        }

        .marks-table td {
            border: 1px solid #cbd5e1;
            padding: 9px 12px;
            font-size: 0.85rem;
            vertical-align: middle;
        }

        .summary-box {
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 8px;
            padding: 1rem 1.25rem;
        }

        .signature-line {
            border-top: 1px solid #0f172a;
            width: 170px;
            margin-top: 55px;
            text-align: center;
            font-size: 0.75rem;
            font-weight: 600;
        }

        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            .marksheet-card {
                box-shadow: none !important;
                border: 2px solid #000 !important;
                max-width: 100% !important;
                border-radius: 0 !important;
                padding: 1.5rem !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="container mb-4 no-print" style="max-width: 860px;">
    <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('student.results') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Results
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">
            <i class="bi bi-printer me-1"></i> Print Marksheet
        </button>
    </div>
</div>

<div class="marksheet-card">
    <!-- School Header -->
    <div class="school-header text-center position-relative">
        <h2 class="fw-bold mb-1 text-uppercase tracking-wide" style="color: #0f172a; letter-spacing: 0.05em;">
            {{ config('app.name', 'School of Excellence') }}
        </h2>
        <p class="text-muted small mb-1">Affiliated & Recognized Institution | Academic Examination Cell</p>
        <span class="badge bg-primary px-3 py-1 text-uppercase fw-semibold" style="letter-spacing: 0.05em;">
            Official Academic Report Card / Marksheet
        </span>
    </div>

    <!-- Exam & Student Particulars -->
    <div class="row g-3 mb-4 align-items-center">
        <div class="col-8 col-sm-9">
            <table class="table table-borderless table-sm mb-0" style="font-size: 0.9rem;">
                <tbody>
                    <tr>
                        <td class="text-muted fw-semibold" style="width: 35%;">Examination:</td>
                        <td class="fw-bold text-primary">{{ $exam->title }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Student Name:</td>
                        <td class="fw-bold text-dark">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Admission / Scholar No:</td>
                        <td class="fw-bold">{{ $profile?->admission_no ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Class & Section:</td>
                        <td class="fw-semibold">
                            Class {{ $enrollment?->studentClass?->class_name ?? 'N/A' }} - Section {{ $enrollment?->section?->name ?? 'N/A' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Roll Number:</td>
                        <td class="fw-semibold">{{ $enrollment?->roll_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Father's Name:</td>
                        <td class="fw-semibold">{{ $profile?->father_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Academic Session:</td>
                        <td class="fw-semibold">{{ $enrollment?->academicSession?->name ?? now()->format('Y') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-4 col-sm-3 text-end d-flex justify-content-end">
            @if($profile?->photo)
                <img src="{{ asset('storage/' . $profile->photo) }}" alt="{{ $user->name }}" class="student-photo shadow-sm">
            @else
                <div class="photo-placeholder">
                    <div>
                        <i class="bi bi-person-bounding-box d-block fs-3 mb-1"></i>
                        <span>Photo</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Subject Marks Table -->
    <h6 class="fw-bold text-uppercase mb-2 text-secondary" style="font-size: 0.8rem; letter-spacing: 0.05em;">
        Statement of Marks
    </h6>
    <div class="table-responsive mb-4">
        <table class="table marks-table mb-0">
            <thead>
                <tr class="text-center">
                    <th class="text-start" style="width: 5%;">#</th>
                    <th class="text-start" style="width: 35%;">Subject</th>
                    <th style="width: 15%;">Theory Marks</th>
                    <th style="width: 15%;">Practical Marks</th>
                    <th style="width: 15%;">Max Marks</th>
                    <th style="width: 15%;">Total Scored</th>
                </tr>
            </thead>
            <tbody>
                @forelse($marks as $index => $mark)
                    <tr class="text-center">
                        <td class="text-start text-muted">{{ $index + 1 }}</td>
                        <td class="text-start fw-bold text-dark">
                            {{ $mark->schedule?->subject?->name ?? 'Subject' }}
                            @if($mark->schedule?->subject?->subject_code)
                                <span class="text-muted fw-normal small">({{ $mark->schedule->subject->subject_code }})</span>
                            @endif
                        </td>
                        <td>{{ $mark->theory_marks ?? '-' }}</td>
                        <td>{{ $mark->practical_marks ?? '-' }}</td>
                        <td class="text-muted">{{ $mark->schedule?->max_marks ?? 100 }}</td>
                        <td class="fw-bold text-dark">{{ $mark->total_marks ?? 0 }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No mark records uploaded yet for this examination.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="table-light fw-bold text-center">
                    <td colspan="4" class="text-end text-uppercase pe-3">Grand Total:</td>
                    <td>{{ $maxTotal }}</td>
                    <td class="text-primary fs-6">{{ $totalScored }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Summary & Performance Stats -->
    <div class="summary-box mb-4">
        <div class="row g-3 text-center align-items-center">
            <div class="col-6 col-sm-3 border-end">
                <span class="text-muted small text-uppercase d-block mb-1 fw-semibold">Percentage</span>
                <span class="fs-4 fw-bold text-dark">{{ $percentage }}%</span>
            </div>
            <div class="col-6 col-sm-3 border-end">
                <span class="text-muted small text-uppercase d-block mb-1 fw-semibold">Overall Grade</span>
                <span class="fs-4 fw-bold text-primary">{{ $grade }}</span>
            </div>
            <div class="col-6 col-sm-3 border-end">
                <span class="text-muted small text-uppercase d-block mb-1 fw-semibold">Passing Standard</span>
                <span class="fs-5 fw-bold text-muted">{{ $exam->passing_marks ?? 33 }}%</span>
            </div>
            <div class="col-6 col-sm-3">
                <span class="text-muted small text-uppercase d-block mb-1 fw-semibold">Result Status</span>
                @if($passed)
                    <span class="badge bg-success-subtle text-success px-3 py-2 fs-6 fw-bold border border-success">
                        <i class="bi bi-check-circle me-1"></i> PASSED
                    </span>
                @else
                    <span class="badge bg-danger-subtle text-danger px-3 py-2 fs-6 fw-bold border border-danger">
                        <i class="bi bi-x-circle me-1"></i> FAILED
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Grading Scale Note -->
    <div class="p-2 px-3 bg-light rounded border mb-4" style="font-size: 0.75rem;">
        <strong>Grading Criteria:</strong>
        A+ (90-100%) | A (80-89%) | B+ (70-79%) | B (60-69%) | C (50-59%) | D (40-49%) | F (Below 40%)
    </div>

    <!-- Signature Row -->
    <div class="d-flex justify-content-between align-items-end pt-4">
        <div>
            <div class="signature-line">Class Teacher Signature</div>
        </div>
        <div class="text-center">
            <span class="badge bg-secondary-subtle text-secondary small px-3 py-1">School Seal</span>
        </div>
        <div>
            <div class="signature-line">Principal / Controller of Exams</div>
        </div>
    </div>

    <div class="mt-4 pt-3 border-top text-center text-muted" style="font-size: 0.7rem;">
        This document is an electronically generated academic transcript from {{ config('app.name', 'School Portal') }}. Date of issue: {{ now()->format('d M Y') }}.
    </div>
</div>

</body>
</html>
