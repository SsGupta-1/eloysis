<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FeeAllocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_enrollment_id' => 'required|exists:student_enrollments,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'fee_head_id' => 'required|exists:fee_heads,id',
            'fee_structure_id' => 'nullable|exists:fee_structures,id',
            'fee_discount_id' => 'nullable|exists:fee_discounts,id',
            'title' => 'required|string|max:255',
            'month' => 'nullable|string|max:50',
            'year' => 'nullable|integer|min:2000|max:2100',
            'due_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'fine_amount' => 'nullable|numeric|min:0',
        ];
    }
}
