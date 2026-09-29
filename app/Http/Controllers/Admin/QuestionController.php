<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\QuestionRequest;
use App\Models\Question;
use App\Services\Admin\QuestionService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionController extends BaseController
{
    public function __construct(
        protected QuestionService $questionService
    ) {}

    public function index(): View
    {
        return view('admin.questions.index', [
            'academicSessions' => academic_session_options(),
            'classes' => class_options(),
            'subjects' => subject_options(),
            'questionTypes' => [
                Question::TYPE_MCQ => 'Multiple Choice (MCQ)',
                Question::TYPE_TRUE_FALSE => 'True / False',
                Question::TYPE_FILL_BLANKS => 'Fill in the Blanks',
                Question::TYPE_SHORT_ANSWER => 'Short Answer',
                Question::TYPE_LONG_ANSWER => 'Long Answer',
                Question::TYPE_DESCRIPTIVE => 'Descriptive',
                Question::TYPE_MATCH_FOLLOWING => 'Match the Following',
            ],
            'difficultyLevels' => [
                Question::DIFFICULTY_EASY => 'Easy',
                Question::DIFFICULTY_MEDIUM => 'Medium',
                Question::DIFFICULTY_HARD => 'Hard',
            ],
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search.value'),
            'class_id' => $request->input('class_id'),
            'subject_id' => $request->input('subject_id'),
            'question_type' => $request->input('question_type'),
            'difficulty_level' => $request->input('difficulty_level'),
            'status' => $request->input('status'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'desc');

        $questions = $this->questionService->getList(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($questions, (int) $request->input('draw', 1));
    }

    public function create(): View
    {
        return view('admin.questions.create', [
            'academicSessions' => academic_session_options(),
            'classes' => class_options(),
            'subjects' => subject_options(),
            'questionTypes' => [
                Question::TYPE_MCQ => 'Multiple Choice (MCQ)',
                Question::TYPE_TRUE_FALSE => 'True / False',
                Question::TYPE_FILL_BLANKS => 'Fill in the Blanks',
                Question::TYPE_SHORT_ANSWER => 'Short Answer',
                Question::TYPE_LONG_ANSWER => 'Long Answer',
                Question::TYPE_DESCRIPTIVE => 'Descriptive',
                Question::TYPE_MATCH_FOLLOWING => 'Match the Following',
            ],
            'difficultyLevels' => [
                Question::DIFFICULTY_EASY => 'Easy',
                Question::DIFFICULTY_MEDIUM => 'Medium',
                Question::DIFFICULTY_HARD => 'Hard',
            ],
            'bloomsTaxonomy' => [
                'remember' => 'Remember (Knowledge)',
                'understand' => 'Understand (Comprehension)',
                'apply' => 'Apply (Application)',
                'analyze' => 'Analyze (Analysis)',
                'evaluate' => 'Evaluate (Evaluation)',
                'create' => 'Create (Synthesis)',
            ],
        ]);
    }

    public function store(QuestionRequest $request): JsonResponse
    {
        try {
            $question = $this->questionService->create($request->validated());

            return $this->success('Question created successfully in Question Bank.', $question);
        } catch (Exception $e) {
            return $this->error('Failed to create question: '.$e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        $question = $this->questionService->find($id);
        abort_if(! $question, 404, 'Question not found.');

        return $this->success('Question fetched successfully.', $question);
    }

    public function edit(int $id): View
    {
        $question = $this->questionService->find($id);
        abort_if(! $question, 404, 'Question not found.');

        return view('admin.questions.edit', [
            'question' => $question,
            'academicSessions' => academic_session_options(),
            'classes' => class_options(),
            'subjects' => subject_options(),
            'questionTypes' => [
                Question::TYPE_MCQ => 'Multiple Choice (MCQ)',
                Question::TYPE_TRUE_FALSE => 'True / False',
                Question::TYPE_FILL_BLANKS => 'Fill in the Blanks',
                Question::TYPE_SHORT_ANSWER => 'Short Answer',
                Question::TYPE_LONG_ANSWER => 'Long Answer',
                Question::TYPE_DESCRIPTIVE => 'Descriptive',
                Question::TYPE_MATCH_FOLLOWING => 'Match the Following',
            ],
            'difficultyLevels' => [
                Question::DIFFICULTY_EASY => 'Easy',
                Question::DIFFICULTY_MEDIUM => 'Medium',
                Question::DIFFICULTY_HARD => 'Hard',
            ],
            'bloomsTaxonomy' => [
                'remember' => 'Remember (Knowledge)',
                'understand' => 'Understand (Comprehension)',
                'apply' => 'Apply (Application)',
                'analyze' => 'Analyze (Analysis)',
                'evaluate' => 'Evaluate (Evaluation)',
                'create' => 'Create (Synthesis)',
            ],
        ]);
    }

    public function update(QuestionRequest $request, int $id): JsonResponse
    {
        try {
            $question = $this->questionService->update($id, $request->validated());

            return $this->success('Question updated successfully.', $question);
        } catch (Exception $e) {
            return $this->error('Failed to update question: '.$e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->questionService->delete($id);

            return $this->success('Question deleted successfully from Question Bank.');
        } catch (Exception $e) {
            return $this->error('Failed to delete question: '.$e->getMessage());
        }
    }

    public function changeStatus(int $id): JsonResponse
    {
        $updated = $this->questionService->updateStatus($id);
        if (! $updated) {
            return $this->error('Question not found.');
        }

        return $this->success('Question status updated successfully.');
    }

    /**
     * AJAX endpoint to search & fetch questions for Question Paper Builder
     */
    public function searchForSelection(Request $request): JsonResponse
    {
        $filters = [
            'class_id' => $request->input('class_id'),
            'subject_id' => $request->input('subject_id'),
            'question_type' => $request->input('question_type'),
            'difficulty_level' => $request->input('difficulty_level'),
            'chapter_name' => $request->input('chapter_name'),
            'search' => $request->input('search'),
        ];

        $questions = $this->questionService->getQuestionsForSelection($filters);

        return $this->success('Questions fetched successfully.', $questions);
    }
}
