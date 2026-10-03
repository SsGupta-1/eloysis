<div class="row g-4">
    {{-- Fee Collection Trend Chart --}}
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-graph-up-arrow text-primary me-2"></i>Fee Collection Trend (Last 6 Months)
                </h6>
                <span class="badge bg-success-subtle text-success border border-success-subtle">
                    This Month: ₹{{ number_format($stats['fee_this_month'] ?? 0, 2) }}
                </span>
            </div>
            <div class="card-body p-3">
                <div id="feeTrendChart" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>

    {{-- Today's Attendance Analytics --}}
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-pie-chart text-info me-2"></i>Today's Attendance
                </h6>
                <span class="badge bg-primary-subtle text-primary">{{ now()->format('d M') }}</span>
            </div>
            <div class="card-body p-3 text-center">
                <div id="attendanceDonutChart" style="min-height: 220px;"></div>
                <div class="row g-2 mt-2 pt-2 border-top small">
                    <div class="col-6 text-start">
                        <span class="text-muted"><i class="bi bi-circle-fill text-success me-1" style="font-size: 8px;"></i> Present:</span>
                        <strong class="text-success ms-1">{{ $today_attendance['present'] ?? 0 }}</strong>
                    </div>
                    <div class="col-6 text-start">
                        <span class="text-muted"><i class="bi bi-circle-fill text-danger me-1" style="font-size: 8px;"></i> Absent:</span>
                        <strong class="text-danger ms-1">{{ $today_attendance['absent'] ?? 0 }}</strong>
                    </div>
                    <div class="col-6 text-start">
                        <span class="text-muted"><i class="bi bi-circle-fill text-warning me-1" style="font-size: 8px;"></i> Late:</span>
                        <strong class="text-warning ms-1">{{ $today_attendance['late'] ?? 0 }}</strong>
                    </div>
                    <div class="col-6 text-start">
                        <span class="text-muted"><i class="bi bi-circle-fill text-info me-1" style="font-size: 8px;"></i> Leave:</span>
                        <strong class="text-info ms-1">{{ $today_attendance['leave'] ?? 0 }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
