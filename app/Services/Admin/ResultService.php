<?php

namespace App\Services\Admin;

use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ExamStudentEnrollment;
use App\Models\StudentAttendance;
use App\Models\StudentEnrollment;
use Exception;

class ResultService
{
    public function __construct(
        protected ExamService $examService
    ) {}

    /**
     * Get exams list with result calculation progress & stats for Result Dashboard
     */
    public function getExamsForResults(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'desc'
    ) {
        $query = Exam::query()
            ->with([
                'academicClass:id,class_name',
                'academicSession:id,name',
                'schedules:id,exam_id,subject_id,total_marks',
                'schedules.subject:id,subject_name,subject_code',
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

        if (isset($filters['is_published']) && $filters['is_published'] !== '') {
            $query->where('is_published', (bool) $filters['is_published']);
        }

        $query->latest('id');

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Compute full Broadsheet / Tabulation matrix for an entire Exam
     */
    public function getExamBroadsheet(int $examId, ?int $classId = null, ?int $sectionId = null): array
    {
        $exam = Exam::with([
            'academicSession',
            'academicClass',
            'schedules' => function ($q) {
                $q->with('subject')->orderBy('id');
            },
        ])->findOrFail($examId);

        $schedules = $exam->schedules;
        if ($schedules->isEmpty()) {
            return [
                'exam' => $exam,
                'schedules' => collect(),
                'students_data' => collect(),
                'summary' => $this->emptySummary(),
            ];
        }

        // Fetch students enrolled in this exam
        $enrollmentQuery = ExamStudentEnrollment::with([
            'studentEnrollment.student.user',
            'studentEnrollment.studentClass',
            'studentEnrollment.section',
        ])
            ->where('exam_id', $examId);

        if ($sectionId) {
            $enrollmentQuery->whereHas('studentEnrollment', function ($q) use ($sectionId) {
                $q->where('section_id', $sectionId);
            });
        }

        $examEnrollments = $enrollmentQuery->get();

        // Fetch all marks for this exam's schedules
        $scheduleIds = $schedules->pluck('id')->toArray();
        $allMarks = ExamMark::whereIn('exam_schedule_id', $scheduleIds)
            ->get()
            ->groupBy('student_enrollment_id');

        $totalExamMaxMarks = (float) $schedules->sum('total_marks');

        $studentsData = collect();

        foreach ($examEnrollments as $examEnrollment) {
            $enrollment = $examEnrollment->studentEnrollment;
            if (! $enrollment || ! $enrollment->student) {
                continue;
            }

            $user = $enrollment->student->user;
            $studentMarks = $allMarks->get($enrollment->id, collect());
            $marksBySchedule = $studentMarks->keyBy('exam_schedule_id');

            $totalObtained = 0.00;
            $failedSubjectsCount = 0;
            $isAbsentInAny = false;
            $subjectDetails = [];

            foreach ($schedules as $schedule) {
                $mark = $marksBySchedule->get($schedule->id);
                $isAbsent = $mark ? (bool) $mark->is_absent : false;
                $isExempted = $mark ? (bool) $mark->is_exempted : false;
                $obtained = $mark ? (float) $mark->total_marks : 0.00;
                $passingMarks = (float) $schedule->passing_marks;

                if ($isAbsent) {
                    $isAbsentInAny = true;
                }

                $isPassed = ! $isAbsent && ! $isExempted && ($obtained >= $passingMarks);

                if (! $isPassed && ! $isExempted) {
                    $failedSubjectsCount++;
                }

                $totalObtained += $obtained;

                $subjectDetails[$schedule->id] = [
                    'schedule_id' => $schedule->id,
                    'subject_name' => $schedule->subject?->subject_name ?? 'Subject',
                    'subject_code' => $schedule->subject?->subject_code ?? '',
                    'max_marks' => (float) $schedule->total_marks,
                    'passing_marks' => $passingMarks,
                    'theory' => $mark ? (float) $mark->theory_marks : 0.00,
                    'practical' => $mark ? (float) $mark->practical_marks : 0.00,
                    'internal' => $mark ? (float) $mark->internal_marks : 0.00,
                    'viva' => $mark ? (float) $mark->viva_marks : 0.00,
                    'total_obtained' => $obtained,
                    'letter_grade' => $mark?->letter_grade ?? ($isAbsent ? 'AB' : 'F'),
                    'grade_point' => $mark?->grade_point ?? 0.00,
                    'is_absent' => $isAbsent,
                    'is_exempted' => $isExempted,
                    'is_passed' => $isPassed,
                ];
            }

            $percentage = $totalExamMaxMarks > 0 ? round(($totalObtained / $totalExamMaxMarks) * 100, 2) : 0;
            $overallGrade = $this->calculateOverallGrade($percentage);

            // Determine Result Status: Pass / Compartment / Fail
            if ($failedSubjectsCount === 0 && ! $isAbsentInAny) {
                $resultStatus = 'PASS';
            } elseif ($failedSubjectsCount > 0 && ($failedSubjectsCount < $schedules->count()) && $failedSubjectsCount <= 2) {
                $resultStatus = 'COMPARTMENT';
            } else {
                $resultStatus = 'FAIL';
            }

            $studentsData->push([
                'exam_enrollment_id' => $examEnrollment->id,
                'student_enrollment_id' => $enrollment->id,
                'student_id' => $enrollment->stu_profile_id,
                'name' => $user?->name ?? 'Student',
                'roll_number' => $enrollment->roll_number ?? '-',
                'admission_no' => $enrollment->student->admission_no ?? '-',
                'section_name' => $enrollment->section?->section_name ?? 'A',
                'section_id' => $enrollment->section_id,
                'eligibility_status' => $examEnrollment->eligibility_status,
                'subjects' => $subjectDetails,
                'total_obtained' => $totalObtained,
                'total_max' => $totalExamMaxMarks,
                'percentage' => $percentage,
                'grade' => $overallGrade['grade'],
                'grade_point' => $overallGrade['point'],
                'division' => $overallGrade['division'],
                'result_status' => $resultStatus,
                'failed_count' => $failedSubjectsCount,
                'class_rank' => 0,
                'section_rank' => 0,
            ]);
        }

        // Rank calculation across the class
        $sorted = $studentsData->sortByDesc('total_obtained')->values()->all();
        $classRank = 1;
        foreach ($sorted as $idx => $item) {
            $sorted[$idx]['class_rank'] = $classRank++;
        }

        // Rank calculation per section
        $groupedBySection = collect($sorted)->groupBy('section_id');
        foreach ($groupedBySection as $sectionGroup) {
            $sectionRank = 1;
            $sectionSorted = $sectionGroup->sortByDesc('total_obtained');
            foreach ($sectionSorted as $item) {
                foreach ($sorted as $k => $s) {
                    if ($s['student_enrollment_id'] === $item['student_enrollment_id']) {
                        $sorted[$k]['section_rank'] = $sectionRank++;
                        break;
                    }
                }
            }
        }

        $sortedCollection = collect($sorted);

        // Compute summary statistics
        $totalStudents = $sortedCollection->count();
        $passedStudents = $sortedCollection->where('result_status', 'PASS')->count();
        $failedStudents = $sortedCollection->where('result_status', 'FAIL')->count();
        $compartmentStudents = $sortedCollection->where('result_status', 'COMPARTMENT')->count();
        $passPercentage = $totalStudents > 0 ? round(($passedStudents / $totalStudents) * 100, 1) : 0;
        $highestMarks = $sortedCollection->max('total_obtained') ?? 0.00;
        $lowestMarks = $sortedCollection->min('total_obtained') ?? 0.00;
        $averagePercentage = $totalStudents > 0 ? round($sortedCollection->avg('percentage'), 1) : 0;

        $summary = [
            'total_students' => $totalStudents,
            'passed_students' => $passedStudents,
            'failed_students' => $failedStudents,
            'compartment_students' => $compartmentStudents,
            'pass_percentage' => $passPercentage,
            'highest_marks' => $highestMarks,
            'lowest_marks' => $lowestMarks,
            'average_percentage' => $averagePercentage,
        ];

        return [
            'exam' => $exam,
            'schedules' => $schedules,
            'students_data' => $sortedCollection->sortBy('roll_number')->values(),
            'summary' => $summary,
        ];
    }

    /**
     * Compute comprehensive Report Card / Marksheet for an individual student
     */
    public function getStudentReportCard(int $examId, int $studentEnrollmentId): array
    {
        $exam = Exam::with([
            'academicSession',
            'academicClass',
            'schedules' => function ($q) {
                $q->with(['subject', 'invigilator'])->orderBy('exam_date')->orderBy('id');
            },
        ])->findOrFail($examId);

        $enrollment = StudentEnrollment::with([
            'student.user',
            'studentClass',
            'section',
            'academicSession',
        ])->findOrFail($studentEnrollmentId);

        $schedules = $exam->schedules;
        $scheduleIds = $schedules->pluck('id')->toArray();

        $marks = ExamMark::whereIn('exam_schedule_id', $scheduleIds)
            ->where('student_enrollment_id', $studentEnrollmentId)
            ->get()
            ->keyBy('exam_schedule_id');

        $subjectRows = [];
        $totalMax = 0.00;
        $totalObtained = 0.00;
        $failedCount = 0;
        $totalGradePoints = 0.00;

        foreach ($schedules as $schedule) {
            $mark = $marks->get($schedule->id);
            $isAbsent = $mark ? (bool) $mark->is_absent : false;
            $isExempted = $mark ? (bool) $mark->is_exempted : false;
            $obtained = $mark ? (float) $mark->total_marks : 0.00;
            $maxMarks = (float) $schedule->total_marks;
            $passMarks = (float) $schedule->passing_marks;

            $isPassed = ! $isAbsent && ! $isExempted && ($obtained >= $passMarks);
            if (! $isPassed && ! $isExempted) {
                $failedCount++;
            }

            $totalMax += $maxMarks;
            $totalObtained += $obtained;

            $gp = $mark ? (float) $mark->grade_point : ($isPassed ? 4.00 : 0.00);
            $totalGradePoints += $gp;

            $subjectRows[] = [
                'subject_name' => $schedule->subject?->subject_name ?? 'Subject',
                'subject_code' => $schedule->subject?->subject_code ?? '-',
                'max_theory' => (float) $schedule->max_theory_marks,
                'max_practical' => (float) $schedule->max_practical_marks,
                'max_internal' => (float) $schedule->max_internal_marks,
                'max_viva' => (float) $schedule->max_viva_marks,
                'total_max' => $maxMarks,
                'pass_marks' => $passMarks,
                'obtained_theory' => $mark ? (float) $mark->theory_marks : 0.00,
                'obtained_practical' => $mark ? (float) $mark->practical_marks : 0.00,
                'obtained_internal' => $mark ? (float) $mark->internal_marks : 0.00,
                'obtained_viva' => $mark ? (float) $mark->viva_marks : 0.00,
                'obtained_total' => $obtained,
                'letter_grade' => $mark?->letter_grade ?? ($isAbsent ? 'AB' : ($isExempted ? 'EX' : 'F')),
                'grade_point' => $gp,
                'is_absent' => $isAbsent,
                'is_exempted' => $isExempted,
                'is_passed' => $isPassed,
                'remarks' => $mark?->remarks ?? '',
            ];
        }

        $percentage = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 2) : 0;
        $overallGrade = $this->calculateOverallGrade($percentage);
        $cgpa = count($subjectRows) > 0 ? round($totalGradePoints / count($subjectRows), 2) : 0.00;

        if ($failedCount === 0) {
            $resultStatus = 'PASSED';
            $remarks = $percentage >= 85 ? 'Outstanding academic excellence! Outstanding achievement.'
                : ($percentage >= 70 ? 'Very good performance. Keep up the consistent work.'
                : 'Good performance. Promoted to next term with regular practice.');
        } elseif ($failedCount > 0 && ($failedCount < count($subjectRows)) && $failedCount <= 2) {
            $resultStatus = 'COMPARTMENT / PROMOTED WITH BACKLOG';
            $remarks = 'Eligible for supplementary examination in reappearing subjects.';
        } else {
            $resultStatus = 'FAILED';
            $remarks = 'Needs significant improvement. Remedial classes recommended.';
        }

        // Attendance stats for student
        $totalDays = StudentAttendance::where('student_enrollment_id', $studentEnrollmentId)->count();
        $presentDays = StudentAttendance::where('student_enrollment_id', $studentEnrollmentId)
            ->where('status', 'present')
            ->count();
        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 100.0;

        // Calculate Rank in Class
        $broadsheet = $this->getExamBroadsheet($examId);
        $studentInBroadsheet = collect($broadsheet['students_data'])->firstWhere('student_enrollment_id', $studentEnrollmentId);

        return [
            'exam' => $exam,
            'enrollment' => $enrollment,
            'student' => $enrollment->student,
            'user' => $enrollment->student?->user,
            'subject_rows' => $subjectRows,
            'total_max' => $totalMax,
            'total_obtained' => $totalObtained,
            'percentage' => $percentage,
            'cgpa' => $cgpa,
            'overall_grade' => $overallGrade['grade'],
            'division' => $overallGrade['division'],
            'result_status' => $resultStatus,
            'failed_count' => $failedCount,
            'class_rank' => $studentInBroadsheet['class_rank'] ?? 1,
            'section_rank' => $studentInBroadsheet['section_rank'] ?? 1,
            'attendance_percentage' => $attendancePercentage,
            'teacher_remarks' => $remarks,
            'grading_scale' => $this->getGradingScaleLegend(),
        ];
    }

    public function togglePublish(int $examId, ?bool $publish = null): Exam
    {
        $exam = Exam::findOrFail($examId);
        $newStatus = $publish !== null ? $publish : ! $exam->is_published;
        $exam->update(['is_published' => $newStatus]);

        return $exam;
    }

    public function calculateOverallGrade(float $percentage): array
    {
        if ($percentage >= 90) {
            return ['grade' => 'A+', 'point' => 10.00, 'division' => 'First Division with Distinction'];
        }
        if ($percentage >= 80) {
            return ['grade' => 'A', 'point' => 9.00, 'division' => 'First Division'];
        }
        if ($percentage >= 70) {
            return ['grade' => 'B+', 'point' => 8.00, 'division' => 'First Division'];
        }
        if ($percentage >= 60) {
            return ['grade' => 'B', 'point' => 7.00, 'division' => 'First Division'];
        }
        if ($percentage >= 50) {
            return ['grade' => 'C', 'point' => 6.00, 'division' => 'Second Division'];
        }
        if ($percentage >= 33) {
            return ['grade' => 'D', 'point' => 4.00, 'division' => 'Third Division'];
        }

        return ['grade' => 'F', 'point' => 0.00, 'division' => 'Failed'];
    }

    public function getGradingScaleLegend(): array
    {
        return [
            ['marks_range' => '90% - 100%', 'grade' => 'A+', 'grade_point' => '10.0', 'status' => 'Outstanding'],
            ['marks_range' => '80% - 89.9%', 'grade' => 'A', 'grade_point' => '9.0', 'status' => 'Excellent'],
            ['marks_range' => '70% - 79.9%', 'grade' => 'B+', 'grade_point' => '8.0', 'status' => 'Very Good'],
            ['marks_range' => '60% - 69.9%', 'grade' => 'B', 'grade_point' => '7.0', 'status' => 'Good'],
            ['marks_range' => '50% - 59.9%', 'grade' => 'C', 'grade_point' => '6.0', 'status' => 'Average'],
            ['marks_range' => '33% - 49.9%', 'grade' => 'D', 'grade_point' => '4.0', 'status' => 'Pass'],
            ['marks_range' => 'Below 33%', 'grade' => 'F', 'grade_point' => '0.0', 'status' => 'Fail'],
        ];
    }

    protected function emptySummary(): array
    {
        return [
            'total_students' => 0,
            'passed_students' => 0,
            'failed_students' => 0,
            'compartment_students' => 0,
            'pass_percentage' => 0,
            'highest_marks' => 0,
            'lowest_marks' => 0,
            'average_percentage' => 0,
        ];
    }

    /**
     * For Mobile API & Student portal
     */
    public function getStudentResultsForApi(int $studentProfileId): array
    {
        $enrollment = StudentEnrollment::where('stu_profile_id', $studentProfileId)
            ->where('status', 1)
            ->latest('id')
            ->first();

        if (! $enrollment) {
            return [];
        }

        $publishedExams = Exam::where('is_published', true)
            ->where(function ($q) use ($enrollment) {
                $q->where('class_id', $enrollment->class_id)
                    ->orWhereNull('class_id');
            })
            ->where('academic_session_id', $enrollment->academic_session_id)
            ->orderBy('id', 'desc')
            ->get();

        $results = [];
        foreach ($publishedExams as $exam) {
            try {
                $card = $this->getStudentReportCard($exam->id, $enrollment->id);
                $results[] = [
                    'exam_id' => $exam->id,
                    'exam_title' => $exam->title,
                    'exam_code' => $exam->exam_code,
                    'exam_type' => $exam->exam_type,
                    'total_max' => $card['total_max'],
                    'total_obtained' => $card['total_obtained'],
                    'percentage' => $card['percentage'],
                    'cgpa' => $card['cgpa'],
                    'overall_grade' => $card['overall_grade'],
                    'result_status' => $card['result_status'],
                    'class_rank' => $card['class_rank'],
                    'subjects' => $card['subject_rows'],
                ];
            } catch (Exception $e) {
                // Skip uncalculated exams
                continue;
            }
        }

        return $results;
    }
}
