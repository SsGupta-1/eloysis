<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class LoginRequest extends BaseRequest
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
            'login' => [
                'required',
                'string',
            ],
            'password' => [
                'required',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'login.required' => 'Username, email or mobile is required.',
            'password.required' => 'Password is required.',
        ];
    }

    public function attributes(): array
    {
        return [
            'login' => 'login',
            'password' => 'password',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'login' => $this->input('username') ?? $this->input('email') ?? $this->input('mobile') ?? $this->input('login'),
        ]);
    }
}
