<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Exam;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $studentProfile = $user->studentProfile;

        if (! $studentProfile) {
            return $this->error('Student profile not found.', 404);
        }

        $enrollment = $studentProfile->enrollments()->where('status', 1)->latest('id')->first();
        if (! $enrollment) {
            return $this->error('Active student enrollment not found.', 404);
        }

        $exams = Exam::with(['schedules.subject', 'academicSession'])
            ->where('status', Exam::STATUS_PUBLISHED)
            ->where(function ($q) use ($enrollment) {
                $q->where('class_id', $enrollment->class_id)
                    ->orWhereNull('class_id');
            })
            ->where('academic_session_id', $enrollment->academic_session_id)
            ->latest('id')
            ->get();

        return $this->success('Exams fetched successfully.', $exams);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $studentProfile = $user->studentProfile;

        $exam = Exam::with([
            'schedules.subject',
            'schedules.invigilator',
            'academicSession',
            'academicClass',
        ])->find($id);

        if (! $exam) {
            return $this->error('Exam not found.', 404);
        }

        return $this->success('Exam details fetched successfully.', $exam);
    }
}
