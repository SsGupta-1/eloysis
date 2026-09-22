<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use App\Models\ClassTimetables;
use App\Models\TeacherSubject;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class ClassTimetableRequest extends BaseRequest
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
        $timetableId = $this->route('class_timetable')?->id;

        return [

            'period_id' => [
                'required',
                'exists:periods,id',
            ],

            'teacher_subject_id' => [
                'required',

                Rule::exists(
                    'teacher_subjects',
                    'id'
                )->where(function ($query) {

                    $query->where('status', 1);
                }),
            ],

            'day' => [
                'required',
                'max:20',
            ],
            'status' => [
                'required',
                'in:1,0',
            ],
        ];
    }

    /**
     * Custom Messages
     */
    public function messages(): array
    {
        return [

            'teacher_subject_id.required' => 'Teacher subject assignment is required.',

            'teacher_subject_id.exists' => 'Selected teacher subject assignment is invalid or inactive.',

            'period_id.required' => 'Period is required.',

            'period_id.exists' => 'Selected period is invalid.',

            'day.required' => 'Day is required.',

            'day.max' => 'Day cannot exceed 20 characters.',

            'status.required' => 'Status is required.',

            'status.in' => 'Status must be 1 or 0.',

        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            $teacherSubjectId = $this->teacher_subject_id;
            $periodId = $this->period_id;
            $day = $this->day;

            $timetableId = $this->route('class_timetable')?->id;

            /*
            |--------------------------------------------------------------------------
            | Get Teacher Subject Assignment
            |--------------------------------------------------------------------------
            */

            $teacherSubject = TeacherSubject::query()
                ->where('id', $teacherSubjectId)
                ->where('status', 1)
                ->first();

            if (! $teacherSubject) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Same Teacher Conflict
            |--------------------------------------------------------------------------
            |
            | A teacher cannot teach two different classes
            | in the same period on the same day.
            |
            */

            $teacherConflict = ClassTimetables::query()
                ->where('period_id', $periodId)
                ->where('day', $day)
                ->where('status', 1)
                ->whereHas('teacherSubject', function ($query) use ($teacherSubject) {

                    $query->where('teacher_id', $teacherSubject->teacher_id);

                })
                ->when($timetableId, function ($query) use ($timetableId) {

                    $query->where('id', '!=', $timetableId);

                })
                ->exists();

            if ($teacherConflict) {

                $validator->errors()->add(
                    'teacher_subject_id',
                    'This teacher is already assigned to another class for the selected day and period.'
                );

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Same Class / Section Conflict
            |--------------------------------------------------------------------------
            |
            | A class section cannot have two different subjects
            | in the same period on the same day.
            |
            */

            $classConflict = ClassTimetables::query()
                ->where('period_id', $periodId)
                ->where('day', $day)
                ->where('status', 1)
                ->whereHas('teacherSubject', function ($query) use ($teacherSubject) {

                    $query->where('class_id', $teacherSubject->class_id)
                        ->where('section_id', $teacherSubject->section_id);

                })
                ->when($timetableId, function ($query) use ($timetableId) {

                    $query->where('id', '!=', $timetableId);

                }
                )
                ->exists();

            if ($classConflict) {

                $validator->errors()->add(
                    'period_id',
                    'This class and section already have a timetable for the selected day and period.'
                );
            }
        });
    }
}
