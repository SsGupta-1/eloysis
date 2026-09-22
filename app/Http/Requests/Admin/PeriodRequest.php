<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class PeriodRequest extends BaseRequest
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
        $periodId = $this->route('period')?->id;

        return [

            'name' => [
                'required',
                'string',
                'max:100',
                'min:2',
                Rule::unique('periods', 'name')->ignore($periodId),
            ],

            'start_time' => [
                'required',
                'string',
                'max:50',
                'min:2',
                Rule::unique('periods', 'start_time')->ignore($periodId),
            ],

            'end_time' => [
                'required',
                'string',
                'max:50',
                'min:2',
                Rule::unique('periods', 'end_time')->ignore($periodId),
            ],

            'sort_order' => [
                'required',
                'integer',
                'max:500',
            ],

            'status' => [
                'required',
                'boolean',
            ],

        ];
    }

    /**
     * Custom Messages
     */
    public function messages(): array
    {
        return [

            'name.required' => 'Please enter class name.',

            'name.unique' => 'Class name already exists.',

            'start_time.required' => 'Start time is required.',

            'start_time.unique' => 'Start time already exists.',

            'end_time.required' => 'End time is required.',

            'end_time.unique' => 'End time already exists.',

            'sort_order.required' => 'Sort order is required.',

            'status.required' => 'Status is required.',

        ];
    }
}
