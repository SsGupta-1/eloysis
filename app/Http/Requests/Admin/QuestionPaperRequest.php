<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class QuestionPaperRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $paperId = $this->route('question_paper')?->id ?? $this->route('question_paper');

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
            'exam_id' => [
                'nullable',
                'integer',
                'exists:exams,id',
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'paper_code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('question_papers', 'paper_code')->ignore($paperId),
            ],
            'total_marks' => [
                'required',
                'numeric',
                'min:1',
                'max:1000',
            ],
            'duration_minutes' => [
                'required',
                'integer',
                'min:10',
                'max:600',
            ],
            'instructions' => [
                'nullable',
                'string',
            ],
            'is_confidential' => [
                'nullable',
                'boolean',
            ],
            'has_sets' => [
                'nullable',
                'boolean',
            ],
            'set_names' => [
                'nullable',
                'array',
            ],
            'shuffle_questions' => [
                'nullable',
                'boolean',
            ],
            'shuffle_options' => [
                'nullable',
                'boolean',
            ],
            'sections' => [
                'nullable',
                'array',
            ],
            'status' => [
                'nullable',
                'string',
                Rule::in(['draft', 'published', 'archived']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'class_id.required' => 'Please select an academic class.',
            'subject_id.required' => 'Please select a subject.',
            'title.required' => 'Please enter the question paper title.',
            'total_marks.required' => 'Please enter the total marks.',
            'duration_minutes.required' => 'Please enter the duration in minutes.',
        ];
    }
}
