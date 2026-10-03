<?php

namespace App\Services\Admin;

use App\Repositories\Admin\FeeAllocationRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class FeeAllocationService
{
    public function __construct(
        protected FeeAllocationRepository $feeAllocationRepository
    ) {}

    public function getAllocations(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ): LengthAwarePaginator {
        return $this->feeAllocationRepository->getAllocations(
            $filters,
            $perPage,
            $page,
            $orderColumn,
            $orderDirection
        );
    }

    public function create(array $data)
    {
        return $this->feeAllocationRepository->create($data);
    }

    public function bulkAllocate(array $data): int
    {
        return $this->feeAllocationRepository->bulkAllocate($data);
    }

    public function update(int $id, array $data)
    {
        return $this->feeAllocationRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->feeAllocationRepository->delete($id);
    }
}
