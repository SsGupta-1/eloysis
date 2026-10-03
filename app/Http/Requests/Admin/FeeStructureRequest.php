<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class FeeStructureRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'academic_class_id' => ['nullable', 'exists:academic_classes,id'],
            'fee_head_id' => ['required', 'exists:fee_heads,id'],
            'fee_group_id' => ['nullable', 'exists:fee_groups,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'frequency' => ['required', 'in:one_time,monthly,quarterly,half_yearly,annually'],
            'due_date' => ['nullable', 'date'],
            'fine_type' => ['required', 'in:none,flat,percentage,daily'],
            'fine_amount' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'academic_session_id.required' => 'Academic session is required.',
            'fee_head_id.required' => 'Fee Head is required.',
            'amount.required' => 'Fee amount is required.',
            'frequency.required' => 'Payment frequency is required.',
        ];
    }
}
