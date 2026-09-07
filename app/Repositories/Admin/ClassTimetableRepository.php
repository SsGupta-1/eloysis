<?php

namespace App\Repositories\Admin;

use App\Repositories\BaseRepository;
use App\Models\ClassTimetables;

class ClassTimetableRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(ClassTimetables $model)
    {
        parent::__construct($model);
    }

    // public function getLists($filters, $page, $perPage, $orderColumn, $orderDirection)
    // {
    //     try {
    //         $query = $this->model->with([
    //             'academicSession:id,name',
    //             'class:id,class_name',
    //             'classSubject.subject:id,subject_name,subject_code',
    //             'period:id,name,start_time,end_time',
    //             'teacher:id,name',
    //             'classSection.section:id,name'
    //         ]);
            
    //     $query->when(
    //         !empty($filters['academic_session_id']),
    //         function ($query) use ($filters) {

    //             $query->where(
    //                 'academic_session_id',
    //                 $filters['academic_session_id']
    //             );
    //         }
    //     );

    //     $query->when(
    //         !empty($filters['class_id']),
    //         function ($query) use ($filters) {

    //             $query->where(
    //                 'class_id',
    //                 $filters['class_id']
    //             );
    //         }
    //     );

    //     $query->when(
    //         !empty($filters['section_id']),
    //         function ($query) use ($filters) {

    //             $query->where(
    //                 'section_id',
    //                 $filters['section_id']
    //             );
    //         }
    //     );

    //     $query->when(
    //         !empty($filters['day']),
    //         function ($query) use ($filters) {

    //             $query->where(
    //                 'day',
    //                 $filters['day']
    //             );
    //         }
    //     );

    //     $query->when(
    //         !empty($filters['teacher_id']),
    //         function ($query) use ($filters) {

    //             $query->where(
    //                 'teacher_id',
    //                 $filters['teacher_id']
    //             );
    //             }
    //     );

    //      /*
    //     |--------------------------------------------------------------------------
    //     | Search
    //     |--------------------------------------------------------------------------
    //     */

    //     $query->when(
    //         !empty($filters['search']),
    //         function ($query) use ($filters) {

    //             $search = $filters['search'];

    //             $query->where(function ($q) use ($search) {

    //                 $q->where('day','like',"%{$search}%");

    //                 $q->orWhereHas(
    //                     'class',
    //                     function ($classQuery) use ($search) {

    //                         $classQuery->where('class_name','like',"%{$search}%");
    //                     }
    //                 );

    //                 $q->orWhereHas(
    //                     'teacher',
    //                     function ($teacherQuery) use ($search) {

    //                         $teacherQuery
    //                             ->where('name','like',"%{$search}%" )
    //                             ->orWhere('email','like',"%{$search}%" )
    //                             ->orWhere('mobile','like',"%{$search}%" );
    //                     }
    //                 );

    //             });
    //         }
    //     );

    //     $query->when(isset($filters['filter_status']) && $filters['filter_status'] !== '',
    //         function ($query) use ($filters) {
    //             $query->where('status', $filters['filter_status']);
    //         });

    //     $sortableColumns = [
    //         1 => 'id',
    //         2 => 'day',
    //     ];

    //     if ($orderColumn !== null && isset($sortableColumns[$orderColumn])) {
    //         $query->orderBy($sortableColumns[$orderColumn], $orderDirection === 'desc' ? 'desc' : 'asc');
    //     } else {

    //         $query->latest('id');

    //     }

    //         // Apply pagination
    //         $classTimetables = $query->paginate($perPage, ['*'], 'page', $page);

    //         return [
    //             'draw' => $filters['draw'] ?? 1,
    //             'recordsTotal' => $classTimetables->total(),
    //             'recordsFiltered' => $classTimetables->total(),
    //             'data' => $classTimetables->items(),
    //         ];
    //     } catch (Exception $exception) {
    //         throw $exception;
    //     }
    // }

     /**
     * Get Class Timetable List.
     */
    public function getLists(
        array $filters = [],
        int $page = 1,
        int $perPage = 10,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        try {

            /*
            |--------------------------------------------------------------------------
            | Base Query
            |--------------------------------------------------------------------------
            */

            $query = $this->model
                ->newQuery()
                ->with([

                    'teacherSubject:id,teacher_id,class_id,section_id,subject_id,status',

                    'teacherSubject.teacher:id,name',

                    'teacherSubject.subjectClass:id,class_name',

                    'teacherSubject.section:id,name',

                    'teacherSubject.subject:id,subject_name,subject_code',

                    'academicSession:id,name',

                    'period:id,name,start_time,end_time',

                ]);

            /*
            |--------------------------------------------------------------------------
            | Academic Session Filter
            |--------------------------------------------------------------------------
            */

            $query->when(!empty($filters['academic_session_id']),
                function ($query) use ($filters) {
                    $query->where('academic_session_id',$filters['academic_session_id']);

                }
            );

            /*
            |--------------------------------------------------------------------------
            | Class Filter
            |--------------------------------------------------------------------------
            */

            $query->when(!empty($filters['class_id']),
                function ($query) use ($filters) {

                    $query->whereHas('teacherSubject',function ($teacherSubjectQuery) use ($filters) {

                            $teacherSubjectQuery->where('class_id',$filters['class_id']);
                        });
                }
            );

            /*
            |--------------------------------------------------------------------------
            | Section Filter
            |--------------------------------------------------------------------------
            */

            $query->when(!empty($filters['section_id']),
                function ($query) use ($filters) {

                    $query->whereHas('teacherSubject',function ($teacherSubjectQuery) use ($filters) {

                            $teacherSubjectQuery->where('section_id',$filters['section_id']);
                        }
                    );
                }
            );

            /*
            |--------------------------------------------------------------------------
            | Teacher Filter
            |--------------------------------------------------------------------------
            */

            $query->when(!empty($filters['teacher_id']),
                function ($query) use ($filters) {

                    $query->whereHas('teacherSubject',function ($teacherSubjectQuery) use ($filters) {

                            $teacherSubjectQuery->where('teacher_id',$filters['teacher_id']);
                        }
                    );
                }
            );

            /*
            |--------------------------------------------------------------------------
            | Day Filter
            |--------------------------------------------------------------------------
            */

            $query->when(!empty($filters['day']),
                function ($query) use ($filters) {

                    $query->where('day',$filters['day']);
                }
            );

            /*
            |--------------------------------------------------------------------------
            | Status Filter
            |--------------------------------------------------------------------------
            */

            $query->when(isset($filters['status']) && $filters['status'] !== '',
                function ($query) use ($filters) {

                    $query->where('status',$filters['status']);
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
                        | Day
                        */

                        $q->where('day','like',"%{$search}%");

                        /*
                        | Teacher
                        */

                        $q->orWhereHas('teacherSubject.teacher',
                            function ($teacherQuery) use ($search) {

                                $teacherQuery
                                    ->where('name','like',"%{$search}%" )
                                    ->orWhere('email','like',"%{$search}%" )
                                    ->orWhere('mobile','like',"%{$search}%" );
                            }
                        );

                        /*
                        | Class
                        */

                        $q->orWhereHas('teacherSubject.subjectClass',
                            function ($classQuery) use ($search) {

                                $classQuery->where('class_name','like',"%{$search}%");
                            }
                        );

                        /*
                        | Subject
                        */

                        $q->orWhereHas('teacherSubject.subject',
                            function ($subjectQuery) use ($search) {

                                $subjectQuery
                                    ->where('subject_name','like',"%{$search}%")
                                    ->orWhere('subject_code','like',"%{$search}%");
                            }
                        );

                    });
                }
            );

            /*
            |--------------------------------------------------------------------------
            | Sorting
            |--------------------------------------------------------------------------
            */

            $sortableColumns = [

                1 => 'day',

            ];

            if ($orderColumn !== null && isset($sortableColumns[$orderColumn])) {

                $query->orderBy($sortableColumns[$orderColumn],
                    $orderDirection === 'desc' ? 'desc' : 'asc'
                );

            } else {

                /*
                | Default sorting
                */

                $query->orderByRaw("FIELD(day, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday' )")->latest('id');
            }

            /*
            |--------------------------------------------------------------------------
            | Pagination
            |--------------------------------------------------------------------------
            */

            $classTimetables = $query->paginate(
                $perPage,
                ['*'],
                'page',
                $page
            );

            /*
            |--------------------------------------------------------------------------
            | DataTables Response
            |--------------------------------------------------------------------------
            */

            return [

                'draw' => $filters['draw'] ?? 1,

                'recordsTotal' => $classTimetables->total(),

                'recordsFiltered' => $classTimetables->total(),

                'data' => $classTimetables->items(),

            ];

        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
