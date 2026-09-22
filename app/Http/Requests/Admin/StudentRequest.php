<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use App\Models\StudentEnrollment;
use App\Models\StudentProfile;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class StudentRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $studentParam = $this->route('student') ?? $this->route('students');
        $studentEnrollmentId = null;
        $studentProfileId = null;
        $userId = null;

        if ($studentParam instanceof StudentEnrollment) {
            $studentEnrollmentId = $studentParam->id;
            $studentProfileId = $studentParam->stu_profile_id;
            $userId = $studentParam->user_id;
        } elseif ($studentParam instanceof StudentProfile) {
            $studentProfileId = $studentParam->id;
            $userId = $studentParam->user_id;
            $studentEnrollmentId = $studentParam->enrollments()->latest('id')->value('id');
        } elseif (is_numeric($studentParam)) {
            $enrollment = StudentEnrollment::find($studentParam);
            if ($enrollment) {
                $studentEnrollmentId = $enrollment->id;
                $studentProfileId = $enrollment->stu_profile_id;
                $userId = $enrollment->user_id;
            }
        }

        return [

            /*
            |--------------------------------------------------------------------------
            | User Details
            |--------------------------------------------------------------------------
            */

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [

                'required',

                'email',

                'max:150',

                Rule::unique('users', 'email')
                    ->ignore($userId ?? $this->route('student')?->user_id),

            ],

            'mobile' => [

                'nullable',

                'string',

                'max:20',

            ],

            /*
            |--------------------------------------------------------------------------
            | Student Profile
            |--------------------------------------------------------------------------
            */

            'admission_no' => [

                'required',

                'string',

                'max:50',

                Rule::unique(
                    'student_profiles',
                    'admission_no'
                )->ignore($studentProfileId),

            ],

            'roll_number' => [

                'nullable',

                'string',

                'max:50',

                Rule::unique('student_enrollments', 'roll_number')
                    ->where(function ($query) {
                        return $query->where('academic_session_id', $this->input('academic_session_id'))
                            ->where('class_id', $this->input('class_id'))
                            ->where('section_id', $this->input('section_id'))
                            ->whereNull('deleted_at');
                    })
                    ->ignore($studentEnrollmentId),

            ],

            'dob' => [

                'nullable',

                'date',

            ],

            'gender' => [

                'nullable',

                Rule::in([
                    'male',
                    'female',
                    'other',
                ]),

            ],

            'address' => [

                'nullable',

                'string',

                'max:500',

            ],

            /*
            |--------------------------------------------------------------------------
            | Enrollment
            |--------------------------------------------------------------------------
            */

            'academic_session_id' => [

                'required',

                'integer',

                'exists:academic_sessions,id',

            ],

            'class_id' => [

                'required',

                'integer',

                'exists:academic_classes,id',

            ],

            'section_id' => [

                'required',

                'integer',

                'exists:sections,id',

            ],

            'admission_date' => [

                'nullable',

                'date',

            ],

            /*
            |--------------------------------------------------------------------------
            | Profile Image
            |--------------------------------------------------------------------------
            */

            'profile_image' => [

                'nullable',

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:2048',

            ],

            'password' => [
                $this->isMethod('POST')
                    ? 'required'
                    : 'nullable',

                'string',

                'min:8',

                'confirmed',
            ],

            'blood_group' => [
                'nullable',
                'string',
                'max:10',
            ],

            'father_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'mother_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'guardian_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'guardian_mobile' => [
                'nullable',
                'string',
                'max:20',
            ],

            'guardian_email' => [
                'nullable',
                'email',
                'max:150',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'state' => [
                'nullable',
                'string',
                'max:100',
            ],

            'pincode' => [
                'nullable',
                'string',
                'max:10',
            ],

            'status' => [
                'required',
                'boolean',
            ],

        ];
    }

    /**
     * Custom Validation Messages
     */
    public function messages(): array
    {
        return [

            'name.required' => 'Please enter student name.',

            'email.required' => 'Please enter email address.',

            'email.email' => 'Please enter a valid email address.',

            'email.unique' => 'Email address already exists.',

            'admission_no.required' => 'Please enter admission number.',

            'admission_no.unique' => 'Admission number already exists.',

            'academic_session_id.required' => 'Please select academic session.',

            'academic_session_id.exists' => 'Selected academic session is invalid.',

            'class_id.required' => 'Please select class.',

            'class_id.exists' => 'Selected class is invalid.',

            'section_id.required' => 'Please select section.',

            'section_id.exists' => 'Selected section is invalid.',

            'status.required' => 'Please select status.',

            'profile_image.image' => 'Profile image must be a valid image.',

            'profile_image.mimes' => 'Profile image must be jpg, jpeg, png or webp.',

            'profile_image.max' => 'Profile image must not exceed 2 MB.',

        ];
    }
}
