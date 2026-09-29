<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Section;
use App\Services\Admin\ResultService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResultController extends BaseController
{
    public function __construct(
        protected ResultService $resultService
    ) {}

    public function index(): View
    {
        return view('admin.results.index', [
            'academicSessions' => academic_session_options(),
            'classes' => class_options(),
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search.value'),
            'academic_session_id' => $request->input('academic_session_id'),
            'class_id' => $request->input('class_id'),
            'is_published' => $request->input('is_published'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'desc');

        $exams = $this->resultService->getExamsForResults(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($exams, (int) $request->input('draw', 1));
    }

    public function examResults(Request $request, int $examId): View
    {
        $sectionId = $request->input('section_id') ? (int) $request->input('section_id') : null;
        $broadsheet = $this->resultService->getExamBroadsheet($examId, null, $sectionId);

        $sections = [];
        if ($broadsheet['exam']->class_id) {
            $sections = Section::pluck('name', 'id')->toArray();
        }

        return view('admin.results.exam_results', [
            'exam' => $broadsheet['exam'],
            'schedules' => $broadsheet['schedules'],
            'students' => $broadsheet['students_data'],
            'summary' => $broadsheet['summary'],
            'sections' => $sections,
            'selectedSection' => $sectionId,
        ]);
    }

    public function studentResults(Request $request, int $studentEnrollmentId): View
    {
        $examId = (int) $request->input('exam_id');
        abort_if(! $examId, 400, 'Exam ID is required.');

        $reportCard = $this->resultService->getStudentReportCard($examId, $studentEnrollmentId);

        return view('admin.results.report_card', $reportCard);
    }

    public function tabulationPrint(Request $request, int $examId): View
    {
        $sectionId = $request->input('section_id') ? (int) $request->input('section_id') : null;
        $broadsheet = $this->resultService->getExamBroadsheet($examId, null, $sectionId);

        return view('admin.results.tabulation_print', [
            'exam' => $broadsheet['exam'],
            'schedules' => $broadsheet['schedules'],
            'students' => $broadsheet['students_data'],
            'summary' => $broadsheet['summary'],
            'selectedSection' => $sectionId,
        ]);
    }

    public function publishToggle(Request $request, int $examId): JsonResponse
    {
        try {
            $exam = $this->resultService->togglePublish($examId);
            $msg = $exam->is_published ? 'Exam results published successfully.' : 'Exam results unpublished (hidden from students).';

            return $this->success($msg, ['is_published' => $exam->is_published]);
        } catch (Exception $e) {
            return $this->error('Failed to change result publish status: '.$e->getMessage());
        }
    }
}
