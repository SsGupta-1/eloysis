<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class NewsRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'published_date' => ['required', 'date'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'url' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'status' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'News title is required.',
            'published_date.required' => 'Published date is required.',
            'published_date.date' => 'Published date must be a valid date.',
            'image.image' => 'Uploaded file must be a valid image.',
        ];
    }
}
