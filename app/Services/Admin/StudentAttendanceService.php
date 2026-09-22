<?php

namespace App\Services\Admin;

use App\Repositories\Admin\StudentAttendanceRepository;
use Illuminate\Support\Facades\DB;

class StudentAttendanceService
{
    protected StudentAttendanceRepository $attendanceRepository;

    /**
     * Create a new class instance.
     */
    public function __construct(StudentAttendanceRepository $attendanceRepository)
    {
        $this->attendanceRepository = $attendanceRepository;
    }

    /**
     * Get students for attendance
     */
    // public function students(array $filters = [])
    // {
    //     return $this->attendanceRepository->getStudents($filters);
    // }

    /**
     * Get students for attendance.
     */
    public function getStudentsForAttendance(
        array $filters = [],
        int $page = 1,
        int $perPage = 10,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        return $this->attendanceRepository
            ->getStudentsForAttendance(
                $filters,
                $page,
                $perPage,
                $orderColumn,
                $orderDirection
            );
    }

    /**
     * Save bulk attendance
     */
    public function save(array $data): array
    {
        $saved = 0;

        DB::transaction(function () use ($data, &$saved) {

            foreach ($data['attendance'] as $attendance) {

                $this->attendanceRepository->saveAttendance([

                    'student_enrollment_id' => (int) $attendance['student_enrollment_id'],
                    'attendance_date' => $data['attendance_date'],
                    'status' => $attendance['status'],
                    'remarks' => $attendance['remarks'] ?? null,
                ]);

                $saved++;
            }

        });

        return [
            'saved' => $saved,
            'attendance_date' => $data['attendance_date'],
        ];
    }

    /**
     * Get attendance history
     */
    public function history(array $filters = [])
    {
        return $this->attendanceRepository->getHistory($filters);
    }
}
