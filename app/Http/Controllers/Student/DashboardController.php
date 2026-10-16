<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\ClassTimetables;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ExamStudentEnrollment;
use App\Models\Periods;
use App\Models\StudentAttendance;
use App\Models\StudentEnrollment;
use App\Models\StudentFeeAllocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Get authenticated student user and current enrollment
     */
    protected function getStudentData(): array
    {
        $user = Auth::guard('student')->user();
        $user->loadMissing([
            'studentProfile',
            'currentEnrollment.studentClass',
            'currentEnrollment.section',
            'currentEnrollment.academicSession',
        ]);

        $profile = $user->studentProfile;
        $enrollment = $user->currentEnrollment;

        // Fallback: If currentEnrollment is null, get latest enrollment from profile
        if (! $enrollment && $profile) {
            $enrollment = StudentEnrollment::where('stu_profile_id', $profile->id)
                ->with(['studentClass', 'section', 'academicSession'])
                ->latest()
                ->first();
        }

        return [$user, $profile, $enrollment];
    }

    /**
     * Student Dashboard / Main Hub
     */
    public function dashboard(): View
    {
        [$user, $profile, $enrollment] = $this->getStudentData();

        $todayDay = strtolower(now()->format('l'));

        $todayClasses = collect();
        $upcomingExams = collect();
        $attendanceStats = [
            'total' => 0,
            'present' => 0,
            'absent' => 0,
            'leave' => 0,
            'percentage' => 0,
        ];
        $recentAttendance = collect();
        $feeSummary = [
            'total_allocated' => 0,
            'total_paid' => 0,
            'total_due' => 0,
            'pending_count' => 0,
        ];
        $recentResults = collect();

        if ($enrollment) {
            // 1. Today's Classes Timetable
            $todayClasses = ClassTimetables::where('academic_session_id', $enrollment->academic_session_id)
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

            // 2. Upcoming Exams (Published exams for student's class)
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
                ->get();

            // 3. Attendance Summary
            $attendances = StudentAttendance::where('student_enrollment_id', $enrollment->id)->get();
            $totalDays = $attendances->count();
            $presentDays = $attendances->where('status', 'present')->count();
            $absentDays = $attendances->where('status', 'absent')->count();
            $leaveDays = $attendances->whereIn('status', ['leave', 'half_day', 'holiday'])->count();

            $attendanceStats = [
                'total' => $totalDays,
                'present' => $presentDays,
                'absent' => $absentDays,
                'leave' => $leaveDays,
                'percentage' => $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0,
            ];

            $recentAttendance = StudentAttendance::where('student_enrollment_id', $enrollment->id)
                ->orderBy('attendance_date', 'desc')
                ->take(7)
                ->get();

            // 4. Fee Summary
            $allocations = StudentFeeAllocation::where('student_enrollment_id', $enrollment->id)->get();
            $totalAllocated = $allocations->sum(function ($a) {
                return max(0, ((float) $a->amount - (float) $a->discount_amount + (float) $a->fine_amount));
            });
            $totalPaid = $allocations->sum('paid_amount');
            $totalDue = max(0, $totalAllocated - $totalPaid);
            $pendingCount = $allocations->where('status', '!=', 'paid')->count();

            $feeSummary = [
                'total_allocated' => $totalAllocated,
                'total_paid' => $totalPaid,
                'total_due' => $totalDue,
                'pending_count' => $pendingCount,
            ];

            // 5. Recent Results
            $recentResults = ExamMark::where('student_enrollment_id', $enrollment->id)
                ->with(['schedule.exam', 'schedule.subject'])
                ->latest()
                ->take(5)
                ->get();
        }

        // School Announcements
        $announcements = Announcement::where('status', 1)
            ->latest()
            ->take(5)
            ->get();

        return view('student.dashboard', compact(
            'user',
            'profile',
            'enrollment',
            'todayDay',
            'todayClasses',
            'upcomingExams',
            'attendanceStats',
            'recentAttendance',
            'feeSummary',
            'recentResults',
            'announcements'
        ));
    }

    /**
     * Student Profile Page
     */
    public function profile(): View
    {
        [$user, $profile, $enrollment] = $this->getStudentData();

        $enrollmentHistory = $profile
            ? StudentEnrollment::where('stu_profile_id', $profile->id)
                ->with(['studentClass', 'section', 'academicSession'])
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        return view('student.profile', compact('user', 'profile', 'enrollment', 'enrollmentHistory'));
    }

    /**
     * Update Student Password
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::guard('student')->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'The provided current password does not match our records.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Your password has been updated successfully!');
    }

    /**
     * Class Timetable
     */
    public function timetable(): View
    {
        [$user, $profile, $enrollment] = $this->getStudentData();

        $periods = Periods::where('status', 1)->orderBy('sort_order')->get();
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $timetableGrid = [];

        if ($enrollment) {
            $timetableRecords = ClassTimetables::where('academic_session_id', $enrollment->academic_session_id)
                ->whereHas('teacherSubject', function ($q) use ($enrollment) {
                    $q->where('class_id', $enrollment->class_id)
                        ->where('section_id', $enrollment->section_id);
                })
                ->with(['period', 'teacherSubject.subject', 'teacherSubject.teacher'])
                ->get();

            foreach ($days as $day) {
                $timetableGrid[$day] = [];
                foreach ($periods as $period) {
                    $item = $timetableRecords->first(function ($rec) use ($day, $period) {
                        return strtolower($rec->day) === $day && $rec->period_id == $period->id;
                    });
                    $timetableGrid[$day][$period->id] = $item;
                }
            }
        }

        $today = strtolower(now()->format('l'));

        return view('student.timetable', compact('user', 'profile', 'enrollment', 'periods', 'days', 'timetableGrid', 'today'));
    }

    /**
     * Attendance Record Page
     */
    public function attendance(Request $request): View
    {
        [$user, $profile, $enrollment] = $this->getStudentData();

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $attendanceLogs = collect();
        $stats = [
            'total' => 0,
            'present' => 0,
            'absent' => 0,
            'leave' => 0,
            'percentage' => 0,
        ];

        if ($enrollment) {
            $query = StudentAttendance::where('student_enrollment_id', $enrollment->id);

            if ($month && $year) {
                $query->whereMonth('attendance_date', $month)->whereYear('attendance_date', $year);
            }

            $attendanceLogs = $query->orderBy('attendance_date', 'desc')->get();

            // Overall stats
            $allAttendances = StudentAttendance::where('student_enrollment_id', $enrollment->id)->get();
            $total = $allAttendances->count();
            $present = $allAttendances->where('status', 'present')->count();
            $absent = $allAttendances->where('status', 'absent')->count();
            $leave = $allAttendances->whereIn('status', ['leave', 'half_day', 'holiday'])->count();

            $stats = [
                'total' => $total,
                'present' => $present,
                'absent' => $absent,
                'leave' => $leave,
                'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
            ];
        }

        return view('student.attendance', compact('user', 'profile', 'enrollment', 'attendanceLogs', 'stats', 'month', 'year'));
    }

    /**
     * Examination Schedule & Details
     */
    public function exams(): View
    {
        [$user, $profile, $enrollment] = $this->getStudentData();

        $exams = collect();

        if ($enrollment) {
            $exams = Exam::where('class_id', $enrollment->class_id)
                ->where('academic_session_id', $enrollment->academic_session_id)
                ->where(function ($q) {
                    $q->where('status', Exam::STATUS_PUBLISHED)
                        ->orWhere('is_published', true);
                })
                ->with(['schedules.subject', 'academicClass', 'academicSession'])
                ->orderBy('start_date', 'desc')
                ->get();
        }

        return view('student.exams', compact('user', 'profile', 'enrollment', 'exams'));
    }

    /**
     * Admit Card View
     */
    public function admitCard(Exam $exam): View
    {
        [$user, $profile, $enrollment] = $this->getStudentData();

        $exam->loadMissing(['schedules.subject', 'academicClass', 'academicSession']);

        // Check enrollment eligibility if record exists
        $examEnrollment = ExamStudentEnrollment::where('exam_id', $exam->id)
            ->where('student_enrollment_id', $enrollment?->id)
            ->first();

        return view('student.admit-card', compact('user', 'profile', 'enrollment', 'exam', 'examEnrollment'));
    }

    /**
     * Results & Marksheet
     */
    public function results(): View
    {
        [$user, $profile, $enrollment] = $this->getStudentData();

        $examResults = collect();

        if ($enrollment) {
            // Find all exams that have marks recorded for this student
            $marks = ExamMark::where('student_enrollment_id', $enrollment->id)
                ->with(['schedule.exam', 'schedule.subject'])
                ->get();

            // Group marks by Exam
            $groupedByExam = $marks->groupBy(function ($item) {
                return $item->schedule?->exam_id;
            });

            foreach ($groupedByExam as $examId => $examMarks) {
                $exam = $examMarks->first()?->schedule?->exam;
                if (! $exam) {
                    continue;
                }

                $totalMarksScored = $examMarks->sum('total_marks');
                $maxMarksTotal = $examMarks->sum(function ($m) {
                    return (float) ($m->schedule?->max_marks ?? 100);
                });
                $percentage = $maxMarksTotal > 0 ? round(($totalMarksScored / $maxMarksTotal) * 100, 2) : 0;

                $examResults->push([
                    'exam' => $exam,
                    'marks' => $examMarks,
                    'total_scored' => $totalMarksScored,
                    'max_marks' => $maxMarksTotal,
                    'percentage' => $percentage,
                    'grade' => $this->calculateGrade($percentage),
                    'passed' => $percentage >= (float) ($exam->passing_marks ?? 33),
                ]);
            }
        }

        return view('student.results', compact('user', 'profile', 'enrollment', 'examResults'));
    }

    /**
     * Detailed Marksheet for an Exam
     */
    public function marksheet(Exam $exam): View
    {
        [$user, $profile, $enrollment] = $this->getStudentData();

        $marks = ExamMark::where('student_enrollment_id', $enrollment?->id)
            ->whereHas('schedule', function ($q) use ($exam) {
                $q->where('exam_id', $exam->id);
            })
            ->with(['schedule.subject'])
            ->get();

        $totalScored = $marks->sum('total_marks');
        $maxTotal = $marks->sum(function ($m) {
            return (float) ($m->schedule?->max_marks ?? 100);
        });
        $percentage = $maxTotal > 0 ? round(($totalScored / $maxTotal) * 100, 2) : 0;
        $passed = $percentage >= (float) ($exam->passing_marks ?? 33);
        $grade = $this->calculateGrade($percentage);

        return view('student.marksheet', compact('user', 'profile', 'enrollment', 'exam', 'marks', 'totalScored', 'maxTotal', 'percentage', 'passed', 'grade'));
    }

    /**
     * Fees & Payment History
     */
    public function fees(): View
    {
        [$user, $profile, $enrollment] = $this->getStudentData();

        $allocations = collect();
        $summary = [
            'total_allocated' => 0,
            'total_paid' => 0,
            'total_due' => 0,
        ];

        if ($enrollment) {
            $allocations = StudentFeeAllocation::where('student_enrollment_id', $enrollment->id)
                ->with(['feeHead', 'feeStructure', 'academicSession'])
                ->orderBy('due_date', 'asc')
                ->get();

            $totalAllocated = $allocations->sum(function ($a) {
                return max(0, ((float) $a->amount - (float) $a->discount_amount + (float) $a->fine_amount));
            });
            $totalPaid = $allocations->sum('paid_amount');
            $totalDue = max(0, $totalAllocated - $totalPaid);

            $summary = [
                'total_allocated' => $totalAllocated,
                'total_paid' => $totalPaid,
                'total_due' => $totalDue,
            ];
        }

        return view('student.fees', compact('user', 'profile', 'enrollment', 'allocations', 'summary'));
    }

    /**
     * Grade calculation helper
     */
    protected function calculateGrade(float $percentage): string
    {
        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B+',
            $percentage >= 60 => 'B',
            $percentage >= 50 => 'C',
            $percentage >= 40 => 'D',
            default => 'F',
        };
    }
}
