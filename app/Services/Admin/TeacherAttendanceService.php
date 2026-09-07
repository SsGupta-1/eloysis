<?php

namespace App\Services\Admin;

use App\Repositories\Admin\TeacherAttendanceRepository;
use Illuminate\Support\Facades\DB;

class TeacherAttendanceService
{
    protected TeacherAttendanceRepository $teacherAttendanceRepository;
    /**
     * Create a new class instance.
     */
    public function __construct(TeacherAttendanceRepository $teacherAttendanceRepository)
    {
        $this->teacherAttendanceRepository = $teacherAttendanceRepository;
    }
    public function getTeachersForAttendance(
        array $filters = [],
        int $page = 1,
        int $perPage = 10,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        return $this->teacherAttendanceRepository
            ->getTeachersForAttendance(
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

                $this->teacherAttendanceRepository->saveAttendance([

                    'teacher_profile_id' => (int) $attendance['teacher_profile_id'],
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
}
