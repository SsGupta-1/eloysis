<div class="card border-0 shadow-sm h-100">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-dark">
            <i class="bi bi-journal-check text-warning me-2"></i>Upcoming Examinations
        </h6>
        <a href="{{ route('admin.exams.index') }}" class="btn btn-sm btn-outline-primary py-0 px-2 small">
            View All &rarr;
        </a>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Exam Title</th>
                    <th>Class</th>
                    <th>Schedule</th>
                    <th>Status</th>
                    <th class="text-center" width="60">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($upcoming_exams as $exam)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $exam->title }}</div>
                            <span class="badge bg-secondary-subtle text-secondary" style="font-size: 10px;">{{ strtoupper(str_replace('_', ' ', $exam->exam_type ?? 'EXAM')) }}</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $exam->academicClass?->class_name ?? 'All Classes' }}
                            </span>
                        </td>
                        <td>
                            <div class="text-dark fw-medium">
                                {{ $exam->start_date ? $exam->start_date->format('d M Y') : ($exam->start_at ? $exam->start_at->format('d M Y') : 'TBA') }}
                            </div>
                            @if($exam->duration_minutes)
                                <div class="text-muted" style="font-size: 11px;">{{ $exam->duration_minutes }} Mins</div>
                            @endif
                        </td>
                        <td>
                            @if($exam->is_published)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Published</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Draft</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.exams.show', $exam->id) }}" class="btn btn-xs btn-outline-primary py-0 px-1" title="View Exam Details">
                                <i class="bi bi-arrow-right-short fs-6"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No scheduled examinations found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
