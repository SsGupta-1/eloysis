<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\TeacherAttendanceRequest;
use App\Services\Admin\TeacherAttendanceService;

class TeacherAttendanceController extends BaseController
{
    protected TeacherAttendanceService $teacherAttendanceService;

    public function __construct(TeacherAttendanceService $teacherAttendanceService)
    {
        $this->teacherAttendanceService = $teacherAttendanceService;
    }

    public function index()
    {
        return view('admin.teacher_attendance.index');
    }

    public function list(TeacherAttendanceRequest $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'attendance_date' => $request->input('attendance_date') ?? date('Y-m-d'),
        ];

        $length = max((int) $request->input('length', 10),1);
        $start = max((int) $request->input('start', 0),0);
        $page = (int) floor($start / $length) + 1;
        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir','asc');


        $teachers = $this->teacherAttendanceService
                ->getTeachersForAttendance(
                    filters: $filters,
                    page: $page,
                    perPage: $length,
                    orderColumn: $orderColumn !== null ? (int) $orderColumn : null,
                    orderDirection: $orderDirection
                );


        return $this->datatable(
            $teachers,
            (int) $request->input('draw', 1)
        );
    }

     /**
     * Save bulk attendance
     */
    public function save(TeacherAttendanceRequest $request)
    {
        $result = $this->teacherAttendanceService->save(
            $request->validated()
        );

        return $this->success(
            'Attendance saved successfully.',
            $result,   
        );
    }

}
