<?php

namespace App\Repositories\Admin;

use App\Models\TeacherAttendance;
use App\Models\TeacherProfile;
use App\Repositories\BaseRepository;

class TeacherAttendanceRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(TeacherAttendance $model)
    {
        parent::__construct($model);
    }

    public function getTeachersForAttendance(
        array $filters = [],
        int $page = 1,
        int $perPage = 10,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        $query = TeacherProfile::query()
            ->with([
                'user:id,name,email,mobile,profile_image,status',
                'attendances' => function ($query) use ($filters) {

                    if (! empty($filters['attendance_date'])) {

                        $query->where(
                            'attendance_date',
                            $filters['attendance_date']
                        );
                    }
                },

            ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        $query->when(
            ! empty($filters['search']),
            function ($query) use ($filters) {

                $search = $filters['search'];

                $query->where(function ($q) use ($search) {

                    $q->where('employee_id', 'like', "%{$search}%");

                    $q->orWhereHas('user', function ($userQuery) use ($search) {

                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%");

                    });
                });
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        $query->when(! empty($filters['status']), function ($query) use ($filters) {

            $query->whereHas('user', function ($userQuery) use ($filters) {

                $userQuery->where('status', $filters['status']);
            });
        });

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        if ($orderColumn !== null && isset($columns[(int) $orderColumn])) {

            $sortColumn = $columns[(int) $orderColumn];

            $query->orderBy($sortColumn, $orderDirection);

        } else {

            $query->orderBy('employee_id', 'asc');

        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $total = $query->count();

        $paginator = $query->paginate(
            $perPage,
            ['*'],
            'page',
            $page
        );

        // $data = $paginator->items();
        $data = collect(
            $paginator->items()
        )->map(function ($teacher) {

            $attendance =
                $teacher->attendances->first();

            return [

                'id' => $teacher->id,
                'teacher_profile_id' => $teacher->id,
                'employee_id' => $teacher->employee_id,
                'teacher_name' => $teacher->user?->name,
                'mobile' => $teacher->user?->mobile,
                'profile_image_url' => $teacher->user?->profile_image_url,
                'attendance_status' => $attendance?->status,
                'remarks' => $attendance?->remarks,

            ];

        })->values()->all();

        return [
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
        ];
    }

    /**
     * Save or update attendance
     */
    public function saveAttendance(
        array $data
    ) {

        return $this->model->updateOrCreate(

            [
                'teacher_profile_id' => $data['teacher_profile_id'],

                'attendance_date' => $data['attendance_date'],
            ],

            [
                'status' => $data['status'],

                'remarks' => $data['remarks'] ?? null,
            ]

        );
    }
}
