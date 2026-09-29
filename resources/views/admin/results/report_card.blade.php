<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Card - {{ $user?->name ?? 'Student' }} ({{ $exam->title }})</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: #f1f5f9;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #1e293b;
        }
        .report-card-container {
            max-width: 900px;
            margin: 24px auto;
        }
        .report-card {
            background: #ffffff;
            border: 2px solid #0f172a;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
            position: relative;
        }
        .report-card::before {
            content: "";
            position: absolute;
            top: 4px;
            left: 4px;
            right: 4px;
            bottom: 4px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            pointer-events: none;
        }
        .school-header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .marks-table th {
            background: #0f172a;
            color: #fff;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px;
        }
        .marks-table td {
            font-size: 13px;
            padding: 7px 8px;
        }
        .summary-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
        }
        .signature-line {
            border-top: 1px dashed #64748b;
            padding-top: 6px;
            text-align: center;
            font-size: 12px;
            font-weight: 600;
            color: #334155;
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
            .report-card-container {
                margin: 0;
                max-width: 100%;
            }
            .report-card {
                box-shadow: none;
                border: 2px solid #000;
                padding: 20px;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>

    <div class="no-print d-flex gap-2">
        <button onclick="window.print()" class="btn btn-primary shadow">
            <i class="bi bi-printer me-1"></i> Print / Save PDF
        </button>
        <button onclick="window.close()" class="btn btn-secondary shadow">
            <i class="bi bi-x-lg"></i> Close
        </button>
    </div>

    <div class="report-card-container">
        <div class="report-card">
            {{-- School Header --}}
            <div class="school-header text-center position-relative">
                <h2 class="fw-bold text-uppercase mb-1" style="letter-spacing: 1.5px; color: #0f172a;">
                    {{ config('app.name', 'Eloysis Academy') }}
                </h2>
                <p class="small text-muted mb-2">Recognized by the Department of Education | Affiliation No: 2026-EX-9988</p>
                <div class="d-inline-block px-4 py-1 bg-dark text-white rounded-pill fw-bold text-uppercase small" style="letter-spacing: 1px;">
                    STUDENT PROGRESS REPORT & MARKS STATEMENT
                </div>
            </div>

            {{-- Examination & Student Details Grid --}}
            <div class="row g-3 mb-3" style="font-size: 13.5px;">
                <div class="col-8">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td width="130" class="text-muted">Student Name:</td>
                                <td class="fw-bold text-primary fs-6">{{ $user?->name ?? 'Student' }}</td>
                                <td width="110" class="text-muted">Roll Number:</td>
                                <td class="fw-bold text-danger fs-6">{{ $enrollment->roll_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Admission No:</td>
                                <td class="font-monospace fw-semibold">{{ $student?->admission_no ?? '-' }}</td>
                                <td class="text-muted">Class & Sec:</td>
                                <td class="fw-semibold">{{ $enrollment->studentClass?->class_name ?? '-' }} ({{ $enrollment->section?->section_name ?? '-' }})</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Father's Name:</td>
                                <td>{{ $student?->father_name ?? '-' }}</td>
                                <td class="text-muted">Academic Term:</td>
                                <td class="fw-semibold">{{ $exam->academicSession?->name ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-4">
                    <div class="p-2 border rounded bg-light text-center">
                        <small class="text-muted d-block">Examination</small>
                        <span class="fw-bold text-dark d-block">{{ $exam->title }}</span>
                        <span class="badge bg-secondary text-uppercase mt-1">{{ str_replace('_', ' ', $exam->exam_type) }}</span>
                    </div>
                </div>
            </div>

            {{-- Subject Marks Table --}}
            <div class="table-responsive mb-3">
                <table class="table table-bordered align-middle text-center marks-table mb-0">
                    <thead>
                        <tr>
                            <th width="35" rowspan="2">#</th>
                            <th rowspan="2" class="text-start">Subject</th>
                            <th colspan="2">Maximum Marks</th>
                            <th colspan="5">Marks Obtained</th>
                            <th rowspan="2" width="60">Grade</th>
                            <th rowspan="2" width="50">GP</th>
                            <th rowspan="2" width="70">Status</th>
                        </tr>
                        <tr class="bg-light text-dark small" style="font-size: 11px;">
                            <th>Total</th>
                            <th>Pass</th>
                            <th>Theory</th>
                            <th>Pract</th>
                            <th>Internal</th>
                            <th>Viva</th>
                            <th class="fw-bold bg-light text-dark">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subject_rows as $idx => $row)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td class="text-start">
                                    <span class="fw-bold text-dark">{{ $row['subject_name'] }}</span>
                                    <small class="text-muted font-monospace d-block" style="font-size: 10px;">{{ $row['subject_code'] }}</small>
                                </td>
                                <td class="fw-semibold">{{ number_format($row['total_max'], 0) }}</td>
                                <td class="text-muted">{{ number_format($row['pass_marks'], 0) }}</td>
                                <td>{{ $row['is_absent'] ? 'AB' : number_format($row['obtained_theory'], 1) }}</td>
                                <td>{{ $row['max_practical'] > 0 ? ($row['is_absent'] ? 'AB' : number_format($row['obtained_practical'], 1)) : '-' }}</td>
                                <td>{{ $row['max_internal'] > 0 ? ($row['is_absent'] ? 'AB' : number_format($row['obtained_internal'], 1)) : '-' }}</td>
                                <td>{{ $row['max_viva'] > 0 ? ($row['is_absent'] ? 'AB' : number_format($row['obtained_viva'], 1)) : '-' }}</td>
                                <td class="fw-bold fs-6 {{ ! $row['is_passed'] ? 'text-danger' : 'text-primary' }}">
                                    @if($row['is_absent'])
                                        <span class="badge bg-danger">AB</span>
                                    @elseif($row['is_exempted'])
                                        <span class="badge bg-secondary">EX</span>
                                    @else
                                        {{ number_format($row['obtained_total'], 1) }}
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold {{ $row['letter_grade'] === 'F' || $row['letter_grade'] === 'AB' ? 'text-danger' : 'text-success' }}">
                                        {{ $row['letter_grade'] }}
                                    </span>
                                </td>
                                <td class="font-monospace small">{{ number_format($row['grade_point'], 1) }}</td>
                                <td>
                                    @if($row['is_passed'])
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Pass</span>
                                    @elseif($row['is_exempted'])
                                        <span class="badge bg-light text-muted border">Exempt</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Fail</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="2" class="text-start">Grand Total</td>
                            <td>{{ number_format($total_max, 1) }}</td>
                            <td>-</td>
                            <td colspan="4" class="text-muted text-end small">Total Obtained:</td>
                            <td class="text-primary fs-6">{{ number_format($total_obtained, 1) }}</td>
                            <td class="fs-6">{{ $overall_grade }}</td>
                            <td>{{ $cgpa }}</td>
                            <td>
                                <span class="badge {{ $result_status === 'PASSED' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $result_status === 'PASSED' ? 'PASS' : 'FAIL' }}
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Summary & Analytics Cards --}}
            <div class="row g-3 mb-3">
                <div class="col-md-7">
                    <div class="summary-box h-100">
                        <div class="row g-2 text-center">
                            <div class="col-4 border-end">
                                <small class="text-muted d-block">Percentage</small>
                                <span class="fw-bold fs-5 text-primary">{{ $percentage }}%</span>
                            </div>
                            <div class="col-4 border-end">
                                <small class="text-muted d-block">CGPA / GPA</small>
                                <span class="fw-bold fs-5 text-success">{{ $cgpa }} <span class="small text-muted">/ 10</span></span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Class Rank</small>
                                <span class="fw-bold fs-5 text-dark">#{{ $class_rank }}</span>
                            </div>
                            <div class="col-12 mt-2 pt-2 border-top text-start">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Attendance Record: <strong>{{ $attendance_percentage }}%</strong></small>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $division }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="summary-box h-100">
                        <small class="text-muted fw-bold d-block mb-1 text-uppercase" style="font-size: 11px;">Teacher's Remarks</small>
                        <p class="mb-0 small fst-italic text-dark" style="line-height: 1.4;">"{{ $teacher_remarks }}"</p>
                    </div>
                </div>
            </div>

            {{-- Grading Scale Legend --}}
            <div class="p-2 mb-4 bg-light rounded border" style="font-size: 10px;">
                <div class="row text-center text-muted">
                    <div class="col-12 fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">Grading Scale (CBSE / NEP Standard)</div>
                    @foreach($grading_scale as $scale)
                        <div class="col">
                            <strong>{{ $scale['grade'] }}</strong> ({{ $scale['marks_range'] }} - GP: {{ $scale['grade_point'] }})
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Signature Blocks --}}
            <div class="row pt-4 mt-2">
                <div class="col-4">
                    <div class="signature-line">Class Teacher's Signature</div>
                </div>
                <div class="col-4">
                    <div class="signature-line">Controller of Examinations</div>
                </div>
                <div class="col-4">
                    <div class="signature-line">Principal / Head of Institution</div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
