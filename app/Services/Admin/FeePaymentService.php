<?php

namespace App\Services\Admin;

use App\Repositories\Admin\FeePaymentRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class FeePaymentService
{
    public function __construct(
        protected FeePaymentRepository $feePaymentRepository
    ) {}

    public function getPayments(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'desc'
    ): LengthAwarePaginator {
        return $this->feePaymentRepository->getPayments(
            $filters,
            $perPage,
            $page,
            $orderColumn,
            $orderDirection
        );
    }

    public function createPayment(array $data)
    {
        return $this->feePaymentRepository->createPayment($data);
    }

    public function cancelPayment(int $id, ?string $reason = null): bool
    {
        return $this->feePaymentRepository->cancelPayment($id, $reason);
    }

    public function getStudentFeeLedger(int $enrollmentId): array
    {
        return $this->feePaymentRepository->getStudentFeeLedger($enrollmentId);
    }
}
