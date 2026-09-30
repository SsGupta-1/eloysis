<?php

namespace App\Repositories\Admin;

use App\Models\Announcement;
use App\Repositories\BaseRepository;

class AnnouncementRepository extends BaseRepository
{
    public function __construct(Announcement $announcement)
    {
        parent::__construct($announcement);
    }

    public function getAnnouncements(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        $sortableColumns = [
            1 => 'title',
            2 => 'badge',
            3 => 'start_date',
            4 => 'end_date',
            5 => 'sort_order',
            6 => 'status',
        ];

        $query = $this->model->newQuery();

        $query->when(! empty($filters['search']), function ($query) use ($filters) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                    ->orWhere('content', 'like', "%{$filters['search']}%")
                    ->orWhere('badge', 'like', "%{$filters['search']}%");
            });
        })
            ->when(! empty($filters['badge']), function ($query) use ($filters) {
                $query->where('badge', $filters['badge']);
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
