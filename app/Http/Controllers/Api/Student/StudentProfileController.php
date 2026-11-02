<?php

namespace App\Http\Controllers\Api\Student;

use App\Models\StudentEnrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentProfileController extends BaseStudentApiController
{
    /**
     * Get Student Profile
     */
    public function show(Request $request): JsonResponse
    {
        [$user, $profile, $enrollment] = $this->getStudentData($request);

        if (! $profile) {
            return $this->error('Student profile not found.', [], 404);
        }

        $enrollmentHistory = StudentEnrollment::where('stu_profile_id', $profile->id)
            ->with(['studentClass', 'section', 'academicSession'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($enr) {
                return [
                    'id' => $enr->id,
                    'roll_number' => $enr->roll_number,
                    'class_name' => $enr->studentClass?->class_name,
                    'section_name' => $enr->section?->section_name,
                    'academic_session' => $enr->academicSession?->name,
                    'status' => $enr->status,
                ];
            });

        $data = [
            'personal' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'profile_image' => $user->profile_image_url,
                'admission_no' => $profile->admission_no,
                'admission_date' => $profile->admission_date ? date('Y-m-d', strtotime($profile->admission_date)) : null,
                'dob' => $profile->dob ? $profile->dob->format('Y-m-d') : null,
                'gender' => $profile->gender,
                'blood_group' => $profile->blood_group,
                'address' => $profile->address,
                'city' => $profile->city,
                'state' => $profile->state,
                'pincode' => $profile->pincode,
            ],
            'parents' => [
                'father_name' => $profile->father_name,
                'mother_name' => $profile->mother_name,
                'guardian_name' => $profile->guardian_name,
                'guardian_mobile' => $profile->guardian_mobile,
                'guardian_email' => $profile->guardian_email,
            ],
            'current_enrollment' => $enrollment ? [
                'id' => $enrollment->id,
                'roll_number' => $enrollment->roll_number,
                'class_id' => $enrollment->class_id,
                'class_name' => $enrollment->studentClass?->class_name,
                'section_id' => $enrollment->section_id,
                'section_name' => $enrollment->section?->section_name,
                'academic_session_id' => $enrollment->academic_session_id,
                'academic_session' => $enrollment->academicSession?->name,
            ] : null,
            'enrollment_history' => $enrollmentHistory,
        ];

        return $this->success('Student profile fetched successfully.', $data);
    }

    /**
     * Change Password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return $this->error('The provided current password does not match our records.', [
                'current_password' => ['Incorrect current password.'],
            ], 422);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return $this->success('Password updated successfully.');
    }
}
