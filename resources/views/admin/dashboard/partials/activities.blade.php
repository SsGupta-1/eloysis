<div class="card border-0 shadow-sm h-100">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-dark">
            <i class="bi bi-bell-fill text-primary me-2"></i>Campus Enquiries & Alerts
        </h6>
        <span class="badge bg-primary-subtle text-primary">Live Status</span>
    </div>
    <div class="card-body p-3">
        <div class="list-group list-group-flush">
            {{-- Pending Admission Enquiries --}}
            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="bg-warning-subtle text-warning p-2 rounded-3 me-3 fs-5">
                        <i class="bi bi-person-lines-fill"></i>
                    </div>
                    <div>
                        <div class="fw-semibold text-dark">Pending Admission Enquiries</div>
                        <div class="text-muted small">New prospective student leads awaiting follow-up</div>
                    </div>
                </div>
                <div class="text-end">
                    <span class="badge bg-warning text-dark fs-6 px-3 py-1 rounded-pill">{{ $stats['pending_enquiries'] ?? 0 }}</span>
                    <div class="mt-1">
                        <a href="{{ route('admin.admission_enquiry.list') }}" class="small text-decoration-none">Review &rarr;</a>
                    </div>
                </div>
            </div>

            {{-- Unread Website Messages --}}
            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="bg-info-subtle text-info p-2 rounded-3 me-3 fs-5">
                        <i class="bi bi-chat-left-dots-fill"></i>
                    </div>
                    <div>
                        <div class="fw-semibold text-dark">Unread Contact Messages</div>
                        <div class="text-muted small">Inquiries received from school portal website</div>
                    </div>
                </div>
                <div class="text-end">
                    <span class="badge bg-info text-white fs-6 px-3 py-1 rounded-pill">{{ $stats['unread_messages'] ?? 0 }}</span>
                    <div class="mt-1">
                        <a href="{{ route('admin.contact-messages.index') }}" class="small text-decoration-none">Open Inbox &rarr;</a>
                    </div>
                </div>
            </div>

            {{-- Total Subjects & Curriculum --}}
            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center border-bottom-0">
                <div class="d-flex align-items-center">
                    <div class="bg-success-subtle text-success p-2 rounded-3 me-3 fs-5">
                        <i class="bi bi-journal-bookmark-fill"></i>
                    </div>
                    <div>
                        <div class="fw-semibold text-dark">Curriculum & Subjects</div>
                        <div class="text-muted small">{{ $stats['classes'] ?? 0 }} Active Classes mapped to {{ $stats['subjects'] ?? 0 }} Subjects</div>
                    </div>
                </div>
                <div class="text-end">
                    <a href="{{ route('admin.subjects.index') }}" class="btn btn-sm btn-outline-success py-1 px-3">
                        Manage
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
