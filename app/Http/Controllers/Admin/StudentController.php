<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\StudentRequest;
use App\Models\FeeDiscount;
use App\Models\StudentEnrollment;
use App\Models\StudentProfile;
use App\Services\Admin\StudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends BaseController
{
    public function __construct(
        protected StudentService $studentService
    ) {}

    /**
     * Get suggested roll number for session, class, and section
     */
    public function getSuggestedRollNumber(Request $request): JsonResponse
    {
        $sessionId = $request->input('academic_session_id');
        $classId = $request->input('class_id');
        $sectionId = $request->input('section_id');

        $suggestedRollNo = $this->studentService->generateSuggestedRollNumber(
            $sessionId ? (int) $sessionId : null,
            $classId ? (int) $classId : null,
            $sectionId ? (int) $sectionId : null
        );

        return response()->json([
            'status' => true,
            'suggested_roll_number' => $suggestedRollNo,
        ]);
    }

    /**
     * Student Management Page
     */
    public function index()
    {
        return view(
            'admin.students.index',
            [
                'academicSessions' => academic_session_options(),
                'classes' => class_options(),
                'sections' => section_options(),
            ]
        );
    }

    public function list(Request $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'status' => $request->input('status'),
            'academic_session_id' => $request->input('academic_session_id'),
            'class_id' => $request->input('class_id'),
            'section_id' => $request->input('section_id'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $students = $this->studentService->getList(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($students, (int) $request->input('draw', 1));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $feeDiscounts = FeeDiscount::where('is_active', true)->orderBy('name')->pluck('name', 'id')->toArray();

        return view(
            'admin.students.create',
            [
                'academicSessions' => academic_session_options(),
                'classes' => class_options(),
                'sections' => section_options(),
                'feeDiscounts' => $feeDiscounts,
            ]
        );
    }

    /**
     * Store Student
     */
    public function store(StudentRequest $request)
    {
        $this->studentService->create(
            $request->validated()
        );

        return $this->success(
            'Student created successfully.'
        );
    }

    // **********************Student actions By student Profile************************************** */
    // /**
    //  * Show Student
    //  */
    // public function show(StudentProfile $student)
    // {
    //     // $student = $this->studentService->find(
    //     //     $id
    //     // );

    //     // return $this->success(
    //     //     'Student details fetched successfully.',
    //     //     $student
    //     // );

    //     $student->load([
    //         'user',
    //         'enrollments.academicSession',
    //         'enrollments.studentClass',
    //         'enrollments.section',
    //     ]);

    //     $enrollment = $student->enrollments
    //         ->sortByDesc('id')
    //         ->first();

    //     return view(
    //         'admin.students.view',
    //         compact(
    //             'student',
    //             'enrollment'
    //         )
    //     );
    // }

    // /**
    //  * Edit
    //  */
    // public function edit(StudentProfile $student)
    // {
    //     $student->load([
    //         'user',
    //         'enrollments',
    //     ]);

    //     $enrollment = $student->enrollments
    //         ->sortByDesc('id')
    //         ->first();

    //     return view('admin.students.edit', [

    //         'student' => $student,

    //         'enrollment' => $enrollment,

    //        'academicSessions' => academic_session_options(1),
    //         'classes' => class_options(),
    //         'sections' => section_options(),

    //     ]);
    // }

    // /**
    //  * Update Student
    //  */
    // public function update(
    //     StudentRequest $request,
    //     StudentProfile $student
    // ) {
    //     $this->studentService->update(
    //         $student->id,
    //         $request->validated()
    //     );

    //     return $this->success(
    //         'Student updated successfully.'
    //     );
    // }

    // /**
    //  * Delete Student
    //  */
    // public function destroy(StudentProfile $student)
    // {
    //     $this->studentService->delete(
    //         $student->id
    //     );

    //     return $this->success(
    //         'Student deleted successfully.'
    //     );
    // }

    // /**
    //  * Update Student Status
    //  */
    // public function changeStatus(
    //     StudentProfile $students
    // ) {
    //     $this->studentService->updateStatus(
    //         $students->id
    //     );

    //     return $this->success(
    //         'Student status updated successfully.'
    //     );
    // }

    // *****************Student actions By student Profile end*********************************** */

    // *******************Student actions By student Enrollment ************************************ */

    /**
     * Show Student
     */
    public function show(StudentEnrollment $student)
    {

        $student->load([
            'student.user',
            'academicSession',
            'studentClass',
            'section',
        ]);

        $enrollment = $student;

        return view(
            'admin.students.view',
            compact(
                'enrollment'
            )
        );
    }

    /**
     * Edit
     */
    public function edit(StudentEnrollment $student)
    {
        $student->load([
            'student.user',
            'feeDiscount',
        ]);

        $feeDiscounts = FeeDiscount::where('is_active', true)->orderBy('name')->pluck('name', 'id')->toArray();

        return view('admin.students.edit', [
            'enrollment' => $student,
            'academicSessions' => academic_session_options(),
            'classes' => class_options(),
            'sections' => section_options(),
            'feeDiscounts' => $feeDiscounts,
        ]);
    }

    /**
     * Update Student
     */
    public function update(
        StudentRequest $request,
        StudentEnrollment $student
    ) {
        $this->studentService->update(
            $student->id,
            $request->validated()
        );

        return $this->success(
            'Student updated successfully.'
        );
    }

    /**
     * Delete Student
     */
    public function destroy(StudentEnrollment $student)
    {
        $this->studentService->delete(
            $student->id
        );

        return $this->success(
            'Student deleted successfully.'
        );
    }

    /**
     * Update Student Status
     */
    public function changeStatus(
        StudentEnrollment $students
    ) {
        $this->studentService->updateStatus(
            $students->id
        );

        return $this->success(
            'Student status updated successfully.'
        );
    }
}
