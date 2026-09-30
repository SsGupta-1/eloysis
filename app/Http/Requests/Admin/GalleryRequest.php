<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class GalleryRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
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
            'image.required' => 'Image is required.',
            'image.image' => 'Uploaded file must be a valid image.',
            'image.max' => 'Image size cannot exceed 4MB.',
        ];
    }
}
