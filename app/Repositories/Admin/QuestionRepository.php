<?php

namespace App\Repositories\Admin;

use App\Models\Question;
use App\Repositories\BaseRepository;

class QuestionRepository extends BaseRepository
{
    public function __construct(Question $question)
    {
        parent::__construct($question);
    }

    public function getList(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'desc'
    ) {
        $sortableColumns = [
            1 => 'question_text',
            2 => 'class_id',
            3 => 'subject_id',
            4 => 'question_type',
            5 => 'difficulty_level',
            6 => 'marks',
            7 => 'status',
        ];

        $query = $this->model->newQuery()
            ->with(['academicClass:id,class_name', 'subject:id,subject_name,subject_code', 'academicSession:id,name', 'creator:id,name']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('question_text', 'like', "%{$search}%")
                    ->orWhere('chapter_name', 'like', "%{$search}%")
                    ->orWhere('topic_name', 'like', "%{$search}%")
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

        if (! empty($filters['question_type'])) {
            $query->where('question_type', $filters['question_type']);
        }

        if (! empty($filters['difficulty_level'])) {
            $query->where('difficulty_level', $filters['difficulty_level']);
        }

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== null) {
            $query->where('status', (int) $filters['status']);
        }

        if ($orderColumn !== null && isset($sortableColumns[$orderColumn])) {
            $query->orderBy($sortableColumns[$orderColumn], $orderDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest('id');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
