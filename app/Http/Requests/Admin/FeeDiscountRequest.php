<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class FeeDiscountRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('fee_discount')?->id ?? $this->input('fee_discount_id');

        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('fee_discounts', 'code')->ignore($id)->whereNull('deleted_at')],
            'discount_type' => ['required', 'in:percentage,fixed'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Discount / Scholarship name is required.',
            'discount_type.required' => 'Please select discount type (Fixed or Percentage).',
            'amount.required' => 'Please specify discount value.',
        ];
    }
}
