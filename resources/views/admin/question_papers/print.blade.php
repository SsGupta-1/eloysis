<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $paper->paper_code }} - {{ $paper->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            background: #f8f9fa;
        }

        .paper-sheet {
            max-width: 850px;
            margin: 20px auto;
            background: #fff;
            padding: 40px 50px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .school-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .school-name {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .exam-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .roll-box {
            display: inline-block;
            width: 24px;
            height: 24px;
            border: 1px solid #000;
            margin-right: 2px;
            text-align: center;
            line-height: 22px;
        }

        .section-header {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 25px 0 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .question-row {
            margin-bottom: 16px;
            page-break-inside: avoid;
        }

        .q-marks {
            float: right;
            font-weight: bold;
        }

        .mcq-options {
            margin-top: 6px;
            padding-left: 20px;
        }

        .mcq-opt {
            display: inline-block;
            width: 48%;
            margin-bottom: 4px;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .paper-sheet {
                box-shadow: none;
                margin: 0;
                padding: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

{{-- Toolbar (Hidden during print) --}}
<div class="no-print bg-dark text-white p-3 shadow-sm mb-3">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-0 fw-bold"><i class="bi bi-printer me-2"></i> Print Preview — {{ $paper->title }}</h6>
            <small class="text-white-50">Paper Code: {{ $paper->paper_code }} | Set: {{ $selectedSet }}</small>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-light" onclick="window.print()">
                <i class="bi bi-printer-fill me-1"></i> Print / Save as PDF
            </button>
            <button type="button" class="btn btn-sm btn-outline-light" onclick="window.close()">
                Close
            </button>
        </div>
    </div>
</div>

<div class="paper-sheet">

    {{-- School Header --}}
    <div class="school-header">
        <div class="school-name">{{ config('app.name', 'ELOYIS ACADEMY') }}</div>
        <div class="exam-title">{{ $paper->title }}</div>
        <div class="text-muted" style="font-size: 14px;">Academic Session: {{ $paper->academicSession->name ?? date('Y').'-'.(date('Y')+1) }}</div>
    </div>

    {{-- Meta Details Row --}}
    <div class="d-flex justify-content-between align-items-end mb-3" style="font-size: 15px;">
        <div>
            <div><strong>Class:</strong> {{ $paper->academicClass->class_name ?? '-' }}</div>
            <div><strong>Subject:</strong> {{ $paper->subject->subject_name ?? '-' }}</div>
        </div>
        <div class="text-center">
            @if($paper->has_sets && $selectedSet !== 'ALL')
                <div class="fs-5 fw-bold border border-2 border-dark px-3 py-1">SET {{ $selectedSet }}</div>
            @endif
        </div>
        <div class="text-end">
            <div><strong>Time Allowed:</strong> {{ $paper->duration_minutes }} Minutes</div>
            <div><strong>Maximum Marks:</strong> {{ number_format($paper->total_marks, 0) }}</div>
        </div>
    </div>

    {{-- Roll Number Box --}}
    <div class="d-flex justify-content-between align-items-center p-2 border mb-3" style="font-size: 14px;">
        <div class="d-flex align-items-center">
            <span class="me-2 fw-bold">Student Roll No:</span>
            <div class="roll-box"></div>
            <div class="roll-box"></div>
            <div class="roll-box"></div>
            <div class="roll-box"></div>
            <div class="roll-box"></div>
            <div class="roll-box"></div>
            <div class="roll-box"></div>
            <div class="roll-box"></div>
        </div>
        <div>
            <strong>Student Name:</strong> ____________________________
        </div>
    </div>

    {{-- Instructions --}}
    @if(!empty($paper->instructions))
        <div class="mb-4" style="font-size: 13.5px; border-left: 3px solid #333; padding-left: 10px;">
            <strong>General Instructions:</strong>
            <div style="white-space: pre-line;">{{ $paper->instructions }}</div>
        </div>
    @endif

    {{-- Sections & Questions --}}
    @php
        $globalQIndex = 1;
    @endphp

    @foreach($paper->sections as $sIdx => $section)
        @php
            $sectionItems = $paper->items->where('section_id', $section->id);
            if ($paper->has_sets && $selectedSet !== 'ALL') {
                $filteredSetItems = $sectionItems->where('set_code', $selectedSet);
                if ($filteredSetItems->isNotEmpty()) {
                    $sectionItems = $filteredSetItems;
                }
            }
        @endphp

        @if($sectionItems->isNotEmpty())
            <div class="section-header">
                {{ $section->section_name }}
                @if(!empty($section->instructions))
                    <div class="text-muted fw-normal" style="font-size: 12px; text-transform: none;">({{ $section->instructions }})</div>
                @endif
            </div>

            @foreach($sectionItems as $item)
                @php $q = $item->question; @endphp
                <div class="question-row">
                    <span class="q-marks">[{{ number_format($item->marks, 0) }}]</span>
                    <div style="padding-right: 45px;">
                        <strong>Q{{ $globalQIndex++ }}.</strong> {{ $q->question_text }}
                    </div>

                    {{-- MCQ Options Layout --}}
                    @if($q->question_type === 'mcq')
                        <div class="mcq-options">
                            <div class="mcq-opt"><strong>(A)</strong> {{ $q->option_a }}</div>
                            <div class="mcq-opt"><strong>(B)</strong> {{ $q->option_b }}</div>
                            <div class="mcq-opt"><strong>(C)</strong> {{ $q->option_c }}</div>
                            <div class="mcq-opt"><strong>(D)</strong> {{ $q->option_d }}</div>
                        </div>
                    @elseif($q->question_type === 'true_false')
                        <div class="mcq-options">
                            <span class="me-4">(A) True</span>
                            <span>(B) False</span>
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    @endforeach

    <div class="text-center mt-5 pt-3 border-top" style="font-size: 13px;">
        *** END OF QUESTION PAPER ***
    </div>

</div>

</body>
</html>
