<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FeeBulkAllocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'academic_class_id' => 'required|exists:academic_classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'fee_structure_ids' => 'required|array|min:1',
            'fee_structure_ids.*' => 'exists:fee_structures,id',
            'month' => 'nullable|string|max:50',
            'year' => 'nullable|integer|min:2000|max:2100',
            'due_date' => 'nullable|date',
            'fee_discount_id' => 'nullable|string',
        ];
    }
}
