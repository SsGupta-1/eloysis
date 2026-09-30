<?php

namespace App\Repositories\Admin;

use App\Models\News;
use App\Repositories\BaseRepository;

class NewsRepository extends BaseRepository
{
    public function __construct(News $news)
    {
        parent::__construct($news);
    }

    public function getNews(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        $sortableColumns = [
            1 => 'title',
            2 => 'published_date',
            3 => 'status',
        ];

        $query = $this->model->newQuery();

        $query->when(! empty($filters['search']), function ($query) use ($filters) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                    ->orWhere('summary', 'like', "%{$filters['search']}%");
            });
        })
            ->when(isset($filters['filter_status']) && $filters['filter_status'] !== '', function ($query) use ($filters) {
                $query->where('status', $filters['filter_status']);
            });

        if ($orderColumn !== null && isset($sortableColumns[$orderColumn])) {
            $query->orderBy($sortableColumns[$orderColumn], $orderDirection === 'desc' ? 'desc' : 'asc');
        } else {
            $query->latest('published_date')->latest('id');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
