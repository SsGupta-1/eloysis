<?php

namespace App\Services\Admin;

use App\Repositories\Admin\PeriodRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class PeriodService
{
    protected $periodRepository;
    /**
     * Create a new class instance.
     */
    public function __construct(PeriodRepository $periodRepository)
    {
        $this->periodRepository = $periodRepository;
    }
    /**
     * Get Academic Class
     */
    public function getLists(
        array $filters = [],
        int $page = 1,
        int $perPage = 10,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        return $this->periodRepository->getLists(
            $filters,
            $page,
            $perPage,
            $orderColumn,
            $orderDirection
        );
    }

    /**
     * Create Academic Class
     */
    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $classes = $this->periodRepository->create($data);

            DB::commit();

            return $classes;

        } catch (Exception $e) {

            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Update Class
     */
    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {

            $classes = $this->periodRepository->update($id, $data);

            DB::commit();

            return $classes;

        } catch (Exception $e) {

            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Delete Class
     */
    public function delete(int $id)
    {
        DB::beginTransaction();

        try {

            $this->periodRepository->delete($id);

            DB::commit();

            return true;

        } catch (Exception $e) {

            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Change Class Status
     */
    public function changeStatus(int $id)
    {
        return $this->periodRepository->changeStatus($id);
    }
}
