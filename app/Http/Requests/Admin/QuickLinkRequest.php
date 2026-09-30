<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class QuickLinkRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:100'],
            'url' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'in:primary,secondary,success,danger,warning,info,dark'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Quick link title is required.',
            'url.required' => 'Destination URL is required.',
            'color.required' => 'Color badge/theme is required.',
            'color.in' => 'Selected color theme is invalid.',
        ];
    }
}
