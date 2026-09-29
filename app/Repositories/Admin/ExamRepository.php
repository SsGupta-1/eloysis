<?php

namespace App\Repositories\Admin;

use App\Models\Exam;
use App\Repositories\BaseRepository;

class ExamRepository extends BaseRepository
{
    public function __construct(Exam $exam)
    {
        parent::__construct($exam);
    }

    public function getList(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'desc'
    ) {
        $sortableColumns = [
            1 => 'title',
            2 => 'exam_code',
            3 => 'academic_session_id',
            4 => 'class_id',
            5 => 'exam_type',
            6 => 'exam_mode',
            7 => 'start_date',
            8 => 'status',
        ];

        $query = $this->model->newQuery()
            ->with([
                'academicClass:id,class_name',
                'subject:id,subject_name',
                'academicSession:id,name',
                'creator:id,name',
            ])
            ->withCount(['schedules', 'enrolledStudents']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('exam_code', 'like', "%{$search}%")
                    ->orWhereHas('academicClass', function ($cq) use ($search) {
                        $cq->where('class_name', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['academic_session_id'])) {
            $query->where('academic_session_id', $filters['academic_session_id']);
        }

        if (! empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (! empty($filters['exam_type'])) {
            $query->where('exam_type', $filters['exam_type']);
        }

        if (! empty($filters['exam_mode'])) {
            $query->where('exam_mode', $filters['exam_mode']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if ($orderColumn !== null && isset($sortableColumns[$orderColumn])) {
            $query->orderBy($sortableColumns[$orderColumn], $orderDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest('id');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
