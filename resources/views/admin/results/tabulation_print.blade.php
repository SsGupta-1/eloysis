<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabulation Sheet - {{ $exam->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        body {
            background: #fff;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #000;
            font-size: 11px;
        }
        .tabulation-container {
            width: 100%;
            padding: 10px;
        }
        .school-header {
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .table-tabulation th, .table-tabulation td {
            padding: 4px 6px;
            font-size: 11px;
            border: 1px solid #333 !important;
        }
        .table-tabulation th {
            background: #f1f5f9 !important;
            font-weight: 700;
            text-align: center;
        }
        .no-print {
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 9999;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print d-flex gap-2">
        <button onclick="window.print()" class="btn btn-primary shadow-sm">
            <i class="bi bi-printer me-1"></i> Print Tabulation Sheet
        </button>
        <button onclick="window.close()" class="btn btn-secondary shadow-sm">
            <i class="bi bi-x-lg"></i> Close
        </button>
    </div>

    <div class="tabulation-container">
        {{-- Header --}}
        <div class="school-header text-center">
            <h3 class="fw-bold text-uppercase mb-0">{{ config('app.name', 'Eloysis Academy') }}</h3>
            <p class="mb-1 small">OFFICIAL EXAMINATION BROADSHEET & TABULATION RECORD</p>
            <div class="d-flex justify-content-between align-items-center mt-2 px-2 small">
                <span><strong>Exam:</strong> {{ $exam->title }} ({{ $exam->exam_code }})</span>
                <span><strong>Class:</strong> {{ $exam->academicClass?->class_name ?? 'All' }}</span>
                <span><strong>Academic Session:</strong> {{ $exam->academicSession?->name ?? '-' }}</span>
                <span><strong>Date Generated:</strong> {{ date('d M, Y') }}</span>
            </div>
        </div>

        {{-- Summary Stats Bar --}}
        <div class="d-flex justify-content-between p-2 mb-2 bg-light border small fw-semibold">
            <span>Total Appeared: {{ $summary['total_students'] }}</span>
            <span>Passed: {{ $summary['passed_students'] }} ({{ $summary['pass_percentage'] }}%)</span>
            <span>Compartment: {{ $summary['compartment_students'] }}</span>
            <span>Failed: {{ $summary['failed_students'] }}</span>
            <span>Highest: {{ number_format($summary['highest_marks'], 1) }}</span>
            <span>Average: {{ $summary['average_percentage'] }}%</span>
        </div>

        {{-- Tabulation Table --}}
        <table class="table table-bordered table-tabulation align-middle text-center mb-4">
            <thead>
                <tr>
                    <th rowspan="2" width="35">Roll</th>
                    <th rowspan="2" class="text-start" style="min-width: 140px;">Student Name</th>
                    <th rowspan="2" width="40">Sec</th>
                    @foreach($schedules as $sch)
                        <th colspan="2">
                            {{ $sch->subject?->subject_name ?? 'Subject' }}
                            <small class="d-block fw-normal">({{ (float)$sch->total_marks }}M)</small>
                        </th>
                    @endforeach
                    <th rowspan="2" width="60">Total</th>
                    <th rowspan="2" width="50">%</th>
                    <th rowspan="2" width="45">Grd</th>
                    <th rowspan="2" width="60">Result</th>
                    <th rowspan="2" width="45">Rank</th>
                </tr>
                <tr>
                    @foreach($schedules as $sch)
                        <th>Mrk</th>
                        <th>Grd</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($students as $st)
                    <tr>
                        <td class="fw-bold">{{ $st['roll_number'] }}</td>
                        <td class="text-start fw-semibold">{{ $st['name'] }}</td>
                        <td>{{ $st['section_name'] }}</td>

                        @foreach($schedules as $sch)
                            @php
                                $sub = $st['subjects'][$sch->id] ?? null;
                                $obtained = $sub ? $sub['total_obtained'] : 0;
                                $grade = $sub ? $sub['letter_grade'] : '-';
                            @endphp
                            <td>
                                @if($sub && $sub['is_absent'])
                                    AB
                                @elseif($sub && $sub['is_exempted'])
                                    EX
                                @else
                                    {{ number_format($obtained, 1) }}
                                @endif
                            </td>
                            <td>{{ $grade }}</td>
                        @endforeach

                        <td class="fw-bold">{{ number_format($st['total_obtained'], 1) }}</td>
                        <td class="fw-bold">{{ $st['percentage'] }}%</td>
                        <td>{{ $st['grade'] }}</td>
                        <td class="fw-bold">{{ $st['result_status'] }}</td>
                        <td class="fw-bold">#{{ $st['class_rank'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Signatures --}}
        <div class="row pt-5 mt-4 text-center">
            <div class="col-4">
                <div style="border-top: 1px solid #000; padding-top: 5px;">Tabulated By (Teacher / Exam Officer)</div>
            </div>
            <div class="col-4">
                <div style="border-top: 1px solid #000; padding-top: 5px;">Verified By (Examination In-Charge)</div>
            </div>
            <div class="col-4">
                <div style="border-top: 1px solid #000; padding-top: 5px;">Approved By (Principal / Headmaster)</div>
            </div>
        </div>
    </div>

</body>
</html>
