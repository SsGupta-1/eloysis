<?php

namespace App\Repositories\Admin;

use App\Models\ImportantMessage;
use App\Repositories\BaseRepository;

class ImportantMessageRepository extends BaseRepository
{
    public function __construct(ImportantMessage $message)
    {
        parent::__construct($message);
    }

    public function getMessages(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        $sortableColumns = [
            1 => 'title',
            2 => 'type',
            3 => 'start_date',
            4 => 'end_date',
            5 => 'sort_order',
            6 => 'status',
        ];

        $query = $this->model->newQuery();

        $query->when(! empty($filters['search']), function ($query) use ($filters) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                    ->orWhere('message', 'like', "%{$filters['search']}%")
                    ->orWhere('type', 'like', "%{$filters['search']}%");
            });
        })
            ->when(! empty($filters['type']), function ($query) use ($filters) {
                $query->where('type', $filters['type']);
            })
            ->when(isset($filters['filter_status']) && $filters['filter_status'] !== '', function ($query) use ($filters) {
                $query->where('status', $filters['filter_status']);
            });

        if ($orderColumn !== null && isset($sortableColumns[$orderColumn])) {
            $query->orderBy($sortableColumns[$orderColumn], $orderDirection === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('sort_order', 'asc')->latest('id');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
