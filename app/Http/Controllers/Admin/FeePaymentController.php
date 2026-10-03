<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\FeePaymentRequest;
use App\Models\AcademicSession;
use App\Models\FeePayment;
use App\Services\Admin\FeePaymentService;
use Illuminate\Http\Request;

class FeePaymentController extends BaseController
{
    public function __construct(
        protected FeePaymentService $feePaymentService
    ) {}

    public function index()
    {
        $academicSessions = AcademicSession::orderByDesc('id')->pluck('name', 'id')->toArray();

        return view('admin.fees.payments.index', compact('academicSessions'));
    }

    public function list(Request $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'academic_session_id' => $request->input('academic_session_id'),
            'payment_mode' => $request->input('payment_mode'),
            'status' => $request->input('status'),
            'from_date' => $request->input('from_date'),
            'to_date' => $request->input('to_date'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'desc');

        $payments = $this->feePaymentService->getPayments(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($payments, (int) $request->input('draw', 1));
    }

    /**
     * Cashier POS screen for collecting fees.
     */
    public function collect(Request $request)
    {
        $selectedEnrollmentId = $request->input('student_enrollment_id');
        $academicSessions = AcademicSession::orderByDesc('id')->pluck('name', 'id')->toArray();

        return view('admin.fees.payments.collect', compact('academicSessions', 'selectedEnrollmentId'));
    }

    /**
     * Fetch student fee ledger data for POS screen.
     */
    public function studentLedger(Request $request)
    {
        $enrollmentId = (int) $request->input('student_enrollment_id');
        if (! $enrollmentId) {
            return $this->error('Student enrollment ID is required.', 422);
        }

        try {
            $ledger = $this->feePaymentService->getStudentFeeLedger($enrollmentId);

            return $this->success('Student ledger fetched successfully.', $ledger);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 404);
        }
    }

    /**
     * Process payment submission.
     */
    public function store(FeePaymentRequest $request)
    {
        try {
            $payment = $this->feePaymentService->createPayment($request->validated());

            return $this->success('Payment collected successfully and Receipt generated.', [
                'payment_id' => $payment->id,
                'receipt_no' => $payment->receipt_no,
                'print_url' => route('admin.fees.payments.print', $payment->id),
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    /**
     * View payment receipt details (JSON).
     */
    public function show(FeePayment $fee_payment)
    {
        $fee_payment->load([
            'enrollment.studentClass',
            'enrollment.section',
            'student.user',
            'collector',
            'academicSession',
            'items.allocation.feeHead',
        ]);

        return $this->success('Payment receipt fetched successfully.', $fee_payment);
    }

    /**
     * Printable A4 / Slip fee receipt.
     */
    public function print(FeePayment $fee_payment)
    {
        $fee_payment->load([
            'enrollment.studentClass',
            'enrollment.section',
            'student.user',
            'collector',
            'academicSession',
            'items.allocation.feeHead',
        ]);

        return view('admin.fees.payments.print', compact('fee_payment'));
    }

    /**
     * Void / Cancel a fee payment receipt.
     */
    public function cancel(Request $request, FeePayment $fee_payment)
    {
        $reason = $request->input('reason', 'Cancelled by administrator');

        try {
            $this->feePaymentService->cancelPayment($fee_payment->id, $reason);

            return $this->success('Payment receipt cancelled and student balance restored successfully.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }
}
