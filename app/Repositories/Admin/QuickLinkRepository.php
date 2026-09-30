<?php

namespace App\Repositories\Admin;

use App\Models\QuickLink;
use App\Repositories\BaseRepository;

class QuickLinkRepository extends BaseRepository
{
    public function __construct(QuickLink $quickLink)
    {
        parent::__construct($quickLink);
    }

    public function getQuickLinks(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        $sortableColumns = [
            1 => 'title',
            2 => 'url',
            3 => 'color',
            4 => 'sort_order',
            5 => 'status',
        ];

        $query = $this->model->newQuery();

        $query->when(! empty($filters['search']), function ($query) use ($filters) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                    ->orWhere('description', 'like', "%{$filters['search']}%")
                    ->orWhere('url', 'like', "%{$filters['search']}%");
            });
        })
            ->when(! empty($filters['color']), function ($query) use ($filters) {
                $query->where('color', $filters['color']);
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
