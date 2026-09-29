<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->studentProfile;
        
        $totalTests = 0;
        $upcomingTests = 0;
        $activeTests = 0;
        $completedTests = 0;

        if ($student) {
            $batches = $student->batches()
                ->with([
                    'batchesSubjects.batchesTests.exam'
                ])
                ->whereHas('batchesSubjects.batchesTests', function ($query) {
                    $query->where('status', 1);
                })
                ->get();

            foreach ($batches as $batch) {
                foreach ($batch->batchesSubjects as $subject) {
                    foreach ($subject->batchesTests as $test) {
                        $totalTests++;
                        $exam = $test->exam;

                        if ($exam) {
                            $currentDate = now();

                            // Exam is active
                            if ($currentDate->gte($exam->start_date) && $currentDate->lte($exam->end_date)) {
                                $activeTests++;
                            }
                            // Exam is upcoming
                            elseif ($currentDate->lt($exam->start_date)) {
                                $upcomingTests++;
                            }
                            // Exam is completed
                            elseif ($currentDate->gt($exam->end_date)) {
                                $completedTests++;
                            }
                        }
                    }
                }
            }
        }

        $data = [
            'upcoming_exams' => $upcomingTests,
            'active_exams' => $activeTests,
            'completed_exams' => $completedTests,
        ];

        return $this->success(
            'Dashboard fetched successfully.',
            $data,
            200
        );
    }
}
