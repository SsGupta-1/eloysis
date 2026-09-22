<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class TeacherAttendanceRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
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

            'attendance.*.teacher_profile_id' => [
                'required',
                'integer',
                'distinct',
                'exists:teacher_profiles,id',
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
                    'half_day',
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

            'attendance_date.required' => 'Attendance date is required.',

            'attendance_date.date' => 'Please provide a valid attendance date.',

            'attendance.required' => 'Please select at least one teacher.',

            'attendance.min' => 'Please select at least one teacher.',

            'attendance.*.teacher_id.required' => 'Teacher is required.',

            'attendance.*.teacher_id.exists' => 'Selected teacher is invalid.',

            'attendance.*.teacher_id.distinct' => 'Duplicate teacher attendance is not allowed.',

            'attendance.*.status.required' => 'Attendance status is required.',

            'attendance.*.status.in' => 'Invalid attendance status.',

            'attendance.*.remarks.max' => 'Remarks cannot exceed 500 characters.',

        ];
    }
}
