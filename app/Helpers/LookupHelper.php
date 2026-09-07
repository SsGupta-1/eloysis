<?php

use App\Models\AcademicClass;
use App\Models\AcademicSession;
use App\Models\ClassSection;
use App\Models\ClassSubject;
use App\Models\Role;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\Periods;
use App\Models\TeacherSubject;

if (! function_exists('role_options')) {

    function role_options(): array
    {
        return Role::query()
            ->where('status', true)
            ->orderBy('role_name')
            ->pluck('role_name', 'id')
            ->toArray();
    }
}

if (! function_exists('academic_session_options')) {

    function academic_session_options($is_current = null): array
    {
        return AcademicSession::query()
            ->when($is_current, fn ($query) => $query->where('is_current', $is_current))
            ->where('status', 1)
            ->orderByDesc('start_date')
            ->pluck('name', 'id')
            ->toArray();
    }
}

if (! function_exists('class_options')) {

    function class_options(): array
    {
        return AcademicClass::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->pluck('class_name', 'id')
            ->toArray();
    }
}

if (! function_exists('section_options')) {

    function section_options(): array
    {
        return Section::query()
            ->where('status', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }
}

if (! function_exists('class_section_options')) {

    function class_section_options(?int $classId = null): array
    {
        return ClassSection::query()
            ->with('section:id,name')
            ->where('status', true)
            ->when(
                $classId,
                fn ($query) => $query->where('class_id', $classId)
            )
            ->whereHas('section', function ($query) {
                $query->where('status', true);
            })
            ->get()
            ->pluck('section.name', 'section_id')
            ->toArray();
    }
}

if (! function_exists('subject_options')) {

    function subject_options(): array
    {
        return Subject::query()
            ->where('status', true)
            ->pluck('subject_name', 'id')
            ->toArray();
    }
}

if (! function_exists('class_subject_options')) {

    function class_subject_options(?int $classId = null): array
    {
        return ClassSubject::query()
            ->with('subject:id,subject_name')
            ->where('status', true)
            ->when(
                $classId,
                fn ($query) => $query->where('class_id', $classId)
            )
            ->whereHas('subject', function ($query) {
                $query->where('status', true);
            })->get()
            ->pluck('subject.subject_name', 'subject_id')
            ->toArray();
    }
}

if (! function_exists('teacher_options')) {

    function teacher_options(): array
    {
        return TeacherProfile::query()
            ->with('user:id,name')
            ->whereHas('user', function ($query) {
                $query->where('status', true);
            })
            ->get()
            ->mapWithKeys(function ($teacher) {

                return [
                    $teacher->user->id => $teacher->user->name.
                        ' ('.$teacher->employee_id.')',
                ];

            })
            ->toArray();
    }
}

if (! function_exists('period_options')) {

    function period_options(): array
    {
        return Periods::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->pluck('name', 'id')
            ->toArray();
    }
}

if (! function_exists('teacher_subject_options')) {

    function teacher_subject_options(): array
    {
        return TeacherSubject::query()
            ->with([
                'teacher:id,name',
                'subjectClass:id,class_name',
                'section:id,name',
                'subject:id,subject_name',
            ])->where('status', 1)
            ->get()
            ->mapWithKeys(function ($teacherSubject) {

                $label = implode(' → ', array_filter([
                    $teacherSubject->teacher?->name,
                    $teacherSubject->subjectClass?->class_name,
                    $teacherSubject->section?->name,
                    $teacherSubject->subject?->subject_name,
                ]));

                return [
                    $teacherSubject->id => $label,
                ];

            })
            ->toArray();
    }
}

if (! function_exists('admission_enquiry_status_options')) {

    function admission_enquiry_status_options(): array
    {
        return [

            'new' => 'New',

            'contacted' => 'Contacted',

            'follow_up' => 'Follow Up',

            'interested' => 'Interested',

            'not_interested' => 'Not Interested',

            'visit_scheduled' => 'Visit Scheduled',

            'visited' => 'Visited',

            'converted' => 'Converted',

            'lost' => 'Lost',

        ];
    }
}

if (! function_exists('admission_attempt_status_options')) {

    function admission_attempt_status_options(): array
    {
        return [

            'connected' => 'Connected',

            'not_connected' => 'Not Connected',

            'busy' => 'Busy',

            'switched_off' => 'Switched Off',

            'wrong_number' => 'Wrong Number',

            'callback_requested' => 'Callback Requested',

        ];
    }
}
