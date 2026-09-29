<?php

namespace App\Services\Admin;

use App\Helpers\UploadHelper;
use App\Models\Question;
use App\Repositories\Admin\QuestionRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionService
{
    public function __construct(
        protected QuestionRepository $questionRepository
    ) {}

    public function getList(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'desc'
    ) {
        return $this->questionRepository->getList($filters, $perPage, $page, $orderColumn, $orderDirection);
    }

    public function find(int $id): ?Question
    {
        return Question::with(['academicClass', 'subject', 'academicSession', 'creator'])->find($id);
    }

    public function create(array $data): Question
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['image_path']) && ! is_string($data['image_path'])) {
                $data['image_path'] = UploadHelper::upload($data['image_path'], 'assets/uploads/questions');
            }

            if (isset($data['attachment_url']) && ! is_string($data['attachment_url'])) {
                $data['attachment_url'] = UploadHelper::upload($data['attachment_url'], 'assets/uploads/questions/docs');
            }

            $data['created_by'] = Auth::guard('admin')->id();

            // Process Options according to question type
            $this->normalizeQuestionData($data);

            return $this->questionRepository->create($data);
        });
    }

    public function update(int|Question $question, array $data): Question
    {
        return DB::transaction(function () use ($question, $data) {
            $questionModel = $question instanceof Question ? $question : $this->find($question);

            if (! $questionModel) {
                throw new \Exception('Question not found.');
            }

            if (isset($data['image_path']) && ! is_string($data['image_path'])) {
                $data['image_path'] = UploadHelper::replace(
                    $data['image_path'],
                    $questionModel->image_path,
                    'assets/uploads/questions'
                );
            }

            if (isset($data['attachment_url']) && ! is_string($data['attachment_url'])) {
                $data['attachment_url'] = UploadHelper::replace(
                    $data['attachment_url'],
                    $questionModel->attachment_url,
                    'assets/uploads/questions/docs'
                );
            }

            $this->normalizeQuestionData($data);

            $this->questionRepository->update($questionModel->id, $data);

            return $questionModel->fresh(['academicClass', 'subject', 'academicSession']);
        });
    }

    public function delete(int $id): bool
    {
        return $this->questionRepository->delete($id);
    }

    public function updateStatus(int $id): bool
    {
        $question = $this->find($id);
        if (! $question) {
            return false;
        }

        $newStatus = ! $question->status;
        $this->questionRepository->update($id, ['status' => $newStatus]);

        return true;
    }

    public function getQuestionsForSelection(array $filters = [])
    {
        $query = Question::query()
            ->where('status', 1)
            ->with(['subject:id,subject_name', 'academicClass:id,class_name']);

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

        if (! empty($filters['chapter_name'])) {
            $query->where('chapter_name', $filters['chapter_name']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('question_text', 'like', "%{$search}%")
                    ->orWhere('chapter_name', 'like', "%{$search}%")
                    ->orWhere('topic_name', 'like', "%{$search}%");
            });
        }

        return $query->latest('id')->get();
    }

    protected function normalizeQuestionData(array &$data): void
    {
        $type = $data['question_type'] ?? 'mcq';

        if ($type === 'mcq') {
            if (isset($data['options']) && is_array($data['options'])) {
                $data['option_a'] = $data['options']['a'] ?? ($data['options'][0] ?? null);
                $data['option_b'] = $data['options']['b'] ?? ($data['options'][1] ?? null);
                $data['option_c'] = $data['options']['c'] ?? ($data['options'][2] ?? null);
                $data['option_d'] = $data['options']['d'] ?? ($data['options'][3] ?? null);
                $data['options_data'] = $data['options'];
            }
        } elseif ($type === 'true_false') {
            $data['option_a'] = 'True';
            $data['option_b'] = 'False';
            $data['options_data'] = ['a' => 'True', 'b' => 'False'];
        } elseif ($type === 'match_following') {
            if (isset($data['match_pairs']) && is_array($data['match_pairs'])) {
                $data['options_data'] = $data['match_pairs'];
            }
        }
    }
}
