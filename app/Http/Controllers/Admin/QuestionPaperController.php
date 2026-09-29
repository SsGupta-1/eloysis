<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\QuestionPaperRequest;
use App\Models\Exam;
use App\Models\QuestionPaper;
use App\Services\Admin\QuestionPaperService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionPaperController extends BaseController
{
    public function __construct(
        protected QuestionPaperService $questionPaperService
    ) {}

    public function index(): View
    {
        return view('admin.question_papers.index', [
            'academicSessions' => academic_session_options(),
            'classes' => class_options(),
            'subjects' => subject_options(),
            'approvalStatuses' => [
                QuestionPaper::APPROVAL_DRAFT => 'Draft',
                QuestionPaper::APPROVAL_PENDING => 'Pending Approval',
                QuestionPaper::APPROVAL_APPROVED => 'Approved',
                QuestionPaper::APPROVAL_REJECTED => 'Rejected',
            ],
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search.value'),
            'class_id' => $request->input('class_id'),
            'subject_id' => $request->input('subject_id'),
            'approval_status' => $request->input('approval_status'),
            'is_locked' => $request->input('is_locked'),
            'status' => $request->input('status'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'desc');

        $papers = $this->questionPaperService->getList(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($papers, (int) $request->input('draw', 1));
    }

    public function create(): View
    {
        $suggestedCode = 'QP-'.date('Y').'-'.strtoupper(substr(uniqid(), -6));

        return view('admin.question_papers.create', [
            'academicSessions' => academic_session_options(),
            'classes' => class_options(),
            'subjects' => subject_options(),
            'exams' => Exam::where('status', '!=', 'closed')->pluck('title', 'id')->toArray(),
            'suggestedCode' => $suggestedCode,
        ]);
    }

    public function store(QuestionPaperRequest $request): JsonResponse
    {
        try {
            $paper = $this->questionPaperService->create($request->validated());

            return $this->success('Question paper created successfully.', [
                'paper' => $paper,
                'redirect_url' => route('admin.question-papers.show', $paper->id),
            ]);
        } catch (Exception $e) {
            return $this->error('Failed to create question paper: '.$e->getMessage());
        }
    }

    public function show(int $id): View
    {
        $paper = $this->questionPaperService->find($id);
        abort_if(! $paper, 404, 'Question paper not found.');

        // Log paper view audit
        $this->questionPaperService->logAudit($paper->id, 'viewed', 'Question paper preview viewed.');

        return view('admin.question_papers.show', [
            'paper' => $paper,
        ]);
    }

    public function edit(int $id): View
    {
        $paper = $this->questionPaperService->find($id);
        abort_if(! $paper, 404, 'Question paper not found.');

        if ($paper->is_locked) {
            return redirect()
                ->route('admin.question-papers.show', $paper->id)
                ->with('error', 'This question paper is locked and cannot be edited. Please unlock it first.');
        }

        return view('admin.question_papers.edit', [
            'paper' => $paper,
            'academicSessions' => academic_session_options(),
            'classes' => class_options(),
            'subjects' => subject_options(),
            'exams' => Exam::where('status', '!=', 'closed')->pluck('title', 'id')->toArray(),
        ]);
    }

    public function update(QuestionPaperRequest $request, int $id): JsonResponse
    {
        try {
            $paper = $this->questionPaperService->update($id, $request->validated());

            return $this->success('Question paper updated successfully.', [
                'paper' => $paper,
                'redirect_url' => route('admin.question-papers.show', $paper->id),
            ]);
        } catch (Exception $e) {
            return $this->error('Failed to update question paper: '.$e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->questionPaperService->delete($id);

            return $this->success('Question paper deleted successfully.');
        } catch (Exception $e) {
            return $this->error('Failed to delete question paper: '.$e->getMessage());
        }
    }

    public function toggleLock(int $id): JsonResponse
    {
        try {
            $paper = $this->questionPaperService->toggleLock($id);
            $msg = $paper->is_locked ? 'Question paper locked successfully.' : 'Question paper unlocked successfully.';

            return $this->success($msg, ['is_locked' => $paper->is_locked]);
        } catch (Exception $e) {
            return $this->error('Failed to change lock state: '.$e->getMessage());
        }
    }

    public function handleApproval(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,pending_approval,approved,rejected',
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            $paper = $this->questionPaperService->handleApproval($id, $validated['status'], $validated['remarks'] ?? null);

            return $this->success('Approval status updated successfully.', [
                'approval_status' => $paper->approval_status,
            ]);
        } catch (Exception $e) {
            return $this->error('Failed to update approval status: '.$e->getMessage());
        }
    }

    public function generateSets(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'set_names' => 'required|array|min:1',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_options' => 'nullable|boolean',
        ]);

        try {
            $paper = $this->questionPaperService->generateSets(
                $id,
                $validated['set_names'],
                (bool) ($validated['shuffle_questions'] ?? true),
                (bool) ($validated['shuffle_options'] ?? false)
            );

            return $this->success('Paper sets ('.implode(', ', $validated['set_names']).') generated successfully with question ordering.', [
                'paper' => $paper,
            ]);
        } catch (Exception $e) {
            return $this->error('Failed to generate paper sets: '.$e->getMessage());
        }
    }

    public function autoGenerateBlueprint(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:academic_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'sections' => 'required|array|min:1',
        ]);

        try {
            $result = $this->questionPaperService->autoGenerateBlueprint($validated);

            return $this->success('Questions auto-selected according to blueprint criteria.', $result);
        } catch (Exception $e) {
            return $this->error('Failed to auto-generate blueprint: '.$e->getMessage());
        }
    }

    public function print(Request $request, int $id): View
    {
        $paper = $this->questionPaperService->find($id);
        abort_if(! $paper, 404, 'Question paper not found.');

        $setCode = $request->input('set', 'ALL');
        $includeSolutions = (bool) $request->input('solutions', 0);

        // Filter items by selected set code
        $itemsQuery = $paper->items();
        if ($paper->has_sets && $setCode !== 'ALL') {
            $setItems = $paper->items->where('set_code', $setCode);
            if ($setItems->isNotEmpty()) {
                $paper->setRelation('items', $setItems);
            }
        }

        $this->questionPaperService->logAudit($paper->id, 'printed', "Question paper printed/exported. Set: [{$setCode}], Solutions: ".($includeSolutions ? 'Yes' : 'No'));

        return view('admin.question_papers.print', [
            'paper' => $paper,
            'selectedSet' => $setCode,
            'includeSolutions' => $includeSolutions,
        ]);
    }
}
