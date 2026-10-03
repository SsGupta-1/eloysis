<div class="card border-0 shadow-sm h-100">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-dark">
            <i class="bi bi-receipt text-success me-2"></i>Recent Fee Transactions (POS)
        </h6>
        <a href="{{ route('admin.fees.payments.index') }}" class="btn btn-sm btn-outline-primary py-0 px-2 small">
            View All &rarr;
        </a>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Receipt No</th>
                    <th>Student</th>
                    <th>Mode</th>
                    <th class="text-end">Paid (₹)</th>
                    <th class="text-center" width="60">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent_payments as $payment)
                    <tr>
                        <td>
                            <span class="fw-bold text-primary">{{ $payment->receipt_no }}</span>
                            <div class="text-muted" style="font-size: 11px;">{{ $payment->payment_date?->format('d M Y') }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $payment->student?->user?->name ?? 'N/A' }}</div>
                            <div class="text-muted" style="font-size: 11px;">
                                {{ $payment->enrollment?->studentClass?->class_name ?? '-' }} ({{ $payment->enrollment?->section?->section_name ?? '-' }})
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ strtoupper($payment->payment_mode) }}</span>
                        </td>
                        <td class="text-end fw-bold text-success">
                            ₹{{ number_format((float) $payment->total_paid, 2) }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.fees.payments.print', $payment->id) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-1" title="Print Receipt">
                                <i class="bi bi-printer"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No fee transactions recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
