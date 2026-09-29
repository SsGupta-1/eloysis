<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ExamRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $examId = $this->route('exam')?->id ?? $this->route('exam');

        return [
            'academic_session_id' => [
                'nullable',
                'integer',
                'exists:academic_sessions,id',
            ],
            'class_id' => [
                'nullable',
                'integer',
                'exists:academic_classes,id',
            ],
            'subject_id' => [
                'nullable',
                'integer',
                'exists:subjects,id',
            ],
            'title' => [
                'required',
                'string',
                'max:200',
            ],
            'exam_code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('exams', 'exam_code')->ignore($examId),
            ],
            'exam_type' => [
                'required',
                'string',
                Rule::in(['unit_test', 'mid_term', 'quarterly', 'half_yearly', 'annual', 'practical', 'entrance', 'mock', 'other']),
            ],
            'exam_mode' => [
                'required',
                'string',
                Rule::in(['offline', 'online', 'both']),
            ],
            'duration_minutes' => [
                'required',
                'integer',
                'min:10',
                'max:600',
            ],
            'total_marks' => [
                'required',
                'numeric',
                'min:1',
                'max:1000',
            ],
            'passing_marks' => [
                'nullable',
                'numeric',
                'min:0',
                'max:1000',
            ],
            'start_date' => [
                'nullable',
                'date',
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'instructions' => [
                'nullable',
                'string',
            ],
            'negative_marking' => [
                'nullable',
                'boolean',
            ],
            'negative_marks_per_wrong' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'shuffle_questions' => [
                'nullable',
                'boolean',
            ],
            'shuffle_options' => [
                'nullable',
                'boolean',
            ],
            'status' => [
                'nullable',
                'string',
                Rule::in(['draft', 'published', 'closed']),
            ],
            'schedules' => [
                'nullable',
                'array',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please enter the exam title.',
            'exam_type.required' => 'Please select the exam type.',
            'exam_mode.required' => 'Please select the exam mode (Offline / Online / Both).',
            'duration_minutes.required' => 'Please enter duration in minutes.',
            'total_marks.required' => 'Please enter total marks.',
            'end_date.after_or_equal' => 'End date must be on or after start date.',
        ];
    }
}
