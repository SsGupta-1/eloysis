<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admit Cards - {{ $exam->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #212529;
        }
        .admit-card-page {
            max-width: 900px;
            margin: 20px auto;
        }
        .admit-card {
            background: #fff;
            border: 2px solid #2b2d42;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            page-break-inside: avoid;
            page-break-after: always;
        }
        .school-header {
            border-bottom: 2px double #2b2d42;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .photo-box {
            width: 100px;
            height: 120px;
            border: 2px dashed #999;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 11px;
            color: #777;
            border-radius: 4px;
        }
        .table-schedule th, .table-schedule td {
            font-size: 12px;
            padding: 6px 10px;
        }
        .instructions-box {
            background: #fdfdfe;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 10px 14px;
            font-size: 11px;
            color: #4a5568;
        }
        .signature-box {
            margin-top: 24px;
            padding-top: 8px;
            border-top: 1px dashed #718096;
            text-align: center;
            font-size: 12px;
            font-weight: 600;
        }
        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        @media print {
            body {
                background: #fff;
            }
            .no-print {
                display: none !important;
            }
            .admit-card-page {
                margin: 0;
                max-width: 100%;
            }
            .admit-card {
                box-shadow: none;
                border: 1.5px solid #000;
                margin-bottom: 0;
            }
        }
    </style>
</head>
<body>

    <div class="no-print d-flex gap-2">
        <button onclick="window.print()" class="btn btn-primary shadow">
            <i class="bi bi-printer me-1"></i> Print All Admit Cards
        </button>
        <button onclick="window.close()" class="btn btn-secondary shadow">
            <i class="bi bi-x-lg"></i> Close
        </button>
    </div>

    <div class="admit-card-page">
        @forelse($students as $examStudent)
            @php
                $st = $examStudent->studentEnrollment;
                $user = $st?->student?->user;
            @endphp
            <div class="admit-card">
                {{-- Header --}}
                <div class="school-header text-center position-relative">
                    <h3 class="fw-bold text-uppercase mb-1" style="letter-spacing: 1px; color: #1e293b;">
                        {{ config('app.name', 'Eloysis Academy') }}
                    </h3>
                    <p class="small text-muted mb-1">Affiliated & Recognized Educational Institution</p>
                    <div class="badge bg-dark fs-6 px-3 py-1 text-uppercase">
                        EXAMINATION ADMIT CARD / HALL TICKET - {{ $exam->academicSession?->name ?? date('Y') }}
                    </div>
                </div>

                {{-- Exam & Student Details Grid --}}
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-9">
                        <table class="table table-sm table-borderless mb-0" style="font-size: 13px;">
                            <tbody>
                                <tr>
                                    <td width="140" class="text-muted">Examination:</td>
                                    <td class="fw-bold text-primary">{{ $exam->title }}</td>
                                    <td width="130" class="text-muted">Exam Code:</td>
                                    <td class="fw-bold font-monospace">{{ $exam->exam_code }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Candidate Name:</td>
                                    <td class="fw-bold text-dark">{{ $user?->name ?? 'Student' }}</td>
                                    <td class="text-muted">Roll Number:</td>
                                    <td class="fw-bold fs-6 text-danger">{{ $st->roll_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Class & Section:</td>
                                    <td class="fw-semibold">{{ $st->studentClass?->class_name ?? '-' }} (Section {{ $st->section?->section_name ?? '-' }})</td>
                                    <td class="text-muted">Admission No:</td>
                                    <td class="fw-semibold font-monospace">{{ $st->student?->admission_no ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Father's Name:</td>
                                    <td>{{ $st->student?->father_name ?? '-' }}</td>
                                    <td class="text-muted">Academic Session:</td>
                                    <td>{{ $exam->academicSession?->name ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-3 text-end">
                        <div class="photo-box ms-auto">
                            <span>Affix Recent Passport Photo</span>
                        </div>
                    </div>
                </div>

                {{-- Examination Timetable --}}
                <div class="mb-3">
                    <h6 class="fw-bold text-dark mb-2 small text-uppercase" style="letter-spacing: 0.5px;">
                        <i class="bi bi-clock-history me-1 text-primary"></i> Examination Schedule & Subjects
                    </h6>
                    <table class="table table-bordered table-schedule align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="35">#</th>
                                <th>Subject</th>
                                <th width="120">Date</th>
                                <th width="140">Time</th>
                                <th width="100">Room No</th>
                                <th width="110">Invigilator Sign</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schedules as $sIdx => $sch)
                                <tr>
                                    <td>{{ $sIdx + 1 }}</td>
                                    <td class="fw-semibold">{{ $sch->subject?->subject_name ?? '-' }}</td>
                                    <td>{{ $sch->exam_date ? $sch->exam_date->format('d/m/Y') : '-' }}</td>
                                    <td>{{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }}</td>
                                    <td>{{ $sch->room_no ?: 'Hall 1' }}</td>
                                    <td></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Timetable not available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Instructions --}}
                <div class="instructions-box mb-4">
                    <strong>Important Instructions for Candidate:</strong>
                    <ul class="mb-0 ps-3 mt-1" style="line-height: 1.4;">
                        <li>Candidate must bring this Admit Card and valid School ID Card to every examination session.</li>
                        <li>Entry to the examination hall is permitted up to 15 minutes before the scheduled start time.</li>
                        <li>Mobile phones, smartwatches, calculators, and unauthorized materials are strictly prohibited in the exam hall.</li>
                        <li>{{ $exam->instructions ?: 'Maintain strict silence and follow all invigilator instructions during the exam.' }}</li>
                    </ul>
                </div>

                {{-- Signatures --}}
                <div class="row pt-3">
                    <div class="col-4">
                        <div class="signature-box">Candidate's Signature</div>
                    </div>
                    <div class="col-4">
                        <div class="signature-box">Invigilator's Signature</div>
                    </div>
                    <div class="col-4">
                        <div class="signature-box">Controller of Examinations</div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-warning text-center py-5">
                <h4>No eligible students found to generate Admit Cards.</h4>
                <p>Please check student exam enrollments and ensure eligibility status is set to 'Eligible'.</p>
            </div>
        @endforelse
    </div>

</body>
</html>
