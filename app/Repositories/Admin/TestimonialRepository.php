<?php

namespace App\Repositories\Admin;

use App\Models\Testimonial;
use App\Repositories\BaseRepository;

class TestimonialRepository extends BaseRepository
{
    public function __construct(Testimonial $testimonial)
    {
        parent::__construct($testimonial);
    }

    public function getTestimonials(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        $sortableColumns = [
            1 => 'name',
            2 => 'role',
            3 => 'rating',
            4 => 'sort_order',
            5 => 'status',
        ];

        $query = $this->model->newQuery();

        $query->when(! empty($filters['search']), function ($query) use ($filters) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('role', 'like', "%{$filters['search']}%")
                    ->orWhere('message', 'like', "%{$filters['search']}%");
            });
        })
            ->when(! empty($filters['rating']), function ($query) use ($filters) {
                $query->where('rating', $filters['rating']);
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
