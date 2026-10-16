<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admit Card - {{ $exam->title }} - {{ $user->name }}</title>

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

        .admit-card {
            background: #ffffff;
            max-width: 820px;
            margin: 0 auto;
            border: 2px solid #0f172a;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .admit-header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .student-photo {
            width: 110px;
            height: 130px;
            object-fit: cover;
            border: 2px solid #0f172a;
            border-radius: 6px;
        }

        .photo-placeholder {
            width: 110px;
            height: 130px;
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

        .schedule-table th {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            font-size: 0.8rem;
            text-transform: uppercase;
            padding: 8px 12px;
        }

        .schedule-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            font-size: 0.85rem;
            vertical-align: middle;
        }

        .signature-line {
            border-top: 1px solid #0f172a;
            width: 160px;
            margin-top: 50px;
            text-align: center;
            font-size: 0.75rem;
            font-weight: 600;
        }

        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            .admit-card {
                box-shadow: none !important;
                border: 2px solid #000 !important;
                max-width: 100% !important;
                border-radius: 0 !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Action Bar -->
    <div class="container text-center mb-4 no-print" style="max-width: 820px;">
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('student.exams') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Exams
            </a>
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-printer me-1"></i> Print / Save Admit Card
            </button>
        </div>
    </div>

    <!-- Official Admit Card Document -->
    <div class="admit-card">
        <!-- Header -->
        <div class="admit-header text-center">
            <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                <img src="{{ asset('assets/common/images/logo.png') }}" alt="Logo" height="48" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2248%22 height=%2248%22><rect width=%2248%22 height=%2248%22 rx=%228%22 fill=%22%233b82f6%22/><text x=%2214%22 y=%2232%22 fill=%22white%22 font-family=%22sans-serif%22 font-weight=%22bold%22 font-size=%2222%22>S</text></svg>'">
                <div>
                    <h3 class="fw-bold mb-0 text-dark">{{ config('app.name', 'Eloysis School') }}</h3>
                    <small class="text-muted text-uppercase fw-semibold" style="letter-spacing: 0.05em;">Student Examination Hall Ticket / Admit Card</small>
                </div>
            </div>
            <div class="badge bg-dark text-white px-3 py-2 text-uppercase fs-6">
                {{ $exam->title }} ({{ $exam->academicSession?->name ?? date('Y') }})
            </div>
        </div>

        <!-- Student Details Grid with Photo -->
        <div class="row align-items-center mb-4 g-3">
            <div class="col-8 col-sm-9">
                <table class="table table-borderless table-sm mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted fw-medium py-1" style="width: 140px;">Candidate Name:</td>
                            <td class="fw-bold text-dark py-1 fs-6">{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-medium py-1">Admission No:</td>
                            <td class="fw-bold text-dark py-1">{{ $profile?->admission_no ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-medium py-1">Roll Number:</td>
                            <td class="fw-bold text-primary py-1 fs-6">{{ $enrollment?->roll_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-medium py-1">Class & Section:</td>
                            <td class="fw-bold text-dark py-1">{{ $enrollment?->studentClass?->class_name ?? '-' }} {{ $enrollment?->section?->name ? '- '.$enrollment->section->name : '' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-medium py-1">Father's Name:</td>
                            <td class="fw-semibold text-dark py-1">{{ $profile?->father_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-medium py-1">Date of Birth:</td>
                            <td class="fw-semibold text-dark py-1">{{ $profile?->dob ? date('d M, Y', strtotime($profile->dob)) : '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-4 col-sm-3 text-end">
                @if($user->profile_image)
                    <img src="{{ $user->profile_image_url }}" alt="{{ $user->name }}" class="student-photo">
                @else
                    <div class="photo-placeholder ms-auto">
                        Affix Recent Passport Size Photo
                    </div>
                @endif
            </div>
        </div>

        <!-- Schedule / Date Sheet -->
        <h6 class="fw-bold text-dark mb-2 border-bottom pb-1">Examination Schedule:</h6>
        <div class="table-responsive mb-4">
            <table class="table schedule-table text-center align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th class="text-start">Subject</th>
                        <th>Exam Date</th>
                        <th>Time Slot</th>
                        <th>Max Marks</th>
                        <th>Room No</th>
                        <th>Invigilator Sign</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exam->schedules as $idx => $sch)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td class="text-start fw-bold text-dark">
                                {{ $sch->subject?->subject_name ?? '-' }}
                                @if($sch->subject?->subject_code)
                                    <small class="text-muted d-block fw-normal">Code: {{ $sch->subject->subject_code }}</small>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $sch->exam_date ? date('d-m-Y (D)', strtotime($sch->exam_date)) : '-' }}</strong>
                            </td>
                            <td>
                                {{ $sch->start_time ? date('h:i A', strtotime($sch->start_time)) : '' }}
                                -
                                {{ $sch->end_time ? date('h:i A', strtotime($sch->end_time)) : '' }}
                            </td>
                            <td>{{ number_format($sch->max_marks ?? 100, 0) }}</td>
                            <td>{{ $sch->room_no ?: 'Hall 1' }}</td>
                            <td style="width: 120px;"></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted py-3">Schedule will be notified.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Instructions -->
        <div class="p-3 bg-light rounded-2 border mb-4">
            <h6 class="fw-bold mb-1 small text-dark"><i class="bi bi-exclamation-triangle me-1 text-danger"></i> Important Instructions for Candidates:</h6>
            <ol class="mb-0 ps-3 small text-muted" style="line-height: 1.45; font-size: 0.78rem;">
                <li>Candidate must carry this Admit Card to the examination hall on all days.</li>
                <li>Candidate should report to the examination hall at least 15 minutes before the scheduled time.</li>
                <li>Electronic gadgets, mobile phones, or smartwatches are strictly prohibited in the exam hall.</li>
                <li>Write your Roll Number and Admission Number clearly on the answer script.</li>
            </ol>
        </div>

        <!-- Signatures -->
        <div class="d-flex justify-content-between align-items-end pt-3">
            <div class="signature-line">
                Candidate's Signature
            </div>
            <div class="signature-line">
                Class Teacher
            </div>
            <div class="signature-line">
                Controller of Examination
            </div>
        </div>
    </div>

</body>
</html>
