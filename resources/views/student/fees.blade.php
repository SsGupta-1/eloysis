@extends('layouts.student.master')

@section('title', 'Fees & Payments')

@section('content')
<div class="row g-4">
    <!-- Header -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h3 class="fw-bold mb-1" style="color: #0f172a;">Fee Records & Invoices</h3>
                    <p class="text-muted mb-0 small">
                        Track your academic fee structure, discounts, due dates, and payment history.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold">
                        <i class="bi bi-calendar-check me-1"></i> Session: {{ $enrollment?->academicSession?->name ?? now()->format('Y') }}
                    </span>
                    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-printer me-1"></i> Print Statement
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 3 Summary Stat Cards -->
    <div class="col-12">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-primary border-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold d-block">Total Allocated</span>
                            <h3 class="fw-bold text-dark mt-1 mb-0">₹{{ number_format($summary['total_allocated'], 2) }}</h3>
                            <span class="text-muted small">Applicable school fees</span>
                        </div>
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-wallet2 fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-success border-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold d-block">Total Paid</span>
                            <h3 class="fw-bold text-success mt-1 mb-0">₹{{ number_format($summary['total_paid'], 2) }}</h3>
                            <span class="text-success small fw-semibold"><i class="bi bi-check2-circle"></i> Successfully cleared</span>
                        </div>
                        <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-danger border-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold d-block">Balance Due</span>
                            <h3 class="fw-bold {{ $summary['total_due'] > 0 ? 'text-danger' : 'text-success' }} mt-1 mb-0">
                                ₹{{ number_format($summary['total_due'], 2) }}
                            </h3>
                            <span class="{{ $summary['total_due'] > 0 ? 'text-danger' : 'text-success' }} small fw-semibold">
                                {{ $summary['total_due'] > 0 ? 'Pending payment' : 'All clear!' }}
                            </span>
                        </div>
                        <div class="rounded-circle {{ $summary['total_due'] > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-receipt fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Allocations Breakdown Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-card-checklist text-primary me-2"></i>Fee Schedule & Invoices
                </h5>
                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">
                    {{ $allocations->count() }} Record(s)
                </span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                        <tr>
                            <th class="ps-4" style="width: 5%;">#</th>
                            <th style="width: 25%;">Fee Head / Description</th>
                            <th style="width: 12%;">Due Date</th>
                            <th class="text-end" style="width: 10%;">Base Fee</th>
                            <th class="text-end" style="width: 10%;">Discount</th>
                            <th class="text-end" style="width: 10%;">Fine</th>
                            <th class="text-end" style="width: 10%;">Net Payable</th>
                            <th class="text-end" style="width: 10%;">Paid</th>
                            <th class="text-end" style="width: 10%;">Balance</th>
                            <th class="text-center pe-4" style="width: 12%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allocations as $index => $alloc)
                            @php
                                $net = $alloc->net_payable;
                                $balance = $alloc->remaining_balance;
                                $isOverdue = $alloc->due_date && $alloc->due_date->isPast() && $balance > 0;
                            @endphp
                            <tr>
                                <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">
                                        {{ $alloc->feeHead?->name ?? ($alloc->title ?: 'Academic Fee') }}
                                    </div>
                                    @php
                                        $monthDisplay = '';
                                        if (!empty($alloc->month)) {
                                            $monthDisplay = is_numeric($alloc->month)
                                                ? date('F', mktime(0, 0, 0, (int) $alloc->month, 10))
                                                : $alloc->month;
                                        }
                                    @endphp
                                    @if($monthDisplay || $alloc->year)
                                        <div class="text-muted small">
                                            Period: {{ trim($monthDisplay . ' ' . ($alloc->year ?? '')) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($alloc->due_date)
                                        <span class="{{ $isOverdue ? 'text-danger fw-bold' : 'text-dark' }}">
                                            {{ $alloc->due_date->format('d M, Y') }}
                                        </span>
                                        @if($isOverdue)
                                            <span class="d-block badge bg-danger-subtle text-danger p-0" style="font-size: 0.65rem;">Overdue</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end text-dark">₹{{ number_format($alloc->amount, 2) }}</td>
                                <td class="text-end {{ $alloc->discount_amount > 0 ? 'text-success fw-semibold' : 'text-muted' }}">
                                    -₹{{ number_format($alloc->discount_amount, 2) }}
                                </td>
                                <td class="text-end {{ $alloc->fine_amount > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">
                                    +₹{{ number_format($alloc->fine_amount, 2) }}
                                </td>
                                <td class="text-end fw-bold text-dark">₹{{ number_format($net, 2) }}</td>
                                <td class="text-end text-success fw-bold">₹{{ number_format($alloc->paid_amount, 2) }}</td>
                                <td class="text-end fw-bold {{ $balance > 0 ? 'text-danger' : 'text-muted' }}">
                                    ₹{{ number_format($balance, 2) }}
                                </td>
                                <td class="text-center pe-4">
                                    @if($balance <= 0)
                                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold border border-success-subtle">
                                            <i class="bi bi-check-circle me-1"></i> Paid
                                        </span>
                                    @elseif($alloc->paid_amount > 0)
                                        <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-2 rounded-pill fw-semibold border border-warning-subtle">
                                            <i class="bi bi-clock-history me-1"></i> Partial
                                        </span>
                                    @elseif($isOverdue)
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-semibold border border-danger-subtle">
                                            <i class="bi bi-exclamation-triangle me-1"></i> Overdue
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill fw-semibold border border-secondary-subtle">
                                            <i class="bi bi-hourglass-split me-1"></i> Pending
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="bi bi-receipt-cutoff fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                    <h6 class="fw-bold mb-1">No Fee Allocations Found</h6>
                                    <p class="small mb-0">No fee records have been assigned to your enrollment for this session yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Notes & Instructions -->
            <div class="card-footer bg-light border-top py-3 px-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <small class="text-muted d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle text-primary"></i>
                            <span>Fees can be deposited directly at the school accounts counter or via official school bank accounts. Always preserve your receipt voucher.</span>
                        </small>
                    </div>
                    <div class="col-md-4 text-md-end mt-2 mt-md-0">
                        <small class="text-muted">Need fee clearance receipt? Contact Accounts Office.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
