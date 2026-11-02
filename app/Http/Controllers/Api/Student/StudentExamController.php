<?php

namespace App\Http\Controllers\Api\Student;

use App\Models\Exam;
use App\Models\ExamStudentEnrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentExamController extends BaseStudentApiController
{
    /**
     * Get published exams for student's class
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

        $exams = Exam::where('class_id', $enrollment->class_id)
            ->where('academic_session_id', $enrollment->academic_session_id)
            ->where(function ($q) {
                $q->where('status', Exam::STATUS_PUBLISHED)
                    ->orWhere('is_published', true);
            })
            ->with(['schedules.subject', 'academicSession', 'academicClass'])
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($exam) {
                $today = now()->toDateString();
                $startDate = $exam->start_date ? $exam->start_date->toDateString() : null;
                $endDate = $exam->end_date ? $exam->end_date->toDateString() : null;

                $phase = 'upcoming';
                if ($startDate && $endDate) {
                    if ($today >= $startDate && $today <= $endDate) {
                        $phase = 'ongoing';
                    } elseif ($today > $endDate) {
                        $phase = 'completed';
                    }
                }

                return [
                    'id' => $exam->id,
                    'title' => $exam->title,
                    'exam_code' => $exam->exam_code,
                    'exam_type' => $exam->exam_type ?? 'Term Exam',
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'phase' => $phase,
                    'passing_marks' => $exam->passing_marks,
                    'instructions' => $exam->instructions,
                    'subjects_count' => $exam->schedules->count(),
                ];
            });

        return $this->success('Exams fetched successfully.', $exams);
    }

    /**
     * Get detailed exam schedules
     */
    public function show(Request $request, int $id): JsonResponse
    {
        [$user, $profile, $enrollment] = $this->getStudentData($request);

        if (! $profile) {
            return $this->error('Student profile not found.', [], 404);
        }

        $exam = Exam::with(['schedules.subject', 'academicClass', 'academicSession'])->find($id);

        if (! $exam) {
            return $this->error('Exam not found.', [], 404);
        }

        $schedules = $exam->schedules->map(function ($s) {
            return [
                'id' => $s->id,
                'subject_id' => $s->subject_id,
                'subject_name' => $s->subject?->subject_name ?? 'N/A',
                'subject_code' => $s->subject?->subject_code ?? null,
                'exam_date' => $s->exam_date ? $s->exam_date->format('Y-m-d') : null,
                'start_time' => $s->start_time ? date('h:i A', strtotime($s->start_time)) : null,
                'end_time' => $s->end_time ? date('h:i A', strtotime($s->end_time)) : null,
                'duration_minutes' => $s->duration_minutes,
                'room_no' => $s->room_no ?? 'Exam Hall',
                'theory_marks' => $s->max_theory_marks,
                'practical_marks' => $s->max_practical_marks,
                'total_marks' => $s->total_marks,
                'passing_marks' => $s->passing_marks,
            ];
        });

        $data = [
            'id' => $exam->id,
            'title' => $exam->title,
            'exam_code' => $exam->exam_code,
            'class_name' => $exam->academicClass?->class_name,
            'academic_session' => $exam->academicSession?->name,
            'start_date' => $exam->start_date ? $exam->start_date->format('Y-m-d') : null,
            'end_date' => $exam->end_date ? $exam->end_date->format('Y-m-d') : null,
            'instructions' => $exam->instructions,
            'schedules' => $schedules,
        ];

        return $this->success('Exam details fetched successfully.', $data);
    }

    /**
     * Get Digital Admit Card
     */
    public function admitCard(Request $request, int $id): JsonResponse
    {
        [$user, $profile, $enrollment] = $this->getStudentData($request);

        if (! $profile || ! $enrollment) {
            return $this->error('Student profile or enrollment not found.', [], 404);
        }

        $exam = Exam::with(['schedules.subject', 'academicClass', 'academicSession'])->find($id);

        if (! $exam) {
            return $this->error('Exam not found.', [], 404);
        }

        // Check enrollment eligibility record if present
        $examEnrollment = ExamStudentEnrollment::where('exam_id', $exam->id)
            ->where('student_enrollment_id', $enrollment->id)
            ->first();

        $isEligible = true;
        $eligibilityMessage = 'Eligible';

        if ($examEnrollment) {
            if ($examEnrollment->eligibility_status === ExamStudentEnrollment::DETAINED) {
                $isEligible = false;
                $eligibilityMessage = 'Student is detained from this examination.';
            } elseif ($examEnrollment->eligibility_status === ExamStudentEnrollment::FEE_DEFAULTER) {
                $isEligible = false;
                $eligibilityMessage = 'Admit card is withheld due to pending fee dues.';
            }
        }

        $schedules = $exam->schedules->map(function ($s) {
            return [
                'subject_name' => $s->subject?->subject_name ?? 'N/A',
                'subject_code' => $s->subject?->subject_code ?? 'N/A',
                'exam_date' => $s->exam_date ? $s->exam_date->format('d M, Y') : 'TBA',
                'timing' => ($s->start_time && $s->end_time)
                    ? date('h:i A', strtotime($s->start_time)).' - '.date('h:i A', strtotime($s->end_time))
                    : 'TBA',
                'room_no' => $s->room_no ?? 'Assigned Center Room',
            ];
        });

        $data = [
            'is_eligible' => $isEligible,
            'eligibility_message' => $eligibilityMessage,
            'admit_card_id' => 'AC-'.$exam->id.'-'.$enrollment->id,
            'exam' => [
                'id' => $exam->id,
                'title' => $exam->title,
                'exam_code' => $exam->exam_code,
                'academic_session' => $exam->academicSession?->name,
                'instructions' => $exam->instructions ?: '1. Candidate must bring this digital/printed admit card to the exam hall.\n2. Electronic gadgets including smartwatches and mobile phones are strictly prohibited.\n3. Report to the examination hall 15 minutes before the scheduled time.',
            ],
            'student' => [
                'name' => $user->name,
                'roll_number' => $enrollment->roll_number,
                'admission_no' => $profile->admission_no,
                'class_name' => $enrollment->studentClass?->class_name,
                'section_name' => $enrollment->section?->section_name,
                'father_name' => $profile->father_name,
                'dob' => $profile->dob ? $profile->dob->format('d-m-Y') : null,
                'profile_image' => $user->profile_image_url,
            ],
            'schedules' => $schedules,
            'verification_qr_string' => json_encode([
                'type' => 'admit_card',
                'student_id' => $user->id,
                'roll_no' => $enrollment->roll_number,
                'exam_id' => $exam->id,
            ]),
        ];

        return $this->success('Admit card generated successfully.', $data);
    }
}
