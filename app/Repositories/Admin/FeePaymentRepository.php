<?php

namespace App\Repositories\Admin;

use App\Models\FeePayment;
use App\Models\FeePaymentItem;
use App\Models\StudentEnrollment;
use App\Models\StudentFeeAllocation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeePaymentRepository
{
    public function getPayments(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'desc'
    ): LengthAwarePaginator {
        $query = FeePayment::with([
            'enrollment.studentClass',
            'enrollment.section',
            'student.user',
            'collector',
            'academicSession',
        ]);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('receipt_no', 'like', "%{$search}%")
                    ->orWhere('transaction_reference', 'like', "%{$search}%")
                    ->orWhereHas('student.user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('admission_no', 'like', "%{$search}%");
                    })
                    ->orWhereHas('enrollment', function ($eq) use ($search) {
                        $eq->where('roll_number', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['academic_session_id'])) {
            $query->where('academic_session_id', $filters['academic_session_id']);
        }

        if (! empty($filters['payment_mode'])) {
            $query->where('payment_mode', $filters['payment_mode']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['from_date'])) {
            $query->whereDate('payment_date', '>=', $filters['from_date']);
        }

        if (! empty($filters['to_date'])) {
            $query->whereDate('payment_date', '<=', $filters['to_date']);
        }

        $columns = [
            0 => 'id',
            1 => 'receipt_no',
            2 => 'payment_date',
            3 => 'stu_profile_id',
            4 => 'total_paid',
            5 => 'payment_mode',
            6 => 'status',
            7 => 'collected_by',
        ];

        if ($orderColumn !== null && isset($columns[$orderColumn])) {
            $query->orderBy($columns[$orderColumn], $orderDirection);
        } else {
            $query->latest('id');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function generateReceiptNumber(): string
    {
        $prefix = 'REC-'.date('Ym').'-';
        $lastPayment = FeePayment::where('receipt_no', 'like', "{$prefix}%")
            ->withTrashed()
            ->latest('id')
            ->first();

        if ($lastPayment && preg_match('/-(\d+)$/', $lastPayment->receipt_no, $matches)) {
            $nextSeq = str_pad((int) $matches[1] + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextSeq = '0001';
        }

        return $prefix.$nextSeq;
    }

    public function createPayment(array $data): FeePayment
    {
        return DB::transaction(function () use ($data) {
            $enrollment = StudentEnrollment::with('student')->findOrFail($data['student_enrollment_id']);

            $receiptNo = $this->generateReceiptNumber();
            $subtotal = 0;
            $discountTotal = 0;
            $fineTotal = 0;
            $totalPaid = 0;

            foreach ($data['items'] as $item) {
                $subtotal += (float) ($item['amount_paid'] ?? 0);
                $discountTotal += (float) ($item['discount_applied'] ?? 0);
                $fineTotal += (float) ($item['fine_paid'] ?? 0);
                $totalPaid += (float) ($item['amount_paid'] ?? 0);
            }

            $payment = FeePayment::create([
                'receipt_no' => $receiptNo,
                'student_enrollment_id' => $enrollment->id,
                'stu_profile_id' => $enrollment->stu_profile_id,
                'academic_session_id' => $enrollment->academic_session_id,
                'payment_date' => $data['payment_date'],
                'subtotal_amount' => $subtotal,
                'discount_amount' => $discountTotal,
                'fine_amount' => $fineTotal,
                'total_paid' => $totalPaid,
                'payment_mode' => $data['payment_mode'],
                'transaction_reference' => $data['transaction_reference'] ?? null,
                'bank_name' => $data['bank_name'] ?? null,
                'cheque_date' => $data['cheque_date'] ?? null,
                'collected_by' => Auth::id() ?? 1,
                'status' => 'paid',
                'remarks' => $data['remarks'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $allocation = StudentFeeAllocation::lockForUpdate()->findOrFail($item['allocation_id']);

                $amountPaid = (float) $item['amount_paid'];
                $discountApplied = (float) ($item['discount_applied'] ?? 0);
                $finePaid = (float) ($item['fine_paid'] ?? 0);

                FeePaymentItem::create([
                    'fee_payment_id' => $payment->id,
                    'student_fee_allocation_id' => $allocation->id,
                    'amount_paid' => $amountPaid,
                    'discount_applied' => $discountApplied,
                    'fine_paid' => $finePaid,
                ]);

                // Update allocation paid amount & status
                $newPaid = (float) $allocation->paid_amount + $amountPaid;
                $netPayable = max(0, (float) $allocation->amount - (float) $allocation->discount_amount + (float) $allocation->fine_amount);

                $status = ($newPaid >= $netPayable) ? 'paid' : 'partial';

                $allocation->update([
                    'paid_amount' => $newPaid,
                    'status' => $status,
                ]);
            }

            return $payment->load(['enrollment.studentClass', 'enrollment.section', 'student.user', 'items.allocation.feeHead']);
        });
    }

    public function cancelPayment(int $id, ?string $reason = null): bool
    {
        return DB::transaction(function () use ($id, $reason) {
            $payment = FeePayment::with('items')->findOrFail($id);

            if ($payment->status === 'cancelled') {
                throw new \Exception('Payment is already cancelled.');
            }

            // Revert allocations
            foreach ($payment->items as $item) {
                $allocation = StudentFeeAllocation::lockForUpdate()->find($item->student_fee_allocation_id);
                if ($allocation) {
                    $newPaid = max(0, (float) $allocation->paid_amount - (float) $item->amount_paid);
                    $netPayable = max(0, (float) $allocation->amount - (float) $allocation->discount_amount + (float) $allocation->fine_amount);

                    $status = 'unpaid';
                    if ($newPaid > 0 && $newPaid < $netPayable) {
                        $status = 'partial';
                    } elseif ($newPaid >= $netPayable && $netPayable > 0) {
                        $status = 'paid';
                    }

                    $allocation->update([
                        'paid_amount' => $newPaid,
                        'status' => $status,
                    ]);
                }
            }

            $payment->update([
                'status' => 'cancelled',
                'remarks' => trim(($payment->remarks ?? '')."\n[Cancelled: ".($reason ?? 'No reason provided').']'),
            ]);

            return true;
        });
    }

    public function getStudentFeeLedger(int $enrollmentId): array
    {
        $enrollment = StudentEnrollment::with([
            'student.user',
            'studentClass',
            'section',
            'academicSession',
        ])->findOrFail($enrollmentId);

        $allocations = StudentFeeAllocation::with(['feeHead', 'feeDiscount'])
            ->where('student_enrollment_id', $enrollmentId)
            ->orderBy('due_date')
            ->get();

        $payments = FeePayment::with(['items.allocation.feeHead', 'collector'])
            ->where('student_enrollment_id', $enrollmentId)
            ->latest('id')
            ->get();

        $totalAllocated = 0;
        $totalDiscount = 0;
        $totalFine = 0;
        $totalPaid = 0;
        $totalBalance = 0;

        $dues = [];
        foreach ($allocations as $alloc) {
            $net = max(0, (float) $alloc->amount - (float) $alloc->discount_amount + (float) $alloc->fine_amount);
            $bal = max(0, $net - (float) $alloc->paid_amount);

            $totalAllocated += (float) $alloc->amount;
            $totalDiscount += (float) $alloc->discount_amount;
            $totalFine += (float) $alloc->fine_amount;
            $totalPaid += (float) $alloc->paid_amount;
            $totalBalance += $bal;

            $dues[] = [
                'id' => $alloc->id,
                'title' => $alloc->title,
                'head_name' => $alloc->feeHead?->name ?? 'Fee',
                'month' => $alloc->month,
                'year' => $alloc->year,
                'due_date' => $alloc->due_date?->format('Y-m-d'),
                'is_overdue' => $alloc->due_date && $alloc->due_date->isPast() && $alloc->status !== 'paid',
                'amount' => (float) $alloc->amount,
                'discount_amount' => (float) $alloc->discount_amount,
                'fine_amount' => (float) $alloc->fine_amount,
                'net_payable' => $net,
                'paid_amount' => (float) $alloc->paid_amount,
                'balance' => $bal,
                'status' => $alloc->status,
            ];
        }

        return [
            'student' => [
                'enrollment_id' => $enrollment->id,
                'name' => $enrollment->student?->user?->name ?? 'N/A',
                'admission_no' => $enrollment->student?->admission_no ?? 'N/A',
                'roll_number' => $enrollment->roll_number ?? 'N/A',
                'class_name' => $enrollment->studentClass?->class_name ?? 'N/A',
                'section_name' => $enrollment->section?->section_name ?? 'N/A',
                'father_name' => $enrollment->student?->father_name ?? 'N/A',
                'guardian_mobile' => $enrollment->student?->guardian_mobile ?? 'N/A',
                'session_name' => $enrollment->academicSession?->name ?? 'N/A',
            ],
            'summary' => [
                'total_allocated' => $totalAllocated,
                'total_discount' => $totalDiscount,
                'total_fine' => $totalFine,
                'net_total' => max(0, $totalAllocated - $totalDiscount + $totalFine),
                'total_paid' => $totalPaid,
                'total_balance' => $totalBalance,
            ],
            'dues' => $dues,
            'recent_payments' => $payments->map(function ($p) {
                return [
                    'id' => $p->id,
                    'receipt_no' => $p->receipt_no,
                    'date' => $p->payment_date?->format('d M Y'),
                    'total_paid' => (float) $p->total_paid,
                    'payment_mode' => strtoupper($p->payment_mode),
                    'status' => $p->status,
                    'collected_by' => $p->collector?->name ?? 'System',
                ];
            }),
        ];
    }
}
