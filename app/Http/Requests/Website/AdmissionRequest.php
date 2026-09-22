<?php

namespace App\Http\Requests\Website;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class AdmissionRequest extends BaseRequest
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
                'regex:/^[6-9][0-9]{9}$/',
            ],

            'date_of_birth' => [
                'required',
                'date',
                'before:today',
                'after:2000-01-01',
            ],

            'gender' => [
                'required',
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
                'regex:/^[6-9][0-9]{9}$/',
            ],

            'alternate_phone' => [
                'nullable',
                'string',
                'regex:/^[6-9][0-9]{9}$/',
                'different:student_phone',
            ],

            'class_id' => [
                'nullable',
                'integer',
                'exists:academic_classes,id',
            ],

            'source' => [
                'nullable',
                'string',
                'in:website,reference,walk_in,google,facebook,instagram,other',
            ],

            'reference_type' => [
                'nullable',
                'string',
                'in:student,parent,teacher,staff,other',
            ],

            'reference_id' => [
                'nullable',
                'integer',
            ],

            'reference_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'reference_phone' => [
                'nullable',
                'string',
                'regex:/^[6-9][0-9]{9}$/',
            ],

            'message' => [
                'nullable',
                'string',
                'max:2000',
            ],

        ];
    }

    public function customMessages(): array
    {
        return [

            'student_name.required' => 'Student name is required.',
            'student_name.max' => 'Student name cannot exceed 150 characters.',
            'student_email.required' => 'Student email is required.',
            'student_email.email' => 'Please enter a valid email address.',
            'student_email.regex' => 'Please enter a valid email address.',
            'student_phone.required' => 'Student phone is required.',
            'student_phone.regex' => 'Please enter a valid phone number.',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.date' => 'Please enter a valid date of birth.',
            'date_of_birth.before' => 'Date of birth cannot be in the future.',
            'date_of_birth.after' => 'Date of birth cannot be before 2000-01-01.',
            'gender.required' => 'Gender is required.',
            'gender.in' => 'Gender must be male, female, or other.',
            'parent_name.required' => 'Parent name is required.',
            'parent_name.max' => 'Parent name cannot exceed 255 characters.',
            'parent_phone.required' => 'Parent phone is required.',
            'parent_phone.regex' => 'Please enter a valid phone number.',
            'alternate_phone.regex' => 'Please enter a valid phone number.',
            'alternate_phone.different' => 'Alternate phone must be different from phone.',
            'class_id.required' => 'Class is required.',
            'class_id.exists' => 'The selected class is invalid.',
            'source.required' => 'Source is required.',
            'source.in' => 'Source must be website, reference, walk_in, google, facebook, instagram, or other.',
            'reference_type.required' => 'Reference type is required.',
            'reference_type.in' => 'Reference type must be student, parent, teacher, staff, or other.',
            'reference_id.required' => 'Reference id is required.',
            'reference_id.integer' => 'Reference id must be an integer.',
            'reference_name.required' => 'Reference name is required.',
            'reference_name.max' => 'Reference name cannot exceed 255 characters.',
            'reference_phone.required' => 'Reference phone is required.',
            'reference_phone.regex' => 'Please enter a valid phone number.',
            'message.required' => 'Message is required.',
            'message.max' => 'Message cannot exceed 255 characters.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email cannot exceed 255 characters.',

            'phone.max' => 'Phone number cannot exceed 20 characters.',

            'class_id.required' => 'Class is required.',
            'class_id.exists' => 'The selected class is invalid.',

            'message.required' => 'Message is required.',
            'message.max' => 'Message cannot exceed 255 characters.',

        ];
    }
}
