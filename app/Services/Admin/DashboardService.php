<?php

namespace App\Services\Admin;

use App\Models\AcademicClass;
use App\Models\AdmissionEnquiry;
use App\Models\ContactMessage;
use App\Models\Exam;
use App\Models\FeePayment;
use App\Models\StaffProfile;
use App\Models\StudentAttendance;
use App\Models\StudentEnrollment;
use App\Models\StudentFeeAllocation;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use Illuminate\Support\Carbon;

class DashboardService
{
    public function getDashboardData(): array
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 1. Statistics Cards
        $totalStudents = StudentEnrollment::where('status', 1)->count();
        if ($totalStudents === 0) {
            $totalStudents = StudentProfile::count();
        }

        $totalTeachers = TeacherProfile::count();
        $totalStaff = StaffProfile::count();
        $totalClasses = AcademicClass::where('status', 1)->count();
        $totalSubjects = Subject::count();
        $totalExams = Exam::count();
        $pendingEnquiries = AdmissionEnquiry::whereIn('status', ['pending', 'contacted', 'open'])->count();
        $unreadMessages = ContactMessage::where('status', 'pending')->count();

        // Fee Statistics
        $feeCollectedToday = (float) FeePayment::where('status', 'paid')
            ->whereDate('payment_date', $today)
            ->sum('total_paid');

        $feeCollectedThisMonth = (float) FeePayment::where('status', 'paid')
            ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->sum('total_paid');

        $totalAllocatedFee = (float) StudentFeeAllocation::sum('amount');
        $totalDiscountGiven = (float) StudentFeeAllocation::sum('discount_amount');
        $totalFineCharged = (float) StudentFeeAllocation::sum('fine_amount');
        $totalFeePaidAllTime = (float) StudentFeeAllocation::sum('paid_amount');
        $totalOutstandingDue = max(0, ($totalAllocatedFee - $totalDiscountGiven + $totalFineCharged) - $totalFeePaidAllTime);

        // 2. Today's Attendance Overview
        $todayAttendance = [
            'present' => StudentAttendance::whereDate('attendance_date', $today)->where('status', 'present')->count(),
            'absent' => StudentAttendance::whereDate('attendance_date', $today)->where('status', 'absent')->count(),
            'late' => StudentAttendance::whereDate('attendance_date', $today)->where('status', 'late')->count(),
            'leave' => StudentAttendance::whereDate('attendance_date', $today)->where('status', 'leave')->count(),
        ];
        $totalMarked = array_sum($todayAttendance);
        $todayAttendance['percentage'] = $totalMarked > 0 ? round(($todayAttendance['present'] / $totalMarked) * 100, 1) : 0;

        // 3. Recent 5 Fee Transactions
        $recentPayments = FeePayment::with(['student.user', 'enrollment.studentClass', 'enrollment.section', 'collector'])
            ->latest('id')
            ->limit(5)
            ->get();

        // 4. Recent 5 Student Admissions
        $latestStudents = StudentEnrollment::with(['student.user', 'studentClass', 'section'])
            ->latest('id')
            ->limit(5)
            ->get();

        // 5. Upcoming Exams
        $upcomingExams = Exam::with(['academicClass'])
            ->latest('id')
            ->limit(4)
            ->get();

        // 6. Monthly Fee Collection Trend (Last 6 Months)
        $monthlyFeeLabels = [];
        $monthlyFeeData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $monthKey = $monthDate->format('M Y');
            $monthlyFeeLabels[] = $monthKey;

            $collected = (float) FeePayment::where('status', 'paid')
                ->whereYear('payment_date', $monthDate->year)
                ->whereMonth('payment_date', $monthDate->month)
                ->sum('total_paid');

            $monthlyFeeData[] = $collected;
        }

        // 7. Class-wise Student Distribution
        $classesWithCount = AcademicClass::withCount(['enrollments' => function ($q) {
            $q->where('status', 1);
        }])
            ->orderBy('class_name')
            ->get();

        $classLabels = $classesWithCount->pluck('class_name')->toArray();
        $classStudentCounts = $classesWithCount->pluck('enrollments_count')->toArray();

        return [
            'stats' => [
                'students' => $totalStudents,
                'teachers' => $totalTeachers,
                'staff' => $totalStaff,
                'classes' => $totalClasses,
                'subjects' => $totalSubjects,
                'exams' => $totalExams,
                'pending_enquiries' => $pendingEnquiries,
                'unread_messages' => $unreadMessages,
                'fee_today' => $feeCollectedToday,
                'fee_this_month' => $feeCollectedThisMonth,
                'total_fee_due' => $totalOutstandingDue,
                'fee_paid_total' => $totalFeePaidAllTime,
            ],
            'today_attendance' => $todayAttendance,
            'recent_payments' => $recentPayments,
            'latest_students' => $latestStudents,
            'upcoming_exams' => $upcomingExams,
            'charts' => [
                'fee_monthly_labels' => $monthlyFeeLabels,
                'fee_monthly_data' => $monthlyFeeData,
                'class_labels' => $classLabels,
                'class_counts' => $classStudentCounts,
            ],
        ];
    }
}
