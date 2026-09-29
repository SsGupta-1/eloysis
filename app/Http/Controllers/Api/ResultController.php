<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\StudentEnrollment;
use App\Services\Admin\ResultService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResultController extends BaseController
{
    public function __construct(
        protected ResultService $resultService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $studentProfile = $user->studentProfile;

        if (! $studentProfile) {
            return $this->error('Student profile not found.', 404);
        }

        $results = $this->resultService->getStudentResultsForApi($studentProfile->id);

        return $this->success('Published exam results fetched successfully.', $results);
    }

    public function show(Request $request, int $examId): JsonResponse
    {
        $user = $request->user();
        $studentProfile = $user->studentProfile;

        if (! $studentProfile) {
            return $this->error('Student profile not found.', 404);
        }

        $enrollment = StudentEnrollment::where('stu_profile_id', $studentProfile->id)
            ->where('status', 1)
            ->latest('id')
            ->first();

        if (! $enrollment) {
            return $this->error('Active student enrollment not found.', 404);
        }

        try {
            $reportCard = $this->resultService->getStudentReportCard($examId, $enrollment->id);

            return $this->success('Student report card fetched successfully.', $reportCard);
        } catch (Exception $e) {
            return $this->error('Unable to fetch report card: '.$e->getMessage(), 404);
        }
    }
}
