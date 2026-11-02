<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\BaseController;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;

abstract class BaseStudentApiController extends BaseController
{
    /**
     * Get authenticated student user, profile, and active enrollment.
     *
     * @return array{0: \App\Models\User, 1: ?\App\Models\StudentProfile, 2: ?\App\Models\StudentEnrollment}
     */
    protected function getStudentData(Request $request): array
    {
        $user = $request->user();

        $user->loadMissing([
            'studentProfile',
            'currentEnrollment.studentClass',
            'currentEnrollment.section',
            'currentEnrollment.academicSession',
        ]);

        $profile = $user->studentProfile;
        $enrollment = $user->currentEnrollment;

        // Fallback: If currentEnrollment is null, get latest enrollment from profile
        if (! $enrollment && $profile) {
            $enrollment = StudentEnrollment::where('stu_profile_id', $profile->id)
                ->with(['studentClass', 'section', 'academicSession'])
                ->latest('id')
                ->first();
        }

        return [$user, $profile, $enrollment];
    }

    /**
     * Calculate Grade from percentage
     */
    protected function calculateGrade(float $percentage): string
    {
        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B+',
            $percentage >= 60 => 'B',
            $percentage >= 50 => 'C',
            $percentage >= 40 => 'D',
            default => 'F',
        };
    }
}
