@extends('layouts.admin.master')

@section('title', 'Question Paper - ' . $paper->title)

@section('content')
<div class="container-fluid">

    <x-ui.page-header
        title="{{ $paper->title }}"
        subtitle="Code: {{ $paper->paper_code }} | {{ $paper->academicClass->class_name ?? 'Class' }} - {{ $paper->subject->subject_name ?? 'Subject' }}">
        <x-slot:actions>
            <a href="{{ route('admin.question-papers.print', $paper->id) }}" target="_blank" class="btn btn-outline-dark me-2">
                <i class="bi bi-printer me-1"></i> Print / Export PDF
            </a>

            @if(!$paper->is_locked)
                <button type="button" class="btn btn-outline-success me-2" data-bs-toggle="modal" data-bs-target="#generateSetsModal">
                    <i class="bi bi-shuffle me-1"></i> Generate Sets (A/B/C/D)
                </button>
            @endif

            <button type="button" class="btn btn-outline-{{ $paper->is_locked ? 'warning' : 'danger' }} me-2" id="btnTogglePaperLock" data-id="{{ $paper->id }}">
                <i class="bi bi-{{ $paper->is_locked ? 'unlock' : 'lock' }} me-1"></i> {{ $paper->is_locked ? 'Unlock Paper' : 'Lock Paper' }}
            </button>

            <button type="button" class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#approvalModal">
                <i class="bi bi-check2-circle me-1"></i> Approval Workflow
            </button>

            <a href="{{ route('admin.question-papers.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Meta Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3">
                        <i class="bi bi-award fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold">Total Marks</small>
                        <h4 class="mb-0 fw-bold">{{ number_format($paper->total_marks, 0) }} Marks</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-info-subtle text-info p-3 rounded-circle me-3">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold">Duration</small>
                        <h4 class="mb-0 fw-bold">{{ $paper->duration_minutes }} Min</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-warning-subtle text-warning p-3 rounded-circle me-3">
                        <i class="bi bi-patch-question fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold">Questions / Sections</small>
                        <h4 class="mb-0 fw-bold">{{ $paper->items->where('set_code', 'ALL')->count() ?: $paper->items->count() }} Q ({{ $paper->sections->count() }} Sec)</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-{{ $paper->approval_status === 'approved' ? 'success' : ($paper->approval_status === 'rejected' ? 'danger' : 'secondary') }}-subtle text-{{ $paper->approval_status === 'approved' ? 'success' : ($paper->approval_status === 'rejected' ? 'danger' : 'dark') }} p-3 rounded-circle me-3">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold">Approval Status</small>
                        <h5 class="mb-0 fw-bold text-uppercase">{{ str_replace('_', ' ', $paper->approval_status) }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content & Tabs --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white">
            <ul class="nav nav-tabs card-header-tabs" id="paperTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="preview-tab" data-bs-toggle="tab" data-bs-target="#previewTabPane" type="button">
                        <i class="bi bi-file-earmark-text me-1"></i> Paper Content & Structure
                    </button>
                </li>
                @if($paper->has_sets && !empty($paper->set_names))
                    <li class="nav-item">
                        <button class="nav-link" id="sets-tab" data-bs-toggle="tab" data-bs-target="#setsTabPane" type="button">
                            <i class="bi bi-collection me-1"></i> Sets ({{ implode(', ', $paper->set_names) }})
                        </button>
                    </li>
                @endif
                <li class="nav-item">
                    <button class="nav-link" id="audit-tab" data-bs-toggle="tab" data-bs-target="#auditTabPane" type="button">
                        <i class="bi bi-clock-history me-1"></i> Confidential Audit Trail ({{ $paper->audits->count() }})
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="paperTabsContent">

                {{-- Tab 1: Preview Content --}}
                <div class="tab-pane fade show active" id="previewTabPane">
                    {{-- Instructions Box --}}
                    @if(!empty($paper->instructions))
                        <div class="alert alert-light border mb-4">
                            <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-1"></i> General Instructions:</h6>
                            <div class="small" style="white-space: pre-line;">{{ $paper->instructions }}</div>
                        </div>
                    @endif

                    {{-- Sections Loop --}}
                    @forelse($paper->sections as $section)
                        @php
                            $sectionItems = $paper->items->where('section_id', $section->id)->where('set_code', 'ALL');
                            if ($sectionItems->isEmpty()) {
                                $sectionItems = $paper->items->where('section_id', $section->id);
                            }
                        @endphp
                        <div class="card border mb-4 shadow-none">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold text-dark">
                                    {{ $section->section_name }}
                                </h6>
                                <span class="badge bg-primary">
                                    {{ $sectionItems->count() }} Questions | {{ $sectionItems->sum('marks') }} Marks
                                </span>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    @forelse($sectionItems as $idx => $item)
                                        @php $q = $item->question; @endphp
                                        <div class="list-group-item p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="fw-bold text-primary">
                                                    Q{{ $idx + 1 }}.
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-success-subtle text-success border me-1">{{ $item->marks }} Marks</span>
                                                    <span class="badge bg-light text-dark border">{{ strtoupper($q->difficulty_level) }}</span>
                                                </div>
                                            </div>
                                            <div class="fs-6 mb-2">{{ $q->question_text }}</div>

                                            {{-- MCQ Options --}}
                                            @if($q->question_type === 'mcq')
                                                <div class="row g-2 mb-2">
                                                    <div class="col-md-6"><span class="badge bg-light text-dark border me-1">A</span> {{ $q->option_a }}</div>
                                                    <div class="col-md-6"><span class="badge bg-light text-dark border me-1">B</span> {{ $q->option_b }}</div>
                                                    <div class="col-md-6"><span class="badge bg-light text-dark border me-1">C</span> {{ $q->option_c }}</div>
                                                    <div class="col-md-6"><span class="badge bg-light text-dark border me-1">D</span> {{ $q->option_d }}</div>
                                                </div>
                                            @elseif($q->question_type === 'true_false')
                                                <div class="d-flex gap-3 mb-2">
                                                    <span><i class="bi bi-circle me-1"></i> True</span>
                                                    <span><i class="bi bi-circle me-1"></i> False</span>
                                                </div>
                                            @endif

                                            @if(!empty($q->explanation))
                                                <div class="small text-muted bg-light p-2 rounded border mt-2">
                                                    <strong>Answer / Solution:</strong> {{ $q->explanation }}
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="p-4 text-center text-muted">No questions found in this section.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-file-earmark-x fs-1 d-block mb-2"></i>
                            No sections configured for this question paper yet.
                        </div>
                    @endforelse
                </div>

                {{-- Tab 2: Sets View --}}
                @if($paper->has_sets && !empty($paper->set_names))
                    <div class="tab-pane fade" id="setsTabPane">
                        <div class="row g-3">
                            @foreach($paper->set_names as $setName)
                                @php
                                    $setQuestions = $paper->items->where('set_code', $setName);
                                @endphp
                                <div class="col-md-6">
                                    <div class="card border shadow-none h-100">
                                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 fw-bold text-primary">
                                                <i class="bi bi-journal-text me-1"></i> Set {{ $setName }}
                                            </h6>
                                            <a href="{{ route('admin.question-papers.print', ['question_paper' => $paper->id, 'set' => $setName]) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                                <i class="bi bi-printer me-1"></i> Print Set {{ $setName }}
                                            </a>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="list-group list-group-flush" style="max-height: 350px; overflow-y: auto;">
                                                @forelse($setQuestions as $sIdx => $sItem)
                                                    <div class="list-group-item p-2 small">
                                                        <span class="fw-bold text-primary">Q{{ $sIdx + 1 }}.</span> {{ \Illuminate\Support\Str::limit($sItem->question->question_text, 65) }}
                                                        <span class="badge bg-light text-dark border float-end">{{ $sItem->marks }}M</span>
                                                    </div>
                                                @empty
                                                    <div class="p-3 text-center text-muted small">No items generated for Set {{ $setName }}.</div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Tab 3: Audit Trail --}}
                <div class="tab-pane fade" id="auditTabPane">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Timestamp</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paper->audits as $audit)
                                    <tr>
                                        <td>{{ $audit->created_at->format('d M Y, h:i A') }}</td>
                                        <td class="fw-semibold">{{ $audit->user->name ?? 'System' }}</td>
                                        <td><span class="badge bg-secondary text-uppercase">{{ str_replace('_', ' ', $audit->action) }}</span></td>
                                        <td>{{ $audit->details ?? '-' }}</td>
                                        <td><code>{{ $audit->ip_address ?? '-' }}</code></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No audit logs recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

{{-- Approval Modal --}}
<div class="modal fade" id="approvalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-shield-check me-2"></i> Approval Workflow</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="approvalForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Action Status</label>
                        <select name="status" id="approval_status_select" class="form-select" required>
                            <option value="pending_approval" {{ $paper->approval_status === 'pending_approval' ? 'selected' : '' }}>Submit for Approval</option>
                            <option value="approved" {{ $paper->approval_status === 'approved' ? 'selected' : '' }}>Approve Question Paper</option>
                            <option value="rejected" {{ $paper->approval_status === 'rejected' ? 'selected' : '' }}>Reject with Remarks</option>
                            <option value="draft" {{ $paper->approval_status === 'draft' ? 'selected' : '' }}>Revert to Draft</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Remarks / Review Notes</label>
                        <textarea name="remarks" id="approval_remarks" class="form-control" rows="3" placeholder="Enter review remarks..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSubmitApproval" data-id="{{ $paper->id }}">
                    Submit Decision
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Generate Sets Modal --}}
<div class="modal fade" id="generateSetsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-shuffle me-2"></i> Generate Multi-Sets (A, B, C, D)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3">
                    Select which sets to generate and whether to shuffle question ordering to prevent copying.
                </p>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Select Sets to Generate:</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="set_names[]" value="A" id="setA" checked>
                            <label class="form-check-label fw-bold" for="setA">Set A</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="set_names[]" value="B" id="setB" checked>
                            <label class="form-check-label fw-bold" for="setB">Set B</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="set_names[]" value="C" id="setC" checked>
                            <label class="form-check-label fw-bold" for="setC">Set C</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="set_names[]" value="D" id="setD" checked>
                            <label class="form-check-label fw-bold" for="setD">Set D</label>
                        </div>
                    </div>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="modalShuffleQuestions" checked>
                    <label class="form-check-label" for="modalShuffleQuestions">Randomize Question Sequence per Set</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="btnExecuteGenerateSets" data-id="{{ $paper->id }}">
                    Generate Sets Now
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const QP_LOCK_URL = "{{ url('admin/question-papers') }}/:id/lock";
    const QP_APPROVAL_URL = "{{ url('admin/question-papers') }}/:id/approval";
    const QP_GENERATE_SETS_URL = "{{ url('admin/question-papers') }}/:id/generate-sets";
</script>
<script src="{{ asset('assets/admin/js/question-papers.js') }}"></script>
@endpush
