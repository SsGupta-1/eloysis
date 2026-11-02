<?php

namespace App\Http\Controllers\Api\Student;

use App\Models\ClassTimetables;
use App\Models\Periods;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentTimetableController extends BaseStudentApiController
{
    /**
     * Get Student Class Timetable
     */
    public function index(Request $request): JsonResponse
    {
        [$user, $profile, $enrollment] = $this->getStudentData($request);

        if (! $profile) {
            return $this->error('Student profile not found.', [], 404);
        }

        if (! $enrollment) {
            return $this->error('Active student enrollment not found.', [], 404);
        }

        $periods = Periods::where('status', 1)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($period) {
                return [
                    'id' => $period->id,
                    'name' => $period->name,
                    'start_time' => $period->start_time ? date('h:i A', strtotime($period->start_time)) : null,
                    'end_time' => $period->end_time ? date('h:i A', strtotime($period->end_time)) : null,
                    'sort_order' => $period->sort_order,
                ];
            });

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $today = strtolower(now()->format('l'));

        $timetableRecords = ClassTimetables::where('academic_session_id', $enrollment->academic_session_id)
            ->whereHas('teacherSubject', function ($q) use ($enrollment) {
                $q->where('class_id', $enrollment->class_id)
                    ->where('section_id', $enrollment->section_id);
            })
            ->with(['period', 'teacherSubject.subject', 'teacherSubject.teacher'])
            ->get();

        $scheduleByDay = [];

        foreach ($days as $day) {
            $daySlots = [];

            foreach ($periods as $period) {
                $record = $timetableRecords->first(function ($rec) use ($day, $period) {
                    return strtolower($rec->day) === $day && $rec->period_id == $period['id'];
                });

                if ($record) {
                    $daySlots[] = [
                        'period_id' => $period['id'],
                        'period_name' => $period['name'],
                        'start_time' => $period['start_time'],
                        'end_time' => $period['end_time'],
                        'subject_id' => $record->teacherSubject?->subject_id,
                        'subject_name' => $record->teacherSubject?->subject?->subject_name ?? 'N/A',
                        'subject_code' => $record->teacherSubject?->subject?->subject_code ?? null,
                        'teacher_name' => $record->teacherSubject?->teacher?->name ?? 'N/A',
                        'is_assigned' => true,
                    ];
                } else {
                    $daySlots[] = [
                        'period_id' => $period['id'],
                        'period_name' => $period['name'],
                        'start_time' => $period['start_time'],
                        'end_time' => $period['end_time'],
                        'subject_id' => null,
                        'subject_name' => 'Free / Recess',
                        'subject_code' => null,
                        'teacher_name' => null,
                        'is_assigned' => false,
                    ];
                }
            }

            $scheduleByDay[$day] = [
                'day_name' => ucfirst($day),
                'is_today' => ($day === $today),
                'classes' => $daySlots,
            ];
        }

        $data = [
            'today' => ucfirst($today),
            'class_name' => $enrollment->studentClass?->class_name,
            'section_name' => $enrollment->section?->section_name,
            'periods' => $periods,
            'schedule' => $scheduleByDay,
        ];

        return $this->success('Timetable fetched successfully.', $data);
    }
}
