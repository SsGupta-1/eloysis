<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class HomeSliderRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'boolean'],
        ];

        if ($this->isMethod('POST')) {
            $rules['image'] = ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'];
        } else {
            $rules['image'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Slider title is required.',
            'image.required' => 'Slider image is required.',
            'image.image' => 'Uploaded file must be a valid image.',
            'image.mimes' => 'Image format must be jpeg, png, jpg, or webp.',
            'image.max' => 'Image size cannot exceed 4MB.',
        ];
    }
}
