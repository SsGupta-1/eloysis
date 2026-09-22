<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class AdmissionEnquiryRequest extends BaseRequest
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
        return [
            'student_name' => [
                'required',
                'string',
                'max:150',
            ],

            'student_email' => [
                'required',
                'email',
                'max:150',
            ],

            'student_phone' => [
                'required',
                'string',
                'max:20',
            ],

            'alternate_phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'date_of_birth' => [
                'nullable',
                'date',
                'before:today',
            ],

            'gender' => [
                'nullable',
                'string',
                'in:male,female,other',
            ],

            'parent_name' => [
                'required',
                'string',
                'max:150',
            ],

            'parent_phone' => [
                'required',
                'string',
                'max:20',
            ],

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

            'source' => [
                'required',
                'string',
                'in:website,walk_in,reference,google,facebook,instagram,advertisement,phone,other',
            ],

            'assigned_to' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'reference_type' => [
                'nullable',
                'string',
                'max:100',
            ],

            'reference_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'reference_phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'message' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'next_follow_up_at' => [
                'nullable',
                'date',
            ],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'student_name.required' => 'Please enter student name.',
            'student_email.required' => 'Please enter student email address.',
            'student_email.email' => 'Please enter a valid email address.',
            'student_phone.required' => 'Please enter student mobile number.',
            'parent_name.required' => 'Please enter parent/guardian name.',
            'parent_phone.required' => 'Please enter parent/guardian phone number.',
            'academic_session_id.required' => 'Please select an academic session.',
            'academic_session_id.exists' => 'Selected academic session is invalid.',
            'class_id.required' => 'Please select an interested class.',
            'class_id.exists' => 'Selected class is invalid.',
            'source.required' => 'Please select enquiry source.',
        ];
    }
}
