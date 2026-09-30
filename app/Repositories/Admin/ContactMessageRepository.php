<?php

namespace App\Repositories\Admin;

use App\Models\ContactMessage;
use App\Repositories\BaseRepository;

class ContactMessageRepository extends BaseRepository
{
    public function __construct(ContactMessage $contactMessage)
    {
        parent::__construct($contactMessage);
    }

    public function getMessages(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        $sortableColumns = [
            1 => 'name',
            2 => 'email',
            3 => 'phone',
            4 => 'subject',
            5 => 'status',
            6 => 'created_at',
        ];

        $query = $this->model->newQuery();

        $query->when(! empty($filters['search']), function ($query) use ($filters) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('email', 'like', "%{$filters['search']}%")
                    ->orWhere('phone', 'like', "%{$filters['search']}%")
                    ->orWhere('subject', 'like', "%{$filters['search']}%")
                    ->orWhere('message', 'like', "%{$filters['search']}%");
            });
        })
            ->when(! empty($filters['status']), function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            });

        if ($orderColumn !== null && isset($sortableColumns[$orderColumn])) {
            $query->orderBy($sortableColumns[$orderColumn], $orderDirection === 'desc' ? 'desc' : 'asc');
        } else {
            $query->latest('id');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
