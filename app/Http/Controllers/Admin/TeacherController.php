<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\TeacherRequest;
use App\Models\TeacherProfile;
use App\Services\Admin\TeacherService;
use Illuminate\Http\Request;

class TeacherController extends BaseController
{
    protected TeacherService $teacherService;

    public function __construct(TeacherService $teacherService)
    {

        $this->teacherService = $teacherService;
    }

    /**
     * Teacher listing page
     */
    public function index()
    {
        return view('admin.teachers.index');
    }

    /**
     * Teacher list AJAX
     */
    public function list(Request $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'filter_status' => $request->input('filter_status'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $teachers = $this->teacherService->getList(
            filters: $filters,
            perPage: $length,
            page: $page,
            orderColumn: $orderColumn !== null ? (int) $orderColumn : null,
            orderDirection: $orderDirection
        );

        return $this->datatable($teachers, (int) $request->input('draw', 1));
    }

    /**
     * Get teacher details
     */
    public function show(TeacherProfile $teacher)
    {
        return $this->success(
            'Teacher details fetched successfully.',
            $this->teacherService->find($teacher->id)
        );
    }

    /**
     * Store teacher
     */
    public function store(TeacherRequest $request)
    {
        $this->teacherService->create(
            $request->validated()
        );

        return $this->success(
            'Teacher created successfully.'
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TeacherProfile $teacher)
    {
        return $this->success(
            'Teacher details fetched successfully.',
            $this->teacherService->find($teacher->id)
        );
    }

    /**
     * Update teacher
     */
    public function update(
        TeacherRequest $request,
        TeacherProfile $teacher
    ) {
        $this->teacherService->update(
            $teacher->id,
            $request->validated()
        );

        return $this->success(
            'Teacher updated successfully.'
        );
    }

    /**
     * Delete teacher
     */
    public function destroy(TeacherProfile $teacher)
    {
        $this->teacherService->delete(
            $teacher->id
        );

        return $this->success(
            'Teacher deleted successfully.'
        );
    }

    /**
     * Change teacher status
     */
    // public function status(
    //     Request $request,
    //     TeacherProfile $teacher
    // ) {
    //     $this->teacherService->changeStatus(
    //         $teacher->id,
    //         (bool) $request->status
    //     );

    //     return $this->success(
    //         'Teacher status updated successfully.'
    //     );
    // }

    /**
     * Change status
     */
    public function changeStatus(TeacherProfile $teachers)
    {
        $this->teacherService->changeStatus($teachers->id);

        return $this->success(
            'Academic Teacher status updated successfully.'
        );
    }

    /**
     * Get Teacher / User Permissions
     */
    public function getPermissions(TeacherProfile $teacher)
    {
        $data = $this->teacherService->getPermissionsData($teacher);

        return $this->success(
            'Teacher permissions fetched successfully.',
            $data
        );
    }

    /**
     * Update Teacher / User Permissions
     */
    public function updatePermissions(Request $request, TeacherProfile $teacher)
    {
        $request->validate([
            'has_custom_permissions' => 'required|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $this->teacherService->updatePermissions(
            $teacher,
            $request->boolean('has_custom_permissions'),
            $request->input('permissions', [])
        );

        return $this->success(
            'Teacher permissions updated successfully.'
        );
    }
}
