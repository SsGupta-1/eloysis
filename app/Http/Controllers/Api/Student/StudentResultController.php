<?php

namespace App\Http\Controllers\Api\Student;

use App\Models\Exam;
use App\Models\ExamMark;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentResultController extends BaseStudentApiController
{
    /**
     * Get Student Exam Results Summary List
     */
    public function index(Request $request): JsonResponse
    {
        [$user, $profile, $enrollment] = $this->getStudentData($request);

        if (! $profile) {
            return $this->error('Student profile not found.', [], 404);
        }

        if (! $enrollment) {
            return $this->error('Active student enrollment not found.', [], 404);
        }

        $marks = ExamMark::where('student_enrollment_id', $enrollment->id)
            ->with(['schedule.exam', 'schedule.subject'])
            ->get();

        $groupedByExam = $marks->groupBy(function ($item) {
            return $item->schedule?->exam_id;
        });

        $results = [];

        foreach ($groupedByExam as $examId => $examMarks) {
            $exam = $examMarks->first()?->schedule?->exam;
            if (! $exam) {
                continue;
            }

            $totalMarksScored = (float) $examMarks->sum('total_marks');
            $maxMarksTotal = (float) $examMarks->sum(function ($m) {
                return (float) ($m->schedule?->total_marks ?? 100);
            });
            $percentage = $maxMarksTotal > 0 ? round(($totalMarksScored / $maxMarksTotal) * 100, 2) : 0;
            $passingPercentage = (float) ($exam->passing_marks ?? 33);
            $passed = $percentage >= $passingPercentage;

            $results[] = [
                'exam_id' => $exam->id,
                'exam_title' => $exam->title,
                'exam_code' => $exam->exam_code,
                'total_scored' => round($totalMarksScored, 2),
                'max_marks' => round($maxMarksTotal, 2),
                'percentage' => $percentage,
                'grade' => $this->calculateGrade($percentage),
                'passed' => $passed,
                'status' => $passed ? 'PASSED' : 'FAILED',
                'subjects_count' => $examMarks->count(),
            ];
        }

        return $this->success('Exam results fetched successfully.', $results);
    }

    /**
     * Get Detailed Marksheet for an Exam
     */
    public function marksheet(Request $request, int $examId): JsonResponse
    {
        [$user, $profile, $enrollment] = $this->getStudentData($request);

        if (! $profile || ! $enrollment) {
            return $this->error('Student profile or enrollment not found.', [], 404);
        }

        $exam = Exam::with(['academicClass', 'academicSession'])->find($examId);

        if (! $exam) {
            return $this->error('Exam not found.', [], 404);
        }

        $marks = ExamMark::where('student_enrollment_id', $enrollment->id)
            ->whereHas('schedule', function ($q) use ($examId) {
                $q->where('exam_id', $examId);
            })
            ->with(['schedule.subject'])
            ->get();

        if ($marks->isEmpty()) {
            return $this->error('No marksheet records found for this exam.', [], 404);
        }

        $subjectMarks = $marks->map(function ($m) {
            $totalMarks = (float) $m->total_marks;
            $maxMarks = (float) ($m->schedule?->total_marks ?? 100);
            $passingMarks = (float) ($m->schedule?->passing_marks ?? 33);
            $percentage = $maxMarks > 0 ? round(($totalMarks / $maxMarks) * 100, 1) : 0;
            $passed = ! $m->is_absent && ($totalMarks >= $passingMarks);

            return [
                'subject_id' => $m->schedule?->subject_id,
                'subject_name' => $m->schedule?->subject?->subject_name ?? 'N/A',
                'subject_code' => $m->schedule?->subject?->subject_code ?? 'N/A',
                'theory_marks' => $m->theory_marks,
                'practical_marks' => $m->practical_marks,
                'internal_marks' => $m->internal_marks,
                'total_marks' => $totalMarks,
                'max_marks' => $maxMarks,
                'passing_marks' => $passingMarks,
                'grade' => $m->letter_grade ?: $this->calculateGrade($percentage),
                'is_absent' => (bool) $m->is_absent,
                'passed' => $passed,
                'remarks' => $m->remarks,
            ];
        });

        $totalScored = (float) $marks->sum('total_marks');
        $maxTotal = (float) $marks->sum(function ($m) {
            return (float) ($m->schedule?->total_marks ?? 100);
        });
        $overallPercentage = $maxTotal > 0 ? round(($totalScored / $maxTotal) * 100, 2) : 0;
        $examPassingMarks = (float) ($exam->passing_marks ?? 33);
        $isOverallPassed = $overallPercentage >= $examPassingMarks && ! $subjectMarks->contains('passed', false);

        $data = [
            'exam' => [
                'id' => $exam->id,
                'title' => $exam->title,
                'exam_code' => $exam->exam_code,
                'class_name' => $exam->academicClass?->class_name,
                'academic_session' => $exam->academicSession?->name,
            ],
            'student' => [
                'name' => $user->name,
                'roll_number' => $enrollment->roll_number,
                'admission_no' => $profile->admission_no,
                'father_name' => $profile->father_name,
            ],
            'subject_marks' => $subjectMarks,
            'summary' => [
                'total_scored' => round($totalScored, 2),
                'max_total' => round($maxTotal, 2),
                'overall_percentage' => $overallPercentage,
                'overall_grade' => $this->calculateGrade($overallPercentage),
                'result_status' => $isOverallPassed ? 'PASSED' : 'FAILED',
            ],
        ];

        return $this->success('Marksheet details fetched successfully.', $data);
    }
}
