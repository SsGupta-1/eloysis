<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FeePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_enrollment_id' => 'required|exists:student_enrollments,id',
            'payment_date' => 'required|date',
            'payment_mode' => 'required|in:cash,upi,card,net_banking,cheque,dd',
            'transaction_reference' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'cheque_date' => 'nullable|date',
            'remarks' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.allocation_id' => 'required|exists:student_fee_allocations,id',
            'items.*.amount_paid' => 'required|numeric|min:0.01',
            'items.*.discount_applied' => 'nullable|numeric|min:0',
            'items.*.fine_paid' => 'nullable|numeric|min:0',
        ];
    }
}
