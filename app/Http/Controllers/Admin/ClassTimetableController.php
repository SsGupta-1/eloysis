<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Services\Admin\ClassTimetableService;
use App\Http\Requests\Admin\ClassTimetableRequest;
use App\Models\ClassTimetables;


class ClassTimetableController extends BaseController
{
    protected $classTimetableService;
    public function __construct(ClassTimetableService $classTimetableService)    
    {
        $this->classTimetableService = $classTimetableService;
    }
    //index
    public function index()
    {
        return view('admin.class_timetables.index',[
            'academicSessions' => academic_session_options(1),
            'classes' => class_options(),
            'sections' => section_options(),
            'teachers' => teacher_options(),
            'periods' => period_options(),
            'teacherSubjects' => teacher_subject_options()
            
        ]);
    }

    //list
    public function list(Request $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'filter_status' => $request->input('filter_status'),
            'academic_session_id' => $request->input('academic_session_id'),
            'class_id' => $request->input('class_id'),
            'section_id' => $request->input('section_id'),
            'teacher_id' => $request->input('teacher_id'),
            'day' => $request->input('day'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $classTimetables = $this->classTimetableService->getLists(
            $filters,
            $page,
            $length,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($classTimetables, (int) $request->input('draw', 1));
    }

    //create
    public function create()
    {
        //return view('admin.class-timetables.create');
    }

    //store
    public function store(ClassTimetableRequest $request)
    {
        $this->classTimetableService->create(
            $request->validated()
        );

        return $this->success(
            'Class timetable created successfully.'
        );
    }

    //edit
    public function edit(ClassTimetables $classTimetable)
    {
         return $this->success(
            'Class timetable fetched successfully.',
            $classTimetable
        );
    }

    //update
    public function update(ClassTimetableRequest $request, ClassTimetables $classTimetable)
    {
        $this->classTimetableService->update(
            $classTimetable->id,
            $request->validated()
        );

        return $this->success(
            'Class timetable updated successfully.'
        );
    }

    //destroy
    public function destroy(ClassTimetables $classTimetable)
    {
        $this->classTimetableService->delete(
            $classTimetable->id
        );

        return $this->success(
            'Class timetable deleted successfully.'
        );
    }

    //change status
    public function changeStatus(ClassTimetables $classTimetable)
    {
        $this->classTimetableService->changeStatus($classTimetable->id);

        return $this->success(
            'Class timetable status updated successfully.'
        );
    }
}
