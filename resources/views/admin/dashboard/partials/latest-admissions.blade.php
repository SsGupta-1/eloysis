<div class="card border-0 shadow-sm h-100">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-dark">
            <i class="bi bi-person-plus text-primary me-2"></i>Latest Student Admissions
        </h6>
        <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-primary py-0 px-2 small">
            View All &rarr;
        </a>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Student</th>
                    <th>Class / Sec</th>
                    <th>Adm No</th>
                    <th>Roll No</th>
                    <th class="text-center" width="60">View</th>
                </tr>
            </thead>
            <tbody>
                @forelse($latest_students as $enrollment)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-2" style="width: 32px; height: 32px; font-size: 12px;">
                                    {{ strtoupper(substr($enrollment->student?->user?->name ?? 'S', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $enrollment->student?->user?->name ?? 'N/A' }}</div>
                                    <div class="text-muted" style="font-size: 11px;">{{ $enrollment->student?->user?->email ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $enrollment->studentClass?->class_name ?? '-' }} ({{ $enrollment->section?->section_name ?? '-' }})
                            </span>
                        </td>
                        <td class="fw-semibold text-secondary">
                            {{ $enrollment->student?->admission_no ?? '-' }}
                        </td>
                        <td>
                            {{ $enrollment->roll_number ?? '-' }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.students.show', $enrollment->id) }}" class="btn btn-xs btn-outline-info py-0 px-1" title="View Student Profile">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                      @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No student admissions recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>