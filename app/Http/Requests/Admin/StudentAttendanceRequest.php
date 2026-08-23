<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class StudentAttendanceRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare request data.
     */
    protected function prepareForValidation(): void
    {
        // $this->merge([
        //     'academic_session_id' => $this->academic_session_id
        //         ? (int) $this->academic_session_id
        //         : null,

        //     'class_id' => $this->class_id
        //         ? (int) $this->class_id
        //         : null,

        //     'section_id' => $this->section_id
        //         ? (int) $this->section_id
        //         : null,
        // ]);

        $attendance = $this->input('attendance');

        /*
        |--------------------------------------------------------------------------
        | Decode attendance JSON
        |--------------------------------------------------------------------------
        */

        if (is_string($attendance)) {

            $attendance = json_decode(
                $attendance,
                true
            );

        }

        $this->merge([

            'academic_session_id' => $this->academic_session_id
                ? (int) $this->academic_session_id
                : null,

            'class_id' => $this->class_id
                ? (int) $this->class_id
                : null,

            'section_id' => $this->section_id
                ? (int) $this->section_id
                : null,

            'attendance' => $attendance,

        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isSaveRequest = $this->isMethod('post');

        return [

            /*
             |--------------------------------------------------------------------------
             | Academic Session
             |--------------------------------------------------------------------------
             */

            'academic_session_id' => [
                'required',
                'integer',
                'exists:academic_sessions,id',
            ],

            /*
             |--------------------------------------------------------------------------
             | Class
             |--------------------------------------------------------------------------
             */

            'class_id' => [
                'required',
                'integer',
                'exists:academic_classes,id',
            ],

            /*
             |--------------------------------------------------------------------------
             | Section
             |--------------------------------------------------------------------------
             */

            'section_id' => [
                'nullable',
                'integer',
                'exists:sections,id',
            ],

            /*
             |--------------------------------------------------------------------------
             | Attendance Date
             |--------------------------------------------------------------------------
             */

            'attendance_date' => [
                'required',
                'date',
            ],

            /*
             |--------------------------------------------------------------------------
             | Attendance
             |--------------------------------------------------------------------------
             */

            'attendance' => $isSaveRequest
                ? ['required', 'array', 'min:1']
                : ['nullable', 'array'],

            /*
             |--------------------------------------------------------------------------
             | Enrollment ID
             |--------------------------------------------------------------------------
             */

            'attendance.*.student_enrollment_id' => [
                'required',
                'integer',
                'distinct',
                'exists:student_enrollments,id',
            ],

            /*
             |--------------------------------------------------------------------------
             | Attendance Status
             |--------------------------------------------------------------------------
             */

            'attendance.*.status' => [
                'required',
                Rule::in([
                    'present',
                    'absent',
                    'late',
                    'leave',
                ]),
            ],

            /*
             |--------------------------------------------------------------------------
             | Remarks
             |--------------------------------------------------------------------------
             */

            'attendance.*.remarks' => [
                'nullable',
                'string',
                'max:500',
            ],

        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [

            'academic_session_id.required' =>
                'Academic session is required.',

            'academic_session_id.exists' =>
                'Selected academic session is invalid.',

            'class_id.required' =>
                'Class is required.',

            'class_id.exists' =>
                'Selected class is invalid.',

            'attendance_date.required' =>
                'Attendance date is required.',

            'attendance_date.date' =>
                'Please provide a valid attendance date.',

            'attendance.required' =>
                'Please select at least one student.',

            'attendance.min' =>
                'Please select at least one student.',

            'attendance.*.student_enrollment_id.required' =>
                'Student enrollment is required.',

            'attendance.*.student_enrollment_id.exists' =>
                'Selected student enrollment is invalid.',

            'attendance.*.student_enrollment_id.distinct' =>
                'Duplicate student enrollment is not allowed.',

            'attendance.*.status.required' =>
                'Attendance status is required.',

            'attendance.*.status.in' =>
                'Invalid attendance status.',

            'attendance.*.remarks.max' =>
                'Remarks cannot exceed 500 characters.',

        ];
    }
}
