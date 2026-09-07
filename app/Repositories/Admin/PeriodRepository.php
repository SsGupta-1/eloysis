<?php

namespace App\Repositories\Admin;

use App\Models\Periods;
use App\Repositories\BaseRepository;

class PeriodRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(Periods $model)
    {
        parent::__construct($model);
    }

    public function getLists(
        array $filters = [],
        int $page = 1,
        int $perPage = 10,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {

        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */

        $query = $this->model->newQuery();

        $query->when(! empty($filters['search']),
            function ($query) use ($filters) {

                $search = $filters['search'];

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                    $q->orWhere('start_time', 'like', "%{$search}%");
                    $q->orWhere('end_time', 'like', "%{$search}%");
                });
            });

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        $query->when(isset($filters['filter_status']) && $filters['filter_status'] !== '',
            function ($query) use ($filters) {
                $query->where('status', $filters['filter_status']);
            });

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $sortableColumns = [

            1 => 'name',
            2 => 'start_time',
            3 => 'end_time',
            4 => 'status',

        ];

        if ($orderColumn !== null && isset($sortableColumns[$orderColumn])) {
            $query->orderBy($sortableColumns[$orderColumn], $orderDirection === 'desc' ? 'desc' : 'asc');
        } else {

            $query->latest('id');

        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        return $query->paginate($perPage, ['*'], 'page', $page);

        // $paginator = $query->paginate(
        //     $perPage,
        //     ['*'],
        //     'page',
        //     $page
        // );

        /*
        |--------------------------------------------------------------------------
        | DataTables Response
        |--------------------------------------------------------------------------
        */

        // return [

        //     'recordsTotal' => $recordsTotal,

        //     'recordsFiltered' => $recordsFiltered,

        //     'data' => $paginator,

        // ];
    }
}
