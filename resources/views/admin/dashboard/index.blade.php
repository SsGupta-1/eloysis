@extends('layouts.admin.master')

@section('title', 'Executive Dashboard')

@section('content')
<div class="d-flex flex-column gap-4">

    {{-- Welcome Banner --}}
    <div class="card border-0 shadow-sm bg-primary text-white overflow-hidden position-relative" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
        <div class="card-body p-4 position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="badge bg-white bg-opacity-25 text-white mb-2 px-3 py-1 fw-semibold">
                        <i class="bi bi-shield-check me-1"></i> Academic Session 2026-27
                    </div>
                    <h2 class="fw-bold mb-1 text-white">
                        Welcome Back, {{ auth('admin')->user()->name }} 👋
                    </h2>
                    <p class="text-white-50 mb-0">
                        {{ now()->format('l, d F Y') }} &bull; Here is today's school operational summary and real-time fee collection.
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('admin.fees.payments.collect') }}" class="btn btn-light text-primary fw-bold shadow-sm px-4 py-2 me-2">
                        <i class="bi bi-cash-coin me-1"></i> POS Cashier
                    </a>
                    <a href="{{ route('admin.students.create') }}" class="btn btn-outline-light fw-bold px-3 py-2">
                        <i class="bi bi-person-plus me-1"></i> Admission
                    </a>
                </div>
            </div>
        </div>
        <div class="position-absolute end-0 top-0 opacity-10" style="transform: translate(15%, -20%); font-size: 14rem; pointer-events: none;">
            <i class="bi bi-mortarboard-fill text-white"></i>
        </div>
    </div>

    {{-- Top Executive Statistics --}}
    <div class="row g-3">
        @include('admin.dashboard.partials.statistics')
    </div>

    {{-- Analytics & Performance Charts --}}
    @include('admin.dashboard.partials.charts')

    {{-- Quick Action Hub --}}
    @include('admin.dashboard.partials.quick-actions')

    {{-- Realtime Data: Fee Transactions & Recent Admissions --}}
    <div class="row g-4">
        <div class="col-12 col-xl-6">
            @include('admin.dashboard.partials.recent-payments')
        </div>
        <div class="col-12 col-xl-6">
            @include('admin.dashboard.partials.latest-admissions')
        </div>
    </div>

    {{-- Academic Schedule & Alerts --}}
    <div class="row g-4">
        <div class="col-12 col-xl-6">
            @include('admin.dashboard.partials.upcoming-exams')
        </div>
        <div class="col-12 col-xl-6">
            @include('admin.dashboard.partials.activities')
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Fee Collection Trend Chart (Spline Area)
    const feeLabels = @json($charts['fee_monthly_labels'] ?? []);
    const feeData = @json($charts['fee_monthly_data'] ?? []);

    const feeOptions = {
        series: [{
            name: 'Fee Collected',
            data: feeData
        }],
        chart: {
            type: 'area',
            height: 280,
            toolbar: { show: false },
            fontFamily: 'Poppins, sans-serif'
        },
        colors: ['#0d6efd'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        xaxis: {
            categories: feeLabels,
            labels: { style: { colors: '#6c757d', fontSize: '12px' } }
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return '₹' + Number(val).toLocaleString('en-IN');
                },
                style: { colors: '#6c757d', fontSize: '12px' }
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return '₹' + Number(val).toLocaleString('en-IN', { minimumFractionDigits: 2 });
                }
            }
        },
        grid: { borderColor: '#f1f1f1' }
    };

    if (document.querySelector("#feeTrendChart")) {
        const feeChart = new ApexCharts(document.querySelector("#feeTrendChart"), feeOptions);
        feeChart.render();
    }

    // 2. Today's Attendance Donut Chart
    const presentCount = {{ $today_attendance['present'] ?? 0 }};
    const absentCount = {{ $today_attendance['absent'] ?? 0 }};
    const lateCount = {{ $today_attendance['late'] ?? 0 }};
    const leaveCount = {{ $today_attendance['leave'] ?? 0 }};
    const totalMarked = presentCount + absentCount + lateCount + leaveCount;

    const attendanceSeries = totalMarked > 0 
        ? [presentCount, absentCount, lateCount, leaveCount] 
        : [1, 0, 0, 0]; // Fallback placeholder if no attendance taken yet

    const attendanceColors = totalMarked > 0 
        ? ['#198754', '#dc3545', '#ffc107', '#0dcaf0']
        : ['#e9ecef', '#e9ecef', '#e9ecef', '#e9ecef'];

    const attendanceOptions = {
        series: attendanceSeries,
        labels: totalMarked > 0 ? ['Present', 'Absent', 'Late', 'Leave'] : ['No Data', '', '', ''],
        chart: {
            type: 'donut',
            height: 220,
            fontFamily: 'Poppins, sans-serif'
        },
        colors: attendanceColors,
        legend: { show: false },
        dataLabels: { enabled: false },
        plotOptions: {
            pie: {
                donut: {
                    size: '72%',
                    labels: {
                        show: true,
                        name: { show: true, fontSize: '12px', color: '#6c757d' },
                        value: {
                            show: true,
                            fontSize: '18px',
                            fontWeight: 700,
                            formatter: function (val) {
                                return totalMarked > 0 ? val : '0';
                            }
                        },
                        total: {
                            show: true,
                            label: 'Present Rate',
                            formatter: function () {
                                return '{{ $today_attendance["percentage"] ?? 0 }}%';
                            }
                        }
                    }
                }
            }
        },
        tooltip: {
            custom: function({ seriesIndex }) {
                if (totalMarked === 0) {
                    return '<div class="p-2 small">No attendance taken today</div>';
                }
                const labels = ['Present', 'Absent', 'Late', 'Leave'];
                return '<div class="p-2 small fw-semibold">' + labels[seriesIndex] + ': ' + attendanceSeries[seriesIndex] + '</div>';
            }
        }
    };

    if (document.querySelector("#attendanceDonutChart")) {
        const attendanceChart = new ApexCharts(document.querySelector("#attendanceDonutChart"), attendanceOptions);
        attendanceChart.render();
    }
});
</script>
@endpush