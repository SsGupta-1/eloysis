@extends('layouts.admin.master')

@section('title', 'Broadsheet & Tabulation - ' . $exam->title)

@section('content')
<div class="container-fluid">

    <x-ui.page-header
        title="Result Tabulation & Broadsheet"
        subtitle="{{ $exam->title }} ({{ $exam->academicClass?->class_name ?? 'Class' }} - {{ $exam->academicSession?->name ?? 'Session' }})">
        <x-slot:actions>
            <div class="form-check form-switch d-inline-block me-3 align-middle">
                <input class="form-check-input btn-publish-switch" type="checkbox" id="publishSwitch" data-id="{{ $exam->id }}" {{ $exam->is_published ? 'checked' : '' }} style="width: 2.5em; height: 1.3em;">
                <label class="form-check-label fw-bold {{ $exam->is_published ? 'text-success' : 'text-muted' }}" for="publishSwitch" id="publishLabel">
                    {{ $exam->is_published ? 'Published to Students' : 'Unpublished (Draft)' }}
                </label>
            </div>
            <a href="{{ route('admin.results.tabulation-print', ['exam' => $exam->id, 'section_id' => $selectedSection]) }}" class="btn btn-outline-info me-2" target="_blank">
                <i class="bi bi-printer me-1"></i> Print Tabulation Sheet
            </a>
            <a href="{{ route('admin.results.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Results
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Performance Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm bg-white border-start border-4 border-primary h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase fw-semibold">Appeared / Total</small>
                    <h3 class="fw-bold mb-0 text-dark">{{ $summary['total_students'] }} <span class="fs-6 text-muted fw-normal">Students</span></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm bg-white border-start border-4 border-success h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase fw-semibold">Passed (Pass Rate)</small>
                    <h3 class="fw-bold mb-0 text-success">{{ $summary['passed_students'] }} <span class="fs-6 fw-bold">({{ $summary['pass_percentage'] }}%)</span></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm bg-white border-start border-4 border-warning h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase fw-semibold">Compartment / Backlog</small>
                    <h3 class="fw-bold mb-0 text-warning">{{ $summary['compartment_students'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm bg-white border-start border-4 border-danger h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase fw-semibold">Failed Students</small>
                    <h3 class="fw-bold mb-0 text-danger">{{ $summary['failed_students'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Section Switcher --}}
    <x-ui.card class="mb-4">
        <form method="GET" action="{{ route('admin.results.exam', $exam->id) }}" class="row g-3 align-items-end">
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
                <a href="{{ route('admin.results.exam', $exam->id) }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
            <div class="col-md-6 text-md-end">
                <span class="badge bg-light text-dark border p-2 fs-6">
                    Highest Marks: <strong class="text-success">{{ number_format($summary['highest_marks'], 1) }}</strong>
                </span>
                <span class="badge bg-light text-dark border p-2 fs-6 ms-2">
                    Class Avg: <strong class="text-primary">{{ $summary['average_percentage'] }}%</strong>
                </span>
            </div>
        </form>
    </x-ui.card>

    {{-- Broadsheet Matrix Table --}}
    <x-ui.card>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 text-center" style="font-size: 13px;">
                <thead class="table-dark align-middle">
                    <tr>
                        <th rowspan="2" width="40">Roll #</th>
                        <th rowspan="2" class="text-start" style="min-width: 180px;">Student Name</th>
                        <th rowspan="2" width="50">Sec</th>
                        @foreach($schedules as $sch)
                            <th colspan="2" class="border-start">
                                {{ $sch->subject?->subject_name ?? 'Subject' }}
                                <small class="d-block fw-normal text-white-50">({{ (float)$sch->total_marks }}M / Pass: {{ (float)$sch->passing_marks }}M)</small>
                            </th>
                        @endforeach
                        <th rowspan="2" width="80" class="border-start">Grand Total</th>
                        <th rowspan="2" width="60">%</th>
                        <th rowspan="2" width="50">Grade</th>
                        <th rowspan="2" width="90">Result</th>
                        <th rowspan="2" width="50">Rank</th>
                        <th rowspan="2" width="90" class="no-print">Action</th>
                    </tr>
                    <tr class="table-secondary text-dark small">
                        @foreach($schedules as $sch)
                            <th>Marks</th>
                            <th>Grd</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $st)
                        <tr class="{{ $st['result_status'] === 'FAIL' ? 'table-danger-subtle' : ($st['result_status'] === 'COMPARTMENT' ? 'table-warning-subtle' : '') }}">
                            <td class="fw-bold text-primary">{{ $st['roll_number'] }}</td>
                            <td class="text-start">
                                <div class="fw-semibold text-dark">{{ $st['name'] }}</div>
                                <small class="text-muted font-monospace">{{ $st['admission_no'] }}</small>
                            </td>
                            <td>{{ $st['section_name'] }}</td>

                            @foreach($schedules as $sch)
                                @php
                                    $subData = $st['subjects'][$sch->id] ?? null;
                                    $isPass = $subData ? $subData['is_passed'] : false;
                                    $obtained = $subData ? $subData['total_obtained'] : 0;
                                    $grade = $subData ? $subData['letter_grade'] : '-';
                                @endphp
                                <td class="border-start fw-semibold {{ ! $isPass ? 'text-danger' : 'text-dark' }}">
                                    @if($subData && $subData['is_absent'])
                                        <span class="badge bg-danger">AB</span>
                                    @elseif($subData && $subData['is_exempted'])
                                        <span class="badge bg-secondary">EX</span>
                                    @else
                                        {{ number_format($obtained, 1) }}
                                    @endif
                                </td>
                                <td>
                                    <span class="small fw-bold {{ $grade === 'F' || $grade === 'AB' ? 'text-danger' : 'text-success' }}">{{ $grade }}</span>
                                </td>
                            @endforeach

                            <td class="border-start fw-bold text-primary fs-6">
                                {{ number_format($st['total_obtained'], 1) }}
                                <small class="d-block text-muted" style="font-size: 10px;">/ {{ (float)$st['total_max'] }}</small>
                            </td>
                            <td class="fw-bold">{{ $st['percentage'] }}%</td>
                            <td>
                                <span class="badge bg-dark">{{ $st['grade'] }}</span>
                            </td>
                            <td>
                                @if($st['result_status'] === 'PASS')
                                    <span class="badge bg-success">PASS</span>
                                @elseif($st['result_status'] === 'COMPARTMENT')
                                    <span class="badge bg-warning text-dark">COMP</span>
                                @else
                                    <span class="badge bg-danger">FAIL</span>
                                @endif
                            </td>
                            <td class="fw-bold text-dark">
                                @if($st['class_rank'] == 1)
                                    <span class="badge bg-warning text-dark"><i class="bi bi-trophy-fill me-1"></i>#1</span>
                                @else
                                    #{{ $st['class_rank'] }}
                                @endif
                            </td>
                            <td class="no-print">
                                <a href="{{ route('admin.results.student', ['student' => $st['student_enrollment_id'], 'exam_id' => $exam->id]) }}" class="btn btn-sm btn-outline-primary" target="_blank" title="View / Print Marksheet">
                                    <i class="bi bi-file-earmark-person"></i> Card
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 9 + ($schedules->count() * 2) }}" class="text-center py-5 text-muted">
                                No student marks recorded for this examination yet.
                            </td>
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
    const PUBLISH_TOGGLE_URL = "{{ route('admin.results.publish', $exam->id) }}";

    $(function () {
        $('.btn-publish-switch').on('change', function () {
            const isChecked = $(this).is(':checked');
            const label = $('#publishLabel');

            $.ajax({
                url: PUBLISH_TOGGLE_URL,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                },
                success: (res) => {
                    if (res.data.is_published) {
                        label.text('Published to Students').removeClass('text-muted').addClass('text-success');
                    } else {
                        label.text('Unpublished (Draft)').removeClass('text-success').addClass('text-muted');
                    }

                    if (typeof Toast !== 'undefined') {
                        Toast.success(res.message);
                    }
                },
                error: (xhr) => {
                    alert(xhr.responseJSON?.message || 'Failed to update publication status.');
                }
            });
        });
    });
</script>
@endpush
