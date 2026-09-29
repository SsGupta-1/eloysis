<?php

namespace App\Services\Admin;

use App\Models\Question;
use App\Models\QuestionPaper;
use App\Models\QuestionPaperAudit;
use App\Models\QuestionPaperItem;
use App\Models\QuestionPaperSection;
use App\Repositories\Admin\QuestionPaperRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class QuestionPaperService
{
    public function __construct(
        protected QuestionPaperRepository $questionPaperRepository
    ) {}

    public function getList(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'desc'
    ) {
        return $this->questionPaperRepository->getList($filters, $perPage, $page, $orderColumn, $orderDirection);
    }

    public function find(int $id): ?QuestionPaper
    {
        return QuestionPaper::with([
            'academicClass',
            'subject',
            'academicSession',
            'creator',
            'approver',
            'sections.items.question',
            'items.question',
            'audits.user',
        ])->find($id);
    }

    public function create(array $data): QuestionPaper
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = Auth::guard('admin')->id();

            if (empty($data['paper_code'])) {
                $data['paper_code'] = 'QP-'.date('Y').'-'.strtoupper(substr(uniqid(), -6));
            }

            $sectionsData = $data['sections'] ?? [];
            unset($data['sections']);

            /** @var QuestionPaper $paper */
            $paper = $this->questionPaperRepository->create($data);

            $this->saveSectionsAndItems($paper, $sectionsData);

            $this->logAudit($paper->id, 'created', 'Question paper created with initial sections and questions.');

            return $paper->fresh(['sections.items.question', 'items.question']);
        });
    }

    public function update(int|QuestionPaper $paper, array $data): QuestionPaper
    {
        return DB::transaction(function () use ($paper, $data) {
            $paperModel = $paper instanceof QuestionPaper ? $paper : $this->find($paper);

            if (! $paperModel) {
                throw new Exception('Question paper not found.');
            }

            if ($paperModel->is_locked) {
                throw new Exception('Question paper is locked and cannot be modified.');
            }

            $sectionsData = $data['sections'] ?? null;
            unset($data['sections']);

            $this->questionPaperRepository->update($paperModel->id, $data);

            if ($sectionsData !== null) {
                // Remove old sections & items and re-save
                $paperModel->sections()->delete();
                $paperModel->items()->delete();
                $this->saveSectionsAndItems($paperModel, $sectionsData);
            }

            $this->logAudit($paperModel->id, 'updated', 'Question paper details and structure updated.');

            return $paperModel->fresh(['sections.items.question', 'items.question']);
        });
    }

    public function delete(int $id): bool
    {
        $paper = $this->find($id);
        if (! $paper) {
            return false;
        }

        if ($paper->is_locked) {
            throw new Exception('Cannot delete a locked question paper.');
        }

        $this->logAudit($id, 'deleted', 'Question paper moved to trash.');

        return $this->questionPaperRepository->delete($id);
    }

    public function toggleLock(int $id): QuestionPaper
    {
        $paper = $this->find($id);
        if (! $paper) {
            throw new Exception('Question paper not found.');
        }

        $newLocked = ! $paper->is_locked;
        $paper->update(['is_locked' => $newLocked]);

        $action = $newLocked ? 'locked' : 'unlocked';
        $this->logAudit($paper->id, $action, "Question paper was {$action} by ".Auth::guard('admin')->user()?->name);

        return $paper->fresh(['audits.user', 'sections.items.question', 'items.question']);
    }

    public function handleApproval(int $id, string $status, ?string $remarks = null): QuestionPaper
    {
        $paper = $this->find($id);
        if (! $paper) {
            throw new Exception('Question paper not found.');
        }

        $validStatuses = [
            QuestionPaper::APPROVAL_PENDING,
            QuestionPaper::APPROVAL_APPROVED,
            QuestionPaper::APPROVAL_REJECTED,
            QuestionPaper::APPROVAL_DRAFT,
        ];

        if (! in_array($status, $validStatuses, true)) {
            throw new Exception('Invalid approval status.');
        }

        $paper->update([
            'approval_status' => $status,
            'approved_by' => in_array($status, [QuestionPaper::APPROVAL_APPROVED, QuestionPaper::APPROVAL_REJECTED], true) ? Auth::guard('admin')->id() : null,
            'approved_at' => $status === QuestionPaper::APPROVAL_APPROVED ? now() : null,
        ]);

        $this->logAudit($paper->id, 'approval_'.$status, "Status set to [{$status}]. Remarks: ".($remarks ?? 'None'));

        return $paper->fresh(['audits.user', 'sections.items.question', 'items.question']);
    }

    /**
     * Generate Sets (A, B, C, D) with randomized question order and option shuffling
     */
    public function generateSets(int $paperId, array $setNames = ['A', 'B', 'C', 'D'], bool $shuffleQuestions = true, bool $shuffleOptions = false): QuestionPaper
    {
        return DB::transaction(function () use ($paperId, $setNames, $shuffleQuestions, $shuffleOptions) {
            $paper = $this->find($paperId);
            if (! $paper) {
                throw new Exception('Question paper not found.');
            }

            if ($paper->is_locked) {
                throw new Exception('Cannot regenerate sets on a locked question paper.');
            }

            $baseItems = QuestionPaperItem::where('question_paper_id', $paperId)
                ->where('set_code', 'ALL')
                ->orderBy('display_order')
                ->get();

            if ($baseItems->isEmpty()) {
                // If no 'ALL' items, fetch by default set
                $baseItems = QuestionPaperItem::where('question_paper_id', $paperId)->get();
            }

            // Group items by section
            $itemsBySection = $baseItems->groupBy('section_id');

            // Delete previous non-ALL set items
            QuestionPaperItem::where('question_paper_id', $paperId)
                ->where('set_code', '!=', 'ALL')
                ->delete();

            foreach ($setNames as $setCode) {
                foreach ($itemsBySection as $sectionId => $sectionItems) {
                    $itemArray = $sectionItems->all();

                    if ($shuffleQuestions && $setCode !== 'A') {
                        shuffle($itemArray);
                    }

                    $order = 1;
                    foreach ($itemArray as $item) {
                        QuestionPaperItem::create([
                            'question_paper_id' => $paper->id,
                            'section_id' => $sectionId,
                            'question_id' => $item->question_id,
                            'set_code' => $setCode,
                            'display_order' => $order++,
                            'marks' => $item->marks,
                        ]);
                    }
                }
            }

            $paper->update([
                'has_sets' => true,
                'set_names' => $setNames,
                'shuffle_questions' => $shuffleQuestions,
                'shuffle_options' => $shuffleOptions,
            ]);

            $this->logAudit($paper->id, 'generated_sets', 'Generated Sets: '.implode(', ', $setNames));

            return $paper->fresh(['sections.items.question', 'items.question']);
        });
    }

    /**
     * Auto Generate Questions according to Blueprint criteria
     */
    public function autoGenerateBlueprint(array $criteria): array
    {
        $classId = $criteria['class_id'];
        $subjectId = $criteria['subject_id'];
        $sections = $criteria['sections'] ?? []; // e.g. [{name: 'Section A', type: 'mcq', count: 10, marks: 1, difficulty: 'easy'}]

        $generatedSections = [];
        $totalMarks = 0;

        foreach ($sections as $index => $sectionConfig) {
            $type = $sectionConfig['type'] ?? 'mcq';
            $count = (int) ($sectionConfig['count'] ?? 5);
            $marksPerQ = (float) ($sectionConfig['marks'] ?? 1);
            $difficulty = $sectionConfig['difficulty'] ?? null;
            $chapter = $sectionConfig['chapter'] ?? null;

            $query = Question::query()
                ->where('status', 1)
                ->where('class_id', $classId)
                ->where('subject_id', $subjectId)
                ->where('question_type', $type);

            if ($difficulty) {
                $query->where('difficulty_level', $difficulty);
            }

            if ($chapter) {
                $query->where('chapter_name', $chapter);
            }

            $selectedQuestions = $query->inRandomOrder()->take($count)->get();

            $sectionTotal = $selectedQuestions->count() * $marksPerQ;
            $totalMarks += $sectionTotal;

            $generatedSections[] = [
                'section_name' => $sectionConfig['name'] ?? 'Section '.chr(65 + $index),
                'section_type' => $type,
                'total_questions' => $selectedQuestions->count(),
                'marks_per_question' => $marksPerQ,
                'instructions' => $sectionConfig['instructions'] ?? null,
                'questions' => $selectedQuestions->map(fn ($q) => [
                    'id' => $q->id,
                    'text' => $q->question_text,
                    'type' => $q->question_type,
                    'difficulty' => $q->difficulty_level,
                    'marks' => $marksPerQ,
                ])->toArray(),
            ];
        }

        return [
            'total_marks' => $totalMarks,
            'sections' => $generatedSections,
        ];
    }

    public function logAudit(int $paperId, string $action, ?string $details = null): void
    {
        try {
            QuestionPaperAudit::create([
                'question_paper_id' => $paperId,
                'user_id' => Auth::guard('admin')->id(),
                'action' => $action,
                'details' => $details,
                'ip_address' => Request::ip(),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Ignore audit fail in background
        }
    }

    protected function saveSectionsAndItems(QuestionPaper $paper, array $sectionsData): void
    {
        $calculatedTotalMarks = 0;

        foreach ($sectionsData as $sIndex => $sData) {
            $section = QuestionPaperSection::create([
                'question_paper_id' => $paper->id,
                'section_name' => $sData['section_name'] ?? ('Section '.chr(65 + $sIndex)),
                'section_type' => $sData['section_type'] ?? 'mcq',
                'total_questions' => count($sData['questions'] ?? []),
                'marks_per_question' => $sData['marks_per_question'] ?? null,
                'instructions' => $sData['instructions'] ?? null,
                'sort_order' => $sIndex + 1,
            ]);

            $questions = $sData['questions'] ?? [];
            foreach ($questions as $qIndex => $qItem) {
                $questionId = is_array($qItem) ? ($qItem['question_id'] ?? $qItem['id']) : $qItem;
                $marks = is_array($qItem) ? ($qItem['marks'] ?? ($sData['marks_per_question'] ?? 1.00)) : ($sData['marks_per_question'] ?? 1.00);

                QuestionPaperItem::create([
                    'question_paper_id' => $paper->id,
                    'section_id' => $section->id,
                    'question_id' => $questionId,
                    'set_code' => 'ALL',
                    'display_order' => $qIndex + 1,
                    'marks' => (float) $marks,
                ]);

                $calculatedTotalMarks += (float) $marks;
            }
        }

        if ($calculatedTotalMarks > 0 && empty($paper->total_marks)) {
            $paper->update(['total_marks' => $calculatedTotalMarks]);
        }
    }
}
