<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class QuestionRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'class_id' => [
                'required',
                'integer',
                'exists:academic_classes,id',
            ],
            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],
            'academic_session_id' => [
                'nullable',
                'integer',
                'exists:academic_sessions,id',
            ],
            'chapter_name' => [
                'nullable',
                'string',
                'max:200',
            ],
            'topic_name' => [
                'nullable',
                'string',
                'max:200',
            ],
            'question_type' => [
                'required',
                'string',
                Rule::in(['mcq', 'true_false', 'fill_blanks', 'short_answer', 'long_answer', 'descriptive', 'match_following']),
            ],
            'difficulty_level' => [
                'required',
                'string',
                Rule::in(['easy', 'medium', 'hard']),
            ],
            'blooms_taxonomy' => [
                'nullable',
                'string',
                Rule::in(['remember', 'understand', 'apply', 'analyze', 'evaluate', 'create']),
            ],
            'question_text' => [
                'required',
                'string',
            ],
            'option_a' => [
                'nullable',
                'string',
            ],
            'option_b' => [
                'nullable',
                'string',
            ],
            'option_c' => [
                'nullable',
                'string',
            ],
            'option_d' => [
                'nullable',
                'string',
            ],
            'correct_option' => [
                'nullable',
                'string',
                'max:50',
            ],
            'options' => [
                'nullable',
                'array',
            ],
            'correct_answer_data' => [
                'nullable',
            ],
            'marks' => [
                'required',
                'numeric',
                'min:0.5',
                'max:100',
            ],
            'negative_marks' => [
                'nullable',
                'numeric',
                'min:0',
                'max:50',
            ],
            'explanation' => [
                'nullable',
                'string',
            ],
            'tags' => [
                'nullable',
                'array',
            ],
            'image_path' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp,svg',
                'max:2048',
            ],
            'attachment_url' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx,png,jpg,jpeg',
                'max:5120',
            ],
            'status' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'class_id.required' => 'Please select an academic class.',
            'class_id.exists' => 'The selected class is invalid.',
            'subject_id.required' => 'Please select a subject.',
            'subject_id.exists' => 'The selected subject is invalid.',
            'question_type.required' => 'Please choose a question type.',
            'question_type.in' => 'Selected question type is invalid.',
            'difficulty_level.required' => 'Please specify the difficulty level.',
            'question_text.required' => 'Question text cannot be empty.',
            'marks.required' => 'Please enter marks for this question.',
            'marks.numeric' => 'Marks must be a valid number.',
        ];
    }
}
