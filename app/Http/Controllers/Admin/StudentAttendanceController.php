<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\StudentAttendanceRequest;
use App\Services\Admin\StudentAttendanceService;

class StudentAttendanceController extends BaseController
{
    protected StudentAttendanceService $attendanceService;

    public function __construct(
        StudentAttendanceService $attendanceService
    ) {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Attendance page
     */
    public function index()
    {
        return view('admin.attendance.index', [
            'academicSessions' => academic_session_options(1),
            'classes' => class_options(),
            'sections' => section_options(),
        ]);
    }

    /**
     * Load students for attendance
     */
    public function students(StudentAttendanceRequest $request)
    {

        $filters = [
            'search' => $request->input('search.value'),

            'academic_session_id' => $request->input('academic_session_id') ?? 2,

            'class_id' => $request->input('class_id') ?? 1,

            'section_id' => $request->input('section_id') ?? 1,

            'attendance_date' => $request->input('attendance_date') ?? date('Y-m-d'),
        ];

        $length = max(
            (int) $request->input('length', 10),
            1
        );

        $start = max(
            (int) $request->input('start', 0),
            0
        );

        $page = (int) floor(
            $start / $length
        ) + 1;

        $orderColumn =
            $request->input('order.0.column');

        $orderDirection =
            $request->input(
                'order.0.dir',
                'asc'
            );

        $students =
            $this->attendanceService
                ->getStudentsForAttendance(
                    filters: $filters,
                    page: $page,
                    perPage: $length,
                    orderColumn: $orderColumn !== null
                            ? (int) $orderColumn
                            : null,
                    orderDirection: $orderDirection
                );

        return $this->datatable(
            $students,
            (int) $request->input('draw', 1)
        );
    }

    /**
     * Save bulk attendance
     */
    public function save(StudentAttendanceRequest $request)
    {
        $result = $this->attendanceService->save(
            $request->validated()
        );

        return $this->success(
            'Attendance saved successfully.',
            $result,
        );
    }

    /**
     * Attendance history
     */
    public function history(StudentAttendanceRequest $request)
    {
        $history = $this->attendanceService->history(
            $request->validated()
        );

        return $this->success(
            'Attendance history loaded successfully.',
            $history,
        );
    }
}
