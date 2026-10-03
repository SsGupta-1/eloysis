<?php

namespace App\Services\Admin;

use App\Repositories\Admin\FeeStructureRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class FeeStructureService
{
    public function __construct(
        protected FeeStructureRepository $feeStructureRepository
    ) {}

    public function getFeeStructures(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        return $this->feeStructureRepository->getFeeStructures($filters, $perPage, $page, $orderColumn, $orderDirection);
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            if (! isset($data['is_active'])) {
                $data['is_active'] = true;
            }

            $structure = $this->feeStructureRepository->create($data);

            DB::commit();

            return $structure;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            $structure = $this->feeStructureRepository->update($id, $data);

            DB::commit();

            return $structure;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $id)
    {
        DB::beginTransaction();

        try {
            $this->feeStructureRepository->delete($id);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function changeStatus(int $id)
    {
        return $this->feeStructureRepository->changeStatus($id);
    }
}
