<?php

namespace App\Repositories\Admin;

use App\Models\QuestionPaper;
use App\Repositories\BaseRepository;

class QuestionPaperRepository extends BaseRepository
{
    public function __construct(QuestionPaper $questionPaper)
    {
        parent::__construct($questionPaper);
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
            2 => 'paper_code',
            3 => 'class_id',
            4 => 'subject_id',
            5 => 'total_marks',
            6 => 'approval_status',
            7 => 'is_locked',
            8 => 'status',
        ];

        $query = $this->model->newQuery()
            ->with([
                'academicClass:id,class_name',
                'subject:id,subject_name,subject_code',
                'academicSession:id,name',
                'creator:id,name',
                'approver:id,name',
            ])
            ->withCount(['sections', 'items']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('paper_code', 'like', "%{$search}%")
                    ->orWhereHas('subject', function ($sq) use ($search) {
                        $sq->where('subject_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('academicClass', function ($cq) use ($search) {
                        $cq->where('class_name', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (! empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (! empty($filters['approval_status'])) {
            $query->where('approval_status', $filters['approval_status']);
        }

        if (isset($filters['is_locked']) && $filters['is_locked'] !== '' && $filters['is_locked'] !== null) {
            $query->where('is_locked', (bool) $filters['is_locked']);
        }

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== null) {
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
