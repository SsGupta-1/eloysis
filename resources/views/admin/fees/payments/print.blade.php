<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $fee_payment->receipt_no }} - {{ config('app.name', 'School ERP') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }
        body {
            background-color: #f1f5f9;
            color: #1e293b;
            padding: 24px;
        }
        .action-bar {
            max-width: 900px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        .btn-primary {
            background-color: #2563eb;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }
        .btn-secondary {
            background-color: #ffffff;
            color: #475569;
            border-color: #cbd5e1;
        }
        .btn-secondary:hover {
            background-color: #f8fafc;
        }
        .receipt-container {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .receipt-wrapper {
            padding: 32px;
        }
        .school-header {
            text-align: center;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .school-name {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .school-subtitle {
            font-size: 13px;
            color: #64748b;
            margin-top: 4px;
        }
        .receipt-badge {
            display: inline-block;
            background-color: #f1f5f9;
            color: #0f172a;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 16px;
            border-radius: 20px;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 8px;
            border: 1px solid #e2e8f0;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 16px;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
        }
        .meta-label {
            color: #64748b;
            font-weight: 500;
        }
        .meta-value {
            color: #0f172a;
            font-weight: 600;
        }
        table.fee-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 20px;
        }
        table.fee-table th {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            border-bottom: 2px solid #cbd5e1;
            padding: 10px 12px;
            text-align: left;
            font-weight: 600;
            color: #475569;
        }
        table.fee-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }
        table.fee-table .text-end {
            text-align: right;
        }
        .totals-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 24px;
            align-items: flex-start;
        }
        .amount-in-words {
            font-size: 13px;
            color: #475569;
            max-width: 50%;
            padding: 12px;
            background: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .totals-table {
            width: 360px;
            font-size: 13px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            color: #64748b;
        }
        .totals-row.grand-total {
            border-top: 2px solid #cbd5e1;
            padding-top: 8px;
            margin-top: 4px;
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }
        .receipt-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 40px;
            padding-top: 16px;
            border-top: 1px dashed #cbd5e1;
            font-size: 12px;
            color: #64748b;
        }
        .signature-box {
            text-align: center;
            width: 180px;
        }
        .signature-line {
            border-top: 1px solid #94a3b8;
            margin-bottom: 4px;
        }
        .cancelled-watermark {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 72px;
            font-weight: 800;
            color: rgba(220, 38, 38, 0.2);
            border: 8px solid rgba(220, 38, 38, 0.2);
            padding: 10px 40px;
            border-radius: 12px;
            pointer-events: none;
            text-transform: uppercase;
        }
        .badge-status {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 4px;
        }
        .badge-partial {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .badge-paid {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .action-bar {
                display: none !important;
            }
            .receipt-container {
                box-shadow: none;
                border: 1px solid #000000;
                margin: 0;
                width: 100%;
                max-width: 100%;
            }
            .receipt-wrapper {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="action-bar">
        <button class="btn btn-secondary" onclick="window.close();">
            <i class="bi bi-arrow-left"></i> Back / Close
        </button>
        <div style="display: flex; gap: 8px;">
            <button class="btn btn-primary" onclick="window.print();">
                <i class="bi bi-printer"></i> Print Receipt
            </button>
        </div>
    </div>

    <div class="receipt-container" style="position: relative;">

        @if($fee_payment->status === 'cancelled')
            <div class="cancelled-watermark">CANCELLED</div>
        @endif

        <div class="receipt-wrapper">
            {{-- School Header --}}
            <div class="school-header">
                <div class="school-name">ELOYSIS PUBLIC SCHOOL</div>
                <div class="school-subtitle">CBSE Affiliated Senior Secondary School &bull; Contact: +91 9876543210 &bull; Email: info@eloysis.edu</div>
                <div class="receipt-badge">OFFICIAL FEE PAYMENT RECEIPT</div>
            </div>

            {{-- Meta Grid --}}
            <div class="meta-grid">
                {{-- Student Details --}}
                <div class="meta-box">
                    <div class="meta-row">
                        <span class="meta-label">Student Name:</span>
                        <span class="meta-value">{{ $fee_payment->student?->user?->name ?? 'N/A' }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Admission No:</span>
                        <span class="meta-value">{{ $fee_payment->student?->admission_no ?? 'N/A' }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Class & Section:</span>
                        <span class="meta-value">{{ $fee_payment->enrollment?->studentClass?->class_name ?? 'N/A' }} - {{ $fee_payment->enrollment?->section?->section_name ?? 'N/A' }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Roll Number:</span>
                        <span class="meta-value">{{ $fee_payment->enrollment?->roll_number ?? 'N/A' }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Father's Name:</span>
                        <span class="meta-value">{{ $fee_payment->student?->father_name ?? 'N/A' }}</span>
                    </div>
                </div>

                {{-- Receipt & Payment Info --}}
                <div class="meta-box">
                    <div class="meta-row">
                        <span class="meta-label">Receipt Number:</span>
                        <span class="meta-value" style="color: #2563eb;">{{ $fee_payment->receipt_no }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Payment Date:</span>
                        <span class="meta-value">{{ $fee_payment->payment_date?->format('d F Y') }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Academic Session:</span>
                        <span class="meta-value">{{ $fee_payment->academicSession?->name ?? 'Current' }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Payment Mode:</span>
                        <span class="meta-value" style="text-transform: uppercase;">{{ $fee_payment->payment_mode }}</span>
                    </div>
                    @if($fee_payment->transaction_reference)
                        <div class="meta-row">
                            <span class="meta-label">Txn / Ref No:</span>
                            <span class="meta-value">{{ $fee_payment->transaction_reference }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Fee Items Breakdown Table --}}
            <table class="fee-table">
                <thead>
                    <tr>
                        <th width="35">#</th>
                        <th>Fee Head / Particulars</th>
                        <th class="text-end" width="105">Base Fee</th>
                        <th class="text-end" width="85">Discount</th>
                        <th class="text-end" width="75">Fine</th>
                        <th class="text-end" width="95">Net Due</th>
                        <th class="text-end" width="115">Paid Now (₹)</th>
                        <th class="text-end" width="105">Remaining</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $i = 1;
                        $totalBaseAmount = 0;
                        $totalDiscountApplied = 0;
                        $totalFinePaid = 0;
                        $totalNetPayable = 0;
                        $totalPaidNow = 0;
                        $totalRemainingAcrossItems = 0;
                    @endphp
                    @foreach($fee_payment->items as $item)
                        @php
                            $alloc = $item->allocation;
                            $baseAmount = $alloc ? (float) $alloc->amount : (float) $item->amount_paid;
                            $discount = (float) $item->discount_applied;
                            $fine = (float) $item->fine_paid;
                            $netDue = max(0, $baseAmount - $discount + $fine);
                            $paidNow = (float) $item->amount_paid;
                            $remBal = $alloc ? max(0, (float) $alloc->remaining_balance) : 0;

                            $totalBaseAmount += $baseAmount;
                            $totalDiscountApplied += $discount;
                            $totalFinePaid += $fine;
                            $totalNetPayable += $netDue;
                            $totalPaidNow += $paidNow;
                            $totalRemainingAcrossItems += $remBal;
                        @endphp
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>
                                <strong>{{ $alloc?->feeHead?->name ?? 'Fee Item' }}</strong>
                                @if($alloc?->title)
                                    <div style="font-size: 11px; color: #64748b;">{{ $alloc->title }}</div>
                                @endif
                            </td>
                            <td class="text-end">₹{{ number_format($baseAmount, 2) }}</td>
                            <td class="text-end text-danger">{{ $discount > 0 ? '-₹' . number_format($discount, 2) : '₹0.00' }}</td>
                            <td class="text-end text-warning">{{ $fine > 0 ? '+₹' . number_format($fine, 2) : '₹0.00' }}</td>
                            <td class="text-end fw-semibold">₹{{ number_format($netDue, 2) }}</td>
                            <td class="text-end" style="font-weight: 700; color: #15803d;">₹{{ number_format($paidNow, 2) }}</td>
                            <td class="text-end">
                                @if($remBal > 0)
                                    <span style="color: #d97706; font-weight: 600;">₹{{ number_format($remBal, 2) }}</span>
                                    <div><span class="badge-status badge-partial">Partial</span></div>
                                @else
                                    <span style="color: #15803d; font-weight: 600;">₹0.00</span>
                                    <div><span class="badge-status badge-paid">Settled</span></div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Totals and Words --}}
            <div class="totals-section">
                <div class="amount-in-words">
                    <div style="font-weight: 600; margin-bottom: 4px;">Cashier Remarks / Note:</div>
                    <div style="margin-bottom: 8px;">{{ $fee_payment->remarks ?: 'Fee payment received with thanks.' }}</div>
                    @if($totalRemainingAcrossItems > 0)
                        <div style="font-size: 12px; color: #b45309; background: #fef3c7; padding: 6px 10px; border-radius: 4px; border: 1px solid #fde68a;">
                            <strong>Balance Due Alert:</strong> A remaining balance of <strong>₹{{ number_format($totalRemainingAcrossItems, 2) }}</strong> is still pending on this student account.
                        </div>
                    @else
                        <div style="font-size: 12px; color: #15803d; background: #dcfce7; padding: 6px 10px; border-radius: 4px; border: 1px solid #bbf7d0;">
                            <strong>Status:</strong> All selected fees in this receipt are fully settled.
                        </div>
                    @endif
                </div>

                <div class="totals-table">
                    <div class="totals-row">
                        <span>Total Allocated Fee:</span>
                        <span style="font-weight: 600; color: #1e293b;">₹{{ number_format($totalBaseAmount, 2) }}</span>
                    </div>
                    @if($totalDiscountApplied > 0)
                        <div class="totals-row" style="color: #dc2626;">
                            <span>Discount Concession:</span>
                            <span>-₹{{ number_format($totalDiscountApplied, 2) }}</span>
                        </div>
                    @endif
                    @if($totalFinePaid > 0)
                        <div class="totals-row" style="color: #d97706;">
                            <span>Late Fine:</span>
                            <span>+₹{{ number_format($totalFinePaid, 2) }}</span>
                        </div>
                    @endif
                    <div class="totals-row" style="border-top: 1px solid #e2e8f0; padding-top: 6px; font-weight: 600;">
                        <span>Net Payable Amount:</span>
                        <span>₹{{ number_format($totalNetPayable, 2) }}</span>
                    </div>
                    <div class="totals-row grand-total">
                        <span>Amount Received (This Receipt):</span>
                        <span style="color: #16a34a;">₹{{ number_format((float) $fee_payment->total_paid, 2) }}</span>
                    </div>
                    @if($totalRemainingAcrossItems > 0)
                        <div class="totals-row" style="color: #dc2626; font-weight: 700; padding-top: 4px;">
                            <span>Remaining Balance Due:</span>
                            <span>₹{{ number_format($totalRemainingAcrossItems, 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Footer / Signatures --}}
            <div class="receipt-footer">
                <div>
                    <div><strong>Collected By:</strong> {{ $fee_payment->collector?->name ?? 'Admin / Cashier' }}</div>
                    <div>Printed on: {{ date('d-m-Y H:i:s') }}</div>
                    <div style="margin-top: 4px; font-size: 11px; font-style: italic;">* This is a computer generated official fee receipt.</div>
                </div>

                <div class="signature-box">
                    <div style="height: 40px;"></div>
                    <div class="signature-line"></div>
                    <div>Authorized Signature & Stamp</div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
