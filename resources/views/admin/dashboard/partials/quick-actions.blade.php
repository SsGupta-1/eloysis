<div class="card border-0 shadow-sm h-100">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0 fw-bold text-dark">
            <i class="bi bi-lightning-charge-fill text-warning me-2"></i>Quick Action Hub
        </h6>
    </div>
    <div class="card-body p-3">
        <div class="row g-3">
            {{-- Quick Fee Collect --}}
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.fees.payments.collect') }}" class="btn btn-light w-100 p-3 text-center border h-100 d-flex flex-column align-items-center justify-content-center text-decoration-none shadow-sm-hover transition-all">
                    <div class="rounded-circle bg-success-subtle text-success p-3 mb-2 fs-5">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <span class="fw-semibold text-dark small">Collect Fees (POS)</span>
                </a>
            </div>

            {{-- New Student Admission --}}
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.students.create') }}" class="btn btn-light w-100 p-3 text-center border h-100 d-flex flex-column align-items-center justify-content-center text-decoration-none shadow-sm-hover transition-all">
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 mb-2 fs-5">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <span class="fw-semibold text-dark small">New Admission</span>
                </a>
            </div>

            {{-- Student Attendance --}}
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.attendance.index') }}" class="btn btn-light w-100 p-3 text-center border h-100 d-flex flex-column align-items-center justify-content-center text-decoration-none shadow-sm-hover transition-all">
                    <div class="rounded-circle bg-info-subtle text-info p-3 mb-2 fs-5">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                    <span class="fw-semibold text-dark small">Mark Attendance</span>
                </a>
            </div>

            {{-- Fee Allocation --}}
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.fees.allocations.create') }}" class="btn btn-light w-100 p-3 text-center border h-100 d-flex flex-column align-items-center justify-content-center text-decoration-none shadow-sm-hover transition-all">
                    <div class="rounded-circle bg-danger-subtle text-danger p-3 mb-2 fs-5">
                        <i class="bi bi-wallet-fill"></i>
                    </div>
                    <span class="fw-semibold text-dark small">Allocate Fee</span>
                </a>
            </div>

            {{-- Create Exam --}}
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.exams.create') }}" class="btn btn-light w-100 p-3 text-center border h-100 d-flex flex-column align-items-center justify-content-center text-decoration-none shadow-sm-hover transition-all">
                    <div class="rounded-circle bg-warning-subtle text-warning p-3 mb-2 fs-5">
                        <i class="bi bi-file-earmark-text-fill"></i>
                    </div>
                    <span class="fw-semibold text-dark small">Schedule Exam</span>
                </a>
            </div>

            {{-- Exam Results --}}
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.results.index') }}" class="btn btn-light w-100 p-3 text-center border h-100 d-flex flex-column align-items-center justify-content-center text-decoration-none shadow-sm-hover transition-all">
                    <div class="rounded-circle bg-purple-subtle text-purple p-3 mb-2 fs-5" style="background-color: #f3e8ff; color: #7e22ce;">
                        <i class="bi bi-trophy-fill"></i>
                    </div>
                    <span class="fw-semibold text-dark small">Exam Results</span>
                </a>
            </div>

            {{-- Admission Enquiries --}}
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.admission-enquiry.index') }}" class="btn btn-light w-100 p-3 text-center border h-100 d-flex flex-column align-items-center justify-content-center text-decoration-none shadow-sm-hover transition-all">
                    <div class="rounded-circle bg-teal-subtle text-teal p-3 mb-2 fs-5" style="background-color: #ccfbf1; color: #0f766e;">
                        <i class="bi bi-telephone-inbound-fill"></i>
                    </div>
                    <span class="fw-semibold text-dark small">Enquiries</span>
                </a>
            </div>

            {{-- System Settings / Logs --}}
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.logs.index') }}" class="btn btn-light w-100 p-3 text-center border h-100 d-flex flex-column align-items-center justify-content-center text-decoration-none shadow-sm-hover transition-all">
                    <div class="rounded-circle bg-secondary-subtle text-secondary p-3 mb-2 fs-5">
                        <i class="bi bi-activity"></i>
                    </div>
                    <span class="fw-semibold text-dark small">Audit Logs</span>
                </a>
            </div>
        </div>
    </div>
</div>
