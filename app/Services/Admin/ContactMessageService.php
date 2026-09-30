<?php

namespace App\Services\Admin;

use App\Repositories\Admin\ContactMessageRepository;

class ContactMessageService
{
    protected ContactMessageRepository $contactMessageRepository;

    public function __construct(ContactMessageRepository $contactMessageRepository)
    {
        $this->contactMessageRepository = $contactMessageRepository;
    }

    public function getMessages(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        return $this->contactMessageRepository->getMessages($filters, $perPage, $page, $orderColumn, $orderDirection);
    }

    public function find(int $id)
    {
        $message = $this->contactMessageRepository->find($id);

        if ($message && $message->status === 'pending') {
            $message->update(['status' => 'read']);
        }

        return $message;
    }

    public function updateStatus(int $id, string $status)
    {
        return $this->contactMessageRepository->update($id, ['status' => $status]);
    }

    public function delete(int $id)
    {
        return $this->contactMessageRepository->delete($id);
    }
}
