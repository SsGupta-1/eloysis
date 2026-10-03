<div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">TOTAL ACTIVE STUDENTS</div>
                    <h3 class="fw-bold text-dark mb-0 mt-2">{{ number_format($stats['students'] ?? 0) }}</h3>
                    <div class="small text-muted mt-1">{{ $stats['classes'] ?? 0 }} Active Classes</div>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-4">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
            <a href="{{ route('admin.students.index') }}" class="small text-decoration-none mt-2 d-inline-block text-primary fw-medium">
                Manage Students &rarr;
            </a>
        </div>
    </div>
</div>

<div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">FEE COLLECTED TODAY</div>
                    <h3 class="fw-bold text-success mb-0 mt-2">₹{{ number_format($stats['fee_today'] ?? 0, 2) }}</h3>
                    <div class="small text-muted mt-1">Month: ₹{{ number_format($stats['fee_this_month'] ?? 0, 2) }}</div>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-circle fs-4">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
            <a href="{{ route('admin.fees.payments.collect') }}" class="small text-decoration-none mt-2 d-inline-block text-success fw-medium">
                Collect Fee (POS) &rarr;
            </a>
        </div>
    </div>
</div>

<div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100 border-start border-4 border-danger">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">OUTSTANDING FEE DUES</div>
                    <h3 class="fw-bold text-danger mb-0 mt-2">₹{{ number_format($stats['total_fee_due'] ?? 0, 2) }}</h3>
                    <div class="small text-danger fw-semibold mt-1">Due for collection</div>
                </div>
                <div class="bg-danger-subtle text-danger p-3 rounded-circle fs-4">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                </div>
            </div>
            <a href="{{ route('admin.fees.allocations.index') }}" class="small text-decoration-none mt-2 d-inline-block text-danger fw-medium">
                View Dues Ledger &rarr;
            </a>
        </div>
    </div>
</div>

<div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">TEACHERS & STAFF</div>
                    <h3 class="fw-bold text-dark mb-0 mt-2">{{ number_format(($stats['teachers'] ?? 0) + ($stats['staff'] ?? 0)) }}</h3>
                    <div class="small text-muted mt-1">{{ $stats['teachers'] ?? 0 }} Faculty, {{ $stats['staff'] ?? 0 }} Staff</div>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-circle fs-4">
                    <i class="bi bi-person-workspace"></i>
                </div>
            </div>
            <a href="{{ route('admin.teachers.index') }}" class="small text-decoration-none mt-2 d-inline-block text-info fw-medium">
                View Directory &rarr;
            </a>
        </div>
    </div>
</div>