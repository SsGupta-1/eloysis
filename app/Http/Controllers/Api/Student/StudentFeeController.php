<?php

namespace App\Http\Controllers\Api\Student;

use App\Models\FeePayment;
use App\Models\StudentFeeAllocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentFeeController extends BaseStudentApiController
{
    /**
     * Get Student Fee Allocations, Summary and Payment History
     */
    public function index(Request $request): JsonResponse
    {
        [$user, $profile, $enrollment] = $this->getStudentData($request);

        if (! $profile) {
            return $this->error('Student profile not found.', [], 404);
        }

        if (! $enrollment) {
            return $this->error('Active student enrollment not found.', [], 404);
        }

        // Fee Allocations for this enrollment
        $allocations = StudentFeeAllocation::where('student_enrollment_id', $enrollment->id)
            ->with(['feeHead', 'feeStructure', 'academicSession'])
            ->orderBy('due_date', 'asc')
            ->get();

        $totalAllocated = 0.0;
        $totalDiscount = 0.0;
        $totalFine = 0.0;
        $totalPaid = 0.0;

        $allocationsList = $allocations->map(function ($alloc) use (&$totalAllocated, &$totalDiscount, &$totalFine, &$totalPaid) {
            $baseAmount = (float) $alloc->amount;
            $discount = (float) $alloc->discount_amount;
            $fine = (float) $alloc->fine_amount;
            $paid = (float) $alloc->paid_amount;
            $netAmount = max(0, $baseAmount - $discount + $fine);
            $due = max(0, $netAmount - $paid);

            $totalAllocated += $baseAmount;
            $totalDiscount += $discount;
            $totalFine += $fine;
            $totalPaid += $paid;

            return [
                'id' => $alloc->id,
                'fee_head_name' => $alloc->feeHead?->name ?? ($alloc->title ?: 'Fee'),
                'fee_group' => $alloc->feeHead?->group?->name ?? 'General',
                'month' => $alloc->month,
                'year' => $alloc->year,
                'due_date' => $alloc->due_date ? $alloc->due_date->format('Y-m-d') : null,
                'formatted_due_date' => $alloc->due_date ? $alloc->due_date->format('d M, Y') : null,
                'base_amount' => round($baseAmount, 2),
                'discount_amount' => round($discount, 2),
                'fine_amount' => round($fine, 2),
                'net_amount' => round($netAmount, 2),
                'paid_amount' => round($paid, 2),
                'due_amount' => round($due, 2),
                'status' => $alloc->status, // paid, partial, unpaid
            ];
        });

        $netPayable = max(0, $totalAllocated - $totalDiscount + $totalFine);
        $totalDue = max(0, $netPayable - $totalPaid);
        $pendingCount = $allocations->where('status', '!=', 'paid')->count();

        // Payment Receipts History
        $payments = FeePayment::where('student_enrollment_id', $enrollment->id)
            ->orderBy('payment_date', 'desc')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'receipt_no' => $p->receipt_no,
                    'payment_date' => $p->payment_date ? $p->payment_date->format('Y-m-d') : null,
                    'formatted_payment_date' => $p->payment_date ? $p->payment_date->format('d M, Y') : null,
                    'subtotal_amount' => round((float) $p->subtotal_amount, 2),
                    'discount_amount' => round((float) $p->discount_amount, 2),
                    'fine_amount' => round((float) $p->fine_amount, 2),
                    'total_paid' => round((float) $p->total_paid, 2),
                    'payment_mode' => strtoupper($p->payment_mode ?? 'CASH'),
                    'transaction_reference' => $p->transaction_reference,
                    'status' => $p->status,
                ];
            });

        $data = [
            'summary' => [
                'total_allocated' => round($totalAllocated, 2),
                'total_discount' => round($totalDiscount, 2),
                'total_fine' => round($totalFine, 2),
                'net_payable' => round($netPayable, 2),
                'total_paid' => round($totalPaid, 2),
                'total_due' => round($totalDue, 2),
                'pending_count' => $pendingCount,
            ],
            'allocations' => $allocationsList,
            'payment_history' => $payments,
        ];

        return $this->success('Fee details fetched successfully.', $data);
    }

    /**
     * Get Detailed Payment Receipt
     */
    public function receipt(Request $request, int $paymentId): JsonResponse
    {
        [$user, $profile, $enrollment] = $this->getStudentData($request);

        if (! $profile || ! $enrollment) {
            return $this->error('Student profile or enrollment not found.', [], 404);
        }

        $payment = FeePayment::with(['enrollment.studentClass', 'enrollment.section', 'academicSession'])
            ->where('id', $paymentId)
            ->where('student_enrollment_id', $enrollment->id)
            ->first();

        if (! $payment) {
            return $this->error('Receipt not found.', [], 404);
        }

        $data = [
            'receipt_no' => $payment->receipt_no,
            'payment_date' => $payment->payment_date ? $payment->payment_date->format('d M, Y') : null,
            'student' => [
                'name' => $user->name,
                'roll_number' => $enrollment->roll_number,
                'admission_no' => $profile->admission_no,
                'class_name' => $enrollment->studentClass?->class_name,
                'section_name' => $enrollment->section?->section_name,
                'father_name' => $profile->father_name,
            ],
            'payment_details' => [
                'subtotal' => round((float) $payment->subtotal_amount, 2),
                'discount' => round((float) $payment->discount_amount, 2),
                'fine' => round((float) $payment->fine_amount, 2),
                'total_paid' => round((float) $payment->total_paid, 2),
                'payment_mode' => strtoupper($payment->payment_mode ?? 'CASH'),
                'transaction_reference' => $payment->transaction_reference,
                'status' => $payment->status,
                'remarks' => $payment->remarks,
            ],
        ];

        return $this->success('Payment receipt fetched successfully.', $data);
    }
}
