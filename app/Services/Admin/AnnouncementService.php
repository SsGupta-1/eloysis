<?php

namespace App\Services\Admin;

use App\Repositories\Admin\AnnouncementRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class AnnouncementService
{
    public function __construct(
        protected AnnouncementRepository $announcementRepository
    ) {}

    public function getAnnouncements(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        return $this->announcementRepository->getAnnouncements($filters, $perPage, $page, $orderColumn, $orderDirection);
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            if (! isset($data['status'])) {
                $data['status'] = true;
            }

            $announcement = $this->announcementRepository->create($data);

            DB::commit();

            return $announcement;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            $announcement = $this->announcementRepository->update($id, $data);

            DB::commit();

            return $announcement;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $id)
    {
        DB::beginTransaction();

        try {
            $this->announcementRepository->delete($id);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function changeStatus(int $id)
    {
        return $this->announcementRepository->changeStatus($id);
    }
}
