<?php

namespace App\Services\Admin;

use App\Repositories\Admin\FeeDiscountRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class FeeDiscountService
{
    public function __construct(
        protected FeeDiscountRepository $feeDiscountRepository
    ) {}

    public function getFeeDiscounts(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        return $this->feeDiscountRepository->getFeeDiscounts($filters, $perPage, $page, $orderColumn, $orderDirection);
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            if (! isset($data['is_active'])) {
                $data['is_active'] = true;
            }

            $discount = $this->feeDiscountRepository->create($data);

            DB::commit();

            return $discount;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            $discount = $this->feeDiscountRepository->update($id, $data);

            DB::commit();

            return $discount;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $id)
    {
        DB::beginTransaction();

        try {
            $this->feeDiscountRepository->delete($id);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function changeStatus(int $id)
    {
        return $this->feeDiscountRepository->changeStatus($id);
    }
}
