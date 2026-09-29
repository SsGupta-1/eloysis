<?php

namespace App\Services\Admin;

use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ExamSchedule;
use App\Models\ExamStudentEnrollment;
use App\Models\StudentEnrollment;
use App\Repositories\Admin\ExamRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamService
{
    public function __construct(
        protected ExamRepository $examRepository
    ) {}

    public function getList(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'desc'
    ) {
        return $this->examRepository->getList($filters, $perPage, $page, $orderColumn, $orderDirection);
    }

    public function find(int $id): ?Exam
    {
        return Exam::with([
            'academicSession',
            'academicClass',
            'subject',
            'creator',
            'schedules.subject',
            'schedules.academicClass',
            'schedules.section',
            'schedules.invigilator',
            'schedules.questionPaper',
            'enrolledStudents.studentEnrollment.student.user',
            'enrolledStudents.studentProfile',
        ])->find($id);
    }

    public function create(array $data): Exam
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = Auth::guard('admin')->id();

            if (empty($data['exam_code'])) {
                $data['exam_code'] = 'EXM-'.date('Y').'-'.strtoupper(substr(uniqid(), -5));
            }

            $schedulesData = $data['schedules'] ?? [];
            unset($data['schedules']);

            /** @var Exam $exam */
            $exam = $this->examRepository->create($data);

            if (! empty($schedulesData)) {
                $this->saveSchedules($exam, $schedulesData);
            }

            // Auto-enroll eligible students from the target class & session
            if (! empty($exam->class_id)) {
                $this->enrollStudents($exam->id, $exam->class_id);
            }

            return $exam->fresh(['schedules', 'enrolledStudents']);
        });
    }

    public function update(int|Exam $exam, array $data): Exam
    {
        return DB::transaction(function () use ($exam, $data) {
            $examModel = $exam instanceof Exam ? $exam : $this->find($exam);

            if (! $examModel) {
                throw new Exception('Exam not found.');
            }

            $schedulesData = $data['schedules'] ?? null;
            unset($data['schedules']);

            $this->examRepository->update($examModel->id, $data);

            if ($schedulesData !== null) {
                $examModel->schedules()->delete();
                $this->saveSchedules($examModel, $schedulesData);
            }

            return $examModel->fresh(['schedules', 'enrolledStudents']);
        });
    }

    public function delete(int $id): bool
    {
        return $this->examRepository->delete($id);
    }

    public function saveSchedules(Exam $exam, array $schedulesData): void
    {
        foreach ($schedulesData as $sData) {
            if (empty($sData['subject_id']) || empty($sData['exam_date'])) {
                continue;
            }

            ExamSchedule::create([
                'exam_id' => $exam->id,
                'class_id' => $sData['class_id'] ?? $exam->class_id,
                'section_id' => $sData['section_id'] ?? null,
                'subject_id' => $sData['subject_id'],
                'question_paper_id' => $sData['question_paper_id'] ?? null,
                'exam_date' => $sData['exam_date'],
                'start_time' => $sData['start_time'] ?? '09:00:00',
                'end_time' => $sData['end_time'] ?? '12:00:00',
                'duration_minutes' => (int) ($sData['duration_minutes'] ?? 180),
                'room_no' => $sData['room_no'] ?? null,
                'invigilator_id' => $sData['invigilator_id'] ?? null,
                'max_theory_marks' => (float) ($sData['max_theory_marks'] ?? 80.00),
                'max_practical_marks' => (float) ($sData['max_practical_marks'] ?? 0.00),
                'max_internal_marks' => (float) ($sData['max_internal_marks'] ?? 20.00),
                'max_viva_marks' => (float) ($sData['max_viva_marks'] ?? 0.00),
                'total_marks' => (float) ($sData['total_marks'] ?? 100.00),
                'passing_marks' => (float) ($sData['passing_marks'] ?? 33.00),
                'status' => $sData['status'] ?? ExamSchedule::STATUS_SCHEDULED,
                'created_by' => Auth::guard('admin')->id(),
            ]);
        }
    }

    public function enrollStudents(int $examId, int $classId, ?int $sectionId = null): int
    {
        $exam = Exam::find($examId);
        if (! $exam) {
            return 0;
        }

        $query = StudentEnrollment::query()
            ->where('status', 1)
            ->where('class_id', $classId);

        if ($exam->academic_session_id) {
            $query->where('academic_session_id', $exam->academic_session_id);
        }

        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        $enrollments = $query->get();
        $enrolledCount = 0;

        foreach ($enrollments as $enrollment) {
            ExamStudentEnrollment::firstOrCreate(
                [
                    'exam_id' => $examId,
                    'student_enrollment_id' => $enrollment->id,
                ],
                [
                    'student_id' => $enrollment->stu_profile_id,
                    'eligibility_status' => ExamStudentEnrollment::ELIGIBLE,
                    'attendance_status' => ExamStudentEnrollment::ATTENDANCE_PRESENT,
                ]
            );
            $enrolledCount++;
        }

        return $enrolledCount;
    }

    public function updateStudentEligibility(int $examId, int $studentEnrollmentId, string $status, ?string $remarks = null): ExamStudentEnrollment
    {
        $enr = ExamStudentEnrollment::where('exam_id', $examId)
            ->where('student_enrollment_id', $studentEnrollmentId)
            ->firstOrFail();

        $enr->update([
            'eligibility_status' => $status,
            'remarks' => $remarks,
        ]);

        return $enr;
    }

    /**
     * Fetch schedule and all eligible students for Matrix Marks Entry
     */
    public function getScheduleForMarksEntry(int $scheduleId): array
    {
        $schedule = ExamSchedule::with([
            'exam',
            'academicClass',
            'section',
            'subject',
            'marks.studentEnrollment.student.user',
        ])->findOrFail($scheduleId);

        // Fetch all enrolled students for this exam / class / section
        $studentsQuery = StudentEnrollment::query()
            ->with(['student.user'])
            ->where('class_id', $schedule->class_id)
            ->where('status', 1);

        if ($schedule->section_id) {
            $studentsQuery->where('section_id', $schedule->section_id);
        }

        if ($schedule->exam->academic_session_id) {
            $studentsQuery->where('academic_session_id', $schedule->exam->academic_session_id);
        }

        $students = $studentsQuery->orderBy('roll_number')->get();

        // Index existing marks by student_enrollment_id
        $existingMarks = $schedule->marks->keyBy('student_enrollment_id');

        return [
            'schedule' => $schedule,
            'students' => $students,
            'existing_marks' => $existingMarks,
        ];
    }

    /**
     * Batch save marks entry for a schedule
     */
    public function saveMarks(int $scheduleId, array $marksRows): int
    {
        $schedule = ExamSchedule::findOrFail($scheduleId);
        $savedCount = 0;
        $userId = Auth::guard('admin')->id();

        DB::transaction(function () use ($schedule, $marksRows, $userId, &$savedCount) {
            foreach ($marksRows as $row) {
                $enrollmentId = $row['student_enrollment_id'] ?? null;
                if (! $enrollmentId) {
                    continue;
                }

                $isAbsent = (bool) ($row['is_absent'] ?? false);
                $isExempted = (bool) ($row['is_exempted'] ?? false);

                $theory = $isAbsent || $isExempted ? 0.00 : (float) ($row['theory_marks'] ?? 0.00);
                $practical = $isAbsent || $isExempted ? 0.00 : (float) ($row['practical_marks'] ?? 0.00);
                $internal = $isAbsent || $isExempted ? 0.00 : (float) ($row['internal_marks'] ?? 0.00);
                $viva = $isAbsent || $isExempted ? 0.00 : (float) ($row['viva_marks'] ?? 0.00);

                $total = $isAbsent || $isExempted ? 0.00 : ($theory + $practical + $internal + $viva);

                // Calculate letter grade & point
                $maxMarks = (float) $schedule->total_marks ?: 100.00;
                $percentage = $maxMarks > 0 ? ($total / $maxMarks) * 100 : 0;
                $gradeData = $this->calculateGrade($percentage, $isAbsent, $isExempted);

                ExamMark::updateOrCreate(
                    [
                        'exam_schedule_id' => $schedule->id,
                        'student_enrollment_id' => $enrollmentId,
                    ],
                    [
                        'theory_marks' => $theory,
                        'practical_marks' => $practical,
                        'internal_marks' => $internal,
                        'viva_marks' => $viva,
                        'total_marks' => $total,
                        'is_absent' => $isAbsent,
                        'is_exempted' => $isExempted,
                        'grade_point' => $gradeData['point'],
                        'letter_grade' => $gradeData['grade'],
                        'remarks' => $row['remarks'] ?? null,
                        'entered_by' => $userId,
                        'verified_by' => $userId,
                    ]
                );

                $savedCount++;
            }
        });

        return $savedCount;
    }

    protected function calculateGrade(float $percentage, bool $isAbsent = false, bool $isExempted = false): array
    {
        if ($isAbsent) {
            return ['grade' => 'AB', 'point' => 0.00];
        }
        if ($isExempted) {
            return ['grade' => 'EX', 'point' => 0.00];
        }

        if ($percentage >= 90) {
            return ['grade' => 'A+', 'point' => 10.00];
        }
        if ($percentage >= 80) {
            return ['grade' => 'A', 'point' => 9.00];
        }
        if ($percentage >= 70) {
            return ['grade' => 'B+', 'point' => 8.00];
        }
        if ($percentage >= 60) {
            return ['grade' => 'B', 'point' => 7.00];
        }
        if ($percentage >= 50) {
            return ['grade' => 'C', 'point' => 6.00];
        }
        if ($percentage >= 33) {
            return ['grade' => 'D', 'point' => 4.00];
        }

        return ['grade' => 'F', 'point' => 0.00];
    }
}
