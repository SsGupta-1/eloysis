<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class ImportantMessageRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'type' => ['required', 'string', 'in:info,warning,danger,success'],
            'action_text' => ['nullable', 'string', 'max:100'],
            'action_url' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Message title is required.',
            'message.required' => 'Message content is required.',
            'type.required' => 'Alert type is required.',
            'type.in' => 'Alert type must be info, warning, danger, or success.',
            'end_date.after_or_equal' => 'End date must be on or after start date.',
        ];
    }
}
