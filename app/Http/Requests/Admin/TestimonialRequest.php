<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class TestimonialRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:150'],
            'role' => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'boolean'],
        ];

        $rules['image'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Author name is required.',
            'message.required' => 'Feedback message is required.',
            'rating.required' => 'Star rating is required.',
            'rating.min' => 'Rating must be between 1 and 5.',
            'rating.max' => 'Rating must be between 1 and 5.',
            'image.image' => 'Uploaded avatar must be an image file.',
            'image.mimes' => 'Avatar format must be jpeg, png, jpg, or webp.',
            'image.max' => 'Avatar file size cannot exceed 3MB.',
        ];
    }
}
