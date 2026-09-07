<?php

namespace App\Http\Requests\Website;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContactRequest extends BaseRequest
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

            'name' => 'required|string|max:100',

            'email' => 'required|email|max:150',

            'phone' => 'nullable|digits_between:10,15',

            'subject' => 'required|max:200',

            'message' => 'required|min:10|max:5000',

        ];
    }

    public function messages()
    {
        return [

            'name.required' => 'Please enter your name.',

            'email.required' => 'Please enter your email.',

            'subject.required' => 'Please enter subject.',

            'message.required' => 'Please enter message.',

        ];
    }
}
