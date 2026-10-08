<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\StaffRequest;
use App\Models\StaffProfile;
use App\Services\Admin\StaffService;
use Illuminate\Http\Request;

class StaffController extends BaseController
{
    public function __construct(
        protected StaffService $staffService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.staffs.index');
    }

    /**
     * Staff Listing AJAX
     */
    public function list(Request $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'filter_status' => $request->input('filter_status'),
            'employee_id' => $request->input('employee_id'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $staff = $this->staffService->list(
            filters: $filters,
            perPage: $length,
            page: $page,
            orderColumn: $orderColumn !== null ? (int) $orderColumn : null,
            orderDirection: $orderDirection
        );

        return $this->datatable($staff, (int) $request->input('draw', 1));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StaffRequest $request)
    {
        $this->staffService->create(
            $request->validated()
        );

        return $this->success(
            'Staff created successfully.'
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(StaffProfile $staff)
    {
        $staff->load([
            'user',
        ]);

        return $this->success(
            'Staff fetched successfully.',
            $staff
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StaffRequest $request, StaffProfile $staff)
    {
        $this->staffService->update(
            $staff,
            $request->validated()
        );

        return $this->success(
            'Staff updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StaffProfile $staff)
    {
        $this->staffService->delete(
            $staff
        );

        return $this->success(
            'Admin deleted successfully.'
        );
    }

    /**
     * Update Student Status
     */
    public function changeStatus(
        StaffProfile $staffs
    ) {
        $this->staffService->updateStatus(
            $staffs
        );

        return $this->success(
            'Staff status updated successfully.'
        );
    }

    /**
     * Get Staff / User Permissions
     */
    public function getPermissions(StaffProfile $staff)
    {
        $data = $this->staffService->getPermissionsData($staff);

        return $this->success(
            'User permissions fetched successfully.',
            $data
        );
    }

    /**
     * Update Staff / User Permissions
     */
    public function updatePermissions(Request $request, StaffProfile $staff)
    {
        $request->validate([
            'has_custom_permissions' => 'required|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $this->staffService->updatePermissions(
            $staff,
            $request->boolean('has_custom_permissions'),
            $request->input('permissions', [])
        );

        return $this->success(
            'User permissions updated successfully.'
        );
    }
}
