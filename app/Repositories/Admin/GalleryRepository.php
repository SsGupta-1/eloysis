<?php

namespace App\Repositories\Admin;

use App\Models\Gallery;
use App\Repositories\BaseRepository;

class GalleryRepository extends BaseRepository
{
    public function __construct(Gallery $gallery)
    {
        parent::__construct($gallery);
    }

    public function getGalleries(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        $sortableColumns = [
            1 => 'title',
            2 => 'category',
            3 => 'sort_order',
            4 => 'status',
        ];

        $query = $this->model->newQuery();

        $query->when(! empty($filters['search']), function ($query) use ($filters) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                    ->orWhere('category', 'like', "%{$filters['search']}%");
            });
        })
            ->when(! empty($filters['category']), function ($query) use ($filters) {
                $query->where('category', $filters['category']);
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
