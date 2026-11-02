<?php

namespace App\Services\Student;

use App\Models\Event;
use App\Models\StudentAttendance;
use Carbon\Carbon;

class StudentAttendanceHelper
{
    /**
     * Build full monthly calendar and statistics for student attendance.
     * Manages Sundays, Holidays, explicit attendance, and unmarked days as Absent.
     *
     * @param int $studentEnrollmentId
     * @param int|null $month
     * @param int|null $year
     * @return array
     */
    public static function buildMonthlyAttendance(int $studentEnrollmentId, ?int $month = null, ?int $year = null): array
    {
        $now = now();
        $month = (int) ($month ?: $now->month);
        $year = (int) ($year ?: $now->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $daysInMonth = $startDate->daysInMonth;
        $endDate = Carbon::createFromDate($year, $month, $daysInMonth)->endOfDay();

        // 1. Fetch DB attendance records for this month
        $dbAttendances = StudentAttendance::where('student_enrollment_id', $studentEnrollmentId)
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->attendance_date)->toDateString();
            });

        // 2. Fetch School Holiday Events for this month (if any)
        $holidayEvents = Event::where('status', 1)
            ->whereBetween('event_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where(function ($q) {
                $q->where('title', 'like', '%holiday%')
                  ->orWhere('title', 'like', '%vacation%')
                  ->orWhere('title', 'like', '%break%');
            })
            ->get()
            ->keyBy(function ($e) {
                return Carbon::parse($e->event_date)->toDateString();
            });

        $calendar = [];
        $presentDays = 0;
        $absentDays = 0;
        $leaveDays = 0;
        $sundayDays = 0;
        $holidayDays = 0;
        $upcomingDays = 0;

        $todayDate = $now->toDateString();

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day);
            $dateStr = $date->toDateString();
            $dayOfWeek = $date->dayOfWeek; // 0 = Sunday, 6 = Saturday
            $isToday = ($dateStr === $todayDate);
            $isFuture = ($date->isFuture() && !$isToday);

            $status = 'present';
            $label = 'Present';
            $remarks = null;
            $isWorkingDay = true;

            // Priority 1: Check Sunday
            if ($dayOfWeek === Carbon::SUNDAY) {
                $status = 'sunday';
                $label = 'Sunday (Weekend)';
                $isWorkingDay = false;
                $sundayDays++;
            }
            // Priority 2: Check School Holiday Event
            elseif (isset($holidayEvents[$dateStr])) {
                $status = 'holiday';
                $label = $holidayEvents[$dateStr]->title ?? 'Holiday';
                $remarks = $holidayEvents[$dateStr]->description;
                $isWorkingDay = false;
                $holidayDays++;
            }
            // Priority 3: Check Future Days
            elseif ($isFuture) {
                $status = 'upcoming';
                $label = 'Upcoming';
                $isWorkingDay = false;
                $upcomingDays++;
            }
            // Priority 4: Working Day in past or today
            else {
                // Check if recorded in database
                if (isset($dbAttendances[$dateStr])) {
                    $record = $dbAttendances[$dateStr];
                    $dbStatus = strtolower(trim($record->status));
                    $remarks = $record->remarks;

                    if ($dbStatus === 'present') {
                        $status = 'present';
                        $label = 'Present';
                        $presentDays++;
                    } elseif ($dbStatus === 'leave') {
                        $status = 'leave';
                        $label = 'Approved Leave';
                        $isWorkingDay = false;
                        $leaveDays++;
                    } elseif ($dbStatus === 'holiday') {
                        $status = 'holiday';
                        $label = 'Holiday';
                        $isWorkingDay = false;
                        $holidayDays++;
                    } else { // absent or other
                        $status = 'absent';
                        $label = 'Absent';
                        $absentDays++;
                    }
                } else {
                    // Unrecorded past working day -> count as Absent!
                    $status = 'absent';
                    $label = 'Absent (Unrecorded)';
                    $absentDays++;
                }
            }

            $calendar[] = [
                'day' => $day,
                'date' => $dateStr,
                'formatted_date' => $date->format('d M, Y'),
                'day_name' => $date->format('l'),
                'short_day' => $date->format('D'),
                'day_of_week' => $dayOfWeek, // 0 to 6
                'status' => $status, // present, absent, sunday, holiday, leave, upcoming
                'label' => $label,
                'remarks' => $remarks,
                'is_today' => $isToday,
                'is_working_day' => $isWorkingDay,
            ];
        }

        $totalWorkingDays = $presentDays + $absentDays;
        $monthlyPercentage = $totalWorkingDays > 0 ? round(($presentDays / $totalWorkingDays) * 100, 1) : 0;

        $monthlyStats = [
            'month' => $month,
            'year' => $year,
            'month_name' => Carbon::createFromDate($year, $month, 1)->format('F'),
            'total_calendar_days' => $daysInMonth,
            'total_working_days' => $totalWorkingDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'leave_days' => $leaveDays,
            'sunday_days' => $sundayDays,
            'holiday_days' => $holidayDays,
            'upcoming_days' => $upcomingDays,
            'percentage' => $monthlyPercentage,
            'is_low_attendance' => ($monthlyPercentage < 75 && $totalWorkingDays > 0),
        ];

        // Overall all-time summary for student's entire enrollment
        $allRecords = StudentAttendance::where('student_enrollment_id', $studentEnrollmentId)->get();
        $totalAll = $allRecords->count();
        $presentAll = $allRecords->where('status', 'present')->count();
        $absentAll = $allRecords->where('status', 'absent')->count();
        $overallPercentage = $totalAll > 0 ? round(($presentAll / $totalAll) * 100, 1) : 0;

        $overallStats = [
            'total_recorded' => $totalAll,
            'present' => $presentAll,
            'absent' => $absentAll,
            'percentage' => $overallPercentage,
        ];

        return [
            'monthly_stats' => $monthlyStats,
            'overall_stats' => $overallStats,
            'calendar' => $calendar,
            'first_day_of_week' => Carbon::createFromDate($year, $month, 1)->dayOfWeek, // for calendar grid blank offsets
        ];
    }
}
