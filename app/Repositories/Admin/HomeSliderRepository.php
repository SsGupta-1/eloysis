<?php

namespace App\Repositories\Admin;

use App\Models\HomeSlider;
use App\Repositories\BaseRepository;

class HomeSliderRepository extends BaseRepository
{
    public function __construct(HomeSlider $slider)
    {
        parent::__construct($slider);
    }

    public function getSliders(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        $sortableColumns = [
            1 => 'title',
            2 => 'subtitle',
            3 => 'sort_order',
            4 => 'status',
        ];

        $query = $this->model->newQuery();

        $query->when(! empty($filters['search']), function ($query) use ($filters) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                    ->orWhere('subtitle', 'like', "%{$filters['search']}%");
            });
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
