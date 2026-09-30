<?php

namespace App\Services\Admin;

use App\Repositories\Admin\ImportantMessageRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class ImportantMessageService
{
    public function __construct(
        protected ImportantMessageRepository $messageRepository
    ) {}

    public function getMessages(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        return $this->messageRepository->getMessages($filters, $perPage, $page, $orderColumn, $orderDirection);
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            if (! isset($data['status'])) {
                $data['status'] = true;
            }

            $message = $this->messageRepository->create($data);

            DB::commit();

            return $message;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            $message = $this->messageRepository->update($id, $data);

            DB::commit();

            return $message;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $id)
    {
        DB::beginTransaction();

        try {
            $this->messageRepository->delete($id);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function changeStatus(int $id)
    {
        return $this->messageRepository->changeStatus($id);
    }
}
