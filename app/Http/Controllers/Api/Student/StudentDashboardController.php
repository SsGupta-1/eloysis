<?php

namespace App\Http\Controllers\Api\Student;

use App\Models\Announcement;
use App\Models\ClassTimetables;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ImportantMessage;
use App\Models\StudentAttendance;
use App\Models\StudentFeeAllocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentDashboardController extends BaseStudentApiController
{
    /**
     * Mobile App Student Dashboard
     */
    public function index(Request $request): JsonResponse
    {
        [$user, $profile, $enrollment] = $this->getStudentData($request);

        if (! $profile) {
            return $this->error('Student profile not found.', [], 404);
        }

        $todayDay = strtolower(now()->format('l'));

        $todayClasses = [];
        $upcomingExams = [];
        $attendanceStats = [
            'total' => 0,
            'present' => 0,
            'absent' => 0,
            'leave' => 0,
            'percentage' => 0,
        ];
        $feeSummary = [
            'total_allocated' => 0.0,
            'total_paid' => 0.0,
            'total_due' => 0.0,
            'pending_count' => 0,
        ];
        $recentResults = [];

        if ($enrollment) {
            // 1. Today's Classes
            $classesRecords = ClassTimetables::where('academic_session_id', $enrollment->academic_session_id)
                ->where('day', $todayDay)
                ->whereHas('teacherSubject', function ($q) use ($enrollment) {
                    $q->where('class_id', $enrollment->class_id)
                        ->where('section_id', $enrollment->section_id);
                })
                ->with(['period', 'teacherSubject.subject', 'teacherSubject.teacher'])
                ->get()
                ->sortBy(function ($item) {
                    return $item->period?->sort_order ?? $item->period_id;
                });

            $todayClasses = $classesRecords->map(function ($item) {
                return [
                    'id' => $item->id,
                    'period_id' => $item->period_id,
                    'period_name' => $item->period?->name ?? 'Period',
                    'start_time' => $item->period?->start_time ? date('h:i A', strtotime($item->period->start_time)) : null,
                    'end_time' => $item->period?->end_time ? date('h:i A', strtotime($item->period->end_time)) : null,
                    'subject_name' => $item->teacherSubject?->subject?->subject_name ?? 'N/A',
                    'subject_code' => $item->teacherSubject?->subject?->subject_code ?? null,
                    'teacher_name' => $item->teacherSubject?->teacher?->name ?? 'N/A',
                ];
            })->values();

            // 2. Upcoming Exams
            $upcomingExams = Exam::where('class_id', $enrollment->class_id)
                ->where('academic_session_id', $enrollment->academic_session_id)
                ->where(function ($q) {
                    $q->where('status', Exam::STATUS_PUBLISHED)
                        ->orWhere('is_published', true);
                })
                ->where(function ($q) {
                    $q->whereDate('end_date', '>=', now()->toDateString())
                        ->orWhereNull('end_date');
                })
                ->with(['schedules.subject'])
                ->orderBy('start_date', 'asc')
                ->take(4)
                ->get()
                ->map(function ($exam) {
                    return [
                        'id' => $exam->id,
                        'title' => $exam->title,
                        'exam_code' => $exam->exam_code,
                        'start_date' => $exam->start_date ? $exam->start_date->format('Y-m-d') : null,
                        'end_date' => $exam->end_date ? $exam->end_date->format('Y-m-d') : null,
                        'total_subjects' => $exam->schedules->count(),
                        'schedules' => $exam->schedules->map(function ($s) {
                            return [
                                'subject_name' => $s->subject?->subject_name,
                                'exam_date' => $s->exam_date ? $s->exam_date->format('Y-m-d') : null,
                                'start_time' => $s->start_time,
                                'end_time' => $s->end_time,
                            ];
                        }),
                    ];
                });

            // 3. Current Month Attendance Stats
            $monthlyAttendance = \App\Services\Student\StudentAttendanceHelper::buildMonthlyAttendance($enrollment->id);
            $attendanceStats = [
                'total' => $monthlyAttendance['monthly_stats']['total_working_days'],
                'present' => $monthlyAttendance['monthly_stats']['present_days'],
                'absent' => $monthlyAttendance['monthly_stats']['absent_days'],
                'leave' => $monthlyAttendance['monthly_stats']['leave_days'],
                'sunday_days' => $monthlyAttendance['monthly_stats']['sunday_days'],
                'percentage' => $monthlyAttendance['monthly_stats']['percentage'],
            ];

            // 4. Fee Summary
            $allocations = StudentFeeAllocation::where('student_enrollment_id', $enrollment->id)->get();
            $totalAllocated = (float) $allocations->sum(function ($a) {
                return max(0, ((float) $a->amount - (float) $a->discount_amount + (float) $a->fine_amount));
            });
            $totalPaid = (float) $allocations->sum('paid_amount');
            $totalDue = (float) max(0, $totalAllocated - $totalPaid);
            $pendingCount = $allocations->where('status', '!=', 'paid')->count();

            $feeSummary = [
                'total_allocated' => round($totalAllocated, 2),
                'total_paid' => round($totalPaid, 2),
                'total_due' => round($totalDue, 2),
                'pending_count' => $pendingCount,
            ];

            // 5. Recent Results
            $recentResults = ExamMark::where('student_enrollment_id', $enrollment->id)
                ->with(['schedule.exam', 'schedule.subject'])
                ->latest('id')
                ->take(5)
                ->get()
                ->map(function ($mark) {
                    $maxMarks = (float) ($mark->schedule?->max_marks ?? 100);
                    $total = (float) $mark->total_marks;
                    $pct = $maxMarks > 0 ? round(($total / $maxMarks) * 100, 1) : 0;

                    return [
                        'id' => $mark->id,
                        'exam_title' => $mark->schedule?->exam?->title ?? 'Exam',
                        'subject_name' => $mark->schedule?->subject?->subject_name ?? 'Subject',
                        'total_marks' => $total,
                        'max_marks' => $maxMarks,
                        'percentage' => $pct,
                        'grade' => $this->calculateGrade($pct),
                    ];
                });
        }

        // 6. Flash Alerts & Announcements
        $flashAlerts = ImportantMessage::active()
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'title' => $msg->title,
                    'message' => $msg->message,
                    'type' => $msg->type ?? 'info',
                    'action_text' => $msg->action_text,
                    'action_url' => $msg->action_url,
                ];
            });

        $announcements = Announcement::active()
            ->latest('id')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'content' => $item->content,
                    'badge' => $item->badge,
                    'link_url' => $item->link_url,
                    'link_text' => $item->link_text,
                    'date' => $item->created_at ? $item->created_at->format('d M, Y') : null,
                ];
            });

        $data = [
            'student' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'profile_image' => $user->profile_image_url,
                'admission_no' => $profile->admission_no,
                'roll_number' => $enrollment?->roll_number,
                'class_name' => $enrollment?->studentClass?->class_name,
                'section_name' => $enrollment?->section?->section_name,
                'academic_session' => $enrollment?->academicSession?->name,
            ],
            'today_day' => ucfirst($todayDay),
            'flash_alerts' => $flashAlerts,
            'announcements' => $announcements,
            'today_classes' => $todayClasses,
            'attendance_stats' => $attendanceStats,
            'fee_summary' => $feeSummary,
            'upcoming_exams' => $upcomingExams,
            'recent_results' => $recentResults,
        ];

        return $this->success('Student dashboard fetched successfully.', $data);
    }
}
