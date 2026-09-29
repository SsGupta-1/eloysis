<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\ExamRequest;
use App\Models\Exam;
use App\Models\QuestionPaper;
use App\Models\Section;
use App\Models\User;
use App\Services\Admin\ExamService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamController extends BaseController
{
    public function __construct(
        protected ExamService $examService
    ) {}

    public function index(): View
    {
        return view('admin.exams.index', [
            'academicSessions' => academic_session_options(),
            'classes' => class_options(),
            'examTypes' => [
                Exam::TYPE_UNIT_TEST => 'Unit Test',
                Exam::TYPE_MID_TERM => 'Mid-Term Exam',
                Exam::TYPE_QUARTERLY => 'Quarterly Exam',
                Exam::TYPE_HALF_YEARLY => 'Half Yearly Exam',
                Exam::TYPE_ANNUAL => 'Annual / Final Exam',
                Exam::TYPE_PRACTICAL => 'Practical Assessment',
                Exam::TYPE_ENTRANCE => 'Entrance Test',
                Exam::TYPE_MOCK => 'Mock Exam',
                Exam::TYPE_OTHER => 'Other',
            ],
            'examModes' => [
                Exam::MODE_OFFLINE => 'Offline (Pen & Paper)',
                Exam::MODE_ONLINE => 'Online (CBT / Digital)',
                Exam::MODE_BOTH => 'Both (Hybrid)',
            ],
            'statuses' => [
                Exam::STATUS_DRAFT => 'Draft',
                Exam::STATUS_PUBLISHED => 'Published / Open',
                Exam::STATUS_CLOSED => 'Closed / Completed',
            ],
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search.value'),
            'academic_session_id' => $request->input('academic_session_id'),
            'class_id' => $request->input('class_id'),
            'exam_type' => $request->input('exam_type'),
            'exam_mode' => $request->input('exam_mode'),
            'status' => $request->input('status'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'desc');

        $exams = $this->examService->getList(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($exams, (int) $request->input('draw', 1));
    }

    public function create(): View
    {
        $suggestedCode = 'EXM-'.date('Y').'-'.strtoupper(substr(uniqid(), -5));

        return view('admin.exams.create', [
            'academicSessions' => academic_session_options(),
            'classes' => class_options(),
            'subjects' => subject_options(),
            'questionPapers' => QuestionPaper::where('approval_status', QuestionPaper::APPROVAL_APPROVED)->pluck('title', 'id')->toArray(),
            'invigilators' => User::where('status', 1)->pluck('name', 'id')->toArray(),
            'suggestedCode' => $suggestedCode,
            'examTypes' => [
                Exam::TYPE_UNIT_TEST => 'Unit Test',
                Exam::TYPE_MID_TERM => 'Mid-Term Exam',
                Exam::TYPE_QUARTERLY => 'Quarterly Exam',
                Exam::TYPE_HALF_YEARLY => 'Half Yearly Exam',
                Exam::TYPE_ANNUAL => 'Annual / Final Exam',
                Exam::TYPE_PRACTICAL => 'Practical Assessment',
                Exam::TYPE_ENTRANCE => 'Entrance Test',
                Exam::TYPE_MOCK => 'Mock Exam',
                Exam::TYPE_OTHER => 'Other',
            ],
            'examModes' => [
                Exam::MODE_OFFLINE => 'Offline (Pen & Paper)',
                Exam::MODE_ONLINE => 'Online (CBT / Digital)',
                Exam::MODE_BOTH => 'Both (Hybrid)',
            ],
        ]);
    }

    public function store(ExamRequest $request): JsonResponse
    {
        try {
            $exam = $this->examService->create($request->validated());

            return $this->success('Exam created successfully.', [
                'exam' => $exam,
                'redirect_url' => route('admin.exams.show', $exam->id),
            ]);
        } catch (Exception $e) {
            return $this->error('Failed to create exam: '.$e->getMessage());
        }
    }

    public function show(int $id): View
    {
        $overview = $this->examService->getExamOverview($id);

        return view('admin.exams.show', [
            'exam' => $overview['exam'],
            'totalSchedules' => $overview['total_schedules'],
            'totalEnrolled' => $overview['total_enrolled'],
            'totalEligible' => $overview['total_eligible'],
            'totalEnteredMarks' => $overview['total_entered_marks'],
            'marksCompletionPercentage' => $overview['marks_completion_percentage'],
        ]);
    }

    public function edit(int $id): View
    {
        $exam = $this->examService->find($id);
        abort_if(! $exam, 404, 'Exam not found.');

        return view('admin.exams.edit', [
            'exam' => $exam,
            'academicSessions' => academic_session_options(),
            'classes' => class_options(),
            'subjects' => subject_options(),
            'questionPapers' => QuestionPaper::where('approval_status', QuestionPaper::APPROVAL_APPROVED)->pluck('title', 'id')->toArray(),
            'invigilators' => User::where('status', 1)->pluck('name', 'id')->toArray(),
            'examTypes' => [
                Exam::TYPE_UNIT_TEST => 'Unit Test',
                Exam::TYPE_MID_TERM => 'Mid-Term Exam',
                Exam::TYPE_QUARTERLY => 'Quarterly Exam',
                Exam::TYPE_HALF_YEARLY => 'Half Yearly Exam',
                Exam::TYPE_ANNUAL => 'Annual / Final Exam',
                Exam::TYPE_PRACTICAL => 'Practical Assessment',
                Exam::TYPE_ENTRANCE => 'Entrance Test',
                Exam::TYPE_MOCK => 'Mock Exam',
                Exam::TYPE_OTHER => 'Other',
            ],
            'examModes' => [
                Exam::MODE_OFFLINE => 'Offline (Pen & Paper)',
                Exam::MODE_ONLINE => 'Online (CBT / Digital)',
                Exam::MODE_BOTH => 'Both (Hybrid)',
            ],
        ]);
    }

    public function update(ExamRequest $request, int $id): JsonResponse
    {
        try {
            $exam = $this->examService->update($id, $request->validated());

            return $this->success('Exam updated successfully.', [
                'exam' => $exam,
                'redirect_url' => route('admin.exams.show', $exam->id),
            ]);
        } catch (Exception $e) {
            return $this->error('Failed to update exam: '.$e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->examService->delete($id);

            return $this->success('Exam deleted successfully.');
        } catch (Exception $e) {
            return $this->error('Failed to delete exam: '.$e->getMessage());
        }
    }

    public function changeStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,published,closed',
        ]);

        try {
            $exam = $this->examService->updateStatus($id, $validated['status']);

            return $this->success('Exam status updated to '.ucfirst($exam->status), [
                'status' => $exam->status,
            ]);
        } catch (Exception $e) {
            return $this->error('Failed to update exam status: '.$e->getMessage());
        }
    }

    public function enrollments(Request $request, int $id): View
    {
        $exam = $this->examService->find($id);
        abort_if(! $exam, 404, 'Exam not found.');
        // dd($exam);
        $sectionId = $request->input('section_id') ? (int) $request->input('section_id') : null;
        $students = $this->examService->getEnrolledStudentsWithDetails($id, $sectionId);

        $sections = [];
        if ($exam->class_id) {
            $sections = Section::pluck('name', 'id')->toArray();
        }

        return view('admin.exams.enrollments', [
            'exam' => $exam,
            'students' => $students,
            'sections' => $sections,
            'selectedSection' => $sectionId,
        ]);
    }

    public function updateStudentEligibility(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'student_enrollment_id' => 'required|exists:student_enrollments,id',
            'eligibility_status' => 'required|in:eligible,detained,fee_defaulter,exempted',
            'remarks' => 'nullable|string|max:255',
        ]);

        try {
            $enrollment = $this->examService->updateStudentEligibility(
                $id,
                (int) $validated['student_enrollment_id'],
                $validated['eligibility_status'],
                $validated['remarks'] ?? null
            );

            return $this->success('Student eligibility status updated successfully.', $enrollment);
        } catch (Exception $e) {
            return $this->error('Failed to update eligibility: '.$e->getMessage());
        }
    }

    public function admitCards(Request $request, int $id): View
    {
        $sectionId = $request->input('section_id') ? (int) $request->input('section_id') : null;
        $studentEnrollmentId = $request->input('student_enrollment_id') ? (int) $request->input('student_enrollment_id') : null;

        $data = $this->examService->getAdmitCardsData($id, $sectionId, $studentEnrollmentId);

        return view('admin.exams.admit_cards', [
            'exam' => $data['exam'],
            'students' => $data['students'],
            'schedules' => $data['schedules'],
        ]);
    }

    public function marksEntry(int $scheduleId): View
    {
        $data = $this->examService->getScheduleForMarksEntry($scheduleId);

        return view('admin.exams.marks_entry', [
            'schedule' => $data['schedule'],
            'students' => $data['students'],
            'existingMarks' => $data['existing_marks'],
        ]);
    }

    public function saveMarks(Request $request, int $scheduleId): JsonResponse
    {
        $validated = $request->validate([
            'marks' => 'required|array|min:1',
            'marks.*.student_enrollment_id' => 'required|integer',
            'marks.*.theory_marks' => 'nullable|numeric|min:0',
            'marks.*.practical_marks' => 'nullable|numeric|min:0',
            'marks.*.internal_marks' => 'nullable|numeric|min:0',
            'marks.*.viva_marks' => 'nullable|numeric|min:0',
            'marks.*.is_absent' => 'nullable|boolean',
            'marks.*.is_exempted' => 'nullable|boolean',
            'marks.*.remarks' => 'nullable|string|max:255',
        ]);

        try {
            $count = $this->examService->saveMarks($scheduleId, $validated['marks']);

            return $this->success("Successfully saved marks for {$count} student(s).", [
                'saved_count' => $count,
            ]);
        } catch (Exception $e) {
            return $this->error('Failed to save marks: '.$e->getMessage());
        }
    }
}
