<?php

namespace App\Repositories\Admin;

use App\Models\StudentAttendance;
use App\Models\StudentEnrollment;
use App\Repositories\BaseRepository;

class StudentAttendanceRepository extends BaseRepository
{

    /**
     * Create a new class instance.
     */
    public function __construct(StudentAttendance $model) {
        parent::__construct($model);
    }

     /**
     * Get students for attendance DataTable.
     */
    public function getStudentsForAttendance(
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

        $query = StudentEnrollment::query()
            ->with([
                'student.user:id,name,email,mobile,profile_image,status',

                'studentClass:id,class_name',

                'section:id,name',

                'academicSession:id,name',

                'attendances' => function ($query) use ($filters) {

                    if (!empty($filters['attendance_date'])) {

                        $query->where(
                            'attendance_date',
                            $filters['attendance_date']
                        );
                    }
                },
            ]);


        /*
        |--------------------------------------------------------------------------
        | Academic Session
        |--------------------------------------------------------------------------
        */

        $query->when(
            !empty($filters['academic_session_id']),
            function ($query) use ($filters) {

                $query->where(
                    'academic_session_id',
                    $filters['academic_session_id']
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Class
        |--------------------------------------------------------------------------
        */

        $query->when(
            !empty($filters['class_id']),
            function ($query) use ($filters) {

                $query->where(
                    'class_id',
                    $filters['class_id']
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Section
        |--------------------------------------------------------------------------
        */

        $query->when(
            !empty($filters['section_id']),
            function ($query) use ($filters) {

                $query->where(
                    'section_id',
                    $filters['section_id']
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Active Student
        |--------------------------------------------------------------------------
        */

        $query->whereHas(
            'student.user',
            function ($query) {

                $query->where(
                    'status',
                    1
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        $query->when(
            !empty($filters['search']),
            function ($query) use ($filters) {

                $search = $filters['search'];

                $query->where(function ($q) use ($search) {

                    /*
                    | Admission Number
                    */

                    $q->whereHas(
                        'student',
                        function ($studentQuery) use ($search) {

                            $studentQuery->where(
                                'admission_no',
                                'like',
                                "%{$search}%"
                            );
                        }
                    );


                    /*
                    | Student Name / Email / Mobile
                    */

                    $q->orWhereHas(
                        'student.user',
                        function ($userQuery) use ($search) {

                            $userQuery
                                ->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'mobile',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );

                });
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Total Records
        |--------------------------------------------------------------------------
        */

        $recordsTotalQuery =
            StudentEnrollment::query();


        $recordsTotalQuery->when(
            !empty($filters['academic_session_id']),
            function ($query) use ($filters) {

                $query->where(
                    'academic_session_id',
                    $filters['academic_session_id']
                );
            }
        );


        $recordsTotalQuery->when(
            !empty($filters['class_id']),
            function ($query) use ($filters) {

                $query->where(
                    'class_id',
                    $filters['class_id']
                );
            }
        );


        $recordsTotalQuery->when(
            !empty($filters['section_id']),
            function ($query) use ($filters) {

                $query->where(
                    'section_id',
                    $filters['section_id']
                );
            }
        );


        $recordsTotal =
            $recordsTotalQuery->count();


        /*
        |--------------------------------------------------------------------------
        | Filtered Records
        |--------------------------------------------------------------------------
        */

        $recordsFiltered =
            $query->count();


        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $sortableColumns = [

            1 => 'id',

        ];


        if (
            $orderColumn !== null
            && isset($sortableColumns[$orderColumn])
        ) {

            $query->orderBy(
                $sortableColumns[$orderColumn],
                $orderDirection === 'desc'
                    ? 'desc'
                    : 'asc'
            );

        } else {

            $query->latest('id');

        }


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $paginator = $query->paginate(
            $perPage,
            ['*'],
            'page',
            $page
        );


        /*
        |--------------------------------------------------------------------------
        | DataTable Data
        |--------------------------------------------------------------------------
        */

        $data = collect(
            $paginator->items()
        )->map(function ($enrollment) {

            $attendance =
                $enrollment->attendances->first();


            return [

                'id' => $enrollment->id,

                'student_enrollment_id' => $enrollment->id,
                'roll_number' => $enrollment->roll_number,

                'admission_no' => $enrollment->student ?->admission_no,

                'student_name' => $enrollment->student?->user?->name,
                'profile_image_url' => $enrollment->student?->user?->profile_image_url,

                'class_name' =>$enrollment->studentClass?->class_name,

                'section_name' =>$enrollment->section?->name,

                'attendance_status' =>$attendance?->status,

                'remarks' =>$attendance?->remarks,

            ];

        })->values()->all();


        /*
        |--------------------------------------------------------------------------
        | DataTable Response
        |--------------------------------------------------------------------------
        */

        return [

            'recordsTotal' =>
                $recordsTotal,

            'recordsFiltered' =>
                $recordsFiltered,

            'data' =>
                $data,

        ];
    }

    /**
     * Get attendance for a particular enrollment and date
     */
    public function getAttendance(
        int $enrollmentId,
        string $date
    ) {
        return $this->model
            ->where(
                'student_enrollment_id',
                $enrollmentId
            )
            ->where(
                'attendance_date',
                $date
            )
            ->first();
    }

    /**
     * Save or update attendance
     */
    public function saveAttendance(
        array $data
    ) {

        return $this->model->updateOrCreate(

            [
                'student_enrollment_id' =>
                    $data['student_enrollment_id'],

                'attendance_date' =>
                    $data['attendance_date'],
            ],

            [
                'status' =>
                    $data['status'],

                'remarks' =>
                    $data['remarks'] ?? null,
            ]

        );
    }

    /**
     * Get attendance history
     */
    public function getHistory(array $filters = [])
    {
        $query = $this->model
            ->with([
                'enrollment.student.user:id,name,email,mobile,profile_image',

                'enrollment.academicSession:id,name',

                'enrollment.studentClass:id,class_name',

                'enrollment.section:id,name',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Date
        |--------------------------------------------------------------------------
        */

        if (! empty($filters['attendance_date'])) {

            $query->where(
                'attendance_date',
                $filters['attendance_date']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Academic Session
        |--------------------------------------------------------------------------
        */

        if (! empty($filters['academic_session_id'])) {

            $query->whereHas(
                'enrollment',
                function ($query) use ($filters) {

                    $query->where(
                        'academic_session_id',
                        $filters['academic_session_id']
                    );

                }
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Class
        |--------------------------------------------------------------------------
        */

        if (! empty($filters['class_id'])) {

            $query->whereHas(
                'enrollment',
                function ($query) use ($filters) {

                    $query->where(
                        'class_id',
                        $filters['class_id']
                    );

                }
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Section
        |--------------------------------------------------------------------------
        */

        if (! empty($filters['section_id'])) {

            $query->whereHas(
                'enrollment',
                function ($query) use ($filters) {

                    $query->where(
                        'section_id',
                        $filters['section_id']
                    );

                }
            );

        }

        return $query
            ->latest('attendance_date')
            ->paginate(
                $filters['per_page'] ?? 20
            );
    }
}
