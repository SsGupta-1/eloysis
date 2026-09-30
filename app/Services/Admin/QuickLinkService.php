<?php

namespace App\Services\Admin;

use App\Repositories\Admin\QuickLinkRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class QuickLinkService
{
    public function __construct(
        protected QuickLinkRepository $quickLinkRepository
    ) {}

    public function getQuickLinks(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        return $this->quickLinkRepository->getQuickLinks($filters, $perPage, $page, $orderColumn, $orderDirection);
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            if (! isset($data['status'])) {
                $data['status'] = true;
            }

            $quickLink = $this->quickLinkRepository->create($data);

            DB::commit();

            return $quickLink;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            $quickLink = $this->quickLinkRepository->update($id, $data);

            DB::commit();

            return $quickLink;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $id)
    {
        DB::beginTransaction();

        try {
            $this->quickLinkRepository->delete($id);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function changeStatus(int $id)
    {
        return $this->quickLinkRepository->changeStatus($id);
    }
}
