<?php

namespace App\Services\Admin;

use App\Repositories\Admin\FeeHeadRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class FeeHeadService
{
    public function __construct(
        protected FeeHeadRepository $feeHeadRepository
    ) {}

    public function getFeeHeads(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        return $this->feeHeadRepository->getFeeHeads($filters, $perPage, $page, $orderColumn, $orderDirection);
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            if (! isset($data['is_active'])) {
                $data['is_active'] = true;
            }

            $feeHead = $this->feeHeadRepository->create($data);

            DB::commit();

            return $feeHead;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            $feeHead = $this->feeHeadRepository->update($id, $data);

            DB::commit();

            return $feeHead;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $id)
    {
        DB::beginTransaction();

        try {
            $this->feeHeadRepository->delete($id);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function changeStatus(int $id)
    {
        return $this->feeHeadRepository->changeStatus($id);
    }
}
