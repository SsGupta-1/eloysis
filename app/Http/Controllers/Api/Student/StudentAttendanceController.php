<?php

namespace App\Http\Controllers\Api\Student;

use App\Services\Student\StudentAttendanceHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentAttendanceController extends BaseStudentApiController
{
    /**
     * Get Student Attendance with full Monthly Calendar,
     * Sundays, Holidays, Unmarked Absents, and Statistics.
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

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $attendanceData = StudentAttendanceHelper::buildMonthlyAttendance($enrollment->id, $month, $year);

        return $this->success('Attendance fetched successfully.', $attendanceData);
    }
}
